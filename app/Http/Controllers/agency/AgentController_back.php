<?php

namespace App\Http\Controllers\Agency;

use App\AbDistributionParties;
use App\Devices;
use App\CommissionDistribution;
use App\BankingAgentService;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\SMSController;
use App\Operator;
use App\BankServiceAccount;
use App\TblAgent;
use App\TblAgentChargeType;
use App\TblAgentFixedCharge;
use App\TblAgentIntervalCharge;
use App\TblAgentIntervalPercentCharge;
use App\TblAgentPercentCharge;
use App\TblAgentService;
use App\TblCharge;
use App\TblTransaction;
use App\TblAgentDevice;
use App\TblABInstitutionAccounts;
use App\TblABInstitutionAccountTypes;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helper\Helper;

//added by Evance Nganyaga
use App\AuditTrailLogs;
use Carbon\Carbon;


use App\TblABBankAccounts;
use App\TblABBankAccountTypes;
use App\AbStatus;

class AgentController extends Controller
{

    public function index()
    {
        $agents = TblAgent::orderBy('agent_id', 'DESC')->get();
        $sql = "SELECT * FROM tbl_agency_banking_agent_language";
        $languages = DB::connection("sqlsrv4")->select($sql);

        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();

        $sql = "SELECT * FROM tbl_agency_branches";
        $branches = DB::connection("sqlsrv4")->select($sql);

        //get all the agent services for menu
        $agentservices = TblAgentService::all();

        return view("agency.agent.index", compact('agents', 'languages', 'statuses', 'branches', 'agentservices'));
    }

    public function create()
    {
        $sql = "SELECT * FROM tbl_agency_banking_agent_language";
        $languages = DB::connection("sqlsrv4")->select($sql);
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $sql = "SELECT * FROM tbl_agency_branches";
        $branches = DB::connection("sqlsrv4")->select($sql);
        //$devices = Devices::on('sqlsrv4')->where('device_status', 0)->get();
        return view("agency.agent.create", compact('languages', 'statuses', 'branches'));
    }

    public function editAgent($id)
    {

        $agent = TblAgent::where('agent_id', $id)->get()[0];
        $sql = "SELECT * FROM tbl_agency_banking_agent_language";
        $languages = DB::connection("sqlsrv4")->select($sql);
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $sql = "SELECT * FROM tbl_agency_branches";
        $branches = DB::connection("sqlsrv4")->select($sql);
        $agentservices = TblAgentService::all();
        return view('agency.agent.edit', compact('agent', 'languages', 'statuses', 'branches', 'agentservices'));
    }

    public function updateAgent(Request $request, $id)
    {

    //implode agent menu
        $service_menu = implode("~", $request->agent_service);
        try {

            $update = TblAgent::where('agent_id', $id)->update([
			'agent_full_name' => $request->agent_name,
                        'agent_msisdn' => $request->mobile_number,
                        'agent_valid_id_number' => $request->agent_number,
                        'email' => $request->email,
                        'agent_business_license_number' => $request->business_licence,
                        'business_certificate_registration_number' => $request->registration_number,
                        'agent_address' => $request->address,
                        'agent_location' => $request->location,
                        'branch_id' => 2,
                        'agent_status' => $request->status_id,
                        'agent_float_limit' => $request->float_limit,
                        'agent_daily_limit' => $request->daily_limit,
                        'isWaitingApproval'=> 1,
                        'approver_id'=> 0
                    ]);
                //'agent_menu' => $service_menu
            if ($update == true) {
                $notification = 'Agent Updated Successfully!';
                $color = 'success';
                $log = new Helper();
                $log->auditTrail("Updated Agent","AB",$notification,'agency/users',Auth::user()->getAuthIdentifier());
            } else {
                $notification = 'Something went wrong!';
                $color = 'danger';
            }

            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {

            return redirect()->back()->with(['notification' => $e, 'color' => "danger"]);
        }
    }

    public function afterInsertion($notification, $color)
    {
        $sql = "SELECT * FROM tbl_agency_banking_agent_language";
        $languages = DB::connection("sqlsrv4")->select($sql);
        $sql = "SELECT * FROM tbl_status";
        $statuses = DB::connection("sqlsrv4")->select($sql);
        $sql = "SELECT * FROM tbl_agency_branches";
        $branches = DB::connection("sqlsrv4")->select($sql);
        return view("agency.agent.create", compact('languages', 'statuses', 'branches', 'notification', 'color'));
    }


    //added by Evance Nganyaga
    public function agentResetPIN(Request $r)
    {
        $agent_id = $r->agent_id;
        //get operator mobile number so as to send the new PIN
        $agent = TblAgent::where('agent_id', $agent_id)->get()[0];

        $agent_msisdn = $agent->agent_msisdn;

        $status = 2;
        $pin = mt_rand(1234, 9999);

        $reset = TblAgent::where('agent_id', $agent_id)
            ->update([
                'agent_password' => Hash::make($pin),
                'agent_status' => $status
            ]);
        if ($reset) {
            $notification = "Agent PIN reset successfully!";
            $color = "success";
            //send the sms
            $msg  = "PIN reset successfully, your new OTP PIN is $pin, use it to reset your account. If this was not you please report it.";
            // $sms = new SMSController();
            // $sms->send($agent_msisdn, $msg);
            $this->sms($msg, $agent_msisdn);
        } else {
            $notification = "Agent PIN reset unsuccessfull, please try again later!";
            $color = "danger";
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

	public function statusAgent(Request $r)
    {

        $agent_id = $r->agent_id;
        $status = $r->status;
        //status codes
        //1 - Activate, 2 - De Activate
        switch ($status) {
            case 1:
                $query = TblAgent::where('agent_id', $agent_id)
                    ->update([
                        'agent_status' => 1
                    ]);
                $notification = "Agent Activated successfully!";
                break;
            case 2:
                $query = TblAgent::where('agent_id', $agent_id)
                    ->update([
                        'agent_status' => 2
                    ]);
                $notification = "Agent Deactivated successfully!";
                break;
        }
        if ($query) {
            $color = "success";
        } else {
            $notification = "There was a problem updating agent status, please try again later!";
            $color = "danger";
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }


    public function store(Request $request)
    {

        $initiator = Auth::user()->id;
         if ($request->dual_control == "initiator") {
            $is_initiator = 1;
            $is_approver = 0;
        } else if ($request->dual_control == "approver") {
            $is_initiator = 0;
            $is_approver = 1;
        } else {
            $is_initiator = null;
            $is_approver = null;
        }
        //implode agent menu
        $service_menu = implode("~", $request->agent_service);
        $mobile = preg_replace("/^0/", "255", $request->mobile_number);
        //create a password
        $password  = mt_rand(1234, 9999);
        $latitude = round($request->latitude, 6);
        $longitude = round($request->longitude, 6);
	$date_registered = Carbon::now()->toDateTimeString();

        //Validate agent clientId to be unique
        $agent = TblAgent::where('clientId',$request->clientId)->get();

        if(count($agent)>0)
        {
            $notification="Agent already exist!";
            $color="danger";
            //change redirect url by James
            return redirect('agency/users')->with('notification', $notification)->with('color', $color);
        }
        else
        {
        $insert = TblAgent::insert([
            'agent_msisdn' => $mobile,
            'agent_language' => $request->language_id,
	    'agent_date_registered' => $date_registered,
            'agent_username' => $mobile,
            'agent_password' => $password,
            'agent_full_name' => $request->name,
            'email' => $request->email,
            'agent_business_license_number' => $request->business_licence,
            'business_certificate_registration_number' => $request->registration_number,
            'agent_status' => $request->status_id,
            'agent_reg_source' => $request->location,
            'agent_address' => $request->address,
            'agent_location' => $request->location,
            'agent_float_limit' => $request->float_limit,
            'agent_daily_limit' => $request->daily_limit,
            'is_initiator' => $is_initiator,
            'is_approver' => $is_approver,
            'branch_id' => 2,
            'branchName' => $request->branchName,
            'cbsbranchID' => $request->branchID,
            'initiator_id' => $initiator,
            'isWaitingApproval' => 1,
            'approver_id' => 0,
            'agent_menu' => $service_menu,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'clientId' => $request->clientId,

        ]);

        if ($insert == true) {
            //send the sms
            $msg  = "Your Agency Banking PIN is $password, please keep it safe and dont share it with anyone.";
        // $sms = new  SMSController();
        // $sms->send($mobile, $msg);
            $this->sms($msg, $mobile);
            $notification = 'Agent Added Successfully!';
            $color = 'success';
            //$log = new Helper();
            //$log->auditTrail("Added new Agent","AB",$notification,'agency/users',Auth::user()->getAuthIdentifier());
        } else {
            $notification = 'Something went wrong!';
            $color = 'danger';
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function approveAgent(Request $request, $id) {

        $agent = TblAgent::findOrFail($id);
        $sql = "SELECT * FROM tbl_agency_banking_agent_language";
        $languages = DB::connection("sqlsrv4")->select($sql);
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $sql = "SELECT * FROM tbl_agency_branches";
        $branches = DB::connection("sqlsrv4")->select($sql);
        $agentservices = TblAgentService::all();
        return view('agency.agent.approve_agent', compact('agent', 'languages', 'branches', 'agentservices', 'statuses'));
    }
    public function approveAgentAct(Request $request, $id) {

        $user_id  = Auth::id();
        if ($request->reject == 'reject') {

        TblAgent::where(['agent_id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect('agency/users')->with(['notification' => 'Agent has been rejected successfully', 'color' => 'success']);
        }

        if ($request->approve == 'approve') {
          TblAgent::where(['agent_id' => $id])->update(['agent_status' => 1, 'isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect('agency/users')->with(['notification' => 'Agent has been approved successfully', 'color' => 'success']);
        }



    }

    public function getOperators($id)
    {
        $operators = Operator::where('agent_id', $id)->orderBy('operator_id', 'desc')->get();
        $agent = TblAgent::on('sqlsrv4')->select('agent_full_name')->find($id);
        $agent_name = $agent->agent_full_name;
        $agentservices = TblAgentService::all();
        //select only devices that are not assigned to an operator
        $devices = TblAgentDevice::where('agent_id', $id)
            ->whereNotIn(
                'device_id',
                Operator::select('device_id')->where('agent_id', $id)->get()->toArray()
            )
            ->get();
        return view('agency.operators.index', compact('operators', 'agent_name', 'id', 'devices', 'agentservices'));
    }

    public function operatorCreateView($id)
    {
        $devices = TblAgentDevice::on('sqlsrv4')->where(['agent_id' => $id, 'status' => 0])->get();
        return view('agency.operators.create', compact('id', 'devices'));
    }

        public function approveOperator(Request $request, $id) {

       $operator = Operator::where('operator_id', $id)->first();
        $agent = TblAgent::find($operator->agent_id);
        $agent_name = $agent->agent_full_name;
        $agentservices = TblAgentService::all();
        //select only devices that are not assigned to an operator
        $devices = TblAgentDevice::where('agent_id', $operator->agent_id)
            ->whereNotIn(
                'device_id',
                Operator::select('device_id')->where('agent_id', $operator->agent_id)->get()->toArray()
            )
            ->get();
    return view('agency.operators.approve_operator', compact('operator', 'agent_name', 'id', 'devices', 'agentservices'));
    }
    public function approveOperatorAct(Request $request, $id) {
        $user_id  = Auth::id();
         $operator = Operator::where('operator_id', $id)->first();
        if ($request->reject == 'reject') {

        Operator::where(['operator_id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect('agency/operators/'.$operator->agent_id)->with(['notification' => 'Operator has been rejected successfully', 'color' => 'success']);
        }

        if ($request->approve == 'approve') {
          Operator::where(['operator_id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect('agency/operators/'.$operator->agent_id)->with(['notification' => 'Operator has been approved successfully', 'color' => 'success']);
        }



    }

    public function deviceCreateView($id)
    {
        $devices = Devices::on('sqlsrv4')->where('device_status', 0)->get();
        return view('agency.devices.create', compact('id', 'devices'));
    }

    public function getDevices($id)
    {
        $devices = TblAgentDevice::orderBy('id', 'DESC')->where('agent_id', $id)->get();
        $agent = TblAgent::where('agent_id', $id)->get()[0];
        $unassigneddevices = Devices::where('device_status', 0)->get();
        //add agent devices and then add a link between the devices and the agent
        //get the agent accounts account type 1 is Trading, 2 is commssion
        $tradingacs = TblABBankAccounts::where('agent_id', $id)->where('account_type_id', '1')->where('account_status', '1')->get();
        $commisionacs = TblABBankAccounts::where('agent_id', $id)->where('account_type_id', '2')->where('account_status', '1')->get();
        $agentdevices = TblAgentDevice::where('agent_id', $id)->get();
        return view('agency.devices.index', compact('unassigneddevices', 'agent', 'devices', 'id', 'tradingacs', 'commisionacs'));
    }

     public function approveDevice(Request $request, $id) {

       $device = TblAgentDevice::findOrFail($id);
        $agent = TblAgent::where('agent_id', $device->agent_id)->get()[0];
        $unassigneddevices = Devices::where('device_status', 0)->get();
        $connecteddevice = Devices::where('device_id', $device->device_id)->first();
        //add agent devices and then add a link between the devices and the agent
        //get the agent accounts account type 1 is Trading, 2 is commssion
        $tradingacs = TblABBankAccounts::where('agent_id', $device->agent_id)->where('account_type_id', '1')->where('account_status', '1')->get();
        $commisionacs = TblABBankAccounts::where('agent_id', $device->agent_id)->where('account_type_id', '2')->where('account_status', '1')->get();
        $agentdevices = TblAgentDevice::where('agent_id', $device->agent_id)->get();
    return view('agency.devices.approve_device', compact('device', 'agent', 'unassigneddevices', 'tradingacs', 'commisionacs', 'connecteddevice'));
    }
    public function approveDeviceAct(Request $request, $id) {
        $user_id  = Auth::id();
         $device = TblAgentDevice::where('id', $id)->first();
        if ($request->reject == 'reject') {

        TblAgentDevice::where(['id' => $id])->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
        return redirect('agency/devices/'.$device->agent_id)->with(['notification' => 'Agent device has been rejected successfully', 'color' => 'success']);
        }

        if ($request->approve == 'approve') {
          TblAgentDevice::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
          return redirect('agency/devices/'.$device->agent_id)->with(['notification' => 'Agent device has been approved successfully', 'color' => 'success']);
        }



    }


    public function editDevice($id)
    {
    }

    //added by Evance Nganyaga
    public function resetOperatorPIN(Request $request)
    {
        $operator_id = $request->operator_id;
        //get operator mobile number so as to send the new PIN
        $operator = Operator::where('operator_id', $operator_id)->get()[0];
        $operator_msisdn = $operator->operator_msisdn;

        $pin = mt_rand(1234, 9999);
        $status = 2;
        $reset = Operator::where('operator_id', $operator_id)
            ->update([
                'operator_password' =>Hash::make($pin),
                'operator_status' => $status,
                'login_counts' => 0

            ]);

        if ($reset) {
            $notification = "Operator PIN reset successfully!";
            $color = "success";
            //send the sms
            $msg = "PIN reset successfully, your new PIN is $pin, if this was not you please report it here.";
            // $sms = new SMSController();
            // $sms->send($operator_msisdn, $msg);
            $this->sms($msg, $operator_msisdn);
        } else {
            $notification = "Operator PIN reset unsuccessfull, please try again later!";
            $color = "danger";
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    //added by Evance Nganyaga
    //this function activates or deactivates an operator based on the payload
    public function statusOperator(Request $r)
    {

        $operator_id = $r->operator_id;
        $status = $r->status;
        //status codes
        //1 - Activate, 2 - De Activate
        switch ($status) {
            case 1:
                $query = Operator::where('operator_id', $operator_id)
                    ->update([
                        'operator_status' => 1
                    ]);
                $notification = "Operator Activated successfully!";
                break;
            case 2:
                $query = Operator::where('operator_id', $operator_id)
                    ->update([
                        'operator_status' => 2
                    ]);
                $notification = "Operator Deactivated successfully!";
                break;
        }
        if ($query) {
            $color = "success";
        } else {
            $notification = "There was a problem updating operator status, please try again later!";
            $color = "danger";
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    //Added by Evance Nganyaga
    public function editOperator($id)
    {
        $operator = Operator::where('operator_id', $id)->get()[0];
        $agent_id = $operator->agent_id;
        $devices = TblAgentDevice::where('agent_id', $agent_id)->get();
        $agentservices = TblAgentService::all();
        return view('agency.operators.edit', compact('operator', 'devices', 'agentservices'));
    }

    public function storeOperator(Request $request, $id)
    {

        $request->validate([
            'full_name' => 'required',
            'phone' => 'required|min:10|max:13',
            'location' => 'required'
        ]);

        //implode agent menu
        $service_menu = implode("~", $request->agent_service);

        if ($request['device'] == 0) {
            return redirect()->back()->with(['notification' => 'Device required', 'color' => 'danger']);
        }


        //create a password
        $password  = mt_rand(1234, 9999);
        //send the sms

        $op_mobile= preg_replace("/^0/", "255", $request->phone);



        $operator = new Operator();
        $operator->operator_fullname = $request['full_name'];
        $operator->operator_msisdn = $op_mobile;
        $operator->location = $request['location'];
        $operator->device_id = $request['device'];
        $operator->agent_id = $id;
        $operator->operator_status = 1;
        $operator->operator_menu = $service_menu;
        $operator->operator_password = $password; //Hash::make(HelperController::generatePassword());
        $operator->initiator_id = Auth::id();
        $operator->isWaitingApproval = 1;
        $operator->approver_id = 0;
        $operator->save();
        $log = new Helper();
        $log->auditTrail("Added Operator","AB",'Operator added successfully','agency/operators',Auth::user()->getAuthIdentifier());

        $msg  = "Your Agency Banking PIN is $password, please keep it safe and dont share it with anyone.";
        // $sms = new SMSController();
        // $sms->send($op_mobile, $msg);
        $msg  = "Your Agency Banking PIN is $password, please keep it safe and dont share it with anyone.";
        $this->sms($msg, $op_mobile);


        return redirect()->back()->with(['notification' => 'Operator added successfully', 'color' => 'success']);
    }

    //added by Evance Nganyaga
    public function updateOperator(Request $request)
    {
        $operator_id = $request->operator_id;
        //implode agent menu
        $service_menu = implode("~", $request->agent_service);
        $update = Operator::where('operator_id', "=", $operator_id)
            ->update(
                [
                    'operator_fullname' => $request->full_name,
                    'operator_msisdn' => $request->phone,
                    'location' => $request->location,
                    'operator_menu' => $service_menu,
                    'isWaitingApproval' => 1,
                    'approver_id' => 0
                ]
            );

        if ($update == true) {
            $notification = 'Operator updated successfully!';
            $color = 'success';
            $log = new Helper();
            $log->auditTrail("Updated Operator","AB",$notification,'agency/operators/'.$request->agent_id,Auth::user()->getAuthIdentifier());
        } else {
            $notification = 'There was a problem trying to update the operator, please try again later.!';
            $color = 'danger';
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function deleteOperatorApproval(Request $request, $id) {

        $operator = Operator::where('operator_id', $id)->first();
        $agent_id = $operator->agent_id;
        $agent_name = TblAgent::where('agent_id', $agent_id)->first()->agent_full_name;
        $devices = TblAgentDevice::where('agent_id', $agent_id)->get();
        $agentservices = TblAgentService::all();
        return view('agency.operators.delete_operator_approval', compact('operator', 'devices', 'agentservices', 'agent_name'));
    }
    public function deleteOperator($id)
    {

       $user_id  = Auth::id();
       $agent_id = Operator::where('operator_id', $id)->first()->agent_id;
       Operator::where(['operator_id' => $id])->update(['isWaitingApproval' => 1, 'approver_id' => 0, 'deletedBy_id' => $user_id, 'isDeleted' => 1]);
        return redirect()->route('agency.operators', $agent_id)->with(['notification' => 'Operator delete request sent for approval', 'color' => 'success']);
    }

    public function deleteOperatorActApproval(Request $request, $id)
    {
        $user_id  = Auth::id();
        $agent_id = Operator::where('operator_id', $id)->first()->agent_id;
        if ($request->reject == 'reject') {

        Operator::where(['operator_id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDeleted' => 0]);
        return redirect()->route('agency.operators', $agent_id)->with(['notification' => 'Bank deleting has been rejected successfully', 'color' => 'success']);
        }

        if ($request->approve == 'approve') {
          Operator::where(['operator_id' => $id])->delete();
          return redirect()->route('agency.operators', $agent_id)->with(['notification' => 'Operator deleting has been approved successfully', 'color' => 'success']);
        }
    }

    public function storeDevice(Request $request, $id)
    {

        $old_device = Devices::where('device_id', $request['device'])->first();
        if (!$old_device) {
            return redirect()->back()->with(['notification' => 'Device not found', 'color' => 'danger']);
        }
        //check if the agent has submitted an account
        if ($request->trading_ac == 0 || $request->commision_ac == 0 || $request->trading_ac == null || $request->commision_ac == null) {
            return redirect()->back()->with(['notification' => 'Please specify agent device before assigning a device.', 'color' => 'danger']);
        }


        DB::beginTransaction();
        try {
            $old_device->device_status = 1;
            $old_device->trading_account_id = $request->trading_ac;
            $old_device->commision_account_id = $request->commision_ac;
            $old_device->save();

            $device = new TblAgentDevice();
            $device->setConnection('sqlsrv4');
            $device->agent_id = $id;
            $device->device_id = $request['device'];
            $device->status = 1;
            $device->initiator_id = Auth::id();
            $device->isWaitingApproval = 1;
            $device->save();

            DB::commit();
            $log = new Helper();
            $log->auditTrail("Assigned Device","AB",'Device asssigned to agent successfully','agency/devices',Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Device asssigned to agent successfully', 'color' => 'success']);
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with(['notification' => 'Device was not assigned to agent. Error:' . $ex, 'color' => 'danger']);
        }
    }

    //added by Evance Nganyaga
    public function updateDeviceStatus(Request $request)
    {
        //update the device status based on the status field.
        $s = $request->status;
        $agent_device_id = $request->agent_device_id;
        switch ($s) {
            case 1:
                //activate device
                $query = TblAgentDevice::where('id', $agent_device_id)
                    ->update([
                        'status' => 1
                    ]);
                $notification = "Device activated successfully!";
                $color = "success";
                break;

            case 2:
                //de activate device
                $query = TblAgentDevice::where('id', $agent_device_id)
                    ->update([
                        'status' => 2
                    ]);
                $notification = "Device de activated successfully!";
                $color = "success";
                break;

            case 3:
                //block device
                $query = TblAgentDevice::where('id', $agent_device_id)
                    ->update([
                        'status' => 3
                    ]);
                $notification = "Device blocked successfully!";
                $color = "success";
                $log = new Helper();
                $log->auditTrail("Blocked Device","AB",$notification,'agency/devices',Auth::user()->getAuthIdentifier());
                break;

            case 4:
                //suspend device
                $query = TblAgentDevice::where('id', $agent_device_id)
                    ->update([
                        'status' => 4
                    ]);
                $notification = "Device suspended successfully!";
                $color = "success";
                break;

            case 0:
                //un assign device
                $query = TblAgentDevice::where('id', $agent_device_id)->delete();
                if ($query) {
                    //remove the status of the device
                    $old_device = Devices::where('device_id', $request['device_id'])->first();
                    $old_device->device_status = 0;
                    $old_device->trading_account_id = null;
                    $old_device->commision_account_id = null;
                    $old_device->save();
                    //remove the device from operators
                    $operator_update = Operator::where('device_id', $request->device_id)
                        ->update([
                            'operator_status' => 4
                        ]);
                    if ($operator_update) {
                        //add code here :D
                    } else {
                        //add code here
                    }
                }
                $notification = "Device un assigned successfully!";
                $color = "success";
                break;
        }
        if ($query == TRUE) {
            //query has been executed successfully
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } else {
            return redirect()->back()->with(['notification' => "Device status update failed, please try again.", 'color' => "danger"]);
        }
    }

    public function storeCommission(Request $request)
    {
        $uid =  Auth::user()->id;
        //updated by Evance Nganyaga
        $request->validate([
            'service' => 'required'
        ]);

        //add the three rate values
        $bank_rate = $request->bank_rate;
        $agent_rate = $request->agent_rate;
        $third_party_rate = $request->third_party_rate;
        $rate_sum =  $bank_rate + $agent_rate + $third_party_rate;




        //validate the rate distribution
        if ($rate_sum > 100) {
            //the rate has exceeded 100 %
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not exceed 100%.', 'color' => 'danger']);
        } else if ($rate_sum <= 0) {
            //there is no rate less than 0
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not be 0%.', 'color' => 'danger']);
        } else if ($rate_sum < 100) {
            //total rate must be 100%
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not be less than 100%.', 'color' => 'danger']);
        }

        if (!isset($request['service'])) {
            return redirect()->back()->with(['notification' => 'Service required', 'color' => 'danger']);
        } else {
            //check to see if the service has existing fields
            $flag = CommissionDistribution::where('service_name_id', $request->service)->get();
            if (count($flag) > 0) {
                //there is existing record
                return redirect()->back()->with(['notification' => 'Duplicate commision distributions are not allowed!', 'color' => 'danger']);
            }
        }


        $commission = new CommissionDistribution();
        $commission->service_name_id = $request['service'];

        $commission->bank_rate_value = $bank_rate;
        $commission->agent_rate_value = $agent_rate;
        $commission->third_parties = $third_party_rate;
        $commission->initiator_id = $uid;
        $commission->approver_id = 0;
        $commission->isWaitingApproval = 1;

        $commission->save();
        $log = new Helper();
        $log->auditTrail("Added Commission","AB",'Commission added successfully','agency/agentcommissions',Auth::user()->getAuthIdentifier());
        return redirect()->back()->with(['notification' => 'Commission added successfully', 'color' => 'success']);
    }



    public function editCommission($id)
    {
        $services = BankingAgentService::all();
        $commission = CommissionDistribution::where('commision_id', $id)->get()[0];

        return view('agency.commissions.edit', compact('commission'));
    }

    public function updateCommission(Request $request)
    {
        $uid =  Auth::user()->id;
        //add the three rate values
        $bank_rate = $request->bank_rate;
        $agent_rate = $request->agent_rate;
        $third_party_rate = $request->third_party_rate;
        $rate_sum =  $bank_rate + $agent_rate + $third_party_rate;

        //validate the rate distribution
        if ($rate_sum > 100) {
            //the rate has exceeded 100 %
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not exceed 100%.', 'color' => 'danger']);
        } else if ($rate_sum <= 0) {
            //there is no rate less than 0
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not be 0%.', 'color' => 'danger']);
        } else if ($rate_sum < 100) {
            //total rate must be 100%
            return redirect()->back()->with(['notification' => 'The sum of the distribution rate can not be less than 100%.', 'color' => 'danger']);
        }


        $commission = CommissionDistribution::where('commision_id', $request->id)
            ->update([
                'bank_rate_value' => $bank_rate,
                'agent_rate_value' => $agent_rate,
                'third_parties' => $third_party_rate,
                'isWaitingApproval' => 1,
                'approver_id' => 0
            ]);

        if ($commission == true) {
            return redirect()->back()->with(['notification' => 'Commission distribution updated successfully', 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Sorry there was a problem trying to updated this commission distribution.', 'color' => 'danger']);
        }
    }

    public function createCommission()
    {
        $services = BankingAgentService::on('sqlsrv4')->get();
        return view('agency.commissions.create', compact('services'));
    }

    public function getCommissions()
    {
        $services = BankingAgentService::all();
        $parties = AbDistributionParties::all();
        $commissions = CommissionDistribution::orderBy('commision_id', 'DESC')->get();

        return view('agency.commissions.commissions', compact('commissions', 'services', 'parties'));
    }

    public function getTransactions()
    {

        $transactions = TblTransaction::whereNotIn('trxn_name',['BALANCE_INQUIRY'])
            ->orderBy('txn_id', 'DESC')
            ->get();

        return view('agency.transactions.transactions', compact('transactions'));
    }

    public function viewTransaction($txn_id)
    {
        $transaction = TblTransaction::where('txn_id', $txn_id)->get()[0];



        return view('agency.transactions.view', compact('transaction'));
    }

    public function reverseTransaction(Request $request)
    {
    $url = "http://172.20.1.37:8984/mkombozi/request/process/ag";
        $serviceType = "REVERSAL";
        $client = new Client;
        $account = $request->serviceAccountId;
        $infoRequest = [
            "serviceType" => $serviceType,
	    "serviceAccountId" => $account,
	    "mobile" => $request->mobile,
	    "charge" => $request->charge,
	    "transactionId" => $request->transactionId,
            "channelType" => "AB",
            "accountID"   => $request->accountID,
	    "trxAmount" => $request->trxAmount,
	    "trxnDescription" => $request->trxnDescription,
            "isCard" => "false",
            "pinBlock" => "",
            "track2Data" => "",
            "destinationAccountId" => ""
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
	if($responseMessage == "SUCCCESS")
{
$responseMessage = "Transaction with ID ".$transactionId." succesfully reversed";
}
        session()->put('responseCode', $responseCode);
        session()->put('message', $responseMessage);
        session()->put('transactionTimestamp', $transactionTimestamp);
        session()->put('transactionId', $transactionId);
         return redirect()->back();

    }


    public function getAccountServices()
    {
        $accounts = BankServiceAccount::on('sqlsrv4')->orderBy('bank_account_id', 'DESC')->get();
        $services = TblAgentService::all();
        $auditlogs = AuditTrailLogs::all();
        return view('agency.account_services.accounts', compact('services', 'accounts', 'auditlogs'));
    }

    //added by Evance Nganyaga
    public function editAccountServices($id)
    {
        $account = BankServiceAccount::on('sqlsrv4')->where('bank_account_id', $id)->get()[0];
        return view('agency.account_services.edit', compact('id', 'account'));
    }

    //added by Evance Nganyaga
    public function updateAccountService(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $account = BankServiceAccount::where('bank_account_id', $request->id)
            ->update([
                'colection_account' => $request->colection_account,
                'disbursement_account' => $request->disbursement_account,
                'agency_commision_account' => $request->agency_commision_account,
                'agency_payable_commision_account' => $request->agency_payable_commision_account,
                'agency_expenses_deposit' => $request->agency_expenses_deposit,
                'agency_deposit_commision_account' => $request->agency_deposit_commision_account
            ]);
        if ($account) {
            $log = new Helper();
            $log->auditTrail("Updated Service Account","AB",'Added Service Account','agency/account/service',Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Service Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Service Account updated un successfully!', 'color' => 'danger']);
        }
    }

    public function editAccountView($id, $account)
    {
        return view('agency.account_services.edit', compact('id', 'account'));
    }

    public function editAccountService(Request $request, $id)
    {
        $account = BankServiceAccount::on('sqlsrv4')->find($id);
        if ($account) {
            $account->bank_account = $request['account'];
            $account->save();
            return redirect()->back()->with(['notification' => 'Account successfully changed', 'color' => 'success', 'account' => $account->bank_account]);
        }
        return redirect()->back()->with(['notification' => 'Account does not exist', 'color' => 'danger']);
    }

    public function editCommissionView($id, $service_id, $name, $value)
    {
        $services = BankingAgentService::on('sqlsrv4')->get();
        return view('agency.commissions.edit', compact('id', 'service_id', 'name', 'value', 'services'));
    }


    public function createAccountService()
    {
        return view('agency.account_services.create');
    }

    //index to add accounts to an agent
    public function accountCreateView($id)
    {
        $agent = TblAgent::where('agent_id', $id)->get()[0];
        $accounts = TblABBankAccounts::orderBy('account_id', 'DESC')->where('agent_id', $id)->get();
        $accounttypes = TblABBankAccountTypes::all();
        $accountstatuses = AbStatus::all();
        return view('agency.agent.index_accounts', compact('agent', 'accounts', 'accounttypes', 'accountstatuses'));
    }

    //change
    public function storeAgentAccount(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required',
            'account_status' => 'required',
            'agent_id' => 'required'
        ]);


        //Validate account number to be unique
        $account_number = TblABBankAccounts::select(['tbl_agency_banking_agent_bank_accounts.bank_account'])
            ->where('bank_account',$request->account_number)
            ->get();

        if(count($account_number)>0)
        {
            $notification="Account already exist!";
            $color="danger";
            //change redirect url by James
            return redirect('agency/accounts/' . $request->agent_id)->with('notification', $notification)->with('color', $color);
        }

        //Validate account type
        if($request->account_type_id==0)
        {
            $notification="Account Type is required";
            $color="danger";
            return redirect('agency/accounts/' . $request->agent_id)->with('notification',$notification)->with('color',$color);
        }


    	 //get agent clientId number in database to be compared with account clientId
        $agent = TblAgent::where('agent_id',$request->agent_id)->first();
        $this->verifyAccount($request);
        	if($agent->clientId != session()->get('clientId')){
        		$notification="Account does not belong to this agent";
            $color="danger";
            return redirect('agency/accounts/' . $request->agent_id)->with('notification',$notification)->with('color',$color);
        	}
           else{

           $responseCode =  session()->get('responseCode');
        if($responseCode == 200)
        {

        DB::beginTransaction();
        try {
            $account = new TblABBankAccounts();
            $account->bank_account = $request->account_number;
            $account->account_type_id = $request->account_type_id;
            $account->account_status = $request->account_status;
            $account->registration_status = 2;
            $account->initiator_id = $uid;
            $account->agent_id = $request->agent_id;
            $account->approver_id = 0;
            $account->isWaitingApproval = 1;
            $account->branchId               =  session()->get('branchId');
            $account->clientId               =  session()->get('clientId');
            $account->clientName             =  session()->get('clientName');
            $account->currencyID             =  session()->get('currencyID');
            $account->productID              =  session()->get('productID');
            $account->productName            =  session()->get('productName');
            $account->accountName            =  session()->get('accountName');
            $account->address                =  session()->get('address');
            $account->city                   =  session()->get('city');
            $account->countryID              =  session()->get('countryID');
            $account->countryName            =  session()->get('countryName');
            $account->mobile                 =  session()->get('mobile');
            $account->emailID                =  session()->get('emailID');
            $account->aCStatus               =  session()->get('aCStatus');
            $account->createdOn              =  session()->get('createdOn');
            $account->updateCount            =  session()->get('updateCount');
            $account->branchName             =  session()->get('branchName');
            $account->save();
            //DB:commit();
            $log = new Helper();
            $log->auditTrail("Added new Account","AB",'Added Account','agency/accounts/'. $request->agent_id,Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Account added successfully!', 'color' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['notification' => 'Account added un successfully! Error' . $e, 'color' => 'danger']);
        }
        }
        else
        {
          return redirect()->back()->with(['notification' => session()->get('responseMessage'), 'color' => 'danger']);
        }
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
        $account_number = TblABInstitutionAccounts::where('account_number',$request->account_number)
            ->where('account_type_id',$request->account_type_id)
            ->get();

        if(count($account_number)>0)
        {
            $notification="Account already exist!";
            $color="danger";
            //change redirect url by James
            return redirect('agency/institution_accounts')->with('notification', $notification)->with('color', $color);
        }

        //$this->verifyAccount($request);

        $account = new TblABInstitutionAccounts();

        $account->account_number = $request->account_number;
        $account->account_type_id = $request->account_type_id;
        $account->initiator_id = $uid;
        $account->save();

        $log = new Helper();
        $log->auditTrail("Added new Account","AB",'Added Account','agency/institution_accounts',Auth::user()->getAuthIdentifier());
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

        $approve = TblABInstitutionAccounts::where('id', $account_id)
            ->update([
                'approver_id' => $uid,
            ]);

        if ($approve == true) {
            return redirect()->back()->with(['notification' => $notification, 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Institution Account approved/disapproved unsuccessfully!', 'color' => 'danger']);
        }
    }

    public function editInstitutionAccount($id)
    {
        $account = TblABInstitutionAccounts::on('sqlsrv4')->where('id', $id)->first();
        $accounts = TblABInstitutionAccounts::on('sqlsrv4')->orderBy('id', 'DESC')->get();

        $account_types = TblABInstitutionAccountTypes::all();
        $auditlogs = AuditTrailLogs::all();
        return view('agency.account_services.edit_institution_accounts', compact('account_types', 'accounts','account', 'auditlogs'));

    }

    public function updateInstitutionAccount(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required',
            'id' => 'required'
        ]);

        $account = TblABInstitutionAccounts::where('id', $request->id)
            ->update([
                'account_number' => $request->account_number,
                'account_type_id' => $request->account_type_id,
                'approver_id' => null
            ]);

        if ($account) {
            $log = new Helper();
            $log->auditTrail("Updated Account","AB",'Account updated successfully!','agency/institutionaccounts/edit/'.$request->id,Auth::user()->getAuthIdentifier());
            return redirect('agency/institutionaccounts/edit/'.$request->id)->with(['notification' => 'Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect('agency/institutionaccounts/edit/'.$request->id)->with(['notification' => 'Account updated un successfully!', 'color' => 'danger']);
        }
    }

    //added by Evance Nganyaga
    public function approveAgentAccount(Request $r){
        $uid =  Auth::user()->id;
        $account_id = $r->account_id;
        $op = $r->op;
        if($op == 1){
            //approve
            $dual_status = 1;
            $notification = "Agent account approved successfully!";
        }else{
            //disapprove
            $dual_status = 2;
            $notification = "Agent account approved unsuccessfully!";
        }

        $approve = TblABBankAccounts::where('account_id', $account_id)
            ->update([
                'registration_status' => $dual_status,
                'approver_id' => $uid,
                'isWaitingApproval' => 0
            ]);
        if ($approve == true) {
            return redirect()->back()->with(['notification' => $notification, 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Agent Account approved/disapproved unsuccessfully!', 'color' => 'danger']);
        }
    }

    //account edit
    public function editAgentAccount($id)
    {
        $account = TblABBankAccounts::where('account_id', $id)->get()[0];
        $accounttypes = TblABBankAccountTypes::all();
        $accountstatuses = AbStatus::all();
        return view('agency.agent.edit_accounts', compact('account', 'accounttypes', 'accountstatuses'));
    }

    //account update
    public function updateAgentAccount(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required',
            'account_status' => 'required'
        ]);

        $account = TblABBankAccounts::where('account_id', $request->account_id)
            ->update([
                'bank_account' => $request->account_number,
                'account_type_id' => $request->account_type_id,
                'account_status' => $request->account_status,
                'registration_status' => 2,
                'isWaitingApproval' => 1,
                'approver_id' => 0
            ]);

        if ($account) {
            $log = new Helper();
            $log->auditTrail("Updated Account","AB",'Account updated successfully!','agency/accounts/'.$request->agent_id,Auth::user()->getAuthIdentifier());
            // return redirect('agency/accounts/'.$request->agent_id)->with(['notification' => 'Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect('agency/accounts/'.$request->agent_id)->with(['notification' => 'Account updated un successfully!', 'color' => 'danger']);
        }
    }


    public function storeAccountService(Request $request)
    {
        $uid =  Auth::user()->id;
        $request->validate([
            'bank_service_ID' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $account = new BankServiceAccount();
            $account->bank_service_ID = $request->bank_service_ID;
            $account->colection_account = $request->colection_account;
            $account->disbursement_account = $request->disbursement_account;
            $account->agency_commision_account = $request->agency_commision_account;
            $account->agency_payable_commision_account = $request->agency_payable_commision_account;
            $account->agency_expenses_deposit = $request->agency_expenses_deposit;
            $account->agency_deposit_commision_account = $request->agency_deposit_commision_account;
            $account->initiator_id = $uid;
            $account->save();
            DB::commit();
            $log = new Helper();
            $log->auditTrail("Added Account","AB",'Added Account','agency/accounts',Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Account added successfully!', 'color' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['notification' => 'Account added un successfully! Error' . $e, 'color' => 'danger']);
        }
    }

    //added by Evance Nganyaga
    public function approveAccountService(Request $r)
    {
        $uid =  Auth::user()->id;
        $account_id = $r->account_id;
        $approve = BankServiceAccount::where('bank_account_id', $account_id)
            ->update([
                'approver_id' => $uid,
                'isWaitingApproval' => 0
            ]);
        if ($approve == true) {
            return redirect()->back()->with(['notification' => 'Account approved successfully!', 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Account approved unsuccessfully!', 'color' => 'danger']);
        }
    }

    public function approveAccount(Request $r)
    {
        $uid =  Auth::user()->id;
        $account_id = $r->account_id;
        $approve = TblABBankAccounts::where('account_id', $account_id)
            ->update([
                'approver_id' => $uid,
                'isWaitingApproval' => 0
            ]);
        if ($approve == true) {
            return redirect()->back()->with(['notification' => 'Account approved successfully!', 'color' => 'success']);
        } else {
            return redirect()->back()->with(['notification' => 'Account approved unsuccessfully!', 'color' => 'danger']);
        }
    }

    public function getCharges()
    {
        $services = TblCharge::all();

        return view('agency.charges.charges', compact('services'));
    }

    public function createCharges()
    {

        $services = TblAgentService::all();
        $types = TblAgentChargeType::all();

        return view('agency.charges.create', compact('services', 'types'));
    }

    public function storeCharges(Request $request)
    {

        $request->validate([
            'service_name' => 'required',
            'charge_type' => 'required'
        ]);

        $insert = TblCharge::insert([
            'service_id' => $request->service_name,
            'charge_type_id' => $request->charge_type
        ]);

        if ($insert == true) {
            $notification = 'Service Charge Added Successfully!';
            $color = 'success';
            $log = new Helper();
            $log->auditTrail("Added Service Charge","AB",$notification,'agency/charges',Auth::user()->getAuthIdentifier());
        } else {
            $notification = 'Oops something went wrong!';
            $color = 'danger';
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function editCharges($id)
    {
        $charge = TblCharge::where('charge_id', $id)->get()[0];
        $services = TblAgentService::all();
        $types = TblAgentChargeType::all();

        return view('agency.charges.edit', compact('charge', 'services', 'types'));
    }

    public function updateCharges(Request $request, $id)
    {
        try {
            $update = DB::connection('sqlsrv4')->table('tbl_agency_banking_charges')->where('charge_id', $id)->update(
                ['service_name' => $request->service_name, 'charge_amount' => $request->charge_amount]
            );

            if ($update == true) {
                $notification = 'Service Charge Updated Successfully!';
                $color = 'success';
                $log = new Helper();
                $log->auditTrail("Updated Service Charge","AB",$notification,'agency/charges',Auth::user()->getAuthIdentifier());
            } else {
                $notification = 'Oops something went wrong!';
                $color = 'danger';
            }
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => $color]);
        }
    }


    public function getInstitutionAccounts()
    {
        $accounts = TblABInstitutionAccounts::on('sqlsrv4')->orderBy('id', 'DESC')->get();
        $account_types = TblABInstitutionAccountTypes::all();
        $auditlogs = AuditTrailLogs::all();
        return view('agency.account_services.institution_accounts', compact('account_types', 'accounts', 'auditlogs'));
    }

     public function verifyAccount(Request $request) {
     $url = "http://172.20.1.37:8984/mkombozi/request/process/ag";
        $serviceType = "INFO";
        $client = new Client;
        $account = $request->account_number;
        $infoRequest = [
            "serviceType" => $serviceType,
            "accountID"   => $account,
            "isCard" => "false",
            "pinBlock" => "",
            "track2Data" => "",
            "destinationAccountId" => ""
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);
       try {
        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;

         if ($responseCode == '006') {
            session()->put('responseCode', $responseCode);
            session()->put('responseMessage', $responseMessage);
            $color = 'danger';
            return redirect()->back()->with(['notification' => 'FAIL', 'color' => $color]);
        }
        if ($responseCode == 100) {
            session()->put('responseCode', $responseCode);
            session()->put('responseMessage', $responseMessage);
            $color = 'danger';
            return redirect()->back()->with(['notification' => 'FAIL', 'color' => $color]);
        }

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

        session()->put('responseCode', $responseCode);
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
    }
} catch (\Exception $e) {

        return redirect()->back();
    }
        return redirect()->back();
}


public function sms($message, $phoneNumber) {
     $url = "172.20.1.37:8984/mkombozi/send/sms";
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
}
