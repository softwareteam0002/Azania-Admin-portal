<?php

namespace App\Http\Controllers\IB;

use App\AuditLogs;
use App\ChequeBook;
use App\Fdr;
use App\Helper\Helper;
use App\IbBank;
use App\IbBranch;
use App\IbInstitution;
use App\IbLetterGuarantee;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMSController;
use App\IbMno;
use App\StandingOrder;
use App\IbTv;
use App\IbAccount;
use App\IbUser;
use DB;
use App\IbClass;
use App\Jobs\BankRequests;
use App\LetterCredit;
use App\Loan;
use App\LoanType;
use App\Mail\PasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class IbActionRequestController extends Controller
{
    private $date_time;
    public $r;

    public function ibRequestHandler(Request $request)
    {
		
        $request->validate([
           'request_type'=>'required'
        ]);

        $type = $request->request_type;
        $id = $request->id;
        $this->date_time = Carbon::now()->setTimezone('Africa/Nairobi');
        //For the requests use only two steps pending-1 approved-4 rejected-5
        switch ($type)
        {
            case "letter_guarantee_request":
                //Request requires status, id , and initiator_id / approver_id

                // 6- show 2- In progress 4- Approved 5- Rejected

                //If status set to 6 then we show page
                if($request->status=="6")
                {
                    $request = IbLetterGuarantee::where('id',$id)->get()[0];

                    return view('ib.customer_request.show_letter_guaranteee',compact('request'));
                }

                //Initiate
                if($request->status=="2")
                {
                    $db_action = IbLetterGuarantee::where('id',$id)->update([
                        'status_id'=>'2',
                        'initiator_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter of guaratee has been initiated";
                        $log = new Helper();
                        $log->auditTrail("Letter Guarantee Initiated","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter of guaratee has been initiated", 'color' => "success"]);
                    }
                    else{

                        return redirect()->back()->with(['notification' => "Letter of guaratee initiation failed", 'color' => "danger"]);
                    }
                }

                //Approve
                if($request->status=="4")
                {
                    $db_action = IbLetterGuarantee::where('id',$id)->update([
                        'status_id'=>'4',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter of guaratee has been Approved";
                        $log = new Helper();
                        $log->auditTrail("Letter Guarantee Approved","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter of guaratee has been approved", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Letter of guaratee approval failed", 'color' => "danger"]);
                    }
                }

                //Rejected
                if($request->status=="5")
                {
                    $db_action = IbLetterGuarantee::where('id',$id)->update([
                        'status_id'=>'5',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter of guaratee has been rejected";
                        $log = new Helper();
                        $log->auditTrail("Letter Guarantee Rejected","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter of guaratee has been rejected", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Letter of guaratee rejection failed", 'color' => "danger"]);
                    }
                }

               break;
            case "loan_request":

                //Request requires status, id , and initiator_id / approver_id

                // 6- show 2- In progress 4- Approved 5- Rejected

                //Show more
                if($request->status=="6")
                {

                    $request = Loan::where('id',$id)->get()[0];

                    return view('ib.customer_request.show_loan_request',compact('request'));
                }

                //Initiate
                if($request->status=="2")
                {
                   $db_action = Loan::where('id',$id)->update([
                      'status_id'=>'2',
                      'initiator_id'=>Auth::user()->getAuthIdentifier()
                   ]);

                   if($db_action==true)
                   {

                       $notification="Loan request has been initiated";
                       $log = new Helper();
                       $log->auditTrail("Loan Request Initiated","IB",$notification,'ib/loan/request',Auth::user()->getAuthIdentifier());
                       return redirect()->back()->with(['notification' => "Loan request has been initiated", 'color' => "success"]);
                   }
                   else{
                       return redirect()->back()->with(['notification' => "Loan request initiation failed", 'color' => "danger"]);
                   }
                }

                //Approve
                if($request->status=="4")
                {
                    $db_action = Loan::where('id',$id)->update([
                        'status_id'=>'4',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Loan request has been approved";
                        $log = new Helper();
                        $log->auditTrail("Loan Request Approved","IB",$notification,'ib/loan/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Loan request has been approved", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Loan request approval failed", 'color' => "danger"]);
                    }
                }

                //Rejected
                if($request->status=="5")
                {
                    $db_action = Loan::where('id',$id)->update([
                        'status_id'=>'5',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Loan request has been rejected";
                        $log = new Helper();
                        $log->auditTrail("Loan Request Rejected","IB",$notification,'ib/loan/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Loan request has been rejected", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Loan request rejection failed", 'color' => "danger"]);
                    }
                }

              break;
            case "letter_credit_request":

                if($request->status=="6")
                {
                    $request = LetterCredit::where('id',$id)->get()[0];

                    return view('ib.customer_request.show_letter_credit',compact('request'));
                }

                //Initiate
                if($request->status=="2")
                {
                    $db_action = LetterCredit::where('id',$id)->update([
                        'status_id'=>'2',
                        'initiator_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter credit request has been initiated";
                        $log = new Helper();
                        $log->auditTrail("Letter Credit Request Initiated","IB",$notification,'ib/letter/credit',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter credit request has been initiated", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Letter credit request initiation failed", 'color' => "danger"]);
                    }
                }

                //Approve
                if($request->status=="4")
                {
                    $db_action = LetterCredit::where('id',$id)->update([
                        'status_id'=>'4',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter credit request has been approved";
                        $log = new Helper();
                        $log->auditTrail("Letter Credit Request Approved","IB",$notification,'ib/letter/credit',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter credit request has been approved", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Letter credit request approval failed", 'color' => "danger"]);
                    }
                }

                //Rejected
                if($request->status=="5")
                {
                    $db_action = LetterCredit::where('id',$id)->update([
                        'status_id'=>'5',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Letter credit request has been rejected";
                        $log = new Helper();
                        $log->auditTrail("Letter Credit Request Rejected","IB",$notification,'ib/letter/credit',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Letter credit request has been rejected", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Letter credit request rejection failed", 'color' => "danger"]);
                    }
                }

              break;

			case "standing_order_request":         
            //Initiate
            
			$standing_order = DB::connection('sqlsrv2')->table('tbl_standing_order')->where('id', $id)->first();
            $user = IbUser::where('id', $standing_order->user_id)->first();
            $username = explode('.', $user->name);
            if (count($username) == 2) {
                $full_name = ucfirst($username[0]) . ' ' . ucfirst($username[1]);
            } else {
                $full_name = $user->name;
            }

            if ($request->status == "2") {
                $db_action = DB::connection('sqlsrv2')->table('tbl_standing_order')->where('id', $id)->update([
                    'status_id' => '2',
                    'initiator_id' => Auth::user()->id,
                ]);

                if ($db_action == true) {
                    $notification = "Standing Order request has been initiated";
                    $log = new Helper();
                    $log->auditTrail("Standing Order Request Initiated", "IB", $notification, 'ib/cheque/request', Auth::user()->getAuthIdentifier());
                    return redirect()->back()->with(['notification' => "Standing Order has been initiated", 'color' => "success"]);
                } else {
                    return redirect()->back()->with(['notification' => "Standing Order initiation failed", 'color' => "danger"]);
                }
            }

            //Success
            if ($request->status == "4") {
                $db_action = DB::connection('sqlsrv2')->table('tbl_standing_order')->where('id', $id)->update([
                    'status_id' => '4',
                    'approver_id' => Auth::user()->id,
                ]);

                if ($db_action == true) {
                    $notification = "Standing Order request successful";
                    $log = new Helper();
                    $log->auditTrail("Standing Order Request Successful", "IB", $notification, 'ib/cheque/request', Auth::user()->getAuthIdentifier());
                    
                    return redirect()->back()->with(['notification' => "Standing Order successful", 'color' => "success"]);

                } else {
                    return redirect()->back()->with(['notification' => "Operation failed", 'color' => "danger"]);
                }
            }

            //Failed
            if ($request->status == "6") {
				
                $db_action = DB::connection('sqlsrv2')->table('tbl_standing_order')->where('id', $id)->update([
                    'status_id' => '6',
                    'approver_id' => Auth::user()->id,
                ]);

                if ($db_action == true) {
                    $notification = "Standing Order request failed";
                    $log = new Helper();
                    $log->auditTrail("Standing Order Request Failed", "IB", $notification, 'ib/cheque/request', Auth::user()->getAuthIdentifier());
                    
                    return redirect()->back()->with(['notification' => "Standing Order failed", 'color' => "success"]);

                } else {
                    return redirect()->back()->with(['notification' => "Operation failed", 'color' => "danger"]);
                }
            }
			
			//blocked
			if (is_null($request->status)) {
				
                $db_action = StandingOrder::where('id', $id)->update([
                    'status_id' => '10',
                    'approver_id' => Auth::user()->id,
                ]);

                if ($db_action == true) {
                    $notification = "Standing Order request is blocked successfully";
                    $log = new Helper();
                    $log->auditTrail("Standing Order Request is blocked ", "IB", $notification, 'ib/cheque/request', Auth::user()->getAuthIdentifier());
                    
                    return redirect()->back()->with(['notification' => "Standing Order is blocked successfully", 'color' => "success"]);

                } else {
                    return redirect()->back()->with(['notification' => "Operation failed", 'color' => "danger"]);
                }
            }

            break;
       
			 
            case "cheque_book_request":

                //If status set to 6 then we show page
                // if($request->status=="6")
                // {

                //     $request = ChequeBook::where('id',$id)->get()[0];

                //     return view('ib.customer_request.show_cheque',compact('request'));
                // }
                //Initiate
                $cheque = ChequeBook::where('id',$id)->first();
                $user = IbUser::where('id', $cheque->user_id)->first();
                $username = explode('.', $user->name);
                if(count($username) == 2)
                {
                    $full_name = ucfirst($username[0]).' '.ucfirst($username[1]);
                }
                else
                {
                  $full_name = $user->name;
                }

                if($request->status=="2")
                {
                    $db_action = ChequeBook::where('id',$id)->update([
                        'status_id'=>'2',
                        'initiator_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Cheque book request has been initiated";
                        $log = new Helper();
                        $log->auditTrail("Cheque Book Request Initiated","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                        return redirect()->back()->with(['notification' => "Cheque request has been initiated", 'color' => "success"]);
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Cheque request initiation failed", 'color' => "danger"]);
                    }
                }

                //Success
                if($request->status=="4")
                {
                    $db_action = ChequeBook::where('id',$id)->update([
                        'status_id'=>'4',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Cheque book request successful";
                        $log = new Helper();
                        $log->auditTrail("Cheque Book Request Successful","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                //cheque success mail to customer
                /*
                 $body = "Dear ".$full_name.", your cheque with transaction ID: ".$cheque->transactionId.' has been successfully processed.';
                $recipient = $user->email;
                $time = 10;
                Queue::later($time, new BankRequests($body,$recipient)); 
                         */
                        return redirect()->back()->with(['notification' => "Cheque request successful", 'color' => "success"]);
                   
                    }
                    else{
                        return redirect()->back()->with(['notification' => "Operation failed", 'color' => "danger"]);
                    }
                }

                //Failed
                if($request->status=="6")
                {
                    $db_action = ChequeBook::where('id',$id)->update([
                        'status_id'=>'6',
                        'approver_id'=>Auth::user()->getAuthIdentifier()
                    ]);

                    if($db_action==true)
                    {
                        $notification="Cheque book request failed";
                        $log = new Helper();
                        $log->auditTrail("Cheque Book Request Failed","IB",$notification,'ib/cheque/request',Auth::user()->getAuthIdentifier());
                        //cheque fail mail to customer
                 /*
                 $body = "Dear ".$full_name.", your cheque with transaction ID: ".$cheque->transactionId.' has failed please visit the near branch for help.';
                $recipient = $user->email;
                $time = 10;
                Queue::later($time, new BankRequests($body,$recipient)); 
                    */
                    return redirect()->back()->with(['notification' => "Cheque request failed", 'color' => "success"]);

                    }
                    else{
                        return redirect()->back()->with(['notification' => "Operation failed", 'color' => "danger"]);
                    }
                }



              break;
            case "ib_bank_settings":

                if($request->submit_value=="approve")
                {


                    $db_action = IbBank::where('id',$request->id)->update(
                        [
                            'approver_id'=>Auth::user()->getAuthIdentifier(),
                            'isWaitingApproval'=>'0'
                        ]
                    );

                    if($db_action==true)
                    {
                        $notification="Changes approved";
                        $color="success";
                        $log = new Helper();
                        $log->auditTrail("Bank Changes Approved","IB",$notification,'ib/view_bank',Auth::user()->getAuthIdentifier());
                        /*
                        $new_details = IbBank::where('id',$request->id)->get()[0];
                        $request['user_id']=Auth::user()->getAuthIdentifier();
                        $request['module']="IB";
                        $request['action']="Approve Bank Changes";
                        $request['action_time']=now();
                        $request['reason']="NULL";
                        $request['old_details']="NULL";
                        $request['new_details']=$new_details;
                        $log = new Helper();
                        return $log->auditTrack($request,$notification,$color);
                        */
                        return redirect('ib/view_bank')->with('notification','Success')->with('color','success');

                    }
                    else{
                        return redirect('ib/view_bank')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {

                    $db_action = IbBank::where('id',$request->id)->update(
                        [
                            'approver_id'=>Auth::user()->getAuthIdentifier(),
                            'isWaitingApproval'=>'2'
                        ]
                    );


                    if($db_action==true)
                    {

                        $notification="Changes rejected";
                        $color="success";
                        $log = new Helper();
                        $log->auditTrail("Bank Changes Rejected","IB",$notification,'ib/view_bank',Auth::user()->getAuthIdentifier());
                        /*
                        $new_details = IbBank::where('id',$request->id)->get()[0];
                        $request['user_id']=Auth::user()->getAuthIdentifier();
                        $request['module']="IB";
                        $request['action']="Reject Banks Changes";
                        $request['action_time']=now();
                        $request['reason']="NULL";
                        $request['old_details']="NULL";
                        $request['new_details']=$new_details;
                        $log = new Helper();
                        return $log->auditTrack($request,$notification,$color);
                        */
                        return redirect('ib/view_bank')->with('notification','Success')->with('color','success');

                    }
                    else{
                        return redirect('ib/view_bank')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;
            case "ib_tv_settings":


                if($request->submit_value=="approve")
                {

                    $db_action = IbTv::where('id',$request->id)->update(
                        [
                            'approver_id'=>Auth::user()->id,
                            'isWaitingApproval'=>'0'

                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("Tv Changes Approved","IB",$notification,'ib/view_tv',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_tv')->with('notification','Success')->with('color','success');

                    }
                    else{

                        return redirect('ib/view_tv')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {

                    $db_action = IbTv::where('id',$request->id)->update(
                        [
                            'approver_id'=>$request->approver_id,
                            'isWaitingApproval'=>'2'
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Denied";
                        $log = new Helper();
                        $log->auditTrail("Tv Changes Rejected","IB",$notification,'ib/view_tv',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_tv')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/view_tv')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;
           case "ib_class_settings":


                if($request->submit_value=="approve")
                {

                    $db_action = IbClass::where('id',$request->id)->update(
                        [
                            'approver_id'=>Auth::user()->id,
                            'isWaitingApproval'=>'0'

                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("Class Changes Approved","IB",$notification,'ib/view_class',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_class')->with('notification','Success')->with('color','success');

                    }
                    else{

                        return redirect('ib/view_class')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {

                    $db_action = IbClass::where('id',$request->id)->update(
                        [
                            'approver_id'=>$request->approver_id,
                            'isWaitingApproval'=>'2'
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Denied";
                        $log = new Helper();
                        $log->auditTrail("Class Changes Rejected","IB",$notification,'ib/view_class',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_class')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/view_class')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;

            case "ib_mno_settings":

                if($request->submit_value=="approve")
                {

                    $db_action = IbMno::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'0',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("MNO Changes Approved","IB",$notification,'ib/view_mno',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_mno')->with('notification','Changes approved')->with('color','success');
                    }
                    else{
                        return redirect('ib/view_mno')->with('notification','No change made')->with('color','danger');
                    }
                }

                if($request->submit_value=="reject")
                {

                    $db_action = IbMno::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'2',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Denied";
                        $log = new Helper();
                        $log->auditTrail("Tv Changes Rejected","IB",$notification,'ib/view_mno',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_mno')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/view_mno')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;
            case "ib_loan_types_settings":

                if($request->submit_value=="approve")
                {

                    $db_action = LoanType::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'0',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("Loan Type Changes Approved","IB",$notification,'ib/view_loan_type',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_loan_type')->with('notification','Changes approved')->with('color','success');
                    }
                    else{
                        return redirect('ib/view_loan_type')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {

                    $db_action = LoanType::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'2',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Rejected";
                        $log = new Helper();
                        $log->auditTrail("Loan Type Changes Rejected","IB",$notification,'ib/view_loan_type',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_loan_type')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/view_loan_type')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;

            case "ib_accounts_settings":
                  
                    if($request->submit_value=="approve")
                {

                    $db_action = IbAccount::where('id',$request->id)->update(
                        [
                            'approver_id'=>Auth::user()->id,
                            'isWaitingApproval'=>'0'

                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("Account Changes Approved","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());
                        
                        return redirect('ib/accounts/index')->with('notification','Success')->with('color','success');

                    }
                    else{

                        return redirect('ib/accounts/index')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {

                    $db_action = IbAccount::where('id',$request->id)->update(
                        [
                            'approver_id'=>$request->approver_id,
                            'isWaitingApproval'=>'2'
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Denied";
                        $log = new Helper();
                        $log->auditTrail("Account Changes Rejected","IB",$notification,'ib/accounts/index',Auth::user()->getAuthIdentifier());
                        return redirect('ib/accounts/index')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/accounts/index')->with('notification','No change made')->with('color','danger');
                    }

                }

               break;

            case "ib_branch_settings":


                if($request->submit_value=="approve")
                {

                    $db_action = IbBranch::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'0',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("Branch Changes Approved","IB",$notification,'ib/view_branch',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_branch')->with('notification','Changes approved')->with('color','success');
                    }
                    else{
                        return redirect('ib/view_branch')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {


                    $db_action = IbBranch::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'2',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Rejected";
                        $log = new Helper();
                        $log->auditTrail("Branch Changes Approved","IB",$notification,'ib/view_branch',Auth::user()->getAuthIdentifier());
                        return redirect('ib/view_branch')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('ib/view_branch')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;
            case "fdr":
                //Determine if approved or rejected


                if($request->submit_value=='approved')
                {
                    try {
                        $db_action = Fdr::where('id', $request->id)
                            ->update(
                                [
                                    'approver_id' => $request->approver_id,
                                    'isWaitingApproval' => '0'
                                ]
                            );

                        if ($db_action == true) {
                            $notification = "Fdr Approved";
                            $log = new Helper();
                            $log->auditTrail("Fdr Changes Approved","IB",$notification,'ib/fdr/index',Auth::user()->getAuthIdentifier());
                            return redirect()->back()->with(['notification' => "Fdr was successfully added", 'color' => "success"]);
                        }
                    }catch (\Exception $e)
                    {
                        return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => "danger"]);
                    }

                }


                if($request->submit_value=='rejected'){
                    try {

                        $db_action = Fdr::where('id', $request->id)
                            ->update(
                                [
                                    'approver_id' => $request->approver_id,
                                    'isWaitingApproval' => "2"
                                ]
                            );

                        if ($db_action == true) {
                            $notification = "Fdr Rejected";
                            $log = new Helper();
                            $log->auditTrail("Fdr Changes Approved","IB",$notification,'ib/fdr/index',Auth::user()->getAuthIdentifier());
                            return redirect()->back()->with(['notification' => "Fdr was rejected", 'color' => "danger"]);
                        }
                    }catch (\Exception $e)
                    {
                        return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => "danger"]);
                    }
                }



                break;
            case "ib_users":

                //Determine if approved or rejected
                if($request->submit_value=='approved')
                {
                    //autogenerate the password that is to be sent to the user.
                    $password  = mt_rand(12345678, 99999999);
                    $db_action = IbUser::where('id',$request->id)
                        ->update(
                            [
				'password'=>$password,
                                'approver_id'=>$request->approver_id,
                                'isWaitingApproval'=>'0'
                            ]
                        );
                     $user = IbUser::where('id',$request->id)->first();
             
                    if($user->otp_option == "SMS")
{
       
        //send the sms 
        $msg  = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone.";
        $sms = new  SMSController();
        $mobile = "255" . ltrim($user->mobile_phone, "0");
        $sms->send($mobile, $msg);
}
 if($user->otp_option == "EMAIL")
{
        $body = "Dear ".$user->name.", your password to access Internet Banking is ".$password;
        $recipient=$user->email;
         $time = 10;
        Queue::later($time, new PasswordMail($body,$recipient));

}
if($user->otp_option == "BOTH")
{
  
        //send the sms 
        $msg  = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone.";
        $sms = new  SMSController();
        $mobile = "255" . ltrim($user->mobile_phone, "0");
        $sms->send($mobile, $msg);

        $body = "Dear ".$user->name.", your password to access Internet Banking is ".$password;
        $recipient=$user->email;
        $time = 10;
        Queue::later($time, new PasswordMail($body,$recipient));


}
              
                }else{
                    $db_action = IbUser::where('id',$request->id)
                        ->update(
                            [
                                'approver_id'=>$request->approver_id,
                                'isWaitingApproval'=>'2'
                            ]
                        );
            
                }


                //Return response
                if($db_action==true && $request->submit_value=="approved")
                {
                    $notification = "Changes Approved";
                    $log = new Helper();
                    $log->auditTrail("User Changes Approved","IB",$notification,'ib/user',Auth::user()->getAuthIdentifier());
                    return redirect()->back()->with(['notification' => "User was approved successfully", 'color' => "success"]);
                }
                else{
                    $notification = "Changes Rejected";
                    $log = new Helper();
                    $log->auditTrail("User Changes Rejected","IB",$notification,'ib/user',Auth::user()->getAuthIdentifier());
                    return redirect()->back()->with(['notification' => "User was rejected", 'color' => "danger"]);
                }

                break;
            case "ib_institutions":

                //Determine if approved or rejected
                if($request->submit_value=='approved')
                {
                    $db_action = IbInstitution::where('id',$request->id)
                        ->update(
                            [
                                'approver_id'=>$request->approver_id,
                                'isWaitingApproval'=>'0'
                            ]
                        );
                }else{
                    $db_action = IbInstitution::where('id',$request->id)
                        ->update(
                            [
                                'approver_id'=>$request->approver_id,
                                'isWaitingApproval'=>'2'
                            ]
                        );
                }
                //Return response
                if($db_action==true && $request->submit_value=="approved")
                {
                    $notification = "Changes Approved";
                    $log = new Helper();
                    $log->auditTrail("Institution Changes Approved","IB",$notification,'ib/institutions/index',Auth::user()->getAuthIdentifier());
                    return redirect()->back()->with(['notification' => "Institution was approved successfully", 'color' => "success"]);
                }
                else{
                    $notification = "Changes Rejected";
                    $log = new Helper();
                    $log->auditTrail("Institution Changes Rejected","IB",$notification,'ib/institutions/index',Auth::user()->getAuthIdentifier());
                    return redirect()->back()->with(['notification' => "Institution was rejected", 'color' => "danger"]);
                }

                break;
        }
    }


    public function message($body,$email)
    {
        $time = 10;
        Queue::later($time, new PasswordMail($body,$email));
    }


    public function resetMarkerChecker(Model $model, $id)
    {
        $db_action = $model::where('id',$id)
            ->update([
                'approver_id'=>'0',
                'initiator_id'=>'0'
            ]);
    }

    public function resetRejects()
    {
        $db_action = ChequeBook::where('status_id','4')->orWhere('status_id','7')
            ->update([
                'status_id'=>'1'
            ]);

    }


}
