<?php

namespace App\Http\Controllers\Admin;

use App\Operator;
use App\TblAgent;
use App\TblABPINPolicy;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use DB;

class OperatorAgentController
{
    public function index()
    {
        return view('home');
    }

    public function operatorLogin(Request $request)
    {
        $data = file_get_contents('php://input');

        //[TODO] Adding an encryption algorithm to encrypt/decrypt the data.
        $json_data = json_decode($data);
        //return $json_data;
        if (!isset($json_data->category) || empty($json_data->category)) {
            return response()->json(['error' => true, 'message' => 'Category required']);
        }

        if (!isset($json_data->password) || empty($json_data->password)) {
            return response()->json(['error' => true, 'message' => 'Password required']);
        }

        if (!isset($json_data->imei) || empty($json_data->imei)) {
            return response()->json(['error' => true, 'message' => 'IMEI required']);
        }

        if ($json_data->category == 'operator') {
            if (empty($json_data->username)) {
                return response()->json(['error' => true, 'message' => 'Username required']);
            }

            $credentials = $request->only('username', 'password');
            $operator = Operator::on('sqlsrv4')->where('operator_msisdn', $json_data->username)->first();

            if ($operator) {
                $operator_id = $operator->operator_id;
                $login_count = intval($operator->login_counts);
                $login_count = $login_count + 1;

                //check against security policy
                $pinpolicy = TblABPINPolicy::get()[0];


                if (Hash::check($json_data->password, $operator->operator_password)) {
                    $login_count = 0;
                    //reset the login count
                    $update = Operator::on('sqlsrv4')->where('operator_id', $operator_id)
                        ->update(
                            [
                                'login_counts' => $login_count
                            ]
                        );

                    //check to see if the status of the operator is active or inactive
                    if ($operator->operator_status == "1"  || $operator->operator_status == "2") {
                        //operator is active, get the device and agent details
                        $operator_agent = $operator->agent;
                        $operator_device = $operator->device;
                        if (intval($operator_agent->agent_status) == 1) {
                            //agent is active
                            if (intval($operator_device->device_status) == 1) {
                                //device is active
                                if ($operator_device->device_imei1 == $json_data->imei || $operator_device->device_imei2 == $json_data->imei) {
                                    //device imei matches
                                    //return the payloads
                                    $trading_ac = $operator_device->tradingac;
                                    $commision_ac = $operator_device->commisionac;

                                    return response()->json(
                                        [
                                          'error' => false,
                                           'operator' => $operator
                                        ]
                                    );
                                } else {
                                    //device imei has not matched
                                    return response()->json(['error' => true, 'message' => 'Problem validating Operator Device Imei(s).']);
                                }
                            } else {
                                //device is inactive, suspsended or not assigned
                                return response()->json(['error' => true, 'message' => 'Operator Device is inactive, suspended or blocked.', 'device_data'=>$operator_device ]);
                            }
                        } else {
                            //agent is inactive
                            return response()->json(['error' => true, 'message' => 'Operator Agent is inactive, suspended or blocked.']);
                        }
                    } else {
                        //operator is inactive or suspended
                        return response()->json(['error' => true, 'message' => 'Operator is inactive, suspended or blocked.']);
                    }
                } else {
                    //the operator has submitted inaccurate login details
                    if ($login_count > $pinpolicy->max_attempts) {
                        //update the user and set to in active
                        $operator_status = 4;
                        $update = Operator::on('sqlsrv4')->where('operator_id', $operator_id)
                            ->update(
                                [
                                    'operator_status' => $operator_status
                                ]
                            );
                    }

                    //update the login count
                    $update = Operator::on('sqlsrv4')->where('operator_id', $operator_id)
                        ->update(
                            [
                                'login_counts' => $login_count
                            ]
                        );
                }
                return response()->json(['error' => true, 'message' => 'Credentials does not match our records']);
            }
            return response()->json(['error' => true, 'message' => 'Operator does not exist']);
        } else {
            if (empty($json_data->agent_username)) {
                return response()->json(['error' => true, 'message' => 'Agent username required']);
            }

            $credentials = $request->only('agent_username', 'password');
            $agent = TblAgent::where('agent_username', $json_data->agent_username)->first();
            if ($agent) {
                //since there is no hash for now, jus do a normal comparison

                if (Hash::check($json_data->password, $agent->agent_password)) {
                    //get the device account
                    return response()->json(['error' => false, 'data' => $agent]);
                }
                return response()->json(['error' => true, 'message' => 'Credentials does not match our records']);
            }
            return response()->json(['error' => true, 'message' => 'Agent does not exist']);
        }
    }

//New API

public function agentPinChange(Request $request)
{
    try {
        Log::info($request);

        if ($request->isOperator == false) {
            $agent = TblAgent::where(['agent_id' => $request->userID])->where('agent_msisdn', $request->phoneNumber)->first();

            if (!$agent) {
                return response()->json(['responseCode' => '401', 'success' => false, 'message' => 'Agent does not exist']);
            } else {
                //return response()->json(['hash_password'=>Hash::check($agent->agent_password, $agent->agent_password), 'agent-password'=>$agent->agent_password]);
                if (Hash::check($request->currentPIN, $agent->agent_password)) {
                    //Update First Login
                    TblAgent::where(['agent_id' => $request->userID])->update(['agent_password' => Hash::make($request->newPIN)]);

                    return response()->json(['responseCode' => '200', 'success' =>true, 'message' => 'Agent PIN Changed Successfully']);
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
         try {
             Log::info($request);

             if ($request->isOperator == true) {
                 $operator = Operator::where(['operator_id' => $request->userID])->where('operator_msisdn', $request->phoneNumber)->first();

                 if (!$operator) {
                     return response()->json(['responseCode' => '401', 'success' => false, 'message' => 'Operator does not exist']);
                 } else {
                     if (Hash::check($request->currentPIN, $operator->operator_password)) {
                         //Update First Login
                         Operator::where(['operator_id' => $request->userID])->update(['operator_password' => Hash::make($request->newPIN)]);

                         return response()->json(['responseCode' => '200', 'success' =>true, 'message' => 'Operator PIN Changed Successfully']);
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

    public function agentEnc()
    {
        abort(403);
    }

    public function operatorEnc()
    {
    }


    //changing admin password after first login
    public function updatePassword(Request $request)
    {
        $user_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'password' 			=> 'required',
            'new_password'		=> 'required|min:8|same:password',
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
}
