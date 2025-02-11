<?php

namespace App\Http\Controllers\agency\API;

use App\Helper\Constants;
use App\Http\Controllers\Controller;
use App\Devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class TransactionController extends Controller
{
    public function processTransactions(Request $request)
    {
        Log::channel('agency')->info("AGENCY-TRANSACTION-REQUEST: " . json_encode($request->all()));
        $url = env('AGENCY_SERVICES_URL');
        $mti = $request->header('MTI');

        // Switch case based on the MTI
        switch ($mti) {
            case Constants::FUND_TRANSFER:
                // Transfer fund transaction
                return $this->sendRequest($url . 'AgentFundTransfer', $request);
                break;
            case Constants::CARD_WITHDRAW:
                // Withdraw card transaction
                return $this->sendRequest($url . 'AgentCashWithdrawal', $request);
                break;
            case Constants::DEPOSIT:
                // Deposit transaction
                return $this->sendRequest($url . 'AgentCashDeposit', $request);
                break;
            case Constants::DEPOSIT_QUERY_NAME:
                // Deposit Query Name transaction
                return $this->sendRequest($url . 'CustomerDetailsInquiry', $request);
                break;
            case Constants::CARDLESS_WITHDRAW:
                // Cardless withdraw transaction
                return $this->sendRequest($url . 'AgentCardlessWithdrawal', $request);
                break;
            case Constants::AGENT_STATEMENT:
                // Agent statement transaction
                return $this->sendRequest($url . 'AgentFullStatementFromDB', $request);
                break;
            case Constants::CUSTOMER_MINISTATEMENT:
                // Customer Ministatement transaction
                return $this->sendRequest($url . 'AgentFullStatement', $request);
                break;
            case Constants::REPRINTING_DATA:
                // Reprinting data transaction
                return $this->sendRequest($url . 'AgentReprintingReceipt', $request);
                break;
            case Constants::SAVE_REPRINTING_DETAILS:
                // Save Reprinting details transaction
                return $this->sendRequest($url . 'AgentSaveReprintingReceiptRequestToDB', $request);
                break;
            case Constants::BALANCE_INQUIRY:
                // Save Reprinting details transaction
                return $this->sendRequest($url . 'AgentBalanceInquiry', $request);
			case Constants::UTILITY_PAYMENTS:
                // Utility Payments transaction
                return $this->sendRequest($url . 'AgentUtilityPayments', $request);
                break;
			case Constants::CUSTOMER_QUERY_CTL_DETAILS:
                // Utility Payments transaction
                return $this->sendRequest($url . 'CustomerQueryControlNumberDetails', $request);
                break;
            default:
                // Invalid MTI
                Log::channel('agency')->info("INVALID-MTI: " . $mti);
                return response()->json(['code'=>170,'error' => 'Invalid MTI']);
        }

    }

    // Method to send HTTP request to the specified endpoint
    private function sendRequest($endpoint, Request $request)
    {
        Log::channel('agency')->info("AGENCY-TRANSACTION-SEND-REQUEST: ", ['ENDPOINT' => $endpoint, 'REQUEST' => $request->all()]);
        //decode token to get authenticated operator details
        $deviceId = $this->decodeToken();
        if (!$deviceId) {
            return response()->json(['code' => 115, 'message' => 'Failed to decode token']);
        }

        //get device imei
        $device = $this->getDevice($deviceId);
        if (!$device) {
            return response()->json(['code' => 117, 'message' => 'Failed to get device details']);
        }
        $imei1 = $device->device_imei1;
        $imei2 = $device->device_imei2;

        //generate checksum
        $checkSum = $this->generateChecksum($imei1, now()->format('Y-m-d'));
        try {
            // Send request with headers
            $response = Http::withHeaders([
                'checksum' => $checkSum,
                'deviceimei1' => $imei1,
                'deviceimei2' => $imei2,
            ])->post($endpoint, $request->all());

			$body = json_decode($response->body(), true);
			Log::channel('agency')->info("AGENCY-TRANSACTION-RESPONSE: " . json_encode($body));
          return $response;
        } catch (\Exception $e) {
            Log::channel('agency')->error("AGENCY-SEND-TRANSACTION-EXCEPTION: " . json_encode($e->getMessage()));
            return response()->json(['code' => 100, 'error' => $e->getMessage()]);

        }
    }

    private function generateChecksum($imei, $date): string
    {
        $checkRef = "AG3n@24!";
        $secretKey = env('CHECKSUM_SECRET_KEY');

        // Concatenate parameters
        $dataToHash = $imei . $date . $checkRef;

        // Generate HMAC-SHA256 checksum
        return hash_hmac('sha256', $dataToHash, $secretKey);
    }

    //decode jwt token from headers
    private function decodeToken()
    {
        try {
            $token = JWTAuth::parseToken()->authenticate();
            return $token->device_id;
        } catch (\Exception $e) {
            Log::error("FAILED-TO-DECODE-TOKEN" . json_encode($e->getMessage()));
            return false;
        }
    }

    private function getDevice($deviceId)
    {
      return Devices::select('device_imei1', 'device_imei2')->where('device_id', $deviceId)->first();
    }
}

