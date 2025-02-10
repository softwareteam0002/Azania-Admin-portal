<?php

namespace App\Http\Controllers\IB;

use App\AtmCard;
use App\AuditTrailLogs;
use App\ChequeBook;
use App\Fdr;
use App\Helper\Helper;
use App\IbAccount;
use App\IbInstitution;
use App\IbChurchInstitution;
use App\IbInstitutionType;
use App\IbInstitutionPayment;
use App\IbInstitutionService;
use App\IbLetterGuarantee;
use App\IbUser;
use App\StandingOrder;
use App\IbUserRole;
use App\TblAdminActionLevel;
use App\Jobs\BankRequests;
use App\Jobs\PasswordHitJob;
use App\Mail\PasswordMail;
use App\Http\Controllers\SMSController;
use App\LetterCredit;
use App\Loan;
use Hash;
use App\LoanType;
use App\IbAccountType;
use App\IbBranch;
use App\IBRole;
use App\IbClass;
use App\OtpOption;
use App\RequestStatus;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IBTransactionType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerRequestsController extends Controller
{
    protected $notification = '';
    //Cheque request
    public function getChequeRequest(){
        $requests = ChequeBook::orderBy('id', 'DESC')->get();

        //dd(Auth::user()->action_id);
        // $i = RequestStatus::insert([
        //     'id'=>5,
        //     'name'=>"Rejected",
        //     'description'=>"Rejected",
        // ]);

        // return RequestStatus::all();

        return view('ib.customer_request.cheque_requests', compact('requests'));
    }
	
	public function getStopChequeRequest(){
        $stop_requests = DB::connection('sqlsrv2')->table('tbl_stop_cheque_payments')->orderBy('id', 'DESC')->get();

        //dd(Auth::user()->action_id);
        // $i = RequestStatus::insert([
        //     'id'=>5,
        //     'name'=>"Rejected",
        //     'description'=>"Rejected",
        // ]);

        // return RequestStatus::all();

        return view('ib.customer_request.stop_cheque_request', compact('stop_requests'));
    }


	public function getStandingOrderRequest(){
        //$standing_orders = DB::connection('sqlsrv2')->table('tbl_standing_order')->orderBy('id', 'DESC')->get();
					$standing_orders = StandingOrder::orderBy('id', 'DESC')->get();

        //return view('ib.customer_request.standing_orders_request', compact('standing_orders'));
	return view('ib.customer_request.standing_orders_request',compact('standing_orders'));
    }


    public function showCheque($id)
    {
      
        $request = ChequeBook::where('id',$id)->get()[0];

        return view('ib.customer_request.show_cheque', compact('request'));
    }

    public function pendingCheque(Request $request)
    {
        $this->validate($request,
            [
               'check_id'=>'required'
            ]);

        $status="2";

        try{
            $update = ChequeBook::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status,
                    'initiator_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function approveCheque(Request $request)
    {
        $user = Auth::user();

        if($user->action_id!=2)
        {
            $notification="You are only allowed to initiate!";
            $color="danger";
            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);
        }

        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="3";

        try{
            $update = ChequeBook::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status,
                    'approver_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function readyCheque(Request $request)
    {
        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="4";

        try{
            $update = ChequeBook::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function collectCheque(Request $request)
    {
        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="5";

        try{
            $update = ChequeBook::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/cheque/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function getCardRequest(){
        $requests = AtmCard::all();

        //$requests = AtmCard::on('sqlsrv2')->with('user')->with('account')->with('status')->get();
        return view('ib.customer_request.card_requests', compact('requests'));
    }

    public function getLetterCredit(){
        $requests = LetterCredit::on('sqlsrv2')->orderBy('id', 'DESC')->get();
        return view('ib.customer_request.letter_of_credit', compact('requests'));
    }

    public function message($title,$status,$email)
    {
        $body = $title .", request has been  ".$status;
        $recipient=$email;
        $time = 10;
        Queue::later($time, new BankRequests($body,$recipient));
    }

    public function showLetterCredit($id)
    {
        $request = LetterCredit::where('id',$id)->get()[0];

        return view('ib.customer_request.show_letter_credit', compact('request'));
    }

    public function approvedLetter(Request $request)
    {

        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="2";

        try{
            $update = LetterCredit::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status,
                    'approver_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $email = LetterCredit::where('id',$request->check_id)->get()[0];
                $this->message("Letter of credit ","approved",$email->users->email);
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/letter/credit')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/letter/credit')->with('notification',$notification)->with('color',$color);
        }
    }

    public function revokedLetter(Request $request)
    {
        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="5";

        try{
            $update = LetterCredit::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status
                ]);

            if($update==true)
            {
                $email = LetterCredit::where('id',$request->check_id)->get()[0];
                $this->message("Letter of credit ","rejected",$email->users->email);
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }


            return redirect('ib/letter/credit')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/letter/credit')->with('notification',$notification)->with('color',$color);
        }
    }

    public function getLetterGuarantee(){

        $requests = IbLetterGuarantee::orderBy('id', 'DESC')->get();

        return view('ib.customer_request.letter_of_guarantee', compact('requests'));
    }

    public function getLoanRequest(){
        $requests = Loan::orderBy('id', 'DESC')->get();

        return view('ib.customer_request.loan_requests', compact('requests'));
    }

    public function showLoanRequest($id)
    {
        $request = Loan::where('id',$id)->get()[0];

        return view('ib.customer_request.show_loan_request', compact('request'));
    }

    //Actions for loan request
    public function pendingLoan(Request $request)
    {
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="2";

        try{
            $update = Loan::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status,
                    'initiator_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Loan status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function approveLoan(Request $request)
    {
        $user = Auth::user();

        if($user->action_id!=2)
        {
            $notification="You are only allowed to initiate!";
            $color="danger";
            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);
        }

        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="3";

        try{
            $update = Loan::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status,
                    'approver_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function readyLoan(Request $request)
    {
        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="4";

        try{
            $update = Loan::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status
                ]);

            if($update==true)
            {
                $notification="Loan status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function collectLoan(Request $request)
    {
        $user_id = Auth::user()->getAuthIdentifier();
        $user_role = IbUser::where('id',$user_id)->get()[0];
        $this->validate($request,
            [
                'check_id'=>'required'
            ]);

        $status="5";

        try{
            $update = Loan::where('id',$request->check_id)
                ->update([
                    'status_id'=>$status
                ]);

            if($update==true)
            {
                $notification="Loan status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/loan/request')->with('notification',$notification)->with('color',$color);
        }
    }

    public function acceptCardRequest($id){
        $cardRequest = AtmCard::on('sqlsrv2')->where('id', $id)->first();
        if (!$cardRequest){
            return redirect()->back()->with(['notification' => 'Card request does not exist', 'color' => 'danger']);
        }

        $cardRequest->status_id = 2;
        $cardRequest->save();
        return redirect()->back()->with(['notification' => 'Card request successfully accepted', 'color' => 'success']);
    }


   //Fixed Deposit Rate
   public function indexFdr()
   {
        $fdrs = Fdr::orderBy('tenure_of_term_deposit', 'ASC')->get();
      
        $tenures = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36];

        return view('ib.customer_request.index_fdr',compact('fdrs','tenures'));
   }

   public function createFdr()
   {
    return view('ib.customer_request.create_fdr');
   }

   public function editFdr($id)
    {

        $fdr = Fdr::where('id',$id)->get()[0];

        $tenures = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36];

        return view('ib.customer_request.edit_fdr',compact('fdr','tenures'));

    }

   public function storeFdr(Request $request)
   {

//            $request->validate(
//                [
//                    'tenure'=>'required',
//                    'rate'=>'required',
//                    'from_amount'=>'required',
//                    'to_amount'=>'required',
//                    'isWaitingApproval'=>'1',
//                    'initiator_id'=>Auth::user()->id,
//                    'approver_id'=>'0'
//                ]
//            );


           

            $amount_interval = $request->from_amount;//$request->from_amount.'-'.$request->to_amount;
            $amount_limit    = $request->to_amount; 
            $amount_interval = str_replace(',', '', $amount_interval);
            $amount_limit    = str_replace(',', '', $amount_limit);
            $insert = new Fdr();
            $insert->tenure_of_term_deposit=$request->tenure;
            $insert->existing_rates=$request->rate;
            $insert->amount_interval=$amount_interval;
            $insert->amount_limit=$amount_limit;
            $insert->isWaitingApproval=1;
            $insert->initiator_id = Auth::user()->getAuthIdentifier();
            $insert->save();
			dd('save is successful');

            if ($insert == true) {

                $new_details = Fdr::where('id',$insert->id)->get();

                //Audit FDR
                $request['user_id']=Auth::user()->getAuthIdentifier();
                $request['module']="IB";
                $request['action']="Store Fdr";
                $request['action_time']=now();
                $request['reason']="NULL";
                $request['old_details']="NULL";
                $request['new_details']=$new_details;

                $log = new Helper();
                $notification = 'Fixed Deposit Rate Added Successfully!';
                $color = 'success';
                return $log->auditTrack($request,$notification,$color);
            } else {
            $notification = 'Oops something went wrong!';
            $color = 'danger';
            }

            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);


   }

   public function updateFdr(Request $request,$id)
   {
        $request->validate(
            [
                'tenure'=>'required',
                'rate'=>'required',
                'amount_interval'=>'required',
            ]
        );

        $amount_interval = $request->amount_interval;
        $amount_limit = str_replace(',', '', $request->amount_limit);

        $old_details = Fdr::where('id',$id)->get()[0];
        $update = Fdr::where('id',$id)
        ->update(
            [
                'tenure_of_term_deposit'=>$request->tenure,
                'existing_rates'=>$request->rate,
                'amount_interval'=>$amount_interval,
                'amount_limit'=>$amount_limit,
                'isWaitingApproval'=>'1',
                'initiator_id'=>Auth::user()->id,
                'approver_id'=>'0'
            ]
        );




        if ($update == true) {
            //Audit trail
            $new_details = Fdr::where('id',$id)->get()[0];
            $request['user_id']=Auth::user()->getAuthIdentifier();
            $request['module']="IB";
            $request['action']="Update Fdr";
            $request['action_time']=now();
            $request['reason']="NULL";
            $request['old_details']=$old_details;
            $request['new_details']=$new_details;
            $request['source']=$request->getClientIp();

            $log = new Helper();

            $notification = 'Fixed Deposit Rate Updated Successfully!';
            $color = 'success';

            return $log->auditTrack($request,$notification,$color);
        } else {
            $notification = 'Oops something went wrong!';
            $color = 'danger';
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    //Institutions
    public function indexInst()
    {
        $institutions = IbInstitution::orderBy('id', 'DESC')->latest()->get();
        $accounttypes = IbAccountType::all();
        $branchs = IbBranch::all();
        $institution_types = IbInstitutionType::orderBy('id', 'ASC')->get();
        return view('ib.institution.index',compact('institutions', 'accounttypes', 'branchs', 'institution_types'));
    }
	 //Church Institutions
    public function indexChurchInst()
    {
        $churchinstitutions = IbChurchInstitution::orderBy('id', 'DESC')->latest()->get();

        return view('ib.parishes.index',compact('churchinstitutions'));
    }

    //manage the institution
    public function manageinst($id){
        $institution = IbInstitution::where('id',$id)->get()[0];
        $users = IbUser::where('institute_id',$id)->get();

        $sql ="SELECT * FROM tbl_institutions";
        $institutions = DB::connection('sqlsrv2')->select($sql);

        $sql ="SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);

        $excluded_roles = [1,2,4,5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();

        $options = OtpOption::all();
        $account_types = IbAccountType::all();

        return view('ib.institution.manage',compact('users','roles','types','institution', 'users','options','account_types'));
    }

    //institution accounts
    public function usersInst($id){
        $institution = IbInstitution::where('id',$id)->first();
        $users = IbUser::where('institute_id',$id)->latest()->get();
        $sql ="SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);
        $excluded_roles = [1,2,4,5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
        $actions = TblAdminActionLevel::all();
        $options = OtpOption::all();
        $account_types = IbAccountType::all();
        return view('ib.institution.users',compact('users','roles','types','institution', 'options','account_types', 'actions'));
    }

    //institution accounts
    public function accountsInst($id){
        $institution = IbInstitution::where('id',$id)->get()[0];
        $accounts = IbAccount::where('institution_id', $id)->latest()->get();
	
        $accounttypes = IbAccountType::all();
        $types = IbAccountType::all();
        $branchs = IbBranch::all();
        return view('ib.institution.accounts', compact('accounts', 'institution', 'accounttypes', 'branchs', 'types'));
    }

    public function activateInstitution(Request $request)
    {
        $status = $request->status_id;

        $request->validate([
            'institution_id'=>'required',
        ]);

        $update = IbInstitution::where('id',$request->institution_id)
            ->update([
                'status_id'=>$status,
                'initiator_id'=>Auth::user()->getAuthIdentifier(),
                'approver_id'=>'0',
                'isWaitingApproval'=>'1'
            ]);

        if ($update == true) {

            if($status=="1")
            {
                $notification = 'Institution activated for payment!';
                $action="Activation";
            }
            else{
                $notification = 'Institution de-activated for payment!';
                $action="Deactivation";
            }

            $color = 'success';

            //Audit trail
            $request['user_id']=Auth::user()->getAuthIdentifier();
            $request['module']="IB";
            $request['action']="Institution ".$action;
            $request['action_time']=now();
            $request['reason']="NULL";
            $request['old_details']="NULL";
            $request['new_details']="NUll";
            $log = new Helper();
            return $log->auditTrack($request,$notification,$color);
        } else {
            $notification = 'Oops something went wrong!';
            $color = 'danger';
        }

        return redirect('ib/institutions/index')->with('notification',$notification)->with('color',$color);
    }

    public function editInst($id)
    {
        $institution = IbInstitution::where('id',$id)->get()[0];
        $institution_types = IbInstitutionType::orderBy('id', 'ASC')->get();
        return view('ib.institution.edit',compact('institution', 'institution_types'));
    }

    public function updateInst(Request $request,$id)
    {
        $request->validate([
            'institute_name'=>'required',
            'address'=>'required',
            'description'=>'required',
            'payment_solution'=>'required'
        ]);
        try{
            $old_details = IbInstitution::where('id',$id)->get()[0];
            $update = IbInstitution::where('id',$id)
                ->update([
                    'institute_name'=>$request->institute_name,
                    'address'=>$request->address,
                    'description'=>$request->description,                    
                    'hasPaySolution'=>$request->payment_solution,
                    'institution_type'=>$request->institution_type,
                    'initiator_id'=>Auth::user()->getAuthIdentifier(),
                    'approver_id'=>'0',
                    'isWaitingApproval'=>'1'
                ]);

            if ($update == true) {
                $new_details = IbInstitution::where('id',$id)->get()[0];
                $notification = 'Institution updated Successfully!';
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

            return redirect('ib/institutions/index')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color = 'danger';
            return redirect('ib/institutions/index')->with('notification',$notification)->with('color',$color);
        }
    }

    public function createInst()
    {
        $accounts = IbAccount::all();

        return view('ib.institution.create',compact('accounts'));
    }

     public function reset(Request $request)
    {
        $password = mt_rand(12345678, 99999999);
        //dd($user);

        $user = IbUser::where('id', $request->user_id)->first();
        $user->password = bcrypt($password);
        $passwd_reset = $user->update();

        if($passwd_reset==true){
            if($user->otp_option == 'BOTH')
        {
            $body = "Dear ".$user->name.", your password to access Internet Banking is ".$password;
        $recipient=$user->email;
        $time = 10;
        Queue::later($time, new PasswordHitJob($body,$recipient));
            
            $this->sms($body, $user->mobile_phone);
        }
        elseif($user->otp_option == 'SMS')
        {
            $this->sms($body, $user->mobile_phone);
        }
        else
        {
           $body = "Dear ".$user->name.", your password to access Internet Banking is ".$password;
        $recipient=$user->email;
        $time = 10;
        Queue::later($time, new PasswordHitJob($body,$recipient)); 
        }
        

return back()->with(['notification' => 'Password reset successfully', 'color' => 'success']);
}
else{
return back()->with(['notification' => 'Password reset failed', 'color' => 'danger']);

}
    }

    public function sms($message, $phoneNumber) {
     $url = "http://172.29.1.133:8984/esb/send/sms";
        $client = new Client;
        $infoRequest = [
            "message" => $message,
            "phoneNumber"   => $phoneNumber
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);
        $smsInfo            = $res->getBody();
        $smsDetail          =  json_decode($smsInfo);

        if (isset($smsDetail->balance)) {
            $color = 'success';
        return redirect()->back()->with(['notification' => 'SMS sent successfully', 'color' => $color]); 
            
        }
       else
       {
        $color = 'danger';
            return redirect()->back()->with(['notification' => 'SMS FAILED', 'color' => $color]);
       }
}

    //Function to add cooperate user
    public function storeCooperateUser(Request $request)
    {

        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|string|email|max:255',
            'option_id'=>'required',
            'institute_id'=>'required',
            'action_id'=>'required',
        ]);

        //Condition to make sure user role is added
//        if($request->role_id==0)
//        {
//            $notification="User must have a role!";
//            $color="danger";
//            return redirect('ib/institutions/users/'.$request->institute_id)->with('notification',$notification)->with('color',$color);
//        }

        //Condition to make sure user otp option is added
        if(strlen($request->option_id)==1)
        {
            $notification="User must be assigned OTP Option!";
            $color="danger";
            return redirect('ib/institutions/users/'.$request->institute_id)->with('notification',$notification)->with('color',$color);
        }
        if(empty($request->action_id))
        {
            $notification="User must be assigned Action level!";
            $color="danger";
            return redirect('ib/institutions/users/'.$request->institute_id)->with('notification',$notification)->with('color',$color);
        }

        if (!(($request->name == trim($request->name) && strpos($request->name, ' ') !== false))) {
               return redirect()->back()->with(['notification' => 'Please provide the full name', 'color' => 'danger']);

        }

        //autogenerate the password that is to be sent to the user.
        $password = strtolower($request->password);
        $password = str_replace(" ", ".", $password);
        $password = $password.mt_rand(12345,99999);

        //$password="12345678";


//        $role = IBRole::where('id',$request->role_id)->get()[0];
          //send the sms 
    $mobile = preg_replace("/^0/", "255", $request->phone);
    
        $sub_name = explode(' ', $request->name);
        $first_name = $sub_name[0];
        $first_name_1 = substr($first_name, 0, 1);
        $first_name_1 = strtoupper($first_name_1);
        $full_name = $first_name_1.trim($sub_name[1].mt_rand(100,999));
        session()->put('login_name', $full_name);

        $insert = new IbUser();
        $insert->name = session()->get('login_name');
        $insert->email = $request->email;
        $insert->password = bcrypt($password);
        $insert->mobile_phone = $mobile;
        $insert->role_id = $request->role_id;
        $insert->otp_option = $request->option_id;
        $insert->action_id = $request->action_id;
        $insert->institute_id = $request->institute_id;
        $insert->initiator_id=Auth::user()->getAuthIdentifier();
        $insert->approver_id=0;
        $insert->isWaitingApproval=1;
        $insert->display_name = $request->name;
        $insert->save();



        if($insert==true)
        {
            
            $user_type = "App\\Models\\Web\\User";
            $insert_role_user = new IbUserRole();
            $insert_role_user->user_id = $insert->id;
            $insert_role_user->role_id = $insert->role_id;
            $insert_role_user->user_type = $user_type;
            $insert_role_user->save();

            $notification="User added successful!";
            $color="success";

            $new_details = IbUser::where('id',$insert->id)->get()[0];

            //Audit trail Store User
            $request['user_id']=Auth::user()->getAuthIdentifier();
            $request['module']="IB";
            $request['action']="Store User";
            $request['action_time']=now();
            $request['reason']="NULL";
            $request['old_details']="NULL";
            $request['new_details']=$new_details;
            $log = new Helper();
            return $log->auditTrack($request,$notification,$color);

        }
        else{
            $notification="Oops something went wrong!";
            $color="danger";
        }

        return redirect('ib/institutions/users/'.$request->institute_id)->with('notification',$notification)->with('color',$color);
    }

    public function usersInstApprove(Request $request, $id) {
    
        $user = IbUser::where('id',$id)->get()[0];
        $excluded_roles = [1,2,4,5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
        $sql ="SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);
        $options = OtpOption::all();
        $account_types = IbAccountType::all();
        return view('ib.institution.approve_user', compact('user', 'roles', 'types', 'options', 'account_types'));
    }
    public function approveInstUserAct(Request $request, $id) {
		
        $user_id  = Auth::id();
		$password  = mt_rand(12345678, 99999999);
        if ($request->reject == 'reject') {
    
        IbUser::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect('ib/institutions/users/'.$request->institute_id)->with(['notification' => 'User has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
			$user = IbUser::where(['id' => $id])->first();
			$enc_password = Hash::make($password);
          IbUser::where(['id' => $id])->update(['password' => $enc_password,'isWaitingApproval' => 0, 'approver_id' => $user_id]);
		  
		   	if($user){
			$username  = $user->name;
			$full_name = $user->display_name;
			$email = $user->email;
			$mobile = $user->mobile_phone;
			
           // if($user->otp_option == "SMS")
{
      // $username = session()->get('login_name');
        //send the sms 
        $msg  = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone, use $username for username on this link http://172.20.1.35:8001/";

        $this->sms($msg, $mobile);
}
 if($user->otp_option == "EMAIL")
{

       //$this->notification($request, $password);
	   $this->notification_new($email,$user->name,$full_name,$password);

}
if($user->otp_option == "BOTH")
{ 
         
        //$username = session()->get('login_name');   
        //send the sms 
        $msg  = "Your Internet Banking Password is $password, please keep it safe and dont share it with anyone, use $username for username on this link http://172.20.1.35:8001/";
      
        $this->sms($msg, $mobile);

       //$this->notification($request, $password);
	   $this->notification_new($email,$user->name,$full_name,$password);

}
			}
          return redirect('ib/institutions/users/'.$request->institute_id)->with(['notification' => 'User has been approved successfully', 'color' => 'success']);   
        }

    }

    public function editCooperaterUser($id)
    {

        $user = IbUser::where('id',$id)->get()[0];
        $excluded_roles = [1,2,4,5];
        $roles = IBRole::on('sqlsrv2')->whereNotIn('id', $excluded_roles)->get();
        $sql ="SELECT * FROM tbl_user_types";
        $types = DB::connection('sqlsrv2')->select($sql);
        $options = OtpOption::all();
        $account_types = IbAccountType::all();
        $actions = TblAdminActionLevel::all();

        return view("ib.institution.users_edit",compact('roles','types','user','options','account_types', 'actions'));

    }


    public function updateCooperateUser(Request $request,$id)
    {
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|string|email|max:255',
            'role_id'=>'required',
            'option_id'=>'required',
            'institute_id'=>'required'
        ]);

        $old_details = IbUser::where('id',$request->id)->get()[0];

        //Condition to make sure user role is added
        if($request->role_id==0)
        {
            $notification="User must have a role!";
            $color="danger";
            return redirect('/ib/institution/edit/'.$id)->with('notification',$notification)->with('color',$color);
        }

        //Condition to make sure user otp option is added
        if(!isset($request->option_id))
        {
            $notification="User must be assigned OTP Option!";
            $color="danger";
            return redirect('/ib/user/edit/'.$request->id)->with('notification',$notification)->with('color',$color);
        }

        $role = IBRole::where('id',$request->role_id)->get()[0];

        if($request->institute_id==0 && $role->name=="customer")
        {
            $update = IbUser::where('id',$request->id)
                ->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'mobile_phone'=>$request->phone,
                    'role_id'=>$request->role_id,
                    'otp_option'=>$request->option_id,
                    'initiator_id'=>Auth::user()->getAuthIdentifier(),
                    'approver_id'=>0,
                    'isWaitingApproval'=>1
                ]);
        }

        else {
            $update = IbUser::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile_phone' => $request->phone,
                    'role_id' => $request->role_id,
                    'otp_option' => $request->option_id,
                    'action_id' => $request->action_id,
                    'institute_id' => $request->institute_id,
                    'initiator_id'=>Auth::user()->getAuthIdentifier(),
                    'approver_id'=>0,
                    'isWaitingApproval'=>1
                ]);
        }

        if($update==true)
        {
            $notification="User update successful!";
            $color="success";
            $new_details = IbUser::where('id',$request->id)->get()[0];

            //Audit trail Update User
            $request['user_id']=Auth::user()->getAuthIdentifier();
            $request['module']="IB";
            $request['action']="Update User";
            $request['action_time']=now();
            $request['reason']="NULL";
            $request['old_details']=$old_details;
            $request['new_details']=$new_details;
            $log = new Helper();
            return $log->auditTrack($request,$notification,$color);
        }
        else{
            $notification="Oops something went wrong!";
            $color="danger";
        }

        return redirect('/ib/institutions/users/'.$request->institute_id)->with('notification',$notification)->with('color',$color);
    }
	
	public function notification_new($email,$name,$full_name,$password)
    {

        //$full_name = session()->get('login_name');
        $body = "Dear ".$full_name.", your password to access Internet Banking is ".$password." and username is ". $name.', visit this link https://ibanking.acbtz.com:8001/';
        $recipient=$email;
        $time = 30;
        Queue::later($time, new PasswordHitJob($body,$recipient));
    }

    public function notification(Request $request,$password)
    {

        $username = session()->get('login_name');
        $body = "Dear ".$request->name.", your password to access Internet Banking is ".$password." and the username is ".$username;
        $recipient=$request->email;
        $time = 10;


        Queue::later($time, new PasswordHitJob($body,$recipient));
    }

    public function storeInst(Request $request)
    {

        $request->validate([
            'institute_name'=>'required',
            'address'=>'required',
            'description'=>'required',
            'payment_solution'=>'required',
            'institution_type' =>'required',
        ]);
		
		//check whether account number exists before adding user 
			if (IbAccount::where('accountID', session()->get('accountID'))->exists()) {
				$this->notification .="This account already exists!";
				$color="danger";
                return redirect()->back()->with('notification',$this->notification)->with('color',$color);
			}
			
        try{
        
            $insert = new IbInstitution();
            $insert->institute_name=$request->institute_name;
            $insert->address=$request->address;
            $insert->description=$request->description;          
            $insert->hasPaySolution=$request->payment_solution;
            $insert->initiator_id =  Auth::user()->getAuthIdentifier();
            $insert->isWaitingApproval = 1;
            $insert->institution_type  = $request->institution_type;
            if ($insert->save()) {

                $this->insertAccount(session()->get('accountID'), $insert->id);
                $notification = 'Institution added Successfully!';
                $color = 'success';

                $new_details = IbInstitution::where('id',$insert->id)->get()[0];
                $request['user_id']=Auth::user()->getAuthIdentifier();
                $request['module']="IB";
                $request['action']="Update User";
                $request['action_time']=now();
                $request['reason']="NULL";
                $request['old_details']="NULL";
                $request['new_details']=$new_details;
                $log = new Helper();
                return $log->auditTrack($request,$notification,$color);


            } else {
                $notification = 'Oops something went wrong!';
                $color = 'danger';
            }
            return redirect('ib/institutions/index')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color = 'danger';
            return redirect('ib/institutions/index')->with('notification',$notification)->with('color',$color);
        }
    }
	
	public function storeChurchInst(Request $request)
    {
    
        $request->validate([
            'institute_name'=>'required',
            'institute_prefix'=>'required',
          
        ]);
        try{
        $verify_account = IbChurchInstitution::where('account_number', session()->get('accountID'))->orWhere('prefix', $request->institute_prefix)->first();
		if($verify_account){
			 $notification = 'Account Already exists!';
             $color = 'danger';
			 return redirect('ib/parishes/index')->with('notification',$notification)->with('color',$color);
		}
		else
		{
			$insert = new IbChurchInstitution();
            $insert->accountName=$request->institute_name;
            $insert->account_number=session()->get('accountID');
            $insert->prefix=$request->institute_prefix;          
            $insert->initiator_id =  Auth::user()->getAuthIdentifier();
            $insert->isWaitingApproval = 1;
			//$insert->accountName            =  session()->get('accountName');
			$insert->aCStatus               =  session()->get('aCStatus');
 
            if ($insert->save()) {

               // $this->insertAccount(session()->get('accountID'), $insert->id);
                $notification = 'Church Institution added Successfully!';
                $color = 'success';

                $new_details = IbChurchInstitution::where('id',$insert->id)->get()[0];
                $request['user_id']=Auth::user()->getAuthIdentifier();
                $request['module']="IB";
                $request['action']="Create Church Institution";
                $request['action_time']=now();
                $request['reason']="NULL";
                $request['old_details']="NULL";
                $request['new_details']=$new_details;
                $log = new Helper();
                return $log->auditTrack($request,$notification,$color);


            } else {
                $notification = 'Oops something went wrong!';
                $color = 'danger';
            }
            return redirect('ib/parishes/index')->with('notification',$notification)->with('color',$color);
		}
        }catch (\Exception $notification)
        {
            $color = 'danger';
            return redirect('ib/parishes/index')->with('notification',$notification)->with('color',$color);
        }
		
            
    }
	
	public function editChurchInst($id)
    {
        $institution = IbChurchInstitution::where('id',$id)->get()[0];
        return view('ib.parishes.edit',compact('institution'));
    }

    public function updateChurchInst(Request $request,$id)
    {
        $request->validate([
            'institute_name'=>'required',
            'account_number'=>'required',
            'institute_prefix'=>'required'
        ]);
        try{
            $old_details = IbChurchInstitution::where('id',$id)->get()[0];
            $update = IbChurchInstitution::where('id',$id)
                ->update([
                    'accountName'=>$request->institute_name,
                    'account_number'=>$request->account_number,
                    'prefix'=>$request->institute_prefix,                    
                    'initiator_id'=>Auth::user()->getAuthIdentifier(),
                    'approver_id'=>'0',
                    'isWaitingApproval'=>'1'
                ]);

            if ($update == true) {
                $new_details = IbChurchInstitution::where('id',$id)->get()[0];
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

            return redirect('ib/parishes/index')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color = 'danger';
            return redirect('ib/parishes/index')->with('notification',$notification)->with('color',$color);
        }
    }


    public function assignCooperateAccount(Request $request)
    {
		
        $request->validate([
            'account_number'=>'required',
            'account_type'=>'required',
            'branch_id'=>'required',
            'min_amount'=>'required',
            'max_amount'=>'required'
        ]);

        //Validate account number to be unique
        $account_number = IbAccount::select(['tbl_account.accountID'])
            ->where('accountID',$request->account_number)
            ->get();

        if(count($account_number)>0)
        {
            $notification="Account already exist!";
            $color="danger";
            //change redirect url by James
            return redirect('ib/institutions/accounts/' . $request->institution_id)->with('notification', $notification)->with('color', $color);
        }

        //Validate account type
        if($request->account_type==0)
        {
            $notification="Account Type is required";
            $color="danger";
            return redirect('ib/institutions/accounts/'.$request->institution_id)->with('notification',$notification)->with('color',$color);
        }

        //Validate branch id
        if(!isset($request->branch_id))
        {
            $notification="Branch Name is required";
            $color="danger";
            return redirect('ib/institutions/accounts/'.$request->institution_id)->with('notification',$notification)->with('color',$color);
        }
        $this->verifyAccount($request);

           $check_client_id = IbAccount::where(['clientId' => session()->get('clientId'), 'institution_id' => auth()->user()->institute_id])->get('clientId');
            //check    
           if (isset($check_client_id[0])) {
            $check_client_id = $check_client_id[0];
            $check_client_id = $check_client_id->clientId;
           }
          $responseCode =  session()->get('responseCode');

           /* dd( session()->get('clientId') .''.$check_client_id);*/
            if ($check_client_id != session()->get('clientId')  and ($responseCode == 200) and (!$check_client_id->isEmpty())) {
                return redirect()->back()->with(['notification' => 'Account does not belong to the customer!', 'color' => 'danger']);
            } else
        if (session()->get('responseCode_acc') == 200 && $check_client_id == session()->get('clientId')) {

               return $this->insertAccount($request->account_number,$request->institution_id);      
        }else  {

            return $this->insertAccount($request->account_number,$request->institution_id);
        }
      
    }

    

    public function insertAccount($account, $institution_id) {
	

        $responseCode = session()->get('responseCode_acc');
        $notification = 'Account added successfully!';
        $color        = 'success';

          try {
            $insert = new IbAccount();
			
            $insert->branchId            	=  session()->get('branchId');
            $insert->clientId               =  session()->get('clientId');
            $insert->clientName             =  session()->get('clientName');
            $insert->currencyID             =  session()->get('currencyID');
            $insert->productID              =  session()->get('productID');
            $insert->productName            =  session()->get('accountCategory');
            $insert->accountID              =  $account;
            $insert->accountName            =  session()->get('clientName');
            $insert->address                =  session()->get('address');
            $insert->city                   =  session()->get('city');
            $insert->countryID              =  session()->get('countryID');
            $insert->countryName            =  session()->get('countryName');
            $insert->mobile                 =  session()->get('phone');
            $insert->emailID                =  session()->get('emailAddress');
            $insert->aCStatus               =  'Active';
            $insert->createdOn              =  session()->get('createdOn');
            $insert->updateCount            =  session()->get('updateCount');
            $insert->branchName             =  session()->get('branchName');
            $insert->minAmount             	=  session()->get('minAmount');
            $insert->maxAmount             	=  session()->get('maxAmount');
            //$insert->balance                =  $this->balanceCheck(session()->get('accountID'));
           // $insert->user_id                = Auth::id();
            $insert->institution_id         = $institution_id;
            $insert->initiator_id           = Auth::user()->getAuthIdentifier();
            $insert->isWaitingApproval      = 1;
            $check_client_id = IbAccount::where(['clientId' => $insert->clientId, 'institution_id' => $institution_id])->get('clientId');
            //check    
           if (isset($check_client_id[0])) {
            $check_client_id = $check_client_id[0];
            $check_client_id = $check_client_id->clientId;
           }
           
            if ($check_client_id != $insert->clientId  and ($responseCode == 200) and (!$check_client_id->isEmpty())) {
                return redirect()->back()->with(['notification' => 'Account does not belong to the customer!', 'color' => 'danger']);
            } else if ($responseCode == 200) {
                $color = 'success';
                $insert->save();
            } else if ($responseCode == 100) {
             $color              = 'danger';          
             $this->notification = session()->get('responseMessage');
            }
            } catch(\Exception $e)

             {  
                $notification = $e->getMessage(); 
                $color              = 'danger';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
             }
            

      return redirect()->back()->with(['notification' => $notification, 'color' => $color]);

    }



public function verifyAccount(Request $request) {
	
        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";
        $client = new Client;
        $account = $request->account_number;
        $infoRequest = [
            "serviceType" => $serviceType,
            "sourceAccountId"   => $account
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);
		
       
		Log::info("ACCOUNT-LOOKUP-REQUEST: ".json_encode($infoRequest));
		
        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
		Log::info("ACCOUNT-LOOKUP-RESPONSE: ".json_encode($accountDetail));
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;
      

        if($responseCode == 200) 
		{
			if ($accountDetail->accountCategory == 'BUSINESS'){
				$clientName  =  $accountDetail->firstName;
			}else{
				$lastName	=  $accountDetail->lastName;
				$clientName =  $accountDetail->firstName.' '.$lastName;
			}
			
			session()->put('responseCode_acc', $responseCode);
			$responseMessage        =  $accountDetail->responseMessage;
			
			
			$phoneNumber            =  $accountDetail->phoneNumber;
			$idType            		=  $accountDetail->idType;
			$idNumber            	=  $accountDetail->idNumber;
			$accountCategory 		=  $accountDetail->accountCategory;
			$productName            =  $accountDetail->accountCategory;
			$clientId               =  $accountDetail->customerNumber;
			$minAmount  			= $request->min_amount;
			$maxAmount  			= $request->max_amount;
			$branchId               = $request->branch_id;
		   /*$transactionTimestamp   =  $accountDetail->transactionTimestamp;
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
			session()->put('branchName', $branchName);*/
			
			session()->put('minAmount', $minAmount);
			session()->put('branchId', $branchId);
			session()->put('maxAmount', $maxAmount);
			session()->put('clientId', $clientId);
			session()->put('responseMessage', $responseMessage);  
			session()->put('clientName', $clientName);
			session()->put('accountName', $clientName); 
			session()->put('phone', $phoneNumber);
			session()->put('id_type', $idType);
			session()->put('id_number', $idNumber);
			session()->put('accountCategory', $accountCategory);
			session()->put('accountID', $request->account_number);
		}
		else
		{
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
	
    public function insertAccount2($account,$institution_id) {

        $url = "http://172.29.1.133:8984/esb/request/process/ib";
        $serviceType = "INFO";
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
            $notification =  "Account could not be added!";
            $color = "danger";
            return redirect('ib/institutions/accounts/'.$institution_id)->with('notification',$notification)->with('color',$color);
        }

        if($responseCode == 200) {

            $insert = new IbAccount();
            $insert->branchId               =  $accountDetail->branchId;
            $insert->clientId               =  $accountDetail->clientId;
            $insert->clientName             =  $accountDetail->clientName;
            $insert->currencyID             =  $accountDetail->currencyID;
            $insert->productID              =  $accountDetail->productID;
            $insert->productName            =  $accountDetail->productName;
            $insert->accountID              =  $accountDetail->accountID;
            $insert->accountName            =  $accountDetail->accountName;
            $insert->address                =  $accountDetail->address;
            $insert->city                   =  $accountDetail->city;
            $insert->countryID              =  $accountDetail->countryID;
            $insert->countryName            =  $accountDetail->countryName;
            $insert->mobile                 =  $accountDetail->mobile;
            $insert->emailID                =  $accountDetail->emailID;
            $insert->aCStatus               =  $accountDetail->aCStatus;
            $insert->createdOn              =  $accountDetail->createdOn;
            $insert->updateCount            =  $accountDetail->updateCount;
            $insert->branchName             =  $accountDetail->branchName;
            $insert->balance                =  $this->balanceCheck($accountDetail->accountID);
            $insert->institution_id         =  $institution_id;
            $insert->save();

            if($insert==true)

            {
                $notification="Account added successful!";
                $color="success";
                return redirect('ib/institutions/accounts/'.$institution_id)->with('notification',$notification)->with('color',$color);
            }else{
                $notification="Account addition failed!";
                $color="danger";
                return redirect('ib/institutions/accounts/'.$institution_id)->with('notification',$notification)->with('color',$color);
            }


        }

    }

    public function balanceCheck($accountNumber)
    {
        $url = "http://172.20.1.37:8984/mkombozi/request/process/ib";
        $client = new Client;
        $transaction_id = mt_rand(123456789,999999999);
        $infoRequest = [
            "serviceType"=> "BALANCE",
            "serviceAccountId"=> "",
            "charge"=> "0",
            "transactionId"=> $transaction_id,
            "channelType"=> "IB",
            "accountID"=> $accountNumber
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

            return $accountDetail->balance;
        }


    }


    //Payments and Services for institutions
    public function indexInstitutionPayments($id)
    {
        $institution = IbInstitution::all();
        $payments = IbInstitutionPayment::all();
        return view('ib.institution.create_payment', compact('payments', 'institution'));
    }

    public function indexInstitutionServices($id)
    {
        $institution = IbInstitution::where('id', $id)->get()[0];
        //$servicetypes = IBTransactionType::all();
        $services = IbInstitutionService::where('institution_id', $id)->latest()->get();
        return view('ib.institution.create_service', compact('services', 'institution'));
    }

    public function editInstitutionPayment($id)
    {
        return view('ib.institution.edit_payment');
    }

    public function editInstitutionService($id)
    {
        return view('ib.institution.edit_service');
    }


    public function storeInstitutionService(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'service_type_id'=>'required',
            'minimum_amount'=>'required',
            'code'=>'required',
            'institution_id'=>'required'
        ]);

        $insert = IbInstitutionService::insert([
            'name'=>$request->name,
            'service_type_id'=>$request->service_type_id,
            'minimum_amount'=>$request->minimum_amount,
            'code'=>$request->code,
            'institution_id'=>$request->institution_id,
            'initiator_id' =>  Auth::user()->getAuthIdentifier()
        ]);

        if($insert==true)
        {
            $notification="Service store successfully";
            $color="success";
        }
        else{
            $notification="Service was not stored!";
            $color="danger";
        }

        return redirect('ib/institutions/services')->with('notification',$notification)->with('color',$color);
    }

    public function storeInstitutionPayment(Request $request)
    {
        $request->validate([
            'tbl_institution_payer_id'=>'required',
            'institute_id'=>'required',
            'amount'=>'required',
            'status'=>'required',
            'service_id'=>'required'
        ]);

        $insert = IbInstitutionPayment::insert([
            'tbl_institution_payer_id'=>$request->tbl_institution_payer_id,
            'institute_id'=>$request->institute_id,
            'amount'=>$request->amount,
            'status'=>$request->status,
            'service_id'=>$request->service_id,
            'initiator_id'=> Auth::user()->getAuthIdentifier()
        ]);

        if($insert==true)
        {
            $notification="Payment store successfully";
            $color="success";
        }
        else{
            $notification="Payment was not stored!";
            $color="danger";
        }

        return redirect('ib/institutions/payments')->with('notification',$notification)->with('color',$color);
    }

    public function updateInstitutionService(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'service_type_id'=>'required',
            'minimum_amount'=>'required',
            'code'=>'required',
            'institution_id'=>'required'
        ]);

        $insert = IbInstitutionService::where('id',$request->id)->update([
            'name'=>$request->name,
            'service_type_id'=>$request->service_type_id,
            'minimum_amount'=>$request->minimum_amount,
            'code'=>$request->code,
            'institution_id'=>$request->institution_id
        ]);

        if($insert==true)
        {
            $notification="Service updated successfully";
            $color="success";
        }
        else{
            $notification="Service was not stored!";
            $color="danger";
        }

        return redirect('ib/institutions/services')->with('notification',$notification)->with('color',$color);
    }



    public function updateInstitutionPayment(Request $request)
    {
        $request->validate([
            'tbl_institution_payer_id'=>'required',
            'institute_id'=>'required',
            'amount'=>'required',
            'status'=>'required',
            'service_id'=>'required'
        ]);

        $insert = IbInstitutionPayment::where('id',$request->id)->update([
            'tbl_institution_payer_id'=>$request->tbl_institution_payer_id,
            'institute_id'=>$request->institute_id,
            'amount'=>$request->amount,
            'status'=>$request->status,
            'service_id'=>$request->service_id
        ]);

        if($insert==true)
        {
            $notification="Payment store successfully";
            $color="success";
        }
        else{
            $notification="Payment was not stored!";
            $color="danger";
        }

        return redirect('ib/institutions/payments')->with('notification',$notification)->with('color',$color);
    }

    public function approveInstitution(Request $request, $id) {
    
        $institution = IbInstitution::findOrFail($id);
        $institution_types = IbInstitutionType::orderBy('id', 'ASC')->get();
        return view('ib.institution.approve_institution', compact('institution', 'institution_types'));
    }
    public function approveInstitutionAct(Request $request, $id) {
        $user_id  = Auth::id();
		
        if ($request->reject == 'reject') {
    
        IbInstitution::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.institutions_index')->with(['notification' => 'Institution has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbInstitution::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.institutions_index')->with(['notification' => 'Institution has been approved successfully', 'color' => 'success']);   
        }

        

    }
	
	public function approveChurchInstitution(Request $request, $id) {
    
        $institution = IbChurchInstitution::findOrFail($id);
		
        return view('ib.parishes.approve_institution', compact('institution'));
    }
    public function approveChurchInstitutionAct(Request $request, $id) {
        $user_id  = Auth::id();
        if ($request->reject == 'reject') {
    
        IbChurchInstitution::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect()->route('ib.churchinstitution_index')->with(['notification' => 'Institution has been rejected successfully', 'color' => 'success']);  
        }
        
        if ($request->approve == 'approve') {
          IbChurchInstitution::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect()->route('ib.churchinstitution_index')->with(['notification' => 'Institution has been approved successfully', 'color' => 'success']);   
        }
    }
	

       public function updateStopChequeRequest(Request $request){
        $this->validate($request,
            [
                'stop_cheque_id'=>'required',
                'status_id'=>'required'
            ]);

        $status = $request->status_id;
        $stop_cheque_id = $request->stop_cheque_id;

       // dd($status);

        try{
            $update = DB::connection('sqlsrv2')->table('tbl_stop_cheque_payments')
                ->where('id',$stop_cheque_id)
                ->update([
                    'status_id'=>$status,
                    'initiator_id'=>Auth::user()->getAuthIdentifier()
                ]);

            if($update==true)
            {
                $notification="Stop Cheque status updated successfully!";
                $color="success";
            }else{
                $notification="Oops something went wrong!";
                $color="success";
            }

            return redirect('ib/stop/cheque/request')->with('notification',$notification)->with('color',$color);

        }catch (\Exception $notification)
        {
            $color="danger";
            return redirect('ib/stop/cheque/request')->with('notification',$notification)->with('color',$color);
        }
    }
	
	public function statusUser(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $color = 'danger';
            return redirect()->back()->with('notification', $validator->errors()->first())->with('color', $color);
        }
		
		$user = IbUser::where('id',$request->user_id)->first();
		
		if ($user){
			//check if the user is blocked or not
			$status = $user->status;
			if($status == 'Active'){
				$notification = 'User is blocked successfully';
				$user->status ='Blocked';
				$user->save();
			}else{
				$notification = 'User is activated successfully';
				$user->status ='Active';
				$user->attempts = null;
				$user->save();
			}
			$color = 'success';
            return redirect()->back()->with('notification', $notification)->with('color', $color);
		}else{
			$color = 'danger';
			$notification = 'Action Failed, User not found';
            return redirect()->back()->with('notification', $notification)->with('color', $color);
		}
	}
}
