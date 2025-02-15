<?php

namespace App\Http\Controllers\agency;

use App\AbAccountProduct;
use App\AbBank;
use App\AbBiller;
use App\AbBillerGroup;
use App\AbBranch;
use App\AbGEPGInstitution;
use App\AbSadakaDigital;
use App\AbStatus;
use App\CommissionDistribution;
use App\Http\Controllers\Controller;
use App\Imports\BanksImport;
use App\TblABOTPPolicy;
use App\TblABPINPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SettingsController extends Controller
{
    public function createBiller()
    {
        $billers = AbBiller::with(['initiator:id,name', 'approver:id,name'])
            ->select('id', 'biller_short_name', 'biller_description', 'biller_status', 'initiator_id', 'approver_id', 'isWaitingApproval')
            ->latest()
            ->get();
        $billergroups = AbBillerGroup::all();
        $statuses = AbStatus::all();

        return view('agency.settings.biller', compact('billers', 'billergroups', 'statuses'));
    }

    public function storeBiller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'biller_short_name' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
            'biller_group' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
            'utility_code' => 'required|numeric',
            'biller_institution_name' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => "danger"]);
        }

        $uid = Auth::user()->id;

        try {
            $db_action = AbBiller::create([
                'biller_short_name' => $request->biller_short_name,
                'biller_description' => $request->biller_description,
                'biller_status' => 0,
                'biller_group' => $request->biller_group,
                'isWaitingApproval' => 1,
                'utility_code' => $request->utility_code,
                'biller_institution_name' => $request->biller_institution_name,
                'initiator_id' => $uid
            ]);

            if ($db_action) {
                $notification = "Biller added successfully";
                $color = "success";

            } else {
                $notification = "Failed to add biller!";
                $color = "danger";
            }
            $this->auditLog($uid, 'Add Biller', "Agency Settings", $notification, $request->ip());
            return redirect('agency/view_biller')->with('notification', $notification)->with('color', $color);
        } catch (\Exception $e) {
            $notification = 'Something went wrong, Try again later!';
            $color = 'danger';
            $this->auditLog($uid, 'Add Biller', "Agency Settings", $e->getMessage(), $request->ip());
            Log::error("Add Billers Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect('agency/view_biller')->with('notification', $notification)->with('color', $color);
        }
    }

    public function approveBiller(Request $request, $id)
    {
        $biller = AbBiller::findOrFail($id);
        $billergroups = AbBillerGroup::all();
        $statuses = AbStatus::all();
        return view('agency.settings.approve_biller', compact('biller', 'billergroups', 'statuses'));
    }

    public function approveBillerAct(Request $request, $id)
    {
        $user_id = Auth::id();
        $color = 'danger';
        $notification = 'Failed to approve/reject biller';

        try {
            // Determine the action based on the request
            if ($request->reject === 'reject') {
                $action = 'Reject Biller';
                $notification = 'Biller has been rejected successfully';
                $dbAction = AbBiller::where('biller_id', $id)
                    ->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
            } elseif ($request->approve === 'approve') {
                $action = 'Approve Biller';
                $notification = 'Biller has been approved successfully';
                $dbAction = AbBiller::where('biller_id', $id)
                    ->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
            } else {
                $dbAction = false;
            }

            // Handle success or failure
            if ($dbAction) {
                $color = 'success';
                $this->auditLog($user_id, $action, "Agency Settings", $notification, $request->ip());
            } else {
                $notification = 'Failed to approve/reject biller';
            }

            return redirect()->route('agency.view_biller')->with([
                'notification' => $notification,
                'color' => $color,
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            $notification = "Something went wrong, Try again later!";
            $color = "danger";
            $this->auditLog($user_id, 'Approve Biller Exception', "Agency Settings", $e->getMessage(), $request->ip());
            Log::error("Approve Biller Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

            return redirect()->route('agency.view_biller')->with([
                'notification' => $notification,
                'color' => $color,
            ]);
        }
    }


    public function editBiller(Request $request)
    {
        $biller = AbBiller::where('biller_id', $request->id)->get()[0];
        $billergroups = AbBillerGroup::all();
        $statuses = AbStatus::all();
        return view('agency.settings.edit_biller', compact('biller', 'billergroups', 'statuses'));
    }

    public function updateBiller(Request $request)
    {
        $uid = Auth::user()->id;
        $request->validate([
            'biller_short_name' => 'required',
            'biller_group' => 'required',
            'utility_code' => 'required',
        ]);

        try {
            $db_action = AbBiller::where('biller_id', $request->id)->update([
                'biller_short_name' => $request->biller_short_name,
                'biller_description' => $request->biller_description,
                'biller_status' => 2,
                'biller_group' => $request->biller_group,
                'utility_code' => $request->utility_code,
                'biller_institution_name' => $request->biller_institution_name,
                'isWaitingApproval' => 1,
                'approver_id' => 0
            ]);

            if ($db_action) {
                $notification = "Biller updated successfully";
                $color = "success";
            } else {
                $notification = "Biller was not updated!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            $notification = "Something went wrong, Try again later!";
            $color = "danger";
            Log::error("Update Biller Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }

        return redirect('agency/view_biller')->with('notification', $notification)->with('color', $color);
    }

    public function createBank()
    {
        $banks = AbBank::select('bank_id', 'bank_name', 'bank_code', 'bank_status', 'initiator_id', 'approver_id', 'isWaitingApproval', 'isDeleted', 'deletedBy_id')
            ->with(['initiator:id,name', 'approver:id,name'])
            ->orderBy('bank_id', 'DESC')
            ->get();

        $statuses = AbStatus::all();

        return view('agency.settings.bank', compact('banks', 'statuses'));
    }

    public function changeBankStatus($id)
    {
        try {
            $id = decrypt($id);

            $bank = AbBank::where('bank_id', $id)->first();
            $color = 'danger';
            if ($bank && $bank->bank_status == 1) {
                $action = $bank->update(['bank_status' => 0]);
                $notification = 'Bank deactivated successfully';
            } else {
                $action = $bank->update(['bank_status' => 1]);
                $notification = 'Bank activated successfully';
            }

            if ($action) {
                $color = 'success';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }
            return redirect()->back()->with(['notification' => "Failed to activate/deactivate bank", 'color' => $color]);
        } catch (\Exception $e) {
            Log::error("Change Bank Status Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "Something went wrong!", 'color' => "danger"]);
        }
    }

    public function changeBillerStatus($id)
    {
        try {
            $id = decrypt($id);

            $biller = AbBiller::where('id', $id)->first();
            $color = 'danger';
            if ($biller && $biller->biller_status == 1) {
                $action = $biller->update(['biller_status' => 0]);
                $notification = 'Biller deactivated successfully';
            } else {
                $action = $biller->update(['biller_status' => 1]);
                $notification = 'Biller activated successfully';
            }

            if ($action) {
                $color = 'success';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }
            return redirect()->back()->with(['notification' => "Failed to activate/deactivate biller", 'color' =>
                $color]);
        } catch (\Exception $e) {
            Log::error("Change Biller Status Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "Something went wrong!", 'color' => "danger"]);
        }
    }

    public function downloadtemplate()
    {
        try {
            $file_name = 'bank_template.xlsx';
            $path = storage_path() . '/' . 'template/' . $file_name;

            if (file_exists($path)) {
                return Response::download($path);
            } else {
                return redirect()->back()->with(['notification' => "Template doesn't exist", 'color' => "danger"]);
            }
        } catch (\Exception $e) {
            Log::error("Download Template Exception: ", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "Something went wrong!", 'color' => "danger"]);
        }
    }

    public function storeBankBatch(Request $request)
    {
        $validator = Validator::make(
            [
                'file' => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
            ],
            [
                'file' => 'required|file|mimes:xlsx,xls',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => "danger"]);
        }

        try {
            Excel::import(new BanksImport(), $request->file('file'));
            return redirect()->back()->with(['notification' => "File Imported Successfully", 'color' => "success"]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failure = $e->failures()[0] ?? null;

            if ($failure) {
                $message = "The value of {$failure->attribute()} on row {$failure->row()} is not valid";
            } else {
                $message = "Validation error in the uploaded file.";
            }

            return redirect()->back()->with(['notification' => $message, 'color' => "danger"]);
        } catch (\Exception $e) {
            Log::error("Store Bank Batch Exception", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "An error occurred while importing the file. Please try again.", 'color' => "danger"]);
        }
    }

    public function storeBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
            'bank_code' => 'required|regex:/^[a-zA-Z0-9]+$/|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => "danger"]);
        }

        try {
            $bankExists = AbBank::where('bank_name', trim($request->bank_name))
                ->orWhere('bank_code', trim($request->bank_code))
                ->exists();

            if ($bankExists) {
                return redirect()->back()->with(['notification' => 'Bank Name or Code already exists!', 'color' => 'danger']);
            }

            $db_action = AbBank::create([
                'bank_name' => $request->bank_name,
                'bank_code' => $request->bank_code,
                'bank_status' => 1,
                'initiator_id' => Auth::user()->id,
                'isWaitingApproval' => 1,
                'isDeleted' => 0,
                'approver_id' => 0
            ]);

            if ($db_action) {
                $notification = "Bank added successfully";
                $color = "success";
                $this->auditLog(Auth::user()->id, 'Add Bank', 'Agency Settings', $notification, $request->ip());
            } else {
                $notification = "Bank was not added!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error('Settings-Add-Bank-Exception: ', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            $notification = "Something went wrong, Try again later!";
            $color = "danger";
            $this->auditLog(Auth::user()->id, 'Exception Add Bank', 'Agency Settings', $notification, $request->ip());
        }

        return redirect('agency/view_bank')->with('notification', $notification)->with('color', $color);
    }

    //added by Evance Nganyaga
    public function approveBank(Request $request, $id)
    {
        $bank = AbBank::findOrFail($id);
        return view('agency.settings.approve_bank', compact('bank'));
    }

    public function approveBankAct(Request $request, $id)
    {
        $user_id = Auth::id();

        try {
            $bank = AbBank::findOrFail($id);

            if ($request->has('reject')) {
                $bank->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
                return redirect()->route('agency.view_bank')->with([
                    'notification' => 'Bank has been rejected successfully',
                    'color' => 'success'
                ]);
            }

            if ($request->has('approve')) {
                $bank->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
                return redirect()->route('agency.view_bank')->with([
                    'notification' => 'Bank has been approved successfully',
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_bank')->with([
                'notification' => 'Invalid action provided!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error("Approve Bank Act Exception: ", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_bank')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }


    public function editBank($id)
    {
        $bank = AbBank::where('bank_id', $id)->get()[0];
        $statuses = AbStatus::all();
        return view('agency.settings.edit_bank', compact('bank', 'statuses'));
    }

    public function updateBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|max:30',
            'bank_code' => 'required|max:20'
        ]);

        try {
            $db_action = AbBank::where('bank_id', $request->id)->update([
                'bank_name' => $request->bank_name,
                'bank_code' => $request->bank_code,
                'isWaitingApproval' => 1,
                'approver_id' => 0,
            ]);

            if ($db_action) {
                $notification = "Bank updated successfully";
                $color = "success";
            } else {
                $notification = "Bank was not updated!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error('Update Bank Exception: ', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            $notification = "An error occurred while updating the bank!";
            $color = "danger";
        }

        return redirect('agency/view_bank')->with('notification', $notification)->with('color', $color);
    }

    public function deleteBankApproval(Request $request, $id)
    {
        try {
            $bank = AbBank::findOrFail($id);
            $statuses = AbStatus::all();
            return view('agency.settings.delete_bank_approval', compact('bank', 'statuses'));
        } catch (\Exception $e) {
            Log::error("Delete Bank Approval Exception: ", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with(['notification' => "Something went wrong!", 'color' => "danger"]);
        }
    }

    public function deleteBank($id)
    {
        $user_id = Auth::id();
        try {
            AbBank::where(['bank_id' => $id])->update([
                'isWaitingApproval' => 1,
                'approver_id' => 0,
                'deletedBy_id' => $user_id,
                'isDeleted' => 1
            ]);
            return redirect()->route('agency.view_bank')->with([
                'notification' => 'Bank delete request sent for approval',
                'color' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Bank Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_bank')->with([
                'notification' => 'An error occurred while sending the delete request!',
                'color' => 'danger'
            ]);
        }
    }

    public function deleteBankActApproval(Request $request, $id)
    {
        $user_id = Auth::id();
        try {
            if ($request->reject === 'reject') {
                AbBank::where(['bank_id' => $id])->update([
                    'isWaitingApproval' => 0,
                    'approver_id' => $user_id,
                    'isDeleted' => 0
                ]);
                return redirect()->route('agency.view_bank')->with([
                    'notification' => 'Bank deleting has been rejected successfully',
                    'color' => 'success'
                ]);
            }

            if ($request->approve === 'approve') {
                AbBank::where(['bank_id' => $id])->delete();
                return redirect()->route('agency.view_bank')->with([
                    'notification' => 'Bank deleting has been approved successfully',
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_bank')->with([
                'notification' => 'Invalid action!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Bank Approval Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_bank')->with([
                'notification' => 'Something went wrong, please try again.',
                'color' => 'danger'
            ]);
        }
    }


    public function createBillerGroup()
    {
        $biller_groups = AbBillerGroup::orderBy('biller_group_id', 'DESC')->get();
        return view('agency.settings.biller_group', compact('biller_groups'));
    }

    public function storeBillerGroup(Request $request)
    {
        $uid = Auth::user()->id;
        $request->validate([
            'biller_group_name' => 'required',
            'biller_group_description' => 'required'
        ]);

        if (strlen($request->biller_group_description) > 100) {
            return redirect('agency/view_biller_group')->with('notification', 'Drescription is too long, please make it short.')->with('color', 'info');
        }

        try {
            $db_action = AbBillerGroup::insert([
                'biller_group_name' => $request->biller_group_name,
                'biller_group_description' => $request->biller_group_description,
                'isWaitingApproval' => 1,
                'approver_id' => 1,
                'initiator_id' => $uid
            ]);

            if ($db_action) {
                $notification = "Biller group added successfully";
                $color = "success";
            } else {
                $notification = "Biller group was not added!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error('Store Biller Group Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $notification = "An error occurred while adding the biller group!";
            $color = "danger";
        }

        return redirect('agency/view_biller_group')->with('notification', $notification)->with('color', $color);
    }

    public function approveBillerGroup(Request $request, $id)
    {
        try {
            $biller_group = AbBillerGroup::findOrFail($id);
            return view('agency.settings.approve_biller_group', compact('biller_group'));
        } catch (\Exception $e) {
            Log::error('Approve Biller Group Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_biller_group')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveBillerGroupAct(Request $request, $id)
    {
        $user_id = Auth::id();
        try {
            $action = $request->reject === 'reject' ? [
                'isWaitingApproval' => 2,
                'approver_id' => $user_id,
                'notification' => 'Biller Group has been rejected successfully'
            ] : ($request->approve === 'approve' ? [
                'isWaitingApproval' => 0,
                'approver_id' => $user_id,
                'notification' => 'Biller Group has been approved successfully'
            ] : null);

            if ($action) {
                AbBillerGroup::where('biller_group_id', $id)->update([
                    'isWaitingApproval' => $action['isWaitingApproval'],
                    'approver_id' => $action['approver_id']
                ]);
                return redirect()->route('agency.view_biller_group')->with([
                    'notification' => $action['notification'],
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_biller_group')->with([
                'notification' => 'Invalid action!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve Biller Group Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_biller_group')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function editBillerGroup($id)
    {
        $biller_group = AbBillerGroup::where('biller_group_id', $id)->first();
        return view('agency.settings.edit_biller_group', compact('biller_group'));
    }

    public function updateBillerGroup(Request $request)
    {
        $request->validate([
            'biller_group_name' => 'required',
            'biller_group_description' => 'required',
        ]);

        try {
            $updateData = [
                'biller_group_name' => $request->biller_group_name,
                'biller_group_description' => $request->biller_group_description,
                'isWaitingApproval' => 1,
                'approver_id' => 0,
            ];

            $db_action = AbBillerGroup::where('biller_group_id', $request->biller_group_id)->update($updateData);

            if ($db_action) {
                return redirect('agency/view_biller_group')->with([
                    'notification' => 'Biller group updated successfully',
                    'color' => 'success',
                ]);
            } else {
                return redirect('agency/view_biller_group')->with([
                    'notification' => 'Biller group was not updated!',
                    'color' => 'danger',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Update Biller Group Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('agency/view_biller_group')->with([
                'notification' => 'An error occurred while updating the biller group!',
                'color' => 'danger',
            ]);
        }
    }

    public function approveCommission(Request $request, $id)
    {
        $commission = CommissionDistribution::where('commision_id', $id)->first();
        return view('agency.commissions.approve_commission', compact('commission'));
    }

    public function approveCommissionAct(Request $request, $id)
    {
        $user_id = Auth::id();

        try {
            $action = $request->reject === 'reject' ? [
                'isWaitingApproval' => 2,
                'approver_id' => $user_id,
                'notification' => 'Commission has been rejected successfully'
            ] : ($request->approve === 'approve' ? [
                'isWaitingApproval' => 0,
                'approver_id' => $user_id,
                'notification' => 'Commission has been approved successfully'
            ] : null);

            if ($action) {
                CommissionDistribution::where(['commision_id' => $id])->update([
                    'isWaitingApproval' => $action['isWaitingApproval'],
                    'approver_id' => $action['approver_id']
                ]);

                return redirect('agency/commissions')->with([
                    'notification' => $action['notification'],
                    'color' => 'success'
                ]);
            }

            return redirect('agency/commissions')->with([
                'notification' => 'Invalid action!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve Commission Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('agency/commissions')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function indexSecurityPolicies()
    {
        $pinpolicy = TblABPINPolicy::all();
        $otppolicy = TblABOTPPolicy::all();
        return view('agency.settings.securitypolicy', compact('pinpolicy', 'otppolicy'));
    }

    public function updateOTPSecurityPolicies(Request $request)
    {
        $uid = Auth::user()->id;
        $request->validate([
            'id' => 'required',
            'min_length' => 'required',
            'max_length' => 'required',
            'max_attempts' => 'required'
        ]);

        try {
            $db_action = TblABOTPPolicy::where('id', $request->id)->update([
                'min_length' => $request->min_length,
                'max_length' => $request->max_length,
                'max_attempts' => $request->max_attempts,
                'initiator_id' => $uid,
                'approver_id' => 0,
                'isWaitingApproval' => 1
            ]);

            if ($db_action) {
                $notification = "OTP Policy updated successfully";
                $color = "success";
            } else {
                $notification = "OTP Policy was not updated!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error('Update OTP Security Policies Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $notification = "An error occurred while updating the OTP Policy!";
            $color = "danger";
        }

        return redirect()->back()->with('notification', $notification)->with('color', $color);
    }

    public function approvePinPolicy($id)
    {
        $pinpolicy = TblABPINPolicy::findOrFail($id);
        return view('agency.settings.approve_ppolicy', compact('pinpolicy'));
    }

    public function approvePinPolicyAct(Request $request, $id)
    {
        try {
            $status = $request->approve === 'approve' ? 0 : ($request->reject === 'reject' ? 2 : null);

            if ($status !== null) {
                TblABPINPolicy::where(['id' => $id])->update([
                    'isWaitingApproval' => $status,
                    'approver_id' => Auth::id()
                ]);

                $notification = $status === 0 ? 'PIN policy successfully approved' : 'PIN policy successfully rejected';
                return redirect()->route('agency.sPolicy.index')->with([
                    'notification' => $notification,
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.sPolicy.index')->with([
                'notification' => 'Invalid action',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve PIN Policy Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.sPolicy.index')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function updatePINSecurityPolicies(Request $request)
    {
        $uid = Auth::user()->id;
        $request->validate([
            'id' => 'required',
            'min_length' => 'required',
            'max_length' => 'required',
            'max_attempts' => 'required',
            'expiry_period' => 'required'
        ]);

        try {
            $updateData = [
                'min_length' => $request->min_length,
                'max_length' => $request->max_length,
                'expiry_period' => $request->expiry_period,
                'max_attempts' => $request->max_attempts,
                'initiator_id' => $uid,
                'approver_id' => 0,
                'isWaitingApproval' => 1
            ];

            $db_action = TblABPINPolicy::where('id', $request->id)->update($updateData);

            $notification = $db_action ? "PIN Policy updated successfully" : "PIN Policy was not updated!";
            $color = $db_action ? "success" : "danger";
        } catch (\Exception $e) {
            Log::error('Update PIN Security Policy Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $notification = "An error occurred while updating the PIN Policy!";
            $color = "danger";
        }

        return redirect()->back()->with('notification', $notification)->with('color', $color);
    }

    public function approveOtpPolicy($id)
    {
        try {
            $otpPolicy = TblABOTPPolicy::findOrFail($id);
            return view('agency.settings.approve_opolicy', compact('otpPolicy'));
        } catch (\Exception $e) {
            Log::error('Approve OTP Policy Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.sPolicy.index')->with([
                'notification' => 'An error occurred while accessing the OTP policy!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveOtpPolicyAct(Request $request, $id)
    {
        try {
            $status = $request->approve === 'approve' ? 0 : ($request->reject === 'reject' ? 2 : null);

            if ($status !== null) {
                TblABOTPPolicy::where(['id' => $id])->update([
                    'isWaitingApproval' => $status,
                    'approver_id' => Auth::id()
                ]);

                $notification = $status === 0 ? 'OTP policy successfully approved' : 'OTP policy successfully rejected';
                return redirect()->route('agency.sPolicy.index')->with([
                    'notification' => $notification,
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.sPolicy.index')->with([
                'notification' => 'Invalid action',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve OTP Policy Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.sPolicy.index')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function createGEPGInstitution()
    {
        abort(404);
        $gepgInstutions = AbGEPGInstitution::orderBy('institution_id', 'DESC')->get();
        $statuses = AbStatus::all();
        return view('agency.settings.gepginstitution', compact('gepgInstutions', 'statuses'));

    }

    public function storeGEPGInstitution(Request $request)
    {
        $uid = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
            'institution_code' => 'required|regex:/^[a-zA-Z0-9]+$/|max:20',
            'collection_account' => 'required|string|regex:/^[a-zA-Z0-9]+$/|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => "danger"]);
        }

        try {
            $institutionExists = AbGEPGInstitution::where('institution_name', $request->institution_name)
                ->orWhere('institution_code', $request->institution_code)
                ->exists();

            if ($institutionExists) {
                return redirect()->back()->with(['notification' => 'Institution Name or Code already exists!', 'color' => 'danger']);
            }

            $db_action = AbGEPGInstitution::create([
                'institution_name' => $request->institution_name,
                'institution_code' => $request->institution_code,
                'institution_charges' => $request->institution_charges,
                'collection_account' => $request->collection_account,
                'initiator_id' => $uid,
                'isWaitingApproval' => 1
            ]);

            if ($db_action) {
                $notification = "GePG Institution added successfully";
                $color = "success";

            } else {
                $notification = "Failed to add GePG Institution!";
                $color = "danger";
            }
            $this->auditLog(Auth::user()->id, 'Add GePG Institution', 'Agency Settings', $notification, $request->ip());
            return redirect('agency/view_gepg_institution')->with('notification', $notification)->with('color', $color);
        } catch (\Exception $exception) {
            $notification = 'Something went wrong, Try again later!';
            $color = 'danger';
            $this->auditLog(Auth::user()->id, 'Add GePG Institution Exception', 'Agency Settings', $notification, $request->ip());
            Log::error('STORE-GePG-INSTITUTION: ' . json_encode($exception->getMessage()));
            return redirect('agency/view_gepg_institution')->with('notification', $notification)->with('color', $color);
        }
    }

    public function editGEPGInstitution($id)
    {
        $gepInstitution = AbGEPGInstitution::where('institution_id', $id)->first();
        $statuses = AbStatus::all();
        return view('agency.settings.edit_gepinstitution', compact('gepInstitution', 'statuses'));
    }

    public function updateGEPGInstitution(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|max:30',
            'institution_code' => 'required|max:20',
        ]);

        try {
            $db_action = AbGEPGInstitution::where('institution_id', $request->id)->update([
                'institution_name' => $request->institution_name,
                'institution_code' => $request->institution_code,
                'institution_charges' => $request->institution_charges ?? 0,
                'collection_account' => $request->collection_account,
                'isWaitingApproval' => 1,
                'approver_id' => 0,
            ]);

            $notification = $db_action ? "GEPG Institution updated successfully" : "GEPG Institution was not updated!";
            $color = $db_action ? "success" : "danger";
        } catch (\Exception $e) {
            Log::error('Update GEPG Institution Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $notification = "An error occurred while updating the GEPG Institution!";
            $color = "danger";
        }

        return redirect('agency/view_gepg_institution')->with('notification', $notification)->with('color', $color);
    }

    public function approveGEPGInstitution(Request $request, $id)
    {
        try {
            $gepginstitution = AbGEPGInstitution::findOrFail($id);
            return view('agency.settings.approve_gepginstitution', compact('gepginstitution'));
        } catch (\Exception $e) {
            Log::error('Approve GEPG Institution Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('agency/view_gepg_institution')->with([
                'notification' => 'An error occurred while accessing the GEPG institution!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveGEPGInstitutionAct(Request $request, $id)
    {
        try {
            $user_id = Auth::id();
            $statusMapping = [
                'approve' => ['isWaitingApproval' => 0, 'message' => 'GEPG Institution has been approved successfully'],
                'reject' => ['isWaitingApproval' => 2, 'message' => 'GEPG Institution has been rejected successfully']
            ];

            $action = $request->approve ?? $request->reject;

            if (isset($statusMapping[$action])) {
                AbGEPGInstitution::where('institution_id', $id)->update([
                    'isWaitingApproval' => $statusMapping[$action]['isWaitingApproval'],
                    'approver_id' => $user_id
                ]);

                return redirect()->route('agency.view_gepg_institution')->with([
                    'notification' => $statusMapping[$action]['message'],
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_gepg_institution')->with([
                'notification' => 'Invalid action',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve GEPG Institution Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_gepg_institution')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function createSadakaDigital()
    {
        $gepgInstutions = AbSadakaDigital::orderBy('id', 'DESC')->get();

        return view('agency.settings.sadaka_digital', compact('gepgInstutions'));

    }

    public function storeSadakaDigital(Request $request)
    {
        $uid = Auth::user()->id;

        $request->validate([
            'charge' => 'required|max:30',
            'account_number' => 'required|max:20',
        ]);

        try {
            $db_action = AbSadakaDigital::create([
                'charge' => $request->charge,
                'account_number' => $request->account_number,
                'initiator_id' => $uid,
                'isWaitingApproval' => 1
            ]);

            $notification = $db_action ? "Sadaka Digital added successfully" : "Sadaka Digital was not added!";
            $color = $db_action ? "success" : "danger";
        } catch (\Exception $e) {
            Log::error('Store Sadaka Digital Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $notification = "An error occurred while adding Sadaka Digital!";
            $color = "danger";
        }

        return redirect('agency/view_sadaka_digital')->with('notification', $notification)->with('color', $color);
    }

    public function editSadakaDigital($id)
    {
        $sadakadigital = AbSadakaDigital::where('id', $id)->first();

        return view('agency.settings.edit_sadaka_digital', compact('sadakadigital'));
    }

    public function updateSadakaDigital(Request $request)
    {
        $request->validate([
            'charge' => 'required|max:50',
            'account_number' => 'required|max:20',
        ]);

        try {
            $db_action = AbSadakaDigital::where('id', $request->id)->update([
                'charge' => $request->charge,
                'account_number' => $request->account_number,
                'isWaitingApproval' => 1,
                'approver_id' => 0,
            ]);

            $notification = $db_action ? "Sadaka Digital updated successfully" : "Sadaka Digital was not updated!";
            $color = $db_action ? "success" : "danger";
        } catch (\Exception $e) {
            Log::error('Update Sadaka Digital Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $notification = "An error occurred while updating Sadaka Digital!";
            $color = "danger";
        }

        return redirect('agency/view_sadaka_digital')->with('notification', $notification)->with('color', $color);
    }

    public function approveSadakaDigital(Request $request, $id)
    {
        try {
            $gepginstitution = AbSadakaDigital::findOrFail($id);
            return view('agency.settings.approve_sadaka_digital', compact('gepginstitution'));
        } catch (\Exception $e) {
            Log::error('Approve Sadaka Digital Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_sadaka_digital')->with([
                'notification' => 'An error occurred while accessing the Sadaka Digital!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveSadakaDigitalAct(Request $request, $id)
    {
        try {
            $user_id = Auth::id();
            $statusMapping = [
                'reject' => ['isWaitingApproval' => 2, 'message' => 'Sadaka Digital has been rejected successfully'],
                'approve' => ['isWaitingApproval' => 0, 'message' => 'Sadaka Digital has been approved successfully']
            ];

            $action = $request->approve ?? $request->reject;

            if (isset($statusMapping[$action])) {
                AbSadakaDigital::where(['id' => $id])->update([
                    'isWaitingApproval' => $statusMapping[$action]['isWaitingApproval'],
                    'approver_id' => $user_id
                ]);

                return redirect()->route('agency.view_sadaka_digital')->with([
                    'notification' => $statusMapping[$action]['message'],
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_sadaka_digital')->with([
                'notification' => 'Invalid action',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve Sadaka Digital Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_sadaka_digital')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger'
            ]);
        }
    }

    public function createBranch()
    {
        $branchs = AbBranch::orderBy('id', 'DESC')->get();

        return view('agency.settings.branch', compact('branchs'));
    }

    public function storeBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branchName' => 'required|string|regex:/^[a-zA-Z\s]+$/u|max:50',
            'branch_code' => 'required|regex:/^[a-zA-Z0-9]+$/|max:20',
            'address' => 'required|string|regex:/^[a-zA-Z0-9\s,.\'-]+$/|max:255',
            'description' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => "danger"]);
        }

        try {
            $branchExists = AbBank::where('branchName', $request->branchName)
                ->orWhere('branch_code', $request->branch_code)
                ->exists();

            if ($branchExists) {
                return redirect()->back()->with([
                    'notification' => 'Branch Name or Code already exists!',
                    'color' => 'danger'
                ]);
            }

            $db_action = AbBranch::create([
                'branchName' => $request->branchName,
                'branch_code' => $request->branch_code,
                'address' => $request->address,
                'description' => $request->description,
                'initiator_id' => Auth::user()->id,
                'approver_id' => '0',
                'isWaitingApproval' => '1'
            ]);

            if ($db_action) {
                $notification = "Branch added successfully";
                $color = "success";
            } else {
                $notification = "Failed to add Branch!";
                $color = "danger";
            }
            $this->auditLog(Auth::user()->id, 'Add Branch', 'Agency Settings', $notification, $request->ip());
            return redirect('agency/view_branch')->with('notification', $notification)->with('color', $color);
        } catch (\Exception $exception) {
            $notification = 'Something went wrong, Try again later!';
            $color = 'danger';
            Log::error("STORE-BRANCH-EXCEPTION: " . json_encode($exception->getMessage()));
            $this->auditLog(Auth::user()->id, 'Add Branch Exception', 'Agency Settings', $exception->getMessage(), $request->ip());
        }
        return redirect('agency/view_branch')->with('notification', $notification)->with('color', $color);
    }

    public function editBranch($id)
    {
        $branch = AbBranch::where('id', $id)->first();
        return view('agency.settings.edit_branch', compact('branch'));
    }

    public function viewBranch($id)
    {
        try {
            $branch = AbBranch::findOrFail($id);
            return view('agency.settings.show_branch', compact('branch'));
        } catch (\Exception $e) {
            Log::error('View Branch Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while viewing the branch!',
                'color' => 'danger'
            ]);
        }
    }

    public function disableBranch($id)
    {
        try {
            $branch = AbBranch::findOrFail($id);
            return view('agency.settings.disable_branch', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Disable Branch Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while accessing the disable branch page!',
                'color' => 'danger'
            ]);
        }
    }

    public function disableBranchAct($id, Request $request)
    {
        try {
            if ($request->performed_act === 'disable') {
                $user_id = Auth::user()->id;
                $db_action = AbBranch::where(['id' => $id])->update([
                    'isWaitingApproval' => '1',
                    'approver_id' => '0',
                    'isDisabled' => 1,
                    'disabledBy_id' => $user_id
                ]);
                if ($db_action) {
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch disable request sent for approval',
                        'color' => 'success'
                    ]);
                }
                return redirect()->route('agency.view_branch')->with([
                    'notification' => 'Failed to disable',
                    'color' => 'danger'
                ]);
            }
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'Invalid action performed!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Disable Branch Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while processing the disable branch request!',
                'color' => 'danger'
            ]);
        }
    }

    public function enableBranch($id)
    {
        try {
            $branch = AbBranch::findOrFail($id);
            return view('agency.settings.enable_branch', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Enable Branch Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while accessing the enable branch page!',
                'color' => 'danger'
            ]);
        }
    }

    public function enableBranchAct(Request $request, $id)
    {
        try {
            $user_id = Auth::id();
            if ($request->performed_act === 'enable') {
                $db_action = AbBranch::where(['id' => $id])->update([
                    'isWaitingApproval' => '1',
                    'approver_id' => '0',
                    'isDisabled' => 1,
                    'disabledBy_id' => $user_id
                ]);

                if ($db_action) {
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch enable request sent successfully',
                        'color' => 'success'
                    ]);
                }
                return redirect()->route('agency.view_branch')->with([
                    'notification' => 'Failed to enable branch',
                    'color' => 'danger'
                ]);

            }
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'Invalid action performed!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Enable Branch Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while processing the enable branch request!',
                'color' => 'danger'
            ]);
        }
    }

    public function disableBranchApproval($id)
    {
        try {
            $branch = AbBranch::findOrFail($id);
            return view('agency.settings.disable_branch_approval', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Disable Branch Approval Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while accessing the disable branch approval page!',
                'color' => 'danger'
            ]);
        }
    }

    public function disableBranchActApproval(Request $request, $id)
    {
        try {
            $user_id = Auth::id();
            $branch = AbBranch::findOrFail($id);

            if ($branch->status === "Active") {
                if ($request->reject === 'reject') {
                    AbBranch::where(['id' => $id])->update([
                        'isWaitingApproval' => 0,
                        'approver_id' => $user_id,
                        'isDisabled' => 0
                    ]);
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch disabling has been rejected successfully',
                        'color' => 'success'
                    ]);
                }

                if ($request->approve === 'approve') {
                    AbBranch::where(['id' => $id])->update([
                        'status' => 'Disabled',
                        'isWaitingApproval' => 0,
                        'approver_id' => $user_id,
                        'isDisabled' => 2
                    ]);
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch disabling has been approved successfully',
                        'color' => 'success'
                    ]);
                }
            }

            if ($branch->status === "Disabled") {
                if ($request->reject === 'reject') {
                    AbBranch::where(['id' => $id])->update([
                        'isWaitingApproval' => 0,
                        'approver_id' => $user_id,
                        'isDisabled' => 2
                    ]);
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch enabling has been rejected successfully',
                        'color' => 'success'
                    ]);
                }

                if ($request->approve === 'approve') {
                    AbBranch::where(['id' => $id])->update([
                        'status' => 'Active',
                        'isWaitingApproval' => 0,
                        'approver_id' => $user_id,
                        'isDisabled' => 0
                    ]);
                    return redirect()->route('agency.view_branch')->with([
                        'notification' => 'Branch enabling has been approved successfully',
                        'color' => 'success'
                    ]);
                }
            }

            return redirect()->route('agency.view_branch')->with([
                'notification' => 'Invalid branch status!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Disable Branch Act Approval Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while processing the disable branch approval request!',
                'color' => 'danger'
            ]);
        }
    }

    public function createAccountProduct()
    {
        abort(404);
        $account_products = AbAccountProduct::orderBy('id', 'DESC')->get();

        return view('agency.settings.account_product', compact('account_products'));
    }

    public function storeAccountProduct(Request $request)
    {
        $request->validate([
            'account_product' => 'required|max:30',
            'account_product_code' => 'required|max:20'
        ]);

        try {
            $db_action = AbAccountProduct::insert([
                'account_product_type_code_name' => $request->account_product,
                'account_product_type_code' => $request->account_product_code,
                'account_description' => $request->account_description
            ]);

            if ($db_action) {
                return redirect('agency/view_account_product')->with([
                    'notification' => 'Account Product added successfully',
                    'color' => 'success'
                ]);
            }

            return redirect('agency/view_account_product')->with([
                'notification' => 'Account Product was not added!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Store Account Product Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('agency/view_account_product')->with([
                'notification' => 'An error occurred while adding Account Product!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveBranch(Request $request, $id)
    {
        try {
            $branch = AbBranch::findOrFail($id);
            return view('agency.settings.approve_branch', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Approve Branch Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while accessing the approve branch page!',
                'color' => 'danger'
            ]);
        }
    }

    public function approveBranchAct(Request $request, $id)
    {
        $user_id = Auth::id();

        try {
            if ($request->reject === 'reject') {
                AbBranch::where(['id' => $id])->update([
                    'isWaitingApproval' => 2,
                    'approver_id' => $user_id
                ]);
                return redirect()->route('agency.view_branch')->with([
                    'notification' => 'Branch has been rejected successfully',
                    'color' => 'success'
                ]);
            }

            if ($request->approve === 'approve') {
                AbBranch::where(['id' => $id])->update([
                    'isWaitingApproval' => 0,
                    'approver_id' => $user_id
                ]);
                return redirect()->route('agency.view_branch')->with([
                    'notification' => 'Branch has been approved successfully',
                    'color' => 'success'
                ]);
            }

            return redirect()->route('agency.view_branch')->with([
                'notification' => 'Invalid action performed!',
                'color' => 'danger'
            ]);
        } catch (\Exception $e) {
            Log::error('Approve Branch Act Exception: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('agency.view_branch')->with([
                'notification' => 'An error occurred while processing the approval action!',
                'color' => 'danger'
            ]);
        }
    }

    private $date_time;

    public function updateBranch(Request $request)
    {
        $request->validate([
            'branchName' => 'required|max:30',
            'branch_code' => 'required|max:20'
        ]);

        try {

            $db_action = AbBranch::where('id', $request->id)->update([
                'branchName' => $request->branchName,
                'branch_code' => $request->branch_code,
                'address' => $request->address,
                'description' => $request->description,
                'initiator_id' => Auth::user()->getAuthIdentifier(),
                'isWaitingApproval' => '1',
                'approver_id' => '0'
            ]);

            $this->date_time = Carbon::now()->setTimezone('Africa/Nairobi');


            if ($db_action == true) {


                $notification = "Branch updated successfully";
                $color = "success";
                return redirect('agency/view_branch')->with('notification', $notification)->with('color', $color);
            }

            $notification = "No change was made!";
            $color = "danger";

        } catch (\Exception $e) {
            $notification = $e->getMessage();
            $color = "danger";
        }


        return redirect('agency/view_branch')->with('notification', $notification)->with('color', $color);
    }


}
