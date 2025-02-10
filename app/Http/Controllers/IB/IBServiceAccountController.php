<?php

namespace App\Http\Controllers\IB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\IBServiceAccount;
use App\IbTransferType;
use App\Helper\Helper;
use Illuminate\Support\Facades\Auth;

class IBServiceAccountController extends Controller
{
    //
    public function index() {
        $accounts = IBServiceAccount::orderBy('id', 'DESC')->get();
	    $services = IbTransferType::all();
        return view('ib.service_account.index', compact('accounts', 'services'));
    }

    public function store(Request $request) {

	if(IBServiceAccount::where('service_code',$request->service_id)->first()){
		$notification = 'Service account already exists!';
		$color = 'danger';
		return redirect()->back()->with('notification', $notification)->with('color', $color);
	}
		try{
			$serviceAccount = new IBServiceAccount();
			$transferType = IbTransferType::where('id', $request->service_id)->first();
			
			$serviceAccount->service_name = $transferType->name;
			$serviceAccount->service_code = $request->service_id;
			$serviceAccount->account_number = $request->account_number;
			$serviceAccount->retail_transaction_limit = $request->retail_trxn_limit;
			$serviceAccount->retail_daily_limit = $request->retail_daily_limit;
			$serviceAccount->corporate_transaction_limit = $request->corporate_trxn_limit;
			$serviceAccount->corporate_daily_limit = $request->corporate_daily_limit;
			$serviceAccount->commission_account = $request->commission_account;
			$serviceAccount->initiator_id = Auth::user()->getAuthIdentifier();
			$serviceAccount->isWaitingApproval = 1;
			$serviceAccount->approver_id = 0;
			$serviceAccount->save();
			$notification = 'Service account added successfully';
			$color = 'success';
			return redirect()->back()->with('notification', $notification)->with('color', $color);
		}catch(\Exception $e){
			$notification = 'Failed to add service account '.$e->getMessage();
			$color = 'danger';
			
			return redirect()->back()->with('notification', $notification)->with('color', $color);
		}
      
    }

    public function edit($id)
    {
        $serviceAccount = IBServiceAccount::where('id',$id)->get()[0];
        $services = IbTransferType::all();
        return view('ib.service_account.edit', compact('serviceAccount', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'service_id'=>'required'
        ]);
        $transferType = IbTransferType::where('id', $request->service_id)->first();
        try {

            $db_action = IBServiceAccount::where('id',$id)->update([
                'service_name' => $transferType->name,
                'account_number' => $request->account_number,
                'commission_account' => $request->commission_account,
				'retail_transaction_limit' => $request->retail_trxn_limit,
				'retail_daily_limit' => $request->retail_daily_limit,
				'corporate_transaction_limit' => $request->corporate_trxn_limit,
				'corporate_daily_limit' => $request->corporate_daily_limit,
                'service_code' => $request->service_id,
                'initiator_id' => Auth::user()->getAuthIdentifier(),
                'isWaitingApproval' => 1,
                'approver_id' => 0
            ]);

            if ($db_action == true) {


                $notification = "Service Account updated successfully";
                $color = "success";

                // $log = new Helper();
                // return $log->auditTrail("Updated Service Account","IB",$notification,'ib/service_account/update',Auth::user()->getAuthIdentifier());
            }else{
                $notification = "No change was made!";
                $color = "danger";
            }

        }catch (\Exception $e)
        {
            $notification = $e->getMessage();
            $color = "danger";
        }


        return redirect()->route('ib.service_acc.index')->with('notification',$notification)->with('color',$color);
    }

    public function approveServiceAccount(Request $request, $id) {
    
        $serviceAccount = IBServiceAccount::findOrFail($id);
        $services = IbTransferType::all();
        return view('ib.service_account.approve_service_account', compact('serviceAccount', 'services'));
    }

    public function approveServiceAccountAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IBServiceAccount::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.service_acc.index')->with(['notification' => 'Service Account has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IBServiceAccount::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.service_acc.index')->with(['notification' => 'Service Account has been approved successfully', 'color' => 'success']);   
        }

        

    }


}
