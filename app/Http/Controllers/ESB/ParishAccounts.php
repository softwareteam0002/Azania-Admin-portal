<?php

namespace App\Http\Controllers\ESB;

use App\AuditTrailLogs;
use App\Helper\Helper;
use App\EsbParishAccount;
use App\EsbSadakaTranType;
use App\EsbParishTransactionType;
use App\TblAdminActionLevel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class ParishAccounts extends Controller
{
	 //Church Institutions
    public function indexChurchInst()
    {
		
        $churchinstitutions = EsbParishAccount::orderBy('id', 'DESC')->get();
		
		$sadakatrantypes = EsbSadakaTranType::get();
		
        return view('esb.parishes.index',compact('churchinstitutions','sadakatrantypes'));
    }
	
	public function storeChurchInst(Request $request)
    {
   
        $request->validate([
            'institute_name'=>'required',
            'institute_prefix'=>'required',
          
        ]);

		try{
			$verify_prefix = EsbParishAccount::where('member_id', $request->institute_prefix)->first();
			
		if($request->sadakatrantypes)
			{
					if($verify_prefix){
			 $notification = 'Parish prefix already exist';
             $color = 'danger';
			 return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);
		}
		else
		{
			
			$parish = new EsbParishAccount();
            $parish->description=$request->institute_name;
			if($request->isDefaulAccount == 'yes')
			{
			$parish->account_number=session()->get('accountID');			
			}
            else
			{
				$parish->account_number='N/A';
			}
            $parish->member_id=$request->institute_prefix; 		
            $parish->initiator_id =  Auth::user()->getAuthIdentifier();
            $parish->isWaitingApproval = '1';			
			$parish->aCStatus               =  session()->get('aCStatus');
			
			$parish->save();
			$notification = 'Parish Account added Successfully!';
            $color = 'success';
           
            
		}
		
			foreach($request->sadakatrantypes as $sadakatrantype)
		{
			
			$verify_account = EsbParishTransactionType::where('account_number', session()->get('accountID'))->where('sadaka_tran_type_id',$sadakatrantype)->where('parish_account_id',$parish->id)->first();
			if($verify_account)
			{
			 $notification = 'Parish account '.session()->get('accountID').' already mapped with transaction type for prefix '.$request->institute_prefix;
             $color = 'danger';
			 return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);	
			}
			else
			{
			$parish_tran_type = new EsbParishTransactionType();
            $parish_tran_type->account_number=session()->get('accountID');
			$parish_tran_type->account_name=session()->get('clientName');
			if($request->isDefaulAccount == 'yes' && $sadakatrantype == 7)
			{
			$parish_tran_type->sadaka_tran_type_id=NULL;			
			}
            else
			{
			$parish_tran_type->sadaka_tran_type_id=$sadakatrantype;
			}           
            $parish_tran_type->parish_account_id=$parish->id; 		
			$parish_tran_type->save();	
			$notification = 'Parish account and transaction type(s) mapped successfully!';
            $color = 'success';
			}
		}
			}
			else
			{
			$notification = 'Please select/mark transaction type to proceed!';
            $color = 'danger';
			return redirect()->back()->with('notification',$notification)->with('color',$color);			
			}
		return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);
       
        }catch (\Exception $e)
        {
			//$notification = 'Something went wrong';
			$notification = $e->getMessage();
            $color = 'danger';
            return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);
        }
		
            
    }
	
	public function editChurchInst($id)
    {
		$account_tran_types_arr = [];
        $institution = EsbParishAccount::where('id',$id)->first();
		if($institution)
		{
		$account_tran_types = EsbParishTransactionType::where('parish_account_id',$institution->id)->get('sadaka_tran_type_id');
		foreach($account_tran_types as $account_tran_type)
		{
			array_push($account_tran_types_arr,trim($account_tran_type->sadaka_tran_type_id));
		}
		}

		$sadakatrantypes = EsbSadakaTranType::get();
        return view('esb.parishes.edit',compact('institution','sadakatrantypes','account_tran_types_arr'));
    }
	
	public function addChurchAccount($id){
		$institution = EsbParishAccount::where('id',$id)->first();
        $institution_accounts = EsbParishTransactionType::where('parish_account_id',$id)->get();

		$sadakatrantypes = EsbSadakaTranType::get();
		
        return view('esb.parishes.account',compact('institution_accounts','sadakatrantypes','institution'));	
	}
	
	public function storeChurchInstAccount(Request $request)
	{
		
		$parish = EsbParishAccount::where('id',$request->institute_id)->first();
			if($request->sadakatrantypes)
			{
		foreach($request->sadakatrantypes as $sadakatrantype)
		{
			
			$verify_account = EsbParishTransactionType::where('sadaka_tran_type_id',$sadakatrantype)->where('parish_account_id',$request->institute_id)->first();
			
			if($verify_account)
			{
			 $notification = 'Parish transaction type '.$verify_account->type->name.' already mapped with another account';
             $color = 'danger';
			 return redirect()->back()->with('notification',$notification)->with('color',$color);	
			}
			else
			{
		
			$parish_tran_type = new EsbParishTransactionType();
            $parish_tran_type->account_number=session()->get('accountID');
			$parish_tran_type->account_name=session()->get('clientName');
			if($request->isDefaulAccount == 'yes' && $sadakatrantype == 7)
			{
			$parish->account_number = session()->get('accountID');
			$parish->save();
			
			$parish_tran_type->sadaka_tran_type_id=NULL;			
			}
            else
			{
			$parish_tran_type->sadaka_tran_type_id=$sadakatrantype;
			}           
            $parish_tran_type->parish_account_id=$request->institute_id;
			$parish_tran_type->initiator_id =  Auth::user()->getAuthIdentifier();
            $parish_tran_type->isWaitingApproval = '1';	
			$parish_tran_type->save();	
			$notification = 'Parish account and transaction type(s) mapped successfully!';
            $color = 'success';
			}
		}
			}
			else
			{
			$notification = 'Please select/mark transaction type to proceed!';
            $color = 'danger';
			return redirect()->back()->with('notification',$notification)->with('color',$color);			
			}
		return redirect()->back()->with('notification',$notification)->with('color',$color);	
	}
	public function deleteChurchInst(Request $request)
	{
		$parish = EsbParishAccount::where('id', $request->parish_id)->first();
		if($parish)
		{
		$institutionaccount = EsbParishTransactionType::where('parish_account_id',$request->parish_id)->first();
		if($institutionaccount)
		{
			$institutionaccount->delete();
		}
		
			$parish->delete();
		$notification = "Parish deleted successfully";
	   $color = "success";
		}
		else
		{
		$notification = "Parish deleting failed";
	   $color = "danger";
		}
		
      
       return redirect()->back()->with('notification',$notification)->with('color',$color);	
	}
	
	public function deleteChurchInstAccount(Request $request){ 
		
		$institutionaccount = EsbParishTransactionType::where('id',$request->parish_account_id)->first();
		$institutionaccount->delete();
       $notification = "Parish account transaction mapping deleted successfully";
	   $color = "success";
       return redirect()->back()->with('notification',$notification)->with('color',$color);	
	}

    public function updateChurchInst(Request $request,$id)
    {
        $request->validate([
            'institute_name'=>'required',
            'account_number'=>'required',
            'institute_prefix'=>'required'
        ]);
        try{
            $old_details = EsbParishAccount::where('id',$id)->get()[0];
            $update = EsbParishAccount::where('id',$id)
                ->update([
                    'description'=>$request->institute_name,
                    'account_number'=>$request->account_number,
                    'member_id'=>$request->institute_prefix,                    
                    'initiator_id'=>Auth::user()->getAuthIdentifier(),
                    'approver_id'=>'0',
                    'isWaitingApproval'=>'1'
                ]);

            if ($update == true) {
                $new_details = EsbParishAccount::where('id',$id)->get()[0];
                $notification = 'Church Institution updated Successfully!';
                $color = 'success';
                //Audit trail Institution update
                $request['user_id']=Auth::user()->getAuthIdentifier();
                $request['module']="IB";
                $request['action']="Update Institution";
                $request['action_time']=now();
                $request['reason']="NULL";
                $request['old_details']=$old_details;
                $request['new_details']=$new_details;
                $log = new Helper();
                return $log->auditTrack($request,$notification,$color);
            } else {
                $notification = 'Oops something went wrong!';
                $color = 'danger';
            }

            return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color = 'danger';
            return redirect('esb/parishes/index')->with('notification',$notification)->with('color',$color);
        }
    }



public function verifyAccount(Request $request) {
        $url = "http://172.20.1.6:8984/mkombozi/request/process/ib";
        $serviceType = "INFO";
        $client = new Client;
        $account = $request->account_number;
        $infoRequest = [
            "serviceType" => $serviceType,
            "accountID"   => $account
        ];
	
        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);
        try {

        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;
     
        if($responseCode == 200) {
        $transactionTimestamp   =  $accountDetail->transactionTimestamp;
        $transactionId          =  $accountDetail->transactionId;
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
        $minAmount             =  $request->min_amount;
        $maxAmount             =  $request->max_amount;

        session()->put('responseCode_acc', $responseCode);
        session()->put('responseMessage', $responseMessage);
        session()->put('transactionTimestamp', $transactionTimestamp);
        session()->put('transactionId', $transactionId);
        session()->put('branchId', $branchId);
        session()->put('clientId', $clientId);       
        session()->put('clientName', $clientName);
        session()->put('currencyID', $currencyID);        
        session()->put('productID', $productID);
        session()->put('productName', $productName);        
        session()->put('accountID', $accountID);
        session()->put('accountName', $accountName);        
        session()->put('address', $address);
        session()->put('city', $city);        
        session()->put('countryID', $countryID);
        session()->put('countryName', $countryName);        
        session()->put('mobile', $mobile);
        session()->put('emailID', $emailID);        
        session()->put('aCStatus', $aCStatus);
        session()->put('createdOn', $createdOn);       
        session()->put('updateCount', $updateCount);
        session()->put('branchName', $branchName);
        session()->put('minAmount', $minAmount);
        session()->put('maxAmount', $maxAmount);

        } 
		else
		{
			session()->put('responseCode_acc', $responseCode);
            session()->put('responseMessage', $responseMessage);
            $color = 'danger';
            return redirect()->back()->with(['notification' => $responseMessage, 'color' => $color]);
		}
        

    } catch (\Exception $e) {

        return redirect()->back();   
    }
    return redirect()->back();
}
    
	
	public function approveChurchInstitution(Request $request, $id) {
    
        $institution = EsbParishAccount::findOrFail($id);
		
        return view('esb.parishes.approve_institution', compact('institution'));
    }
    public function approveChurchInstitutionAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        EsbParishAccount::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('esb.churchinstitution_index')->with(['notification' => 'Institution has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          EsbParishAccount::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('esb.churchinstitution_index')->with(['notification' => 'Institution has been approved successfully', 'color' => 'success']);   
        }
    }
	
	public function approveChurchService(Request $request, $id) {
    
        $institution_service = EsbParishTransactionType::findOrFail($id);
		
        return view('esb.parishes.approve_institution_service', compact('institution_service'));
    }
    public function approveChurchServiceAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        EsbParishTransactionType::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('esb.churchinstitution.account', $request->institute_id)->with(['notification' => 'Institution service has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          EsbParishTransactionType::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('esb.churchinstitution.account', $request->institute_id)->with(['notification' => 'Institution service has been approved successfully', 'color' => 'success']);   
        }
    }
	
}
