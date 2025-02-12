<?php

namespace App\Http\Controllers\agency;

use App\AbBranch;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\IbInstitution;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbActionRequestController extends Controller
{
    private $dateTime;
    public $r;
    public const RETURN_URL = 'agency/view_branch';

    public function abRequestHandler(Request $request)
    {
        $request->validate([
            'request_type' => 'required'
        ]);

        $type = $request->request_type;
        $this->dateTime = Carbon::now()->setTimezone('Africa/Nairobi');
        //For the requests use only two steps pending-1 approved-4 rejected-5
        try {
            switch ($type) {
                case "ab_branch_settings":
                    if ($request->submit_value === "approve") {
                        $db_action = AbBranch::where('id', $request->id)->update(
                            [
                                'isWaitingApproval' => '0',
                                'approver_id' => Auth::user()->getAuthIdentifier()
                            ]
                        );

                        if ($db_action) {
                            $notification = "Changes Approved";
                            $log = new Helper();
                            return $log->auditTrail("Branch Changes Approved", "AB", $notification, self::RETURN_URL, Auth::user()->getAuthIdentifier());
                        }

                        return redirect(self::RETURN_URL)->with('notification', 'No change made')->with('color', 'danger');
                    }

                    if ($request->submit_value === "reject") {
                        $db_action = AbBranch::where('id', $request->id)->update(
                            [
                                'isWaitingApproval' => '2',
                                'approver_id' => Auth::user()->getAuthIdentifier()
                            ]
                        );

                        if ($db_action) {
                            $notification = "Changes Rejected";
                            $log = new Helper();
                            return $log->auditTrail("Branch Changes Approved", "AB", $notification, self::RETURN_URL, Auth::user()->getAuthIdentifier());
                        }
                        return redirect('agency/view_branch')->with('notification', 'No change made')->with('color', 'danger');
                    }

                    break;
                case "ab_institutions":
                    //Determine if approved or rejected
                    if ($request->submit_value === 'approved') {
                        $db_action = IbInstitution::where('id', $request->id)
                            ->update(
                                [
                                    'approver_id' => $request->approver_id,
                                    'isWaitingApproval' => '0'
                                ]
                            );
                    } else {
                        $db_action = IbInstitution::where('id', $request->id)
                            ->update(
                                [
                                    'approver_id' => $request->approver_id,
                                    'isWaitingApproval' => '2'
                                ]
                            );
                    }
                    //Return response
                    if ($db_action && $request->submit_value === "approved") {
                        $notification = "Changes Approved";
                        $log = new Helper();
                        $log->auditTrail("GEPG Institution Changes Approved", "AB", $notification, 'agency/user', Auth::user()->getAuthIdentifier());
                    } else {
                        $notification = "Changes Rejected";
                        $log = new Helper();
                        $log->auditTrail("GEPG Institution Changes Rejected", "AB", $notification, 'agency/user', Auth::user()->getAuthIdentifier());
                    }
                    break;
                default:
                    return back()->with(['notification' => 'No change made', 'color' => 'danger']);
            }
        } catch (\Exception $e) {
            Log::error("Ab Action Request Exception: ", ['message' => $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile()]);
            return redirect()->back()->with(['notification' => 'Something went wrong, Try again later!', 'color' => 'danger']);
        }
    }
}
