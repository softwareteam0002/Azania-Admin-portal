<?php

namespace App\Http\Controllers\IB;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\IbAccount;
use App\IbAccountType;
use App\IbBranch;
use App\IbInstitution;
use App\IBRole;
use App\IbUser;
use App\IbUserRole;
use App\Jobs\PasswordHitJob;
use App\Mail\PasswordMail;
use App\OtpOption;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Log;


class UsersController extends Controller
{

    protected $insertAccount = [
        'account_number' => 'unique'
    ];

    protected $notification = '';

    public function index()
    {

        $subscribers = IbUser::whereNull('institute_id')->orderBy('id', 'DESC')->get();


        /*$institutions = IbInstitution::all();

			$sql = "SELECT * FROM tbl_user_types";
			$types = DB::connection('sqlsrv2')->select($sql);

			$excluded_roles = [1, 2, 4, 5];
			$roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
			$account_types = IbAccountType::all();
		*/

        $options = OtpOption::all();
        return view("ib.users.index", compact('subscribers', 'options'));
    }

    public function updateUserStatus(Request $request)
    {
        $user_id = Auth::id();
        $status = $request['status'];
        $db_action = IbUser::where('id', $request['user_id'])->update([
            'isWaitingApproval' => 1,
            'approver_id' => '0',
            'disabledBy_id' => $user_id,
            'isBlocked' => 1
        ]);

        if ($db_action == true) {
            switch ($request['status']) {
                case "Active":

                    $notification = "User activation request sent successfully";
                    $color = "success";

                    break;
                case "Blocked":

                    $notification = "User blocking request sent successfully";
                    $color = "success";

                    break;

                default:

                    $notification = "No change made";
                    $color = "danger";

                    break;
            }


        } else {
            $notification = "No change made";
            $color = "danger";
        }


        return redirect('ib/user')->with('notification', $notification)->with('color', $color);
    }

    public function reset(Request $request)
    {
        $password = mt_rand(12345678, 99999999);


        $user = IbUser::where('id', $request->user_id)->first();
        //dd($user->otp_option);
        $user->password = Hash::make($password);
        $user->isVerified = 0;
        $passwd_reset = $user->update();

        if ($passwd_reset == true) {
            if ($user->otp_option == 'BOTH') {
                $body = "Dear " . $user->name . ", your password to access Internet Banking is " . $password . " link: https://ibanktest.acbtz.com:8001/";
                $recipient = $user->email;
                $time = 10;
                //Queue::later($time, new PasswordHitJob($body,$recipient));
                Mail::to($recipient)->send(new PasswordMail($body));
                $this->sms($body, $user->mobile_phone);
            } elseif ($user->otp_option == 'SMS') {
                $body = "Dear " . $user->name . ", your password to access Internet Banking is " . $password . " link: https://ibanktest.acbtz.com:8001/";;
                $this->sms($body, $user->mobile_phone);
            } else {
                $body = "Dear " . $user->name . ", your password to access Internet Banking is " . $password . " link: https://ibanktest.acbtz.com:8001/";;
                $recipient = $user->email;
                $time = 10;
                Mail::to($recipient)->send(new PasswordMail($body));
                //Queue::later($time, new PasswordHitJob($body,$recipient)); 
            }


            return back()->with(['notification' => 'Password reset successfully', 'color' => 'success']);
        } else {
            return back()->with(['notification' => 'Password reset failed', 'color' => 'danger']);

        }
    }

    public function sms($message, $phoneNumber)
    {
        $url = "http://172.29.1.133:8984/esb/send/sms";
        $client = new Client;

        $infoRequest = [
            "message" => $message,
            "phoneNumber" => $phoneNumber
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);
        $smsInfo = $res->getBody();
        $smsDetail = json_decode($smsInfo);

        if (isset($smsDetail->balance)) {
            $color = 'success';
            return redirect()->back()->with(['notification' => 'SMS sent successfully', 'color' => $color]);

        } else {
            $color = 'danger';
            return redirect()->back()->with(['notification' => 'SMS FAILED', 'color' => $color]);
        }
    }

    public function disableUserApproval($id)
    {
        $user = IbUser::findOrFail($id);
        $options = OtpOption::all();
        $accounts = IbAccount::where('user_id', $id)->get();
        return view('ib.users.disable_user', compact('user', 'options', 'accounts'));
    }


    public function disableUserActApproval(Request $request, $id)
    {
        $user_id = Auth::id();
        $user = IbUser::findOrFail($id);
        if ($user->status == "Active") {
            if ($request->reject == 'reject') {

                IbUser::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 2]);
                return redirect()->route('ib.users-index')->with(['notification' => 'User blocking has been rejected successfully', 'color' => 'success']);
            }

            if ($request->approve == 'approve') {
                IbUser::where(['id' => $id])->update(['status' => 'Blocked', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 0]);
                return redirect()->route('ib.users-index')->with(['notification' => 'User blocking has been approved successfully', 'color' => 'success']);
            }
        }
        if ($user->status == "Blocked") {
            if ($request->reject == 'reject') {

                IbUser::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 2]);
                return redirect()->route('ib.users-index')->with(['notification' => 'User activation has been rejected successfully', 'color' => 'success']);
            }

            if ($request->approve == 'approve') {
                IbUser::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 0, 'attempts' => null]);
                return redirect()->route('ib.users-index')->with(['notification' => 'User activation has been approved successfully', 'color' => 'success']);
            }
        }

    }


    public function create()
    {

        $sql = "SELECT * FROM tbl_institutions";
        $institutions = DB::connection('sqlsrv2')->select($sql);

        $sql = "SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);

        $excluded_roles = [1, 2, 4, 5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
        $options = OtpOption::all();
        $account_types = IbAccountType::all();

        return view("ib.users.create", compact('roles', 'types', 'institutions', 'options', 'account_types'));

    }

    public function afterInsertion($notification, $color)
    {
        $excluded_roles = [1, 2, 4, 5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
        $sql = "SELECT * FROM tbl_institutions";
        $institutions = DB::connection('sqlsrv2')->select($sql);
        $sql = "SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);


        return view("ib.users.create", compact('roles', 'types', 'institutions', 'notification', 'color'));
    }

    public function edit($id)
    {
        $user = IbUser::where('id', $id)->get()[0];
        $excluded_roles = [1, 2, 4, 5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();

        $sql = "SELECT * FROM tbl_institutions";
        $institutions = DB::connection('sqlsrv2')->select($sql);
        $sql = "SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);
        $options = OtpOption::all();
        $account_types = IbAccountType::all();

        return view("ib.users.edit", compact('roles', 'types', 'institutions', 'user', 'options', 'account_types'));
    }


    public function assignAccount($id)
    {
        $types = IbAccountType::all();
        $branchs = IbBranch::all();
        $institutions = IbInstitution::all();
        $users = IbUser::all();
        $user = IbUser::where('id', $id)->get()[0];
        $accounts = IbAccount::where('user_id', $id)->get();

        return view('ib.users.account', compact('accounts', 'types', 'branchs', 'institutions', 'users', 'user'));
    }

    public function storeAccount(Request $request)
    {
        session()->forget('error_get_account');
        $request->validate([
            'account_number' => 'required',
        ]);

        //Validate account number to be unique
        $account_number = IbAccount::select(['tbl_account.accountID'])
            ->where('accountID', $request->account_number)
            ->get();

        if (count($account_number) > 0) {

            //Audit Trail
            $request['user_id'] = Auth::user()->id;
            $request['module'] = "IB";
            $request['action'] = "Account Store";
            $request['action_time'] = now();
            $request['reason'] = "NULL";
            $request['old_details'] = "";
            $request['new_details'] = $request->account_number;
            $log = new Helper();
            $log->auditTracker($request);


            $notification = "Account already exist!";
            $color = "danger";
            return redirect('ib/user/add_account/' . $request->id)->with('notification', $notification)->with('color', $color);
        }
        $this->verifyAccount($request);
        if (session()->get('responseCode_acc') == 200) {
            //dd('reach here');
            session()->get('responseCode_acc');
            return $this->insertAccount($request->account_number, $request->id, $request->institute_id);
        } elseif (session()->get('responseCode_acc') == 100) {
            $color = 'danger';
            session()->forget('clientName');
            session()->forget('phoneNumber');
            session()->put('accountID', $request->account_number);
            return redirect()->back()->with(['notification' => session()->get('responseMessage_acc'), 'color' => $color]);

        } elseif (session()->get('responseCode_acc') == '006') {
            $color = 'danger';
            session()->forget('clientName');
            session()->forget('phoneNumber');
            session()->put('accountID', $request->account_number);
            return redirect()->back()->with(['notification' => session()->get('responseMessage_acc'), 'color' => $color]);

        } else {
            return redirect('ib/user/add_account/' . $request->id)->with('notification', 'Response ' . session()->get('responseCode_acc') . ' found!')->with('color', 'secondary');
        }

    }

    public function verifyAccount(Request $request)
    {


        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";
        $client = new Client;
        $infoRequest = [
            "serviceType" => $serviceType,
            "sourceAccountId" => $request->account_number
        ];

        //dd($infoRequest);


        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo = $res->getBody();
        $accountDetail = json_decode($accountInfo);

        $responseCode = $accountDetail->responseCode;
        $responseMessage = $accountDetail->responseMessage;
        Log::info('Account verify request: ' . json_encode($infoRequest));
        Log::info('Account verify response: ' . $accountInfo);
        //dd('infor is :'.$accountDetail);
        session()->put('responseCode_acc', $responseCode);
        session()->put('responseMessage_acc', $responseMessage);

        /*if($responseCode == 100) {
                 $color = 'danger';
                 session()->forget('clientName');
                 session()->forget('phoneNumber');
                  session()->put('accountID', $request->account_number);
                 return redirect()->back()->with(['notification' => $accountDetail->responseMessage, 'color' => $color]);
        
        }
       if($responseCode == '006') {
                 $color = 'danger';
                 session()->forget('clientName');
                 session()->forget('phoneNumber');
                  session()->put('accountID', $request->account_number);
                 return redirect()->back()->with(['notification' => $accountDetail->responseMessage, 'color' => $color]);
        
        }*/

        if ($responseCode == 200) {

            $responseMessage = $accountDetail->responseMessage;

            $lastName = $accountDetail->lastName;
            $clientName = $accountDetail->firstName . ' ' . $lastName;
            $phoneNumber = $accountDetail->phoneNumber;
            $idType = $accountDetail->idType;
            $idNumber = $accountDetail->idNumber;
            $accountCategory = $accountDetail->accountCategory;

            //$transactionTimestamp   =  $accountDetail->transactionTimestamp;
            //$transactionId          =  $accountDetail->transactionId;
            //$branchId               =  $accountDetail->branchId;
            //$clientId               =  $accountDetail->clientId;
            //$emailID                =  $accountDetail->emailID;
            //$aCStatus               =  $accountDetail->aCStatus;
            //$createdOn              =  $accountDetail->createdOn;
            //$updateCount            =  $accountDetail->updateCount;
            //$branchName             =  $accountDetail->branchName;
            //$currencyID             =  $accountDetail->currencyID;
            //$productID              =  $accountDetail->productID;
            //$productName            =  $accountDetail->productName; 
            //$accountID              =  $accountDetail->accountID;
            //$accountName            =  $accountDetail->accountName;
            //$address                =  $accountDetail->address;
            //$city                   =  $accountDetail->city;
            //$countryID              =  $accountDetail->countryID;
            //$countryName            =  $accountDetail->countryName;
            session()->put('responseMessage', $responseMessage);
            session()->put('clientName', $clientName);
            session()->put('phone', $phoneNumber);
            session()->put('id_type', $idType);
            session()->put('id_number', $idNumber);
            session()->put('accountCategory', $accountCategory);
            session()->put('accountID', $request->account_number);


            /* session()->put('currencyID', $currencyID);        
             session()->put('productID', $productID);
             session()->put('productName', $productName);        
             
             session()->put('accountName', $accountName);        
             session()->put('address', $address);
             session()->put('city', $city);        
             session()->put('countryID', $countryID);
             session()->put('countryName', $countryName);        
             session()->put('phone', $phoneNumber);
             session()->put('emailID', $emailID);        
             session()->put('aCStatus', $aCStatus);
             session()->put('createdOn', $createdOn);       
             session()->put('updateCount', $updateCount);
             session()->put('branchName', $branchName);
             session()->put('transactionTimestamp', $transactionTimestamp);
             session()->put('transactionId', $transactionId);
             session()->put('branchId', $branchId);
             session()->put('clientId', $clientId); */
        } else {

            $color = 'danger';
            session()->forget('clientName');
            session()->forget('phoneNumber');
            session()->forget('id_type');
            session()->forget('id_number');
            session()->forget('accountCategory');
            session()->put('accountID', $request->account_number);
            return redirect()->back()->with(['notification' => $accountDetail->responseMessage, 'color' => $color]);

        }

        return redirect()->back();
    }


    public function insertAccount($account, $user_id, $institution_id)
    {

        try {
            $responseCode = session()->get('responseCode_acc');
            $insert = new IbAccount();
            $insert->clientName = session()->get('clientName');
            $insert->mobile = session()->get('phone');
            $insert->balance = $this->balanceCheck(session()->get('accountID'));
            $insert->user_id = $user_id;
            $insert->institution_id = $institution_id;

            $insert->branchId = session()->get('branchId');
            $insert->clientId = session()->get('clientId');
            $insert->currencyID = session()->get('currencyID');
            $insert->productID = session()->get('productID');
            $insert->productName = session()->get('productName');
            $insert->accountID = session()->get('accountID');
            $insert->accountName = session()->get('clientName');
            $insert->address = session()->get('address');
            $insert->city = session()->get('city');
            $insert->countryID = session()->get('countryID');
            $insert->countryName = session()->get('countryName');
            $insert->emailID = session()->get('emailID');
            $insert->aCStatus = "Active";
            $insert->createdOn = session()->get('createdOn');
            $insert->updateCount = session()->get('updateCount');
            $insert->branchName = session()->get('branchName');
            $insert->initiator_id = Auth::user()->getAuthIdentifier();
            $insert->approver_id = 0;
            $insert->isWaitingApproval = 1;

            $check_client_id = IbAccount::where(['clientId' => $insert->clientId, 'user_id' => $user_id])->get('clientId');

            //check    
            if (isset($check_client_id[0])) {
                $check_client_id = $check_client_id[0];
                $check_client_id = $check_client_id->clientId;
            }
            if ($check_client_id != $insert->clientId and ($responseCode == 200) and (!$check_client_id->isEmpty())) {
                return redirect()->back()->with(['notification' => 'Account does not belong to the customer!', 'color' => 'danger']);
            } else if ($responseCode == 200) {
                //dd('one step to save'.$insert);
                $color = 'success';
                $insert->save();
            } else if ($responseCode == 100) {
                $color = 'danger';
                $this->notification = session()->get('responseMessage');

            }
        } catch (\Exception $e) {

            if ($e->getCode() == 23000) {
                $color = 'danger';
                return redirect()->back()->with(['notification' => 'Account already exists', 'color' => $color]);
            }
            $this->notification = $e->getMessage();
            $color = 'danger';
            return redirect()->back()->with(['notification' => $this->notification, 'color' => $color]);
        }

        $color = 'success';
        session()->forget('branchId');
        session()->forget('clientId');
        session()->forget('clientName');
        session()->forget('currencyID');
        session()->forget('productID');
        session()->forget('productName');
        session()->forget('accountID');
        session()->forget('accountName');
        session()->forget('address');
        session()->forget('city');
        session()->forget('countryID');
        session()->forget('countryName');
        session()->forget('phoneNumber');
        session()->forget('emailID');
        session()->forget('aCStatus');
        session()->forget('createdOn');
        session()->forget('updateCount');
        session()->forget('branchName');


        return redirect()->back()->with(['notification' => $this->notification, 'color' => $color]);


    }

    public function insertAccount2($account, $user_id, $institution_id)
    {

        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";
        $client = new Client;

        $infoRequest = [
            "serviceType" => $serviceType,
            "sourceAccountId" => $account
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo = $res->getBody();
        $accountDetail = json_decode($accountInfo);
        $responseCode = $accountDetail->responseCode;
        $responseMessage = $accountDetail->responseMessage;
        //$transactionTimestamp   =  $accountDetail->transactionTimestamp;
        //$transactionId          =  $accountDetail->transactionId;


        if ($responseCode == 200) {


            $insert = new IbAccount();
            $insert->responseCode = $accountDetail->responseCode;
            $insert->responseMessage = $accountDetail->responseMessage;

            $insert->mobile = $accountDetail->phoneNumber;

            $insert->clientName = $accountDetail->firstName . ' ' . $accountDetail->lastName;

            $insert->branchId = NULL;
            $insert->clientId = NULL;
            $insert->currencyID = NULL;
            $insert->productID = NULL;
            $insert->productName = NULL;

            $insert->accountID = $account;
            $insert->accountName = $accountDetail->firstName . ' ' . $accountDetail->lastName;
            $insert->address = NULL;
            $insert->city = NULL;
            $insert->countryID = NULL;
            $insert->countryName = NULL;
            $insert->emailID = NULL;
            $insert->aCStatus = 'Active';
            $insert->createdOn = NULL;
            $insert->updateCount = NULL;
            $insert->branchName = NULL;
            //$balance = NULL;
            $balance = $this->balanceCheck($account);
            if (is_array($balance)) {
                $insert->balance = $balance['Available balance'];
            } else {
                $insert->balance = $balance;
            }
            //dd($balance[0]-> Available balance);
            //$insert->balance                =  $balance['Available balance'];
            $insert->user_id = $user_id;
            $insert->institution_id = NULL;
            $insert->initiator_id = Auth::user()->getAuthIdentifier();
            $insert->approver_id = '0';
            $insert->isWaitingApproval = '1';
            //dd($insert);
            $insert->save();

            if ($insert == true) {
                $notification = "Account added successful!";
                $color = "success";
                return redirect('ib/user')->with('notification', $notification)->with('color', $color);
            } else {
                $notification = "Account addition failed!";
                $color = "danger";
                return redirect('ib/user/add_account/' . $user_id)->with('notification', $notification)->with('color', $color);
            }

        } else {
            $notification = $responseMessage . ", Account could not be added!";
            $color = "danger";
            return redirect('ib/user/add_account/' . $user_id)->with('notification', $notification)->with('color', $color);
        }

    }

    public function balanceCheck($accountNumber)
    {
        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $client = new Client;
        $transaction_id = mt_rand(123456789, 999999999);
        $infoRequest = [
            "serviceType" => "BALANCE",
            "sourceAccountId" => $accountNumber
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);

        $accountInfo = $res->getBody();
        $accountDetail = json_decode($accountInfo, true);
        $responseCode = $accountDetail['responseCode'];

        if ($responseCode == 200) {
            session()->put('clearBalance', $accountDetail['balances']['Ledger balance']);

            return $accountDetail['balances']['Available balance'];
        } else {
            throw new \Exception('Failed to get account info');
        }

    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'option_id' => 'required',
        ]);

        //role_id

        //Condition to make sure user role is added
//        if($request->role_id==0)
//        {
//            $notification="User must have a role!";
//            $color="danger";
//            return redirect('/ib/user/edit/'.$request->id)->with('notification',$notification)->with('color',$color);
//        }

        //Condition to make sure user otp option is added
        if (!isset($request->option_id)) {
            $this->notification = "User must be assigned OTP Option!";
            $color = "danger";
            return redirect('/ib/user/edit/' . $request->id)->with('notification', $this->notification)->with('color', $color);
        }

//        $role = IBRole::where('id',$request->role_id)->get()[0];

        $old_details = IbUser::where('id', $request->id)->get()[0];
//        && $role->name=="customer"
        if ($request->institute_id == 0) {
            $update = IbUser::where('id', $request->id)
                ->update([
                    'email' => $request->email,
                    'role_id' => $request->role_id,
                    'otp_option' => $request->option_id,
                    'initiator_id' => Auth::user()->getAuthIdentifier(),
                    'approver_id' => '0',
                    'isWaitingApproval' => '1',
                    'isBlocked' => 0,
                ]);
        } else {
            $update = IbUser::where('id', $request->id)
                ->update([
                    'email' => $request->email,
                    'role_id' => $request->role_id,
                    'otp_option' => $request->option_id,
                    'institute_id' => $request->institute_id
                ]);
        }

        if ($update == true) {
            $trail['user_id'] = Auth::user()->id;
            $trail['module'] = "IB";
            $trail['action'] = "Account Update";
            $trail['action_time'] = now();
            $trail['reason'] = "NULL";
            $trail['old_details'] = $old_details;
            $trail['new_details'] = $request;
            $log = new Helper();
            $log->auditTracker($trail);


            $notification = "User update successful!";
            $color = "success";
        } else {
            $notification = "Oops something went wrong!";
            $color = "danger";
        }

        return redirect('/ib/user')->with('notification', $notification)->with('color', $color);


    }

    public function notification_new($email, $name, $full_name, $password)
    {

        //$full_name = session()->get('login_name');
        $body = "Dear " . $full_name . ", your password to access Internet Banking is " . $password . " and username is " . $name . ', visit this link https://ibanktest.acbtz.com:8001/';
        $recipient = $email;
        $time = 30;
        Queue::later($time, new PasswordHitJob($body, $recipient));
    }

    public function notification(Request $request, $password)
    {

        $full_name = session()->get('login_name');
        $body = "Dear " . $request->name . ", your password to access Internet Banking is " . $password . " and username is " . $full_name . ', visit this link The link should be https://ibanktest.acbtz.com:8001/login';
        $recipient = $request->email;
        $time = 30;
        Queue::later($time, new PasswordHitJob($body, $recipient));
    }

    public function store(Request $request)
    {

        $validator = $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255',
            'phone' => 'sometimes',
            'id_number' => 'required',
            'id_type' => 'required',
            'option_id' => 'required',
        ]);//      'role_id'=>'required',

        //Condition to make sure user role is added
//        if($request->role_id==0)
//        {
//            $notification="User must have a role!";
//            $color="danger";
//            return redirect('/ib/user/edit/'.$request->id)->with('notification',$notification)->with('color',$color);
//        }
        $password = mt_rand(12345678, 99999999);
        //Condition to make sure user otp option is added
        if (strlen($request->option_id) == 1) {
            $this->notification = "User must be assigned OTP Option!";
            $color = "danger";
            return redirect('/ib/user')->with('notification', $this->notification)->with('color', $color);
        }
        $phoneNumber = preg_replace("/^0/", "255", $request->phone);
//        $role = IBRole::where('id',$request->role_id)->get()[0];
//        && $role->name=="customer"


        $sub_name = explode(' ', $request->name);
        $first_name = $sub_name[0];
        $first_name_1 = substr($first_name, 0, 1);
        $first_name_1 = strtoupper($first_name_1);
        $full_name = $first_name_1 . trim($sub_name[1]) . mt_rand(100, 999);
        session()->put('login_name', $full_name);

        if ($request->institute_id == 0) {
            //check whether account number exists before adding user 
            if (IbAccount::where('accountID', session()->get('accountID'))->exists()) {
                $this->notification .= "This account already exists!";
                $color = "danger";
                return redirect()->back()->with('notification', $this->notification)->with('color', $color);
            }
            $insert = new IbUser();
            //$insert->name = $request->name;
            $insert->name = session()->get('login_name');
            $insert->email = $request->email;
            $insert->password = Hash::make($password);
            $insert->mobile_phone = $phoneNumber;
            $insert->role_id = $request->role_id;
            $insert->otp_option = $request->option_id;
            $insert->id_type = $request->id_type;
            $insert->id_number = $request->id_number;
            $insert->initiator_id = Auth::user()->getAuthIdentifier();
            $insert->approver_id = '0';
            $insert->isWaitingApproval = '1';
            $insert->display_name = $request->name;
            $insert->isBlocked = 0;
            try {


                $save = $insert->save();

            } catch (\Exception $e) {
                $this->notification = $e->getMessage();
                $color = "danger";
                return redirect()->back()->with('notification', $this->notification)->with('color', $color);
            }

        } else {


            $insert = new IbUser();
            $insert->name = $request->name;
            $insert->email = $request->email;
            $insert->password = Hash::make($password);
            $insert->phoneNumber_phone = $phoneNumber;
            $insert->role_id = $request->role_id;
            $insert->otp_option = $request->option_id;
            $insert->institute_id = $request->institute_id;
            $insert->id_type = $request->id_type;
            $insert->id_number = $request->id_number;
            $insert->initiator_id = Auth::user()->getAuthIdentifier();
            $insert->approver_id = '0';
            $insert->isWaitingApproval = '1';
            $insert->isBlocked = 0;
            $insert->save();
        }


        if ($insert == true) {


            //$this->notification($request,$password);
            $user_type = "App\\Models\\Web\\User";
            $insert_role_user = new IbUserRole();
            $insert_role_user->user_id = $insert->id;
            $insert_role_user->role_id = $insert->role_id;
            $insert_role_user->user_type = $user_type;
            $insert_role_user->save();
            $id = $insert->id;

            $this->insertAccount2(session()->get('accountID'), $id, $request->institute_id);


            $this->notification .= "User added successful!";
            $color = "success";

            $new_details = IbUser::where('id', $insert->id)->get()[0];
            $request['user_id'] = Auth::user()->getAuthIdentifier();
            $request['module'] = "IB";
            $request['action'] = "Store User";
            $request['action_time'] = now();
            $request['reason'] = "NULL";
            $request['old_details'] = "NULL";
            $request['new_details'] = $new_details;
            $log = new Helper();
            return $log->auditTrack($request, $this->notification, $color);

        } else {
            $this->notification = "Oops something went wrong!";
            $color = "danger";
        }

        return redirect('/ib/user')->with('notification', $this->notification)->with('color', $color);
    }

    public function approveUser(Request $request, $id)
    {


        $user = IbUser::where('id', $id)->get()[0];
        $excluded_roles = [1, 2, 4, 5];

        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();

        $sql = "SELECT * FROM tbl_institutions";
        $institutions = DB::connection('sqlsrv2')->select($sql);

        $sql = "SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);

        $options = OtpOption::all();

        $account_types = IbAccountType::all();

        $accounts = IbAccount::where('user_id', $id)->get();

        return view('ib.users.approve_user', compact('roles', 'types', 'institutions', 'user', 'options', 'account_types', 'accounts'));
    }

    public function approveUserAct(Request $request, $id)
    {
        $user_id = Auth::id();

        $password = mt_rand(12345678, 99999999);
        if ($request->reject == 'reject') {

            IbUser::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
            return redirect()->route('ib.users-index')->with(['notification' => 'User has been rejected successfully', 'color' => 'success']);
        }

        if ($request->approve == 'approve') {
            $user = IbUser::where(['id' => $id])->first();
            $enc_password = Hash::make($password);

            IbUser::where(['id' => $id])->update(['password' => $enc_password, 'isWaitingApproval' => 0, 'approver_id' => $user_id]);
            if ($user) {

                $username = $user->name;
                $full_name = $user->display_name;
                $email = $user->email;
                $phoneNumber = $user->mobile_phone;
                if ($user->otp_option == "SMS") {
                    // $username = session()->get('login_name');
                    //send the sms 
                    $msg = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone, use $username for username on this link https://ibanktest.acbtz.com:8001/";

                    $this->sms($msg, $phoneNumber);
                }

                if ($user->otp_option == "EMAIL") {
                    //$this->notification($request, $password);
                    //dd("user email =>"$email,"user name =>"$user->name, "user fullname =>"$full_name, "user password =>"$password);
                    $this->notification_new($email, $user->name, $full_name, $password);
                }

                if ($user->otp_option == "BOTH") {
                    //$username = session()->get('login_name');   
                    //send the sms 
                    $msg = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone, use $username for username on this link https://ibanktest.acbtz.com:8001/";

                    $this->sms($msg, $phoneNumber);

                    //$this->notification($request, $password);
                    $this->notification_new($email, $user->name, $full_name, $password);

                }
            }
            return redirect()->route('ib.users-index')->with(['notification' => 'User has been approved successfully', 'color' => 'success']);
        }


    }


}
