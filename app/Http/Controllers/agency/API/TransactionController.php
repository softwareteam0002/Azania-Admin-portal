<?php

namespace App\Http\Controllers\agency\API;

use App\Helper\Constants;
use App\Helper\Converter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TransactionController extends Controller
{
    private $agentId;
    private $operatorId;
    private $terminalId;
    private $agentAccount;
    private $agentMsisdn;
    private $imei1;
    private $imei2;
    private $key;
    private const TEXT_CONTENT_TYPE = 'text/xml';
    private const JSON_CONTENT_TYPE = 'application/json';
    private const AUTH_KEY = 'JJ';

    public function __construct(Request $request)
    {
        try {
            Log::channel('agency')->info("----REQUEST FROM POS---- ");
            Log::channel('agency')->info("AGENCY-TRANSACTION-ALL: " . json_encode($request->all()));
            Log::channel('agency')->info("----START INITIALIZE POS REQUEST---- ");
            $operator = Auth::guard('operator')->user();

            if (!$operator) {
                throw new \Exception("----FAILED TO GET OPERATOR----");
            }

            $this->agentId = $operator->agent_id;
            $this->operatorId = $operator->operator_id;
            $this->agentMsisdn = $operator->operator_msisdn;
            $this->terminalId = $operator->device->terminal_ID;
            $this->agentAccount = $operator->device->tradingac->bank_account;
            $this->imei1 = $operator->device->device_imei1;
            $this->imei2 = $operator->device->device_imei2;

            Log::channel('agency')->info("OPERATOR-MSISDN: " . $this->agentMsisdn);
            Log::channel('agency')->info("DEVICE-IMEI1: " . $this->imei1);
            Log::channel('agency')->info("DEVICE-IMEI2: " . $this->imei2);


            $this->key = $this->getDeviceKey($this->imei1, $this->imei2);

            if (!$this->key || is_null($this->key)) {
                throw new \Exception('----FAILED TO GET KEY----');
            }
            Log::channel('agency')->info("----END INITIALIZE POS REQUEST---- ");
        } catch (\Exception $e) {
            Log::channel('agency')->info("----INITIALIZATION EXCEPTION---- ");
            Log::channel('agency')->error("AGENCY-INITIALIZATION-ERROR: " . json_encode($e->getMessage()));
            abort($this->handleError(100, "Initialization Failed", $e->getMessage()));
        }
    }

    public function initiateTransaction($data)
    {
        try {
            Log::channel('agency')->info("----DECRYPTING REQUEST FROM POS---- ");
            return $this->decrypt($data, $this->key);
        } catch (\Exception $e) {
            Log::error("----FAILED TO DECRYPT REQUEST FROM POS----");
            Log::error("FAILED-TO-DECRYPT: " . $e->getMessage());
            return $this->handleError(500, 'Exception occurred', $e->getMessage());
        }
    }

    public function processTransactions(Request $request): \Illuminate\Http\JsonResponse
    {
        $mti = $request->header('MTI');

        $url = env('AGENCY_SERVICES_TWO_URL');
        try {
            if ($mti != Constants::DATA_MAP) {
                $data = $this->initiateTransaction($request->data);
                Log::channel('agency')->info("AGENCY-TRANSACTION-REQUEST: " . $data);
                $decodedRequest = json_decode($data, true);
            }
            Log::channel('agency')->info("SERVICE-MTI: " . $mti);
            // Switch case based on the MTI
            switch ($mti) {
                case Constants::FUND_TRANSFER:
                    $payload = [
                        "MTI" => (string)$mti,
                        "type" => (string)$decodedRequest['type'],
                        "amount" => (string)$decodedRequest['amount'],
                        "accountID" => (string)$this->agentAccount,
                        "customerAccount" => (string)$decodedRequest['customerAccount'],
                        "transferReason" => (string)$decodedRequest['transferReason'],
                        "operator_id" => (string)$this->operatorId,
                        "terminalId" => (string)$this->terminalId,
                        "auth_key" => self::AUTH_KEY,
                        "agentID" => (string)$this->agentId,
                        "track_2_data" => (string)$decodedRequest['track_2_data'] ?? "",
                        "encyptedPIN" => (string)$decodedRequest['encyptedPIN'],
                        "isCard" => (string)$decodedRequest['isCard']
                    ];
                    // Transfer fund transaction
                    return $this->sendRequest($url . 'AgentFundTransfer', $payload);
                    break;
                case Constants::CARD_WITHDRAW:
                    $payload = [
                        "type" => "card_withdrawal",
                        "amount" => (string)$decodedRequest['amount'],
                        "account" => (string)$decodedRequest['customerAccount'],
                        "app_type" => "0",
                        "auth_key" => self::AUTH_KEY,
                        "card_track2" => (string)$decodedRequest['track_2_data'] ?? "",
                        "card_pin" => (string)$decodedRequest['encyptedPIN'],
                        "uid" => (string)$decodedRequest['bin'],
                        "agent_id" => (string)$this->operatorId,
                        "customer_msisdn" => (string)$decodedRequest['mobile'],
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    // Withdraw card transaction
                    return $this->sendRequest($url . 'AgentTransactions', $payload, self::TEXT_CONTENT_TYPE);
                    break;
                case Constants::DEPOSIT:
                    $payload = [
                        "type" => "deposit",
                        "amount" => (string)$decodedRequest['amount'],
                        "recipient_account" => (string)$decodedRequest['customerAccount'],
                        "name" => (string)$decodedRequest['customerAccountName'],
                        "account" => (string)$this->agentAccount,
                        "uid" => $decodedRequest['bin'],
                        "auth_key" => self::AUTH_KEY,
                        "receiver_uid" => $decodedRequest['receiver_bin'],
                        "app_type" => "0",
                        "card_track2" => (string)$decodedRequest['track_2_data'],
                        "card_pin" => (string)$decodedRequest['encyptedPIN'],
                        "agent_id" => (string)$this->operatorId,
                        "customer_msisdn" => (string)$decodedRequest['depositorMobile'],
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    // Deposit transaction
                    return $this->sendRequest($url . 'AgentTransactions', $payload, self::TEXT_CONTENT_TYPE);
                    break;
                case Constants::DEPOSIT_QUERY_NAME:
                    $payload = [
                        "agent_id" => (string)$this->operatorId,
                        "type" => "QueryCustAccDeposit",
                        "app_type" => "0",
                        "uid" => $decodedRequest['bin'],
                        "operation" => "QueryCustAccDeposit",
                        "accountNumber" => $decodedRequest['accountID'],
                        "brn" => "",
                        "language" => "ENG",
                        "auth_key" => self::AUTH_KEY,
                    ];

                    // Deposit Query Name transaction
                    return $this->sendRequest($url . 'mwangaHakikaAccountInquiry', $payload);
                    break;
                case Constants::CARDLESS_WITHDRAW:
                    $payload = [
                        "MTI" => (string)$mti,
                        "type" => (string)$decodedRequest['type'],
                        "accountID" => (string)$this->agentAccount,
                        "operator_id" => (string)$this->operatorId,
                        "terminalId" => (string)$this->terminalId,
                        "agentID" => (string)$this->agentId,
                        "token" => (string)$decodedRequest['token'],
                        "mobile" => (string)$decodedRequest['mobile'],
                        "auth_key" => self::AUTH_KEY,
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    // Deposit transaction
                    return $this->sendRequest($url . 'AgentTransactions', $payload, self::TEXT_CONTENT_TYPE);
                    break;
                case Constants::AGENT_STATEMENT:
                    $payload = [
                        "MTI" => (string)$mti,
                        "start_date" => (string)$decodedRequest['startdate'],
                        "end_date" => (string)$decodedRequest['enddate'],
                        "app_type" => (string)$decodedRequest['app_type'],
                        "from" => "0",
                        "to" => "10000",
                        "agent_id" => (string)$this->operatorId,
                        "type" => (string)$decodedRequest['app_type'],
                        "auth_key" => self::AUTH_KEY,
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    return $this->sendRequest($url . 'AgencyFullStatement', $payload, self::TEXT_CONTENT_TYPE);
                    break;
                case Constants::AGENT_MINISTATEMENT:
                    $payload = [
                        "MTI" => (string)$mti,
                        "from_date" => (string)$decodedRequest['from_date'],
                        "agentID" => (string)$this->agentId,
                        "to_date" => (string)$decodedRequest['to_date'],
                        "terminalID" => (string)$this->terminalId,
                        "accountID" => (string)$this->agentAccount,
                        "operator_id" => (string)$this->operatorId,
                        "auth_key" => self::AUTH_KEY,
                        "track_2_data" => (string)$decodedRequest['track_2_data'] ?? "",
                        "isCard" => (string)$decodedRequest['isCard'],
                        "encyptedPIN" => (string)$decodedRequest['encyptedPIN'],
                        "type" => (string)$decodedRequest['type'],
                    ];
                    // Customer Ministatement transaction
                    return $this->sendRequest($url . 'AgentFullStatement', $payload);
                    break;
                case Constants::REPRINTING_DATA:
                    $payload = [
                        "MTI" => $mti,
                        "transactionID" => $decodedRequest['transactionID']
                    ];
                    // Reprinting data transaction
                    return $this->sendRequest($url . 'AgentReprintingReceipt', $payload);
                    break;
                case Constants::SAVE_REPRINTING_DETAILS:
                    $payload = [
                        "MTI" => $mti,
                        "transactionID" => (string)$decodedRequest['transactionID'],
                        "reprintingData" => (string)$decodedRequest['reprintingData']
                    ];
                    // Save Reprinting details transaction
                    return $this->sendRequest($url . 'AgentSaveReprintingReceiptRequestToDB', $payload);
                    break;
                case Constants::BALANCE_INQUIRY:
                    $payload = [
                        "type" => "balance_inquiry",
                        "amount" => "0",
                        "account" => $decodedRequest['account'],
                        "uid" => $decodedRequest['bin'],
                        "agent_id" => (string)$this->operatorId,
                        "app_type" => "0",
                        "auth_key" => self::AUTH_KEY,
                        "card_track2" => (string)$decodedRequest['track_2_data'] ?? "",
                        "card_pin" => (string)$decodedRequest['encryptedPIN'] ?? "",
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    // Save Reprinting details transaction
                    return $this->sendRequest($url . 'AgentTransactions', $payload, self::TEXT_CONTENT_TYPE);
                case Constants::UTILITY_PAYMENTS:
                    $payload = [
                        "app_type" => (string)$decodedRequest['app_type'],
                        "type" => (string)$decodedRequest['type'],
                        "reference_number" => (string)$decodedRequest['ref_no'],
                        "customer_msisdn" => (string)$decodedRequest['customer_msisdn'],
                        "agent_msisdn" => (string)$this->agentMsisdn,
                        "amount" => (string)$decodedRequest['amount'],
                        "utility_type" => (string)$decodedRequest['utility_type'],
                        "agent_account" => (string)$this->agentAccount,
                        "uid" => (string)$decodedRequest['bin'],
                        "auth_key" => self::AUTH_KEY,
                        "agent_id" => (string)$this->operatorId,
                        "card_track2" => (string)$decodedRequest['card_track2'] ?? "",
                        "card_pin" => (string)$decodedRequest['card_pin'] ?? "",
                    ];
                    //convert array to xml
                    $payload = Converter::arrayToXml($payload);
                    // Utility Payments transaction
                    return $this->sendRequest($url . 'AgentTransactions', $payload, self::TEXT_CONTENT_TYPE);
                    break;
                case Constants::CUSTOMER_QUERY_CTL_DETAILS:
                    $payload = [
                        "controlNumber" => $decodedRequest['controlNumber'],
                        "customerMSIDN" => $decodedRequest['customerMSIDN']
                    ];
                    // Utility Payments transaction
                    return $this->sendRequest($url . 'NMBGetControlNumberDetails', $payload);
                    break;
                case Constants::CUSTOMER_MINISTATEMENT:
                    $payload = [
                        "MTI" => (string)$mti,
                        "type" => (string)$decodedRequest['type'],
                        "accountID" => (string)$this->agentAccount,
                        "operator_id" => (string)$this->operatorId,
                        "auth_key" => self::AUTH_KEY,
                        "agent_id" => (string)$this->operatorId,
                        "terminalId" => (string)$this->terminalId,
                        "card_track2" => (string)$decodedRequest['card_track2'] ?? "",
                        "card_pin" => (string)$decodedRequest['field_52'],
                        "isCard" => (string)$decodedRequest['isCard'],
                    ];
                    return $this->sendRequest($url . 'AgentTransactions', $payload);
                    break;
                case Constants::DATA_MAP:
                    $url = env("AGENCY_SERVICES_TWO_URL");
                    $payload = [
                        "dataMap" => "",
                    ];

                    return $this->sendRequest($url . 'getMHBDatamapAccountOpenning', $payload);
                    break;
                case Constants::GEPG_PAYMENTS:
                    $payload = [
                        "serviceType" => (string)$decodedRequest['serviceType'],
                        "utilityType" => (string)$decodedRequest['utilityType'],
                        "sourceBankId" => (string)$decodedRequest['sourceBankId'],
                        "utilityReference" => (string)$decodedRequest['utilityReference'],
                        "transactionId" => (string)$decodedRequest['transactionId'],
                        "channelType" => (string)$decodedRequest['channelType'],
                        "payerName" => (string)$decodedRequest['payerName'],
                        "msisdn" => (string)$decodedRequest['msisdn'],
                        "utilityMsisdn" => (string)$decodedRequest['utilityMsisdn'],
                        "amount" => (string)$decodedRequest['amount'],
                        "serviceFee" => (string)$decodedRequest['serviceFee'],
                        "retrievalReferenceNumber" => (string)$decodedRequest['retrievalReferenceNumber'],
                        "spName" => (string)$decodedRequest['spName'],
                        //"content" => $decodedRequest['content'],
                        "billDescription" => (string)$decodedRequest['billDescription'],
                        "trxnDescription" => (string)$decodedRequest['trxnDescription'],
                        "sourceAccountId" => (string)$this->agentAccount,
                        "serviceAccount" => (string)$decodedRequest['serviceAccount'],
                        "agentID" => (string)$this->operatorId
                    ];

                    return $this->sendRequest($url . 'AzaniaGEPGPayment', $payload);
                    break;
                case Constants::NIDA:
                    $url = env("AGENCY_SERVICES_TWO_URL");
                    if (array_key_exists('requestCode', $decodedRequest)) {
                        $payload = [
                            "questionAnswer" => (string)$decodedRequest['questionAnswer'],
                            "requestCode" => (string)$decodedRequest['requestCode'],
                            "nationalIdentificationNumber" => (string)$decodedRequest['nationalIdentificationNumber'],
                        ];
                    } else {
                        $payload = [
                            "questionAnswer" => null,
                            "requestCode" => null,
                            "nationalIdentificationNumber" => (string)$decodedRequest['nationalIdentificationNumber'],
                        ];
                    }
                    return $this->sendRequest($url . 'getCustomerDetailsFromNIDA', $payload);
                    break;
                case Constants::ACCOUNT_OPENING:
                    $url = env("AGENCY_SERVICES_TWO_URL");
                    $payload = [
                        'agentID' => (string)$this->agentId,
                        'addressCity' => $decodedRequest['addressCity'] ?? null,
                        'addressFromDate' => $decodedRequest['addressFromDate'] ?? null,
                        'addressLine1' => $decodedRequest['addressLine1'] ?? null,
                        'addressLine2' => $decodedRequest['addressLine2'] ?? null,
                        'addressLine3' => $decodedRequest['addressLine3'] ?? null,
                        'addressLine4' => $decodedRequest['addressLine4'] ?? null,
                        'addressPropertyTypeId' => $decodedRequest['addressPropertyTypeId'] ?? null,
                        'addressTypeId' => $decodedRequest['addressTypeId'] ?? null,
                        'birthDate' => $decodedRequest['birthDate'] ?? null,
                        'contact' => $decodedRequest['contact'] ?? null,
                        'countryOfBirthId' => $decodedRequest['countryOfBirthId'] ?? null,
                        'countryOfIdIssue' => $decodedRequest['countryOfIdIssue'] ?? null,
                        'countryOfResidenceId' => $decodedRequest['countryOfResidenceId'] ?? null,
                        'customerCategory' => $decodedRequest['customerCategory'] ?? null,
                        'customerSegmentId' => $decodedRequest['customerSegmentId'] ?? null,
                        'customerType' => $decodedRequest['customerType'] ?? null,
                        'dependantCount' => $decodedRequest['dependantCount'] ?? null,
                        'serviceLevelId' => $decodedRequest['serviceLevelId'] ?? null,
                        'employAddressLine' => $decodedRequest['employAddressLine'] ?? null,
                        'employed' => $decodedRequest['employed'] ?? null,
                        'employerName' => $decodedRequest['employerName'] ?? null,
                        'employmentAddress' => $decodedRequest['employmentAddress'] ?? null,
                        'employmentCategoryId' => $decodedRequest['employmentCategoryId'] ?? null,
                        'employmentCity' => $decodedRequest['employmentCity'] ?? null,
                        'employmentStartMonth' => $decodedRequest['employmentStartMonth'] ?? null,
                        'employmentStartYear' => $decodedRequest['employmentStartYear'] ?? null,
                        'firstName' => $decodedRequest['firstName'] ?? null,
                        'gender' => $decodedRequest['gender'] ?? null,
                        'grossAnnualSalId' => $decodedRequest['grossAnnualSalId'] ?? null,
                        'idCityOfIssue' => $decodedRequest['idCityOfIssue'] ?? null,
                        'idExpiryDate' => $decodedRequest['idExpiryDate'] ?? null,
                        'idIssueDate' => $decodedRequest['idIssueDate'] ?? null,
                        'idPhoto' => $decodedRequest['idPhoto'] ?? null,
                        'identificationId' => $decodedRequest['identificationId'] ?? null,
                        'identificationNumber' => $decodedRequest['identificationNumber'] ?? null,
                        'identityType' => $decodedRequest['identityType'] ?? null,
                        'identityTypeId' => $decodedRequest['identityTypeId'] ?? null,
                        'industryCode' => $decodedRequest['industryCode'] ?? null,
                        'industryId' => $decodedRequest['industryId'] ?? null,
                        'lastName' => $decodedRequest['lastName'] ?? null,
                        'marketingCampaignCd' => $decodedRequest['marketingCampaignCd'] ?? null,
                        'marketingCampaignId' => $decodedRequest['marketingCampaignId'] ?? null,
                        'marriageFlag' => $decodedRequest['marriageFlag'] ?? null,
                        'middleName' => $decodedRequest['middleName'] ?? null,
                        'nationalityId' => $decodedRequest['nationalityId'] ?? null,
                        'occupationId' => $decodedRequest['occupationId'] ?? null,
                        'openingReasonId' => $decodedRequest['openingReasonId'] ?? null,
                        'photo' => $decodedRequest['photo'] ?? null,
                        'postalCode' => $decodedRequest['postalCode'] ?? null,
                        'primaryAddress' => $decodedRequest['primaryAddress'] ?? null,
                        'profQualificationCode' => $decodedRequest['profQualificationCode'] ?? null,
                        'profQualificationId' => $decodedRequest['profQualificationId'] ?? null,
                        'professionCd' => $decodedRequest['professionCd'] ?? null,
                        'professionId' => $decodedRequest['professionId'] ?? null,
                        'qualificationCode' => $decodedRequest['qualificationCode'] ?? null,
                        'qualificationId' => $decodedRequest['qualificationId'] ?? null,
                        'religionId' => $decodedRequest['religionId'] ?? null,
                        'resident' => $decodedRequest['resident'] ?? null,
                        'riskCode' => $decodedRequest['riskCode'] ?? null,
                        'riskCountryId' => $decodedRequest['riskCountryId'] ?? null,
                        'riskId' => $decodedRequest['riskId'] ?? null,
                        'customerSignature' => $decodedRequest['customerSignature'] ?? null,
                        'socialSecurityNo' => $decodedRequest['socialSecurityNo'] ?? null,
                        'spouseName' => $decodedRequest['spouseName'] ?? null,
                        'staffInfo' => $decodedRequest['staffInfo'] ?? null,
                        'taxGroupCode' => $decodedRequest['taxGroupCode'] ?? null,
                        'taxGroupId' => $decodedRequest['taxGroupId'] ?? null,
                        'taxStatusId' => $decodedRequest['taxStatusId'] ?? null,
                        'titleId' => $decodedRequest['titleId'] ?? null,
                        'verified' => $decodedRequest['verified'] ?? null,
                        'sourceOfFundCd' => $decodedRequest['sourceOfFundCd'] ?? null,
                        'sourceOfFundId' => $decodedRequest['sourceOfFundId'] ?? null,
                    ];
                    $response = $this->sendRequest($url . 'openAccountMHB', $payload);
                    $this->storeAccountOpeningDetails($payload, $response);
                    return $response;
                    break;
                default:
                    // Invalid MTI
                    Log::channel('agency')->info("INVALID-MTI: " . $mti);
                    return $this->handleError(100, 'Invalid service request');
            }
        } catch (\Exception $ex) {
            Log::channel('agency')->error("AGENCY-TRANSACTION-ERROR: " . $ex->getMessage());
            Log::channel('agency')->error("AGENCY-TRANSACTION-ERROR: " . $ex->getTraceAsString());
            return $this->handleError(500, 'Exception occurred', $ex->getMessage());
        }
    }

    private function sendRequest($endpoint, $payload, $contentType = self::JSON_CONTENT_TYPE): \Illuminate\Http\JsonResponse
    {
        Log::channel('agency')->info("----SENDING TRANSACTION REQUEST----");
        Log::channel('agency')->info("AGENCY-TRANSACTION-SEND-REQUEST:", [
            'ENDPOINT' => $endpoint,
            'REQUEST' => json_encode($payload, JSON_PRETTY_PRINT),
            'CONTENT-TYPE' => $contentType,
        ]);

        // Encrypt request
        /*try {
            $encryptedRequest = $this->encrypt(json_encode($payload), $this->key);
            $request = ["data" => $encryptedRequest];
        } catch (\Exception $ex) {
            Log::channel('agency')->error("AGENCY-TRANSACTION-ENCRYPTION-EXCEPTION: " . $ex->getMessage());
            return $this->handleError(500, 'Exception occurred', $ex->getMessage());
        }*/

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'deviceimei1: ' . $this->imei1,
            'deviceimei2: ' . $this->imei2,
            'Content-Type: ' . $contentType,
        ]);

        // Set payload format
        if ($contentType == self::JSON_CONTENT_TYPE) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } elseif ($contentType == self::TEXT_CONTENT_TYPE) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            throw new \Exception("Invalid Content Type");
        }

        try {
            // Execute cURL request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }
            Log::channel('agency')->info("----RECEIVING TRANSACTION RESPONSE----");
            Log::channel('agency')->info("AGENCY-TRANSACTION-RESPONSE:", [$response]);

            // Handle response by content type
            if ($contentType == self::TEXT_CONTENT_TYPE) {
                $response = Converter::XmlToArray($response); // Convert XML to array
            } else {
                $response = json_decode($response, true);
            }
            Log::channel('agency')->info("----ENCRYPTING TRANSACTION RESPONSE----");
            $encryptedResponse = $this->encrypt(json_encode($response), $this->key);
            Log::channel('agency')->info("----SENDING RESPONSE BACK TO POS----");
            return response()->json(['data' => $encryptedResponse]);
        } catch (\Exception $e) {
            Log::channel('agency')->info("----SENDING TRANSACTION EXCEPTION----");
            Log::channel('agency')->error("AGENCY-SEND-TRANSACTION-EXCEPTION:", ['Error' => $e->getMessage(), 'Trace' => $e->getTraceAsString()]);
            return $this->handleError(500, 'Exception occurred', $e->getMessage());
        } finally {
            // Close cURL session
            curl_close($ch);
        }
    }


    private function storeAccountOpeningDetails($payload, $response): void
    {
        $decodedRequest = $this->decrypt($response, $this->key);
        $response = json_decode($decodedRequest, true);

        $firstName = $payload['firstName'] ?? '';
        $middleName = $payload['middleName'] ?? '';
        $lastName = $payload['lastName'] ?? '';

        $fullname = trim("$firstName $middleName $lastName");

        try {
            DB::connection('sqlsrv4')->table('tbl_agency_banking_account_openning')->insert([
                'phone_number' => $payload['contact'] ?? null,
                'account_number' => $response['data']['customerNo'] ?? null,
                'datetime' => now(),
                'agent_id' => $this->agentId,
                'national_identification_number' => $payload['identificationNumber'] ?? null,
                'operator_id' => $this->operatorId,
                'responseCode' => $response['code'] ?? null,
                'responseMessage' => $response['status'] ?? null,
                'fullname' => $fullname,
                'auditRef' => $response['auditRef'] ?? null,
                'request_dump' => json_encode($payload),
                'response_dump' => json_encode($response),
            ]);
        } catch (\Exception $e) {
            Log::channel('agency')->error("ACCOUNT-OPENING-DETAILS-EXCEPTION: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    public function handleError($code, $message, $ex = 'false'): \Illuminate\Http\JsonResponse
    {
        $response = ['code' => $code, 'message' => $message, 'Exception' => $ex];

        if (!$this->key) {
            Log::channel('agency')->error("FAILED-TO-GET-KEY");
            //return plain response as no key was found
            return response()->json($response);
        }

        return response()->json(['data' => $this->encrypt(json_encode($response), $this->key)]);
    }
}
