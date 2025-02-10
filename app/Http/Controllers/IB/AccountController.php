<?php

namespace App\Http\Controllers\IB;

use App\Account;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\IbAccount;
use App\IbAccountType;
use App\IbBranch;
use App\IbInstitution;
use App\IbUser;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AccountController extends Controller
{

    public function index()
    {
        $accounts = IbAccount::orderBy('id', 'DESC')->get();

        return view('ib.account.index',compact('accounts'));
    }

    public function updateAccStatus(Request $request)
    {
       $user_id = Auth::id();

        $db_action = IbAccount::where('id', $request['account_id'])->update([
            'isWaitingApproval' => 1,
	    'approver_id' => '0',
            'disabledBy_id' => $user_id,
            'isBlocked' => 1
        ]);

        if ($db_action == true) {
            switch ($request['aCStatus']) {
                case "Active":

                    $notification = "Account activation request sent successfully";
                    $color = "success";

                  //$log = new Helper();
                //return $log->auditTrail("Account Activation","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());

                    break;
                case "Blocked":

                    $notification = "Account blocking request sent successfully";
                    $color = "success";


                    //$log = new Helper();
                //return $log->auditTrail("Account Blocked","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());

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


        return redirect('ib/accounts/index')->with('notification', $notification)->with('color', $color);

    }

     public function disableAccountApproval($id) {
           $account = IbAccount::findOrFail($id);
	   $types = IbAccountType::all();
           $branchs = IbBranch::all();
           $institutions = IbInstitution::all();
            return view('ib.account.disable_account',compact('account','types','institutions','branchs'));    
    }


   public function disableAccountActApproval(Request $request, $id) {
        $user_id = Auth::id();
    	$account = IbAccount::findOrFail($id);
        if($account->aCStatus== "Active")
	{
	if ($request->reject == 'reject') {
    
        IbAccount::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 2]);
        return redirect()->route('ib.accounts_index')->with(['notification' => 'Account blocking has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbAccount::where(['id' => $id])->update(['aCStatus' => 'Blocked', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 0]);
          return redirect()->route('ib.accounts_index')->with(['notification' => 'Account blocking has been approved successfully', 'color' => 'success']);   
        }
	}
	if($account->aCStatus== "Blocked")
	{
	if ($request->reject == 'reject') {
    
        IbAccount::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 2]);
        return redirect()->route('ib.accounts_index')->with(['notification' => 'Account activation has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbAccount::where(['id' => $id])->update(['aCStatus' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isBlocked' => 0]);
          return redirect()->route('ib.accounts_index')->with(['notification' => 'Account activation has been approved successfully', 'color' => 'success']);   
        }
	}

}


    public function create()
    {
        $types = IbAccountType::all();
        $branchs = IbBranch::all();
        $institutions = IbInstitution::all();
        $users = IbUser::all();

        return view('ib.account.create',compact('types','branchs','institutions','users'));
    }

    public function edit($id)
    {
        $account = IbAccount::where('id',$id)->get()[0];
        $types = IbAccountType::all();
        $branchs = IbBranch::all();
        $institutions = IbInstitution::all();
        $users = IbUser::all();

        return view('ib.account.edit',compact('account','types','institutions','users','branchs'));
    }

    public function update(Request $request,$id)
    {
            $request->validate([
                //'account_number'=>'required',
                //'account_name'=>'required',
                //'account_type'=>'required',
                //'branch_id'=>'required',
                'min_amount'=>'required',
                'max_amount'=>'required'
            ]);
           
            session()->put('minAmount', $request->minAmount);
            session()->put('maxAmount', $request->maxAmount);

            //$account_number = IbAccount::select(['tbl_account.*'])
              //  ->where('accountID',$request->account_number)
              //  ->whereNotIn('id',[$id])
               // ->get();

            /*
            if(count($account_number)>0)
            {
                $notification="Account already exist!";
                $color="danger";
                $log = new Helper();
                return $log->auditTrail("Account Updated","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());

            }
            */
             //dd($request->all());

            if ($request->institution_id == 0) {
                $update = Account::where('id', $id)
                    ->update([
                        //'accountID' => $request->account_number,
                        //'accountName' => $request->account_name,
                        //'account_type_id' => $request->account_type,
                        //'branchId' => $request->branch_id,
                        //'initiator_id'=>Auth::user()->getAuthIdentifier(),
						'initiator_id'=>Auth::user()->id,
                        'isWaitingApproval'=>'1',
                        'approver_id'=>'0',
                        'minAmount' =>$request->min_amount,
                        'maxAmount' =>$request->max_amount
                    ]);
            } else {
				
                $update = Account::where('id', $id)
                    ->update([
                        //'accountID' => $request->account_number,
                        //'accountName' => $request->account_name,
                        //'account_type_id' => $request->account_type,
                        //'branchId' => $request->branch_id,
                        //'institution_id' => $request->institution_id,
                        'initiator_id'=>Auth::user()->getAuthIdentifier(),
                        'isWaitingApproval'=>'1',
                        'approver_id'=>'0',
                        'minAmount' =>$request->min_amount,
                        'maxAmount' =>$request->max_amount
                    ]);
           }

            if ($update == true) {
				$account  = Account::find($id)->first();
               // $this->updateAccount($account->accountID, $id);
                $notification = "Account details updated successful";
                $color = "success";

            } else {
                $notification = "Oops something went wrong!";
                $color = "danger";
            }


        return redirect('ib/accounts/index')->with('notification',$notification)->with('color',$color);
    }

    public function store(Request $request)
    {
         $request->validate([
             'account_number'=>'required',
             'account_type'=>'required',
             'branch_id'=>'required',
             'min_amount'=>'required',
            'max_amount'=>'required'

         ]);



         $active = "Active";

            $insert = new Account();
            $insert->accountID = $request->account_number;
            $insert->accountName = $request->account_name;
            $insert->balance = $request->balance;
            $insert->account_type_id = $request->account_type;
            $insert->branchId = $request->branch_id;
            $insert->user_id = $request->user_id;
            $insert->institution_id = $request->institution_id;
            $insert->aCStatus = $active;
            $insert->initiator_id=Auth::user()->getAuthIdentifier();
            $insert->approver_id=0;
            $insert->isWaitingApproval=1;
            session()->put('minAmount', $request->min_amount);
            session()->put('maxAmount', $request->max_amount);
            //$insert->minAmount             =  $request->min_amount;
            //$insert->maxAmount             =  $request->max_amount;
            $insert->save();



             if ($insert == true) {
                 $this->insertAccount($insert->accountID,$insert->user_id);
                 $notification = "User Tied to account successful";
                 $color = "success";
             } else {
                 $notification = "Oops something went wrong!";
                 $color = "danger";
             }


         return redirect('ib/accounts/index')->with('notification',$notification)->with('color',$color);
    }


    public function insertAccount($account, $user_id) {
		dd('here');

        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";//41.188.
        $client = new Client;

        $infoRequest = [
            "serviceType" => $serviceType,
            "accountID"   => $account
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;
        $transactionTimestamp   =  $accountDetail->transactionTimestamp;
        $transactionId          =  $accountDetail->transactionId;

        if ($responseCode == 100) {

            $notification =  $responseMessage;
            $color = "danger";
            return redirect('ib/accounts/index')->with('notification',$notification)->with('color',$color);
        }

        if($responseCode == 200) {

            $branchId               =  $accountDetail->branchId;
            $clientId               =  $accountDetail->clientId;
            $clientName             =  $accountDetail->clientName;
            $currencyID             =  $accountDetail->currencyID;
            $productID              =  $accountDetail->productID;
            $productName            =  $accountDetail->productName;
            $accountID              =  $accountDetail->accountID;
            $accountName            =  $accountDetail->accountName;
            $address                =  $accountDetail->address;
            $city                   =  $accountDetail->city;
            $countryID              =  $accountDetail->countryID;
            $countryName            =  $accountDetail->countryName;
            $mobile                 =  $accountDetail->mobile;
            $emailID                =  $accountDetail->emailID;
            $aCStatus               =  $accountDetail->aCStatus;
            $createdOn              =  $accountDetail->createdOn;
            $updateCount            =  $accountDetail->updateCount;
            $branchName             =  $accountDetail->branchName;
            $balance                =  $this->balanceCheck($accountID);
            //$insert->minAmount             =  session()->get('minAmount');
            //$insert->maxAmount             =  session()->get('maxAmount');

            $insert = IbAccount::insert([
                "balance" => $balance,
                "responseCode" => $responseCode,
                "responseMessage" => $responseMessage,
                "transactionTimestamp" => $transactionTimestamp,
                "transactionId" => $transactionId,
                "branchId" => $branchId,
                "clientId" => $clientId,
                "clientName" => $clientName,
                "currencyID" => $currencyID,
                "productID" => $productID,
                "productName" => $productName,
                "accountID" => $accountID,
                "accountName" => $accountName,
                "address" => $address,
                "city" => $city,
                "countryID" => $countryID,
                "countryName" => $countryName,
                "mobile" => $mobile,
                "emailID" => $emailID,
                "aCStatus" => $aCStatus,
                "createdOn" => $createdOn,
                "updateCount" => $updateCount,
                "branchName" => $branchName,
                "user_id" => $user_id,
                "minAmount" => session()->get('minAmount'),
                "maxAmount" => session()->get('maxAmount')
            ]);

        }


    }

    public function updateAccount($account, $account_id){
        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";
        $client = new Client;

        $infoRequest = [
            "serviceType" => $serviceType,
            "sourceAccountId"   => $account,
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;


        if($responseCode == 200) {

           /* $branchId               =  $accountDetail->branchId;
            $clientId               =  $accountDetail->clientId;
            $clientName             =  $accountDetail->clientName;
            $currencyID             =  $accountDetail->currencyID;
            $productID              =  $accountDetail->productID;
            $productName            =  $accountDetail->productName;
            $accountID              =  $accountDetail->accountID;
            $accountName            =  $accountDetail->accountName;
            $address                =  $accountDetail->address;
            $city                   =  $accountDetail->city;
            $countryID              =  $accountDetail->countryID;
            $countryName            =  $accountDetail->countryName;
            $mobile                 =  $accountDetail->mobile;
            $emailID                =  $accountDetail->emailID;
            $aCStatus               =  $accountDetail->aCStatus;
            $createdOn              =  $accountDetail->createdOn;
            $updateCount            =  $accountDetail->updateCount;
            $branchName             =  $accountDetail->branchName;*/
            $balance                =  $this->balanceCheck($account);
            //$insert->minAmount             =  session()->get('minAmount');
            //$insert->maxAmount             =  session()->get('maxAmount');
            $update = IbAccount::where('id',$account_id)
                ->update([
                    "responseCode" => $responseCode,
                    "responseMessage" => $responseMessage,
                    /*"transactionTimestamp" => $transactionTimestamp,
                    "transactionId" => $transactionId,
                    "branchId" => $branchId,
                    "clientId" => $clientId,
                    "clientName" => $clientName,
                    "currencyID" => $currencyID,
                    "productID" => $productID,
                    "productName" => $productName,
                    "accountID" => $accountID,
                    "accountName" => $accountName,
                    "address" => $address,
                    "city" => $city,
                    "countryID" => $countryID,
                    "countryName" => $countryName,
                    "mobile" => $mobile,
                    "emailID" => $emailID,
                    "aCStatus" => $aCStatus,
                    "createdOn" => $createdOn,
                    "updateCount" => $updateCount,
                    "branchName" => $branchName,*/
                    "balance"=>$balance,
                    "minAmount" => session()->get('minAmount'),
                    "maxAmount" => session()->get('maxAmount')
                ]);

            if($update==true)
            {
                $notification="Account updated successful!";
                $color="success";
                $log = new Helper();
                return $log->auditTrail("Account Updated","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());

            }else{
                $notification="Account update failed!";
                $color="danger";
                $log = new Helper();
                return $log->auditTrail("Account Activation","IB",$notification,'ib/accounts/edit/'.$account_id,Auth::user()->getAuthIdentifier());
//                return redirect('ib/accounts/edit/'.$account_id)->with('notification',$notification)->with('color',$color);
            }

        } else {
           
                $notification =  "Account number updating failed!";
                $color = "danger";
                return redirect('ib/accounts/edit/'.$account_id)->with('notification',$notification)->with('color',$color);
        
        }
    }

    public function balanceCheck($accountNumber)
    {
        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $client = new Client;
        $transaction_id = mt_rand(123456789,999999999);
        $infoRequest = [
            "serviceType"=> "BALANCE",
            "sourceAccountId"=> $accountNumber,
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);

        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;

        if ($responseCode == 100) {

            alert()->warning('Failed to get account info', 'Info gather failed!');
        }

        if ($responseCode == 200) {
			$accountInfo          =  json_decode($accountInfo,true);
            return $accountInfo['balances']['Available balance'];
        }


    }

     public function approveAccount(Request $request, $id) {
    
        $account = IbAccount::findOrFail($id);
        $types = IbAccountType::all();
        $branchs = IbBranch::all();
        $institutions = IbInstitution::all();
        $users = IbUser::all();
        return view('ib.account.approve_account', compact('account', 'types', 'branchs', 'institutions', 'users'));
    }

      public function approveAccountAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbAccount::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.accounts_index')->with(['notification' => 'Account has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbAccount::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.accounts_index')->with(['notification' => 'Account has been approved successfully', 'color' => 'success']);   
        }

        

    }

}
