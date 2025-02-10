<?php

namespace App\Http\Controllers\agency;

use App\AuditLogs;
use App\Helper\Helper;
use App\AbBank;
use App\AbBranch;
use App\AbGEPGInstitution;
use App\Http\Controllers\Controller;
use App\AbUser;
use App\Jobs\BankRequests;
use App\Mail\PasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class AbActionRequestController extends Controller
{
    private $date_time;
    public $r;

    public function abRequestHandler(Request $request)
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
            
           
            case "ab_branch_settings":


                if($request->submit_value=="approve")
                {

                    $db_action = AbBranch::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'0',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );

                    if($db_action==true)
                    {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        return $log->auditTrail("Branch Changes Approved","AB",$notification,'agency/view_branch',Auth::user()->getAuthIdentifier());
                        //return redirect('agency/view_branch')->with('notification','Changes approved')->with('color','success');
                    }
                    else{
                        return redirect('agency/view_branch')->with('notification','No change made')->with('color','danger');
                    }
                }


                if($request->submit_value=="reject")
                {


                    $db_action = AbBranch::where('id',$request->id)->update(
                        [
                            'isWaitingApproval'=>'2',
                            'approver_id'=>Auth::user()->getAuthIdentifier()
                        ]
                    );


                    if($db_action==true)
                    {
                        $notification = "Changes Rejected";
                        $log = new Helper();
                        return $log->auditTrail("Branch Changes Approved","AB",$notification,'agency/view_branch',Auth::user()->getAuthIdentifier());
                        //return redirect('agency/view_branch')->with('notification','Changes rejected')->with('color','danger');
                    }
                    else{
                        return redirect('agency/view_branch')->with('notification','No change made')->with('color','danger');
                    }

                }

                break;
           
            
            case "ab_institutions":

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
                    $log->auditTrail("GEPG Institution Changes Approved","AB",$notification,'agency/user',Auth::user()->getAuthIdentifier());
                    //return redirect()->back()->with(['notification' => "GEPG Institution was approved successfully", 'color' => "success"]);
                }
                else{
                    $notification = "Changes Rejected";
                    $log = new Helper();
                    $log->auditTrail("GEPG Institution Changes Rejected","AB",$notification,'agency/user',Auth::user()->getAuthIdentifier());
                    //return redirect()->back()->with(['notification' => "Institution was rejected", 'color' => "danger"]);
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
