<?php

namespace App\Http\Controllers\IB;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtmCardRequestController extends Controller
{
   
    public function index()
    {
        
		$card_requests = DB::connection('sqlsrv2')
		->table('tbl_atm_card_request')
		->orderBy('id', 'DESC')
		->get();
		
        return view('ib.ATM.card_request', compact('card_requests'));
    }
	
	public function approveRequest($id)
	{
		$approver_id = Auth::user()->id;
		$approved = DB::connection('sqlsrv2')->table('tbl_atm_card_request')
		->where('id', $id)
		->update([
		'status_id' => 2,
		'updated_at' => date('Y-m-d H:i:s'),
		'approver_id' => $approver_id 
		]);
		
		if($approved){
			
			return back()->with(['notification' => "Card Request Approved Successfully", 'color' => 'success']);
		}else{
			alert()->error('Card Request Approval Failed', 'Failed');
			return back();
		}
		
	}
	
	public function rejectRequest($id)
	{
	
		$approved = DB::connection('sqlsrv2')->table('tbl_atm_card_request')
		->where('id', $id)
		->update([
		'status_id' => 6,
		'updated_at' => date('Y-m-d H:i:s'),
		'approver_id' => auth::user()->id
		]);
		
		if($approved){
			
			$notification="Card Request Rejected!";
            $color="success";
            return back()->with('notification',$notification)->with('color',$color);
		}else{
			alert()->error('Card Request Rejection Failed', 'Failed');
			return back();
		}
		
	}

}

