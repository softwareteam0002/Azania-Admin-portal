<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Constants;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Operator;
use App\TblABPINPolicy;
use App\TblAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomeController extends Controller
{
    private const MWANGA_BIN = 581677;

    public function index()
    {
        return view('home');
    }

    public function operatorLogin(Request $request)
    {
        Log::channel('agency')->info("OPERATOR-REQUEST: " . json_encode($request->all()));

        // Check if either device IMEI header is present
        if (!$request->hasHeader('deviceimei1') && !$request->hasHeader('deviceimei2')) {
            return response()->json(['code' => 400, 'message' => 'Bad request format']);
        }

        $imei1 = $request->header('deviceimei1');
        $imei2 = $request->header('deviceimei2');

        Log::channel('agency')->info("OPERATOR-DEVICE-IMEI1: " . $imei1);
        Log::channel('agency')->info("OPERATOR-DEVICE-IMEI2: " . $imei2);

        // Validate and decrypt the incoming data
        $data = $request->data;
        $key = $this->getDeviceKey($imei1, $imei2);

        if (!$key) {
            return response()->json(['code' => 129, 'message' => Constants::FAILED_TO_GET_KEY]);
        }

        $json_data = $this->decrypt($data, trim($key));
        Log::channel('agency')->info("OPERATOR-DECRYPTED-REQUEST: " . $json_data);

        if (!$json_data) {
            return response()->json(['code' => 119, 'message' => Constants::DECRYPTION_FAILED]);
        }

        // Validate decrypted JSON data
        $json_data = json_decode($json_data);
        $validator = Validator::make((array)$json_data, [
            'category' => 'required',
            'password' => 'required|digits:4',
            'imei' => 'required',
            'username' => 'required|tanzanian_mobile'
        ], ['username.tanzanian_mobile' => 'Invalid Credentials']);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }

        // Proceed with operator login if category is 'operator'
        if ($json_data->category == 'operator') {
            $operatorMsisdn = $json_data->username;
            $operatorPassword = $json_data->password;

            //check and convert number to begin with 255 and not 0
            if (Str::startsWith($operatorMsisdn, '0')) {
                $operatorMsisdn = Str::replaceFirst('0', '255', $operatorMsisdn);
            }

            $operator = Operator::on('sqlsrv4')->where('operator_msisdn', $operatorMsisdn)->first();
            Log::channel('agency')->info("OPERATOR-DETAILS: " . json_encode($operator));
            if ($operator) {
                // Increment login count
                $loginCount = intval($operator->login_counts) + 1;
                $operator->update(['login_counts' => $loginCount]);

                // Check operator and device status
                $operatorStatus = $operator->operator_status;
                $operatorAgent = $operator->agent;
                $operatorDevice = $operator->device;
                if (in_array($operatorStatus, ['1', '2']) && intval($operatorAgent->agent_status) == 1) {

                    Log::channel('agency')->info("OPERATOR-DEVICE: " . json_encode($operatorDevice));
                    if (intval($operatorDevice->device_status) == 1) {
                        if ($operatorDevice->device_imei1 == $json_data->imei || $operatorDevice->device_imei2 == $json_data->imei) {
                            if (Hash::check($operatorPassword, $operator->operator_password)) {
                                // Reset login count on successful login
                                $operator->update(['login_counts' => 0]);
                                $bankDetails = [
                                    'BankList' => $this->getBanks(),
                                ];
                                $bankLimits = [
                                    'BankLimit' => $this->getBankLimits(),
                                ];

                                $interoperabilityBins = $this->getInteroperabilityBins();

                                //Login successfully
                                $trading_ac = $operatorDevice->tradingac->bank_account;
                                $commision_ac = $operatorDevice->commisionac;
                                $token = JWTAuth::fromUser($operator);
                                Log::channel('agency')->info("Login Successful");
                                return $this->signPayload(json_encode(['token' => $token, 'error' => 'false', 'operator' => $operator, 'trading_account' => $trading_ac, 'BanksDetails' => $bankDetails, 'BankLimits' => $bankLimits, 'InteroperabilityBins' => $interoperabilityBins, 'bank_bin' => self::MWANGA_BIN]), $key);
                            } else {
                                // Handle incorrect password
                                $this->handleIncorrectPassword($operator, $loginCount);
                                return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::INVALID_CREDENTIALS]), $key);
                            }
                        } else {
                            return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::FAILED_VALIDATING_IMEI]), $key);
                        }
                    } else {
                        return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::DEVICE_INACTIVE_SUSPENDED, 'device_data' => $operatorDevice]), $key);
                    }
                } else {
                    return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::OPERATOR_INACTIVE_SUSPENDED]), $key);
                }
            }
            return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::OPERATOR_NOT_EXIST]), $key);
        } else {
            if (!$json_data->agent_username) {
                return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::AGENT_USERNAME_REQUIRED]), $key);
            }

            $agent = TblAgent::where('agent_username', $json_data->agent_username)->first();
            if ($agent) {
                if (Hash::check($json_data->password, $agent->agent_password)) {
                    //get the device account
                    return $this->signPayload(json_encode(['error' => 'false', 'data' => $agent]), $key);
                }
                return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::INVALID_CREDENTIALS]), $key);
            }
            return $this->signPayload(json_encode(['error' => 'true', 'message' => Constants::AGENT_NOT_EXIST]), $key);
        }
    }

    private function handleIncorrectPassword($operator, $loginCount)
    {
        $pinPolicy = TblABPINPolicy::get()[0];
        if ($loginCount > $pinPolicy->max_attempts) {
            // Update operator status to inactive if max attempts exceeded
            $operator->update(['operator_status' => 4]);
        }
    }

//New API

    public function agentPinChange(Request $request)
    {
        Log::channel('agency')->info("AGENT-PIN-CHANGE-REQUEST: " . json_encode($request->all()));
        try {
            if ($request->isOperator == false) {
                $agent = TblAgent::where(['agent_id' => $request->userID])->where('agent_msisdn', $request->phoneNumber)->first();

                if (!$agent) {
                    return response()->json(['responseCode' => '401', 'success' => false, 'message' => 'Agent does not exist']);
                } else {
                    //return response()->json(['hash_password'=>Hash::check($agent->agent_password, $agent->agent_password), 'agent-password'=>$agent->agent_password]);
                    if (Hash::check($request->currentPIN, $agent->agent_password)) {
                        //Update First Login
                        TblAgent::where(['agent_id' => $request->userID])->update(['agent_password' => Hash::make($request->newPIN)]);

                        return response()->json(['responseCode' => '200', 'success' => true, 'message' => 'Agent PIN Changed Successfully']);
                    } else {
                        return response()->json(['responseCode' => '401', 'success' => false, 'message' => 'Records do not match']);
                    }
                }
            } else {
                return response()->json(['responseCode' => '403', 'success' => false, 'message' => 'Unauthorized request']);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(array(
                'success' => false,
                'errors' => $e
            ), 400);
        }
    }

    public function operatorPinChange(Request $request)
    {
        Log::channel('agency')->info("OPERATOR-PIN-CHANGE-REQUEST: " . json_encode($request->all()));
        try {
            $operator = Auth::guard('operator')->user();
            $imei1 = $operator->device->device_imei1;
            $imei2 = $operator->device->device_imei2;

            $key = $this->getDeviceKey($imei1, $imei2);
            if (!$key) {
                return response()->json(['code' => 129, 'message' => Constants::FAILED_TO_GET_KEY]);
            }
            $decryptedRequest = json_decode($this->decrypt($request->data, $key), true);
            Log::channel('agency')->info("OPERATOR-PIN-CHANGE-DECRYPTED-REQUEST: " . json_encode($decryptedRequest));

            //validate request
            $validator = Validator::make($decryptedRequest, [
                'currentPIN' => 'required|digits:4',
                'newPIN' => 'required|digits:4|different:currentPIN',
                'isOperator' => 'required|in:true',
            ], ['newPIN.different' => 'The new PIN must be different from the current PIN',
                'isOperator.in' => 'PIN change is strictly for operators'
            ]);

            if ($validator->fails()) {
                $response = ['responseCode' => '100', 'error' => true, 'message' => $validator->errors()->first()];
                $encryptedResponse = $this->encrypt(json_encode($response), $key);
                return response()->json(["data" => $encryptedResponse]);
            }

            if (Hash::check($decryptedRequest['currentPIN'], $operator->operator_password)) {
                //Update First Login
                $operator->update(['operator_password' => Hash::make($decryptedRequest['newPIN'])]);
                $response = ['responseCode' => '200', 'success' => true, 'message' => Constants::SUCCESS_OPERATOR_PIN_CHANGE];
                $encryptedResponse = $this->encrypt(json_encode($response), $key);
                return response()->json(["data" => $encryptedResponse]);
            } else {
                $response = ['responseCode' => '401', 'success' => false, 'message' => Constants::NO_MATCHING_RECORDS];
                $encryptedResponse = $this->encrypt(json_encode($response), $key);
                return response()->json(["data" => $encryptedResponse]);
            }
        } catch (\Exception $e) {
            Log::channel('agency')->info("OPERATOR-PIN-CHANGE-EXCEPTION: " . json_encode($e->getMessage()));
            Log::channel('agency')->info("OPERATOR-PIN-CHANGE-EXCEPTION: " . json_encode($e));
            $response = ['responseCode' => '100', 'success' => false, 'errors' => $e->getMessage()];
            $encryptedResponse = $this->encrypt(json_encode($response), $key);
            return response()->json(["data" => $encryptedResponse]);
        }
    }

    public function agentPin()
    {
        $agents = TblAgent::get();
        foreach ($agents as $agent) {
            $updated = TblAgent::where(['agent_id' => $agent->agent_id])->update(['agent_password' => Hash::make($agent->agent_password)]);
        }
        echo "Success";
    }

    public function operatorPin()
    {
        $operators = Operator::get();
        foreach ($operators as $operator) {
            $updated = Operator::where(['operator_id' => $operator->operator_id])->update(['operator_password' => Hash::make($operator->operator_password)]);
        }
        echo "Success";
    }

    //changing admin password after first login
    public function updatePassword(Request $request)
    {
        $user_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|min:8|same:password',
        ]);

        if ($validator->fails()) {
            $log = new Helper();

            $log->auditTrack($user_id, "Passwords dont match", "danger");

            return redirect()->back()->with(['notification' => 'Passwords do not match', 'color' => 'danger']);

        }

        //adding new password for admin
        $result = DB::table('dbo.users')->where('id', '=', $user_id)->update([
            'password' => Hash::make($request->new_password),

        ]);

        $log = new Helper();
        $log->auditTrack($result, "Password updated successfully", "success");

        return redirect()->back()->with(['notification' => 'Password has been changed successfully', 'color' => 'success']);
    }

    public function changePassword()
    {
        return view('admin.changePassword.change_password');
    }

    public function getBanks(): \Illuminate\Support\Collection
    {
        return DB::connection('sqlsrv4')->table('tbl_agency_banking_banks')
            ->select('bank_name', 'bin', 'identifier')
            ->where('bank_status', 1)
            ->where('isWaitingApproval', 0)
            ->get();
    }

    public function getBankLimits(): \Illuminate\Support\Collection
    {
        return DB::connection('sqlsrv4')
            ->table('tbl_agency_banking_bank_limits')
            ->select('minimumBankLimit', 'maximumBankLimit', 'minimumInteroperabilityLimit', 'maximumInteroperabilityLimit', 'transactionType')
            ->get();
    }

    public function getInteroperabilityBins(): array
    {
        return DB::connection('sqlsrv4')
            ->table('tbl_agency_banking_interoperability_client_list')
            ->select('bin')
            ->where('isActive', 1)
            ->get()->pluck('bin')->toArray();
    }

}
