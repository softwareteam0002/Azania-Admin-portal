<?php

namespace App\Http\Controllers\IB;

use App\AuditLogs;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthGates;
use App\IbBank;
use App\IbBranch;
use App\IbLoanType;
use App\IbLoanTypes;
use App\IbMno;
use App\IbTv;
use App\IbClass;
use App\IbTransferType;
use App\IbTransactionClass;
use App\TblIBInstitutionAccounts;
use App\TblIBInstitutionAccountTypes;
use App\AuditTrailLogs;
use App\IbInstitution;
use App\TblIbOTPPolicy;
use App\TblIbPasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

//TODO: Deploy on server
class SettingsController extends Controller
{

    public function createClass(){
        $classes = IbClass::orderBy('id', 'DESC')->get();
        $institutions = IbInstitution::all();
        return view('ib.settings.class',compact('classes', 'institutions'));
    }
  
    public function storeClass(Request $request){
        $request->validate([
            'name'=>'required',
            'fromAmount'=>'required',
	    'toAmount'=>'required',
            'institution_id'=>'required'
        ]);

        $db_action = IbClass::insert([
            'class_name'=>$request->name,
            'fromAmount'=>$request->fromAmount,
            'toAmount'=>$request->toAmount,
            'institution_id'=>$request->institution_id,
            'initiator_id'=>Auth::user()->id,
            'approver_id'=>'0',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Class added successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Created Class","IB",$notification,'ib/view_class',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Class was not added!";
            $color="danger";
        }

        return redirect('ib/view_class')->with('notification',$notification)->with('color',$color);
    }

   public function editClass($id){
        $class = IbClass::where('id',$id)->get()[0];
        $institutions = IbInstitution::all();
        return view('ib.settings.edit_class', compact('class', 'institutions'));
    }

    public function updateClass(Request $request){
        $request->validate([
            'name'=>'required',
            'fromAmount'=>'required',
	    'toAmount'=>'required',
            'institution_id'=>'required'
        ]);

        $db_action = IbClass::where('id',$request->id)->update([
            'class_name'=>$request->name,
            'fromAmount'=>$request->fromAmount,
            'toAmount'=>$request->toAmount,
            'institution_id'=>$request->institution_id,
            'initiator_id'=>Auth::user()->id,
            'approver_id'=>'0',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Class updated successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Updated Class","IB",$notification,'ib/view_class',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Class was not updated!";
            $color="danger";
        }

        return redirect('ib/view_class')->with('notification',$notification)->with('color',$color);
    }

    public function createTv(){
        $tvs = IbTv::orderBy('id', 'DESC')->get();

        return view('ib.settings.tv',compact('tvs'));
    }

    public function storeTv(Request $request){
        $request->validate([
            'name'=>'required',
            'description'=>'required'
        ]);

        $db_action = IbTv::insert([
            'name'=>$request->name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->id,
            'approver_id'=>'0',
            'status'=>'Active',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="TV service provider added successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Created TV","IB",$notification,'ib/view_tv',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="TV service provider was not added!";
            $color="danger";
        }

        return redirect('ib/view_tv')->with('notification',$notification)->with('color',$color);
    }

    public function editTv($id){
        $tv = IbTv::where('id',$id)->get()[0];
        return view('ib.settings.edit_tv', compact('tv'));
    }

    public function updateTv(Request $request){
        $request->validate([
            'name'=>'required',
            'description'=>'required'
        ]);

        $db_action = IbTv::where('id',$request->id)->update([
            'name'=>$request->name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="TV service provider updated successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Updated TV","IB",$notification,'ib/view_tv',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="TV service provider was not updated!";
            $color="danger";
        }

        return redirect('ib/view_tv')->with('notification',$notification)->with('color',$color);
    }


    public function disableTv($id) {
            $tv = IbTv::findOrFail($id);
            return view('ib.settings.disable_tv',compact('tv'));    
    }


   public function disableTvAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'disable') {
    
             IbTv::where(['id' => $id])->update(['isWaitingApproval'=>'1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_tv')->with(['notification' => 'Tv disable request sent successfully', 'color' => 'success']);
      
          }
}

  public function disableTvApproval($id)
{
           $tv = IbTv::findOrFail($id);
           return view('ib.settings.disable_tv_approval',compact('tv'));
}
public function disableTvActApproval(Request $request, $id)
{

        $user_id = Auth::id();
        $tv = IbTv::findOrFail($id);
        if($tv->status == "Active")
    {
    if ($request->reject == 'reject') {
    
        IbTv::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
        return redirect()->route('ib.view_tv')->with(['notification' => 'Tv disabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbTv::where(['id' => $id])->update(['status' => 'Disabled', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
          return redirect()->route('ib.view_tv')->with(['notification' => 'Tv disabling has been approved successfully', 'color' => 'success']);   
        }
    }
    if($tv->status == "Disabled")
    {
    if ($request->reject == 'reject') {
    
        IbTv::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
        return redirect()->route('ib.view_tv')->with(['notification' => 'Tv enabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbTv::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
          return redirect()->route('ib.view_tv')->with(['notification' => 'Tv enabling has been approved successfully', 'color' => 'success']);   
        }
    }

}


 public function enableTv($id) {
            $tv = IbTv::findOrFail($id);
            return view('ib.settings.enable_tv',compact('tv'));    
    }


   public function enableTvAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'enable') {
    
             IbTv::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_tv')->with(['notification' => 'Tv enable request sent successfully', 'color' => 'success']);
      
          }

}

   public function approveTv(Request $request, $id) {
    
        $tv = IbTv::findOrFail($id);
        return view('ib.settings.approve_tv', compact('tv'));
    }
    public function approveTvAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbTv::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.view_tv')->with(['notification' => 'Tv has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbTv::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.view_tv')->with(['notification' => 'Tv has been approved successfully', 'color' => 'success']);   
        }      

    }
	public function createTransactionClass(){
	$transactionClasses = IbTransactionClass::orderBy('id', 'DESC')->get();
	$transferTypes = IbTransferType::all();
	$classes = array("A+B+C", "A+B", "A+C", "B+C");
        return view('ib.settings.transaction_class',compact('transactionClasses', 'transferTypes', 'classes'));
    }
   public function storeTransactionClass(Request $request){
        $request->validate([
            'class_name'=>'required',
            'transfer_type_id'=>'required'        ]);

        $db_action = IbTransactionClass::insert([
            'class_name'=>$request->class_name,
            'transfer_type_id'=>$request->transfer_type_id,
        ]);

        if($db_action==true)
        {
            $notification="Transation Class added successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Created Transation  Class","IB",$notification,'ib/view_transaction_class',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Transation  Class was not added!";
            $color="danger";
        }

        return redirect('ib/view_transaction_class')->with('notification',$notification)->with('color',$color);
    }
	public function editTransactionClass($id){
        $transactionClass = IbTransactionClass::where('id',$id)->get()[0];
	$transferTypes = IbTransferType::all();
	$classes = array("A+B+C", "A+B", "A+C", "B+C");
        return view('ib.settings.edit_transaction_class', compact('transferTypes', 'transactionClass', 'classes'));
    }

    public function updateTransactionClass(Request $request){
        $request->validate([
            'class_name'=>'required',
            'transfer_type_id'=>'required',
        ]);

        $db_action = IbTransactionClass::where('id',$request->id)->update([
            'class_name'=>$request->class_name,
            'transfer_type_id'=>$request->transfer_type_id
        ]);

        if($db_action==true)
        {
            $notification="Transaction Class updated successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Updated Transaction  Class","IB",$notification,'ib/view_transaction_class',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Transaction Class was not updated!";
            $color="danger";
        }

        return redirect('ib/view_transaction_class')->with('notification',$notification)->with('color',$color);
    }


    public function createBranch()
    {
        $branchs = IbBranch::orderBy('id', 'DESC')->get();
        $banks = IbBank::orderBy('id', 'DESC')->get();
        return view('ib.settings.branch',compact('branchs', 'banks'));
    }

    public function storeBranch(Request $request)
    {
        $request->validate([
            'branchName'=>'required|max:30',
            'branch_code'=>'required|max:20',
            'bank_code'=>'required|max:20'
        ]);

        $db_action = IbBranch::insert([
            'branchName'=>$request->branchName,
            'branch_code'=>$request->branch_code,
            'swift_code'=>$request->swift_code,
            'bank_code'=>$request->bank_code,
            'address'=>$request->address,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->id,
            'approver_id'=>'0',
            'status'=>'Active',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Branch added successfully";
            $color="success";
            /*$log = new Helper();
            return $log->auditTrail("Created Branch","IB",$notification,'ib/view_branch',Auth::user()->getAuthIdentifier());*/
         return redirect('ib/view_branch')->with('notification',$notification)->with('color',$color);
        }
        else{
            $notification="Branch was not added!";
            $color="danger";
        }

        return redirect('ib/view_branch')->with('notification',$notification)->with('color',$color);
    }

    

    public function editBranch($id)
    {
        $branch = IbBranch::where('id',$id)->get()[0];
        $banks = IbBank::orderBy('id', 'DESC')->get();
        return view('ib.settings.edit_branch', compact('branch', 'banks'));
    }

    private $date_time;
    public function updateBranch(Request $request)
    {
        $request->validate([
            'branchName'=>'required|max:30',
            'branch_code'=>'required|max:20',
            'bank_code'=>'required|max:20'
        ]);

        try {

            $db_action = IbBranch::where('id',$request->id)->update([
                'branchName' => $request->branchName,
                'branch_code' => $request->branch_code,
                'address' => $request->address,
                'description' => $request->description,
                'swift_code'=>$request->swift_code,
                'bank_code'=>$request->bank_code,
                'initiator_id' => Auth::user()->getAuthIdentifier(),
                'isWaitingApproval' => '1',
                'approver_id' => '0'
            ]);

            $this->date_time = Carbon::now()->setTimezone('Africa/Nairobi');


            if ($db_action == true) {


                $notification = "Branch updated successfully";
                $color = "success";

                $log = new Helper();
                return $log->auditTrail("Updated Branch","IB",$notification,'ib/view_branch',Auth::user()->getAuthIdentifier());
            }else{
                $notification = "No change was made!";
                $color = "danger";
            }

        }catch (\Exception $e)
        {
            $notification = $e->getMessage();
            $color = "danger";
        }


        return redirect('ib/view_branch')->with('notification',$notification)->with('color',$color);
    }

    
    public function viewBranch($id) {

      $branch = IbBranch::findOrFail($id);
      return view('ib.settings.show_branch', compact('branch'));

    }

    public function approveBranch(Request $request, $id) {
    
        $branch = IbBranch::findOrFail($id);
        $banks = IbBank::orderBy('id', 'DESC')->get();
        return view('ib.settings.approve_branch', compact('branch', 'banks'));
    }
    public function approveBranchAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbBranch::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.view_branch')->with(['notification' => 'Branch has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbBranch::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.view_branch')->with(['notification' => 'Branch has been approved successfully', 'color' => 'success']);   
        }

        

    }


    public function disableBranch($id) {
            $branch = IbBranch::findOrFail($id);
            $banks = IbBank::orderBy('id', 'DESC')->get();
            return view('ib.settings.disable_branch',compact('branch', 'banks'));    
    }


   public function disableBranchAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'disable') {
    
             IbBranch::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_branch')->with(['notification' => 'Branch disable request sent successfully', 'color' => 'success']);
      
          }

}
 public function enableBranch($id) {
            $branch = IbBranch::findOrFail($id);
            $banks = IbBank::orderBy('id', 'DESC')->get();
            return view('ib.settings.enable_branch',compact('branch', 'banks'));    
    }


   public function enableBranchAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'enable') {
    
             IbBranch::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_branch')->with(['notification' => 'Branch enable request sent successfully', 'color' => 'success']);
      
          }

}
 public function disableBranchApproval($id)
{
           $branch = IbBranch::findOrFail($id);
           $banks = IbBank::orderBy('id', 'DESC')->get();
           return view('ib.settings.disable_branch_approval',compact('branch', 'banks'));
}
public function disableBranchActApproval(Request $request, $id)
{

        $user_id = Auth::id();
        $branch = IbBranch::findOrFail($id);
        if($branch->status == "Active")
    {
    if ($request->reject == 'reject') {
    
        IbBranch::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
        return redirect()->route('ib.view_branch')->with(['notification' => 'Branch disabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbBranch::where(['id' => $id])->update(['status' => 'Disabled', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
          return redirect()->route('ib.view_branch')->with(['notification' => 'Branch disabling has been approved successfully', 'color' => 'success']);   
        }
    }
    if($branch->status == "Disabled")
    {
    if ($request->reject == 'reject') {
    
        IbBranch::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
        return redirect()->route('ib.view_branch')->with(['notification' => 'Branch enabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbBranch::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
          return redirect()->route('ib.view_branch')->with(['notification' => 'Branch enabling has been approved successfully', 'color' => 'success']);   
        }
    }

}

    public function createBank()
    {
        $banks = IbBank::orderBy('id', 'DESC')->get();

        return view('ib.settings.bank', compact('banks'));
    }


    public function storeBank(Request $request)
    {

        $request->validate([
            'name'=>'required|max:30',
            'bank_code'=>'required|max:20',
            'status'=>'Active'
        ]);
 
//, 'address'=>'required|max:100'
        $db_action = IbBank::insert([
            'name'=>$request->name,
            'bank_code'=>$request->bank_code,
            'address'=>$request->address,
            'swift_code'=>$request->swift_code,
            'shortName'=>$request->shortName,
            'cBankID'=>$request->bank_code,
            'address'=>$request->address,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'status'=>'Active',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action)
        {
            $notification="Bank added successfully";
            $color="success";
            return redirect('ib/view_bank')->with('notification',$notification)->with('color',$color);
           $log = new Helper();
            return $log->auditTrail("Created Bank","IB",$notification,'ib/view_bank',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Bank was not added!";
            $color="danger";
             return redirect('ib/view_bank')->with('notification',$notification)->with('color',$color);
        }

       
    }

    public function editBank($id)
    {
        $bank = IbBank::where('id',$id)->get()[0];
        return view('ib.settings.edit_bank', compact('bank'));
    }

    public function updateBank(Request $request)
    {
        $request->validate([
            'name'=>'required|max:30',
            'bank_code'=>'required|max:20'
        ]);

        $db_action = IbBank::where('id',$request->id)->update([
            'name'=>$request->name,
            'bank_code'=>$request->bank_code,
            'address'=>$request->address,
            'swift_code'=>$request->swift_code,
            'shortName'=>$request->shortName,
            'description'=>$request->description,
            'initiator_id'=>$request->initiator_id,
            'approver_id'=>$request->approver_id,
            'isWaitingApproval'=>'1'
        ]);

        $new_details = IbBank::where('id',$request->id)->get()[0];

        $this->date_time = Carbon::now()->setTimezone('Africa/Nairobi');

        if($db_action==true)
        {

            $notification="Bank updated successfully";
            $color="success";

            $log = new Helper();
            return $log->auditTrail("Updated Bank","IB",$notification,'ib/view_bank',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Bank was not updated!";
            $color="danger";
        }

        return redirect('ib/view_bank')->with('notification',$notification)->with('color',$color);
    }

     public function disableBank($id) {
            $bank = IbBank::findOrFail($id);
            return view('ib.settings.disable_bank',compact('bank'));    
    }


   public function disableBankAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'disable') {
    
             IbBank::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_bank')->with(['notification' => 'Bank disable request sent successfully', 'color' => 'success']);
      
          }
}

public function enableBank($id) {
            $bank = IbBank::findOrFail($id);
            return view('ib.settings.enable_bank',compact('bank'));    
    }


   public function enableBankAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'enable') {
    
             IbBank::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_bank')->with(['notification' => 'Bank enable request sent successfully', 'color' => 'success']);
      
          }
      }

   public function disableBankApproval($id)
{
           $bank = IbBank::findOrFail($id);
           return view('ib.settings.disable_bank_approval',compact('bank'));
}
public function disableBankActApproval(Request $request, $id)
{

        $user_id = Auth::id();
        $bank = IbBank::findOrFail($id);
        if($bank->status == "Active")
    {
    if ($request->reject == 'reject') {
    
        IbBank::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
        return redirect()->route('ib.view_bank')->with(['notification' => 'Bank disabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbBank::where(['id' => $id])->update(['status' => 'Disabled', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
          return redirect()->route('ib.view_bank')->with(['notification' => 'Bank disabling has been approved successfully', 'color' => 'success']);   
        }
    }
    if($bank->status == "Disabled")
    {
    if ($request->reject == 'reject') {
    
        IbBank::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
        return redirect()->route('ib.view_bank')->with(['notification' => 'Bank enabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbBank::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
          return redirect()->route('ib.view_bank')->with(['notification' => 'Bank enabling has been approved successfully', 'color' => 'success']);   
        }
    }

}

 public function approveBank(Request $request, $id) {
    
        $bank = IbBank::findOrFail($id);
        return view('ib.settings.approve_bank', compact('bank'));
    }
    public function approveBankAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbBank::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.view_bank')->with(['notification' => 'Bank has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbBank::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.view_bank')->with(['notification' => 'Bank has been approved successfully', 'color' => 'success']);   
        }      

    }

    public function auditTrail($request,$new_details)
    {

        $log = AuditLogs::insert([
            'id'=> $request->id,
            'initiator_id'=> $request->initiator_id,
            'approver_id'=> $request->approver_id,
            'action'=>$request->action,
            'module'=>$request->module,
            'date_time'=>$this->date_time,
            'old_details'=>$request->old_details,
            'new_details'=>$new_details,
            'isWaitingApproval'=>'1'
        ]);
    }



    public function createMno()
    {
        $mnos = IbMno::orderBy('id', 'DESC')->get();

        return view('ib.settings.mno', compact('mnos'));
    }

    public function storeMno(Request $request)
    {
        $request->validate([
            'name'=>'required|max:50',
			'type_name' => 'required',
            'description'=>'required'
        ]);

        $db_action = IbMno::insert([
            'name'=>$request->name,
			'type'=>$request->type_name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'status'=>'Active',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Mobile network operator added successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Created MNO","IB",$notification,'ib/view_mno',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Mobile network operator was not added!";
            $color="danger";
        }

        return redirect('ib/view_mno')->with('notification',$notification)->with('color',$color);
    }

    public function editMno($id)
    {
        $mno = IbMno::where('id',$id)->get()[0];
        return view('ib.settings.edit_mno', compact('mno'));
    }

    public function updateMno(Request $request)
    {
        $request->validate([
            'name'=>'required|max:50',
            'description'=>'required'
        ]);

        $db_action = IbMno::where('id',$request->id)->update([
            'name'=>$request->name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Mobile network operator updated successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Updated MNO","IB",$notification,'ib/view_mno',Auth::user()->getAuthIdentifier());

        }
        else{
            $notification="Mobile network operator was not updated!";
            $color="danger";
        }

        return redirect('ib/view_mno')->with('notification',$notification)->with('color',$color);
    }

       public function disableMno($id) {
            $mno = IbMno::findOrFail($id);
            return view('ib.settings.disable_mno',compact('mno'));    
    }


   public function disableMnoAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'disable') {    
             IbMno::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_mno')->with(['notification' => 'Mno disable request sent successfully', 'color' => 'success']);
      
          }
}

public function enableMno($id) {
            $mno = IbMno::findOrFail($id);
            return view('ib.settings.enable_mno',compact('mno'));    
    }


   public function enableMnoAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'enable') {
    
             IbMno::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_mno')->with(['notification' => 'Mno enable request sent successfully', 'color' => 'success']);
      
          }

}

    
     public function disableMnoApproval($id)
{
           $mno = IbMno::findOrFail($id);
           return view('ib.settings.disable_mno_approval',compact('mno'));
}
public function disableMnoActApproval(Request $request, $id)
{

        $user_id = Auth::id();
        $mno = IbMno::findOrFail($id);
        if($mno->status == "Active")
    {
    if ($request->reject == 'reject') {
    
        IbMno::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
        return redirect()->route('ib.view_mno')->with(['notification' => 'Mno disabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbMno::where(['id' => $id])->update(['status' => 'Disabled', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
          return redirect()->route('ib.view_mno')->with(['notification' => 'Mno disabling has been approved successfully', 'color' => 'success']);   
        }
    }
    if($mno->status == "Disabled")
    {
    if ($request->reject == 'reject') {
    
        IbMno::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
        return redirect()->route('ib.view_mno')->with(['notification' => 'Mno enabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbMno::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
          return redirect()->route('ib.view_mno')->with(['notification' => 'Mno enabling has been approved successfully', 'color' => 'success']);   
        }
    }

}

 public function approveMno(Request $request, $id) {
    
        $mno = IbMno::findOrFail($id);
        return view('ib.settings.approve_mno', compact('mno'));
    }
    public function approveMnoAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbMno::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.view_mno')->with(['notification' => 'Mno has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbMno::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.view_mno')->with(['notification' => 'Mno has been approved successfully', 'color' => 'success']);   
        }      

    }

    public function createLoanType()
    {
        $loantypes = IbLoanTypes::orderBy('id', 'DESC')->latest()->get();
        return view('ib.settings.loan_types',compact('loantypes'));
    }

    public function storeLoanType(Request $request)
    {
        $request->validate([
            'name'=>'required|max:50',
            'description'=>'required'
        ]);

        $date_time = Carbon::now()->setTimezone('Africa/Nairobi');

        $db_action = IbLoanType::insert([
            'name'=>$request->name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'isWaitingApproval'=>'1',
            'status'=>'Active',
            'created_at'=>$date_time,
            'updated_at'=>$date_time
        ]);

        if($db_action==true)
        {
            $notification="Loan type added successfully";
            $color="success";
            $log = new Helper();
            return $log->auditTrail("Created Loan Type","IB",$notification,'ib/view_loan_type',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Loan type was not added!";
            $color="danger";
        }

        return redirect('ib/view_loan_type')->with('notification',$notification)->with('color',$color);
    }

    public function editLoanType($id)
    {
        $loantype = IbLoanTypes::where('id',$id)->get()[0];
        return view('ib.settings.edit_loan_types', compact('loantype'));
    }

    public function updateLoanType(Request $request)
    {
        $request->validate([
            'name'=>'required|max:50',
            'description'=>'required'
        ]);

        $db_action = IbLoanType::where('id',$request->id)->update([
            'name'=>$request->name,
            'description'=>$request->description,
            'initiator_id'=>Auth::user()->getAuthIdentifier(),
            'approver_id'=>'0',
            'isWaitingApproval'=>'1'
        ]);

        if($db_action==true)
        {
            $notification="Loan type updated successfully";
            $color="success";

            $log = new Helper();
            return $log->auditTrail("Updated Loan Type","IB",$notification,'ib/view_loan_type',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Loan type was not updated!";
            $color="danger";
        }

        return redirect('ib/view_loan_type')->with('notification',$notification)->with('color',$color);
    }


    public function approveLoanType(Request $request, $id) {
    
        $loantype = IbLoanTypes::findOrFail($id);
        return view('ib.settings.approve_loan_type', compact('loantype'));
    }
    public function approveLoanTypeAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type has been approved successfully', 'color' => 'success']);   
        }

        

    }


    public function disableLoanType($id) {
            $loantype = IbLoanTypes::findOrFail($id);
            return view('ib.settings.disable_loan_type',compact('loantype'));    
    }


   public function disableLoanTypeAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'disable') {
    
             IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type disable request sent successfully', 'color' => 'success']);
      
          }

}
 public function enableLoanType($id) {
            $loantype = IbLoanTypes::findOrFail($id);
            return view('ib.settings.enable_loan_type',compact('loantype'));    
    }


   public function enableLoanTypeAct(Request $request, $id) {
            $user_id = Auth::id();
            if ($request->performed_act == 'enable') {
    
             IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => '1', 'approver_id' => '0', 'isDisabled' => 1, 'disabledBy_id' => $user_id]);
           return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type enable request sent successfully', 'color' => 'success']);
      
          }

}
 public function disableLoanTypeApproval($id)
{
           $loantype = IbLoanTypes::findOrFail($id);
           return view('ib.settings.disable_loan_type_approval',compact('loantype'));
}
public function disableLoanTypeActApproval(Request $request, $id)
{

        $user_id = Auth::id();
        $loantype = IbLoanTypes::findOrFail($id);
        $status = preg_replace('/\s+/', '', $loantype->status);

        if($status == 'Active')
    {

    if ($request->reject == 'reject') {
    
        IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
        return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type disabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbLoanTypes::where(['id' => $id])->update(['status' => 'Disabled', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
          return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type disabling has been approved successfully', 'color' => 'success']);   
        }
    }
    if($status == 'Disabled')
    {
    if ($request->reject == 'reject') {
    
        IbLoanTypes::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 2]);
        return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type enabling has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
        IbLoanTypes::where(['id' => $id])->update(['status' => 'Active', 'isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDisabled' => 0]);
          return redirect()->route('ib.view_loan_type')->with(['notification' => 'Loan type enabling has been approved successfully', 'color' => 'success']);   
        }
    }

}



    public function indexSecurityPolicies(){
        $passwordpolicy = TblIbPasswordPolicy::all();
        $otppolicy = TblIbOTPPolicy::all();
        return view('ib.settings.securitypolicy',compact('passwordpolicy','otppolicy'));
    }

    public function updateOTPSecurityPolicies(Request $request){
        $request->validate([
            'id'=>'required',
            'min_length'=>'required',
            'max_length'=>'required',
            'max_attempts'=>'required',
        ]);

        $db_action = TblIbOTPPolicy::where('id', $request->id)->update([
            'min_length'=>$request->min_length,
            'max_length'=>$request->max_length,
            'max_attempts'=>$request->max_attempts,
            'isWaitingApproval' => 1
        ]);

        if($db_action==true)
        {
            $notification="OTP Policy updated successfully";
            $color="success";
        }
        else{
            $notification="OTP Policy was not updated!";
            $color="danger";
        }

        return redirect()->back()->with('notification',$notification)->with('color',$color);
    }

    public function updatePasswordSecurityPolicies(Request $request){
        $request->validate([
            'id'=>'required',
            'min_length'=>'required',
            'max_length'=>'required',
            'max_attempts'=>'required',
            'uppercase_count'=>'required',
            'numeric_count'=>'required',
            'expiry_period'=>'required'
        ]);

        $db_action = TblIbPasswordPolicy::where('id', $request->id)->update([
            'min_length'=>$request->min_length,
            'max_length'=>$request->max_length,
            'expiry_period'=>$request->expiry_period,
            'numeric_char_count'=>$request->numeric_count,
            'uppercase_char_count'=>$request->uppercase_count,
            'max_attempts'=>$request->max_attempts,
            "isWaitingApproval" => 1,
            'approver_id'=>0
        ]);

        if($db_action==true)
        {
            $notification="Password Policy updated successfully";
            $color="success";
        }
        else{
            $notification="Password Policy was not updated!";
            $color="danger";
        }

        return redirect()->back()->with('notification',$notification)->with('color',$color);
    }

    public function viewSecurityPolicy($id) {
        $passwordPolicy = TblIbPasswordPolicy::findOrFail($id);
        return view('ib.settings.show_spolicy', compact('passwordPolicy'));
    } 

    public function approveSecurityPolicy($id) {
       
        $passwordPolicy = TblIbPasswordPolicy::findOrFail($id);
        return view('ib.settings.approve_spolicy', compact('passwordPolicy'));

    }

    public function approveSecurityPolicyAct(Request $request, $id) {
          if ($request->approve === 'approve') {
            TblIbPasswordPolicy::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => Auth::id()]);
            return redirect()->route('ib.sPolicy.index')->with(['notification' => 'Password policy successfully approved', 'color' => 'success']);
          }
           if ($request->reject === 'reject') { 
            TblIbPasswordPolicy::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => Auth::id()]);
            return redirect()->route('ib.sPolicy.index')->with(['notification' => 'Password policy successfully rejected', 'color' => 'success']);
          }
    }


    //otp
         public function viewOtpPolicy($id) {
        $passwordPolicy = TblIbOTPPolicy::findOrFail($id);
        return view('ib.settings.show_spolicy', compact('passwordPolicy'));
    } 

    public function approveOtpPolicy($id) {
       
        $otpPolicy = TblIbOTPPolicy::findOrFail($id);
        return view('ib.settings.approve_opolicy', compact('otpPolicy'));

    }

    public function approveOtpPolicyAct(Request $request, $id) {
          if ($request->approve === 'approve') {
            TblIbOTPPolicy::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => Auth::id()]);
            return redirect()->route('ib.sPolicy.index')->with(['notification' => 'OTP policy successfully approved', 'color' => 'success']);
          }
           if ($request->reject === 'reject') { 
            TblIbOTPPolicy::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => Auth::id()]);
            return redirect()->route('ib.sPolicy.index')->with(['notification' => 'OTP policy successfully rejected', 'color' => 'success']);
          }
    }

    public function getInstitutionAccounts()
    {
        $accounts = TblIBInstitutionAccounts::orderBy('id', 'DESC')->get();
        $account_types = TblIBInstitutionAccountTypes::all();
        $auditlogs = AuditTrailLogs::all();
        return view('ib.account_services.institution_accounts', compact('account_types', 'accounts', 'auditlogs'));
    }

    public function editInstitutionAccount($id)
    {
        $account = TblIBInstitutionAccounts::where('id', $id)->first();
        $accounts = TblIBInstitutionAccounts::orderBy('id', 'DESC')->get();
        
        $account_types = TblIBInstitutionAccountTypes::all();
        $auditlogs = AuditTrailLogs::all();
        return view('ib.account_services.edit_institution_accounts', compact('account_types', 'accounts','account', 'auditlogs'));
   
    }

     public function updateInstitutionAccount(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required',
            'id' => 'required'
        ]);
       
        $account = TblIBInstitutionAccounts::where('id', $request->id)
            ->update([
                'account_number' => $request->account_number,
                'account_type_id' => $request->account_type_id,
                'approver_id' => null
            ]);

        if ($account) {
            $log = new Helper();
            $log->auditTrail("Updated Account","IB",'Account updated successfully!','ib/institutionaccounts/edit/'.$request->id,Auth::user()->getAuthIdentifier());
            return redirect('ib/institutionaccounts/edit/'.$request->id)->with(['notification' => 'Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect('ib/institutionaccounts/edit/'.$request->id)->with(['notification' => 'Account updated un successfully!', 'color' => 'danger']);
        }
    }

     public function storeInstitutionAccount(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required'
        ]);

        //Validate account number to be unique
        $account_number = TblIBInstitutionAccounts::where('account_number',$request->account_number)
            ->where('account_type_id',$request->account_type_id)
            ->get();

        if(count($account_number)>0)
        {
            $notification="Account already exist!";
            $color="danger";
            //change redirect url by James
            return redirect('ib/institution_accounts')->with('notification', $notification)->with('color', $color);
        }

        //$this->verifyAccount($request);

        $account = new TblIBInstitutionAccounts();

        $account->account_number = $request->account_number;
        $account->account_type_id = $request->account_type_id;
        $account->initiator_id = $uid;
        $account->save();

        $log = new Helper();
        $log->auditTrail("Added new Account","IB",'Added Account','ib/institution_accounts',Auth::user()->getAuthIdentifier());
        return redirect()->back()->with(['notification' => 'Account added successfully!', 'color' => 'success']);
    }

    public function approveInstitutionAccount(Request $r){
        $uid =  Auth::user()->id;
        $account_id = $r->account_id;
        $op = $r->op;
        if($op == 1){
            //approve
            $dual_status = 1;
            $notification = "Institution account approved successfully!";
        }else{
            //disapprove
            $dual_status = 2;
            $notification = "Institution account approved unsuccessfully!";
        }

        $approve = TblIBInstitutionAccounts::where('id', $account_id)
            ->update([
                'approver_id' => $uid,
            ]);

        if ($approve == true) {
            return redirect()->back()->with(['notification' => $notification, 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Institution Account approved/disapproved unsuccessfully!', 'color' => 'danger']);
        }
    }


}
