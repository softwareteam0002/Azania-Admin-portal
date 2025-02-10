<?php

namespace App\Http\Controllers\Agency;

use App\AbDistributionParties;
use App\AbStatus;
use App\AuditTrailLogs;
use App\BankingAgentService;
use App\BankServiceAccount;
use App\CommissionDistribution;
use App\Devices;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\SmsJob;
use App\Operator;
use App\TblABBankAccounts;
use App\TblABBankAccountTypes;
use App\TblABInstitutionAccounts;
use App\TblABInstitutionAccountTypes;
use App\TblAgent;
use App\TblAgentChargeType;
use App\TblAgentDevice;
use App\TblAgentService;
use App\TblCharge;
use App\TblTransaction;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class AgentController extends Controller
{
    public function create()
    {
        $languages = DB::connection("sqlsrv4")->table('tbl_agency_banking_agent_language')->get();
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $branches = DB::connection("sqlsrv4")->table('tbl_agency_branches')->get();
        return view("agency.agent.create", compact('languages', 'statuses', 'branches'));
    }

    public function editAgent($id)
    {
        $agent = TblAgent::where('agent_id', $id)->first();
        $languages = DB::connection("sqlsrv4")->table('tbl_agency_banking_agent_language')->get();
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $branches = DB::connection("sqlsrv4")->table('tbl_agency_branches')->get();
        $agentservices = TblAgentService::all();
        $regions = DB::table('regions')->get();
        $districts = DB::table('districts')->get();
        return view('agency.agent.edit', compact('agent', 'languages', 'statuses', 'branches', 'agentservices', 'regions', 'districts'));
    }

    public function updateAgent(Request $request, $id)
    {
        //implode agent menu
        $service_menu = implode("~", $request->agent_service);
        try {
            $update = TblAgent::where('agent_id', $id)->update([
                'agent_valid_id_number' => $request->agent_number,
                'email' => $request->email,
                'business_licence_number' => $request->business_licence,
                'agent_tin_number' => $request->tin_number,
                'agent_language' => $request->language_id,
                'agent_address' => $request->address,
                'agent_location' => $request->location,
                'agent_status' => $request->status_id,
                'agent_float_limit' => $request->float_limit,
                'branchName' => $request->branchName,
                'agent_daily_limit' => $request->daily_limit,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'street' => $request->street,
                'area_famous_name' => $request->famous_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'agent_menu' => $service_menu,
                'isWaitingApproval' => 1,
                'approver_id' => 0
            ]);

            if ($update) {
                $notification = 'Agent Updated Successfully!';
                $color = 'success';
            } else {
                $notification = 'Something went wrong!';
                $color = 'danger';
            }

            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Update Agent', 'Agency Banking', $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Update Agent', 'Agency Banking', 'Exception Occurred', $request->ip());
            return redirect()->back()->with(['notification' => $e, 'color' => "danger"]);
        }
    }

    public function afterInsertion($notification, $color)
    {
        $languages = DB::connection("sqlsrv4")->table('tbl_agency_banking_agent_language')->get();
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $branches = DB::connection("sqlsrv4")->table('tbl_agency_branches')->get();
        return view("agency.agent.create", compact('languages', 'statuses', 'branches', 'notification', 'color'));
    }

    public function agentResetPIN(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'agent_id' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        if ($validate->fails()) {
            $notification = $validate->errors()->first();
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }

        try {
            $agent_id = $r->agent_id;

            $agent = TblAgent::where('agent_id', $agent_id)->first();

            if (!$agent) {
                $notification = 'Agent not found!';
                $color = 'danger';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }

            $agent_msisdn = $agent->agent_msisdn;

            $pin = random_int(1000, 9999);

            $reset = $agent->update([
                'agent_password' => Hash::make($pin),
                'agent_status' => 2
            ]);

            if ($reset) {
                $notification = "Agent PIN reset successfully!";
                $color = "success";
                $msg = "PIN reset successfully. Your new PIN is $pin. Use it to reset your account. If this was not you, please report it.";
                SmsJob::dispatch($msg, $agent_msisdn);
            } else {
                $notification = "Failed to reset agent PIN, please try again later!";
                $color = "danger";
            }
            $this->auditLog(Auth::user()->id, 'Agent PIN Reset', 'Agency Banking', $notification, $r->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->id, 'Agent PIN Reset', 'Agency Banking', 'Exception occurred', $r->ip());
            Log::error("Reset Agent PIN Exception: " . $e->getMessage());
            Log::error("Reset Agent PIN Exception: " . $e->getTraceAsString());
            $notification = "Failed to Reset Agent PIN, please try again later!";
            $color = "danger";
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function statusAgent(Request $r)
    {
        $r->validate([
            'agent_id' => ['required', 'string', 'regex:/^\d+$/'],
            'status' => ['required', 'string', 'regex:/^\d+$/', 'in:1,2'],
        ]);

        try {
            $agent_id = $r->agent_id;
            $status = $r->status;

            // Determine the status update
            $notification = $status == 1
                ? "Agent Activated successfully!"
                : "Agent Deactivated successfully!";

            $query = TblAgent::where('agent_id', $agent_id)->update(['agent_status' => $status]);

            if ($query) {
                $color = "success";
            } else {
                $notification = "There was a problem updating agent status. Please try again later.";
                $color = "danger";
            }
        } catch (\Exception $e) {
            $notification = "An error occurred while updating agent status: " . $e->getMessage();
            $color = "danger";
        }
        $this->auditLog(Auth::user()->id, 'Update Agent Status', 'Agency Banking', $notification, $r->ip());
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'dual_control' => 'required|string|in:initiator,approver',
            'agent_service' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['BI', 'DC', 'WC', 'FT', 'AO', 'AS', 'MS', 'UP', 'BW'];
                    foreach ($value as $item) {
                        if (!in_array($item, $allowedValues)) {
                            $fail("The {$attribute} contains an invalid item: {$item}.");
                        }
                    }
                },
            ],
            'mobile_number' => 'required|tanzanian_mobile',
            'language_id' => 'required|string|in:1,2',
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s().]+$/'],
            'email' => ['nullable', 'email'],
            'tin_number' => 'required|regex:/^\d{9}$/',
            'business_licence' => 'required|regex:/^\d{7}$/',
            'status_id' => 'required|string|in:1,2',
            'location' => 'required|regex:/^[a-zA-Z\s]+$/|max:30',
            'region' => 'required|regex:/^\d+$/',
            'district' => 'required|regex:/^\d+$/',
            'ward' => 'required|regex:/^[a-zA-Z\s]+$/|max:30',
            'street' => 'required|string|max:255',
            'famous_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:100',
            'float_limit' => 'required|numeric|min:0',
            'daily_limit' => 'required|numeric|min:0',
            'branchName' => 'required|regex:/^[a-zA-Z\s]+$/|max:30',
            'branchID' => 'nullable|integer',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'clientId' => ['nullable', 'string'],
        ], ['mobile_number.tanzanian_mobile' => 'Mobile Number is invalid!']);

        if ($validate->fails()) {
            $notification = $validate->errors()->first();
            $color = "danger";
            return back()->with(['notification' => $notification, 'color' => $color]);
        }

        //check email
        if (isset($request->email)) {
            $emailExists = TblAgent::where('email', $request->email)->exists();

            if ($emailExists) {
                $notification = "Email already exists!";
                $color = "danger";
                return back()->with(['notification' => $notification, 'color' => $color]);
            }
        }


        $initiator = Auth::user()->id;

        $is_initiator = $request->dual_control === "initiator" ? 1 : 0;
        $is_approver = $request->dual_control === "approver" ? 1 : 0;

        $service_menu = implode("~", $request->agent_service);
        $mobile = preg_replace(["/^0/", "/^\+/"], "255", $request->mobile_number);
        $password = random_int(1111, 9999);

        $latitude = round($request->latitude, 6);
        $longitude = round($request->longitude, 6);

        $date_registered = Carbon::now()->toDateTimeString();

        if (isset($request->clientId)) {
            //Validate agent clientId to be unique
            $agent = TblAgent::where('clientId', $request->clientId)->exists();
        } else {
            $agent = TblAgent::where('agent_msisdn', $mobile)->exists();
        }

        if ($agent) {
            $notification = "Agent already exist!";
            $color = "danger";
            return redirect('agency/users')->with('notification', $notification)->with('color', $color);
        }

        try {
            $insert = TblAgent::insert([
                'agent_msisdn' => $mobile,
                'agent_language' => $request->language_id,
                'agent_date_registered' => $date_registered,
                'agent_username' => $mobile,
                'agent_password' => Hash::make($password),
                'agent_full_name' => $request->name,
                'email' => $request->email,
                'agent_tin_number' => $request->tin_number,//TIN NUMBER,
                'agent_business_license_number' => $request->business_licence, //BUSINESS LICENCE,
                'business_certificate_registration_number' => $request->business_licence, //BUSINESS LICENCE,
                'agent_status' => $request->status_id,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'street' => $request->street,
                'area_famous_name' => $request->famous_name,
                'agent_address' => $request->address,
                'agent_location' => $request->location,
                'agent_float_limit' => $request->float_limit,
                'agent_daily_limit' => $request->daily_limit,
                'is_initiator' => $is_initiator,
                'is_approver' => $is_approver,
                'branch_id' => null,
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

            if ($insert) {
                $notification = 'Agent Added Successfully!';
                $color = 'success';
            } else {
                $notification = 'Failed to Add Agent!';
                $color = 'danger';
            }
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Added new Agent', 'Agency Banking', $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Added Agent', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::info("AGENT-STORE-EXCEPTION: " . $e->getMessage());
            Log::info("AGENT-STORE-EXCEPTION: " . $e->getTraceAsString());
            $notification = 'Something went wrong!';
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function approveAgent(Request $request, $id)
    {
        $agent = TblAgent::findOrFail($id);
        $languages = DB::connection("sqlsrv4")->table('tbl_agency_banking_agent_language')->get();
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->get();
        $branches = DB::connection("sqlsrv4")->table('tbl_agency_branches')->get();
        $agentservices = TblAgentService::all();
        return view('agency.agent.approve_agent', compact('agent', 'languages', 'branches', 'agentservices', 'statuses'));
    }

    public function approveAgentAct(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'reject' => ['nullable', 'string', 'in:reject'],
            'approve' => ['nullable', 'string', 'in:approve'],
        ]);

        $user_id = Auth::id();
        $notification = 'Failed, Invalid action!';
        $color = 'danger';
        try {
            $agent = TblAgent::where('agent_id', $id)->first();

            if (!$agent) {
                $notification = 'Agent does not exist!';
                return redirect('agency/users')->with(['notification' => $notification, 'color' => $color]);
            }

            if ($request->reject == 'reject') {
                $agent->update([
                    'isWaitingApproval' => 2, // 2 for rejected
                    'approver_id' => $user_id,
                ]);
                $notification = 'Agent has been rejected successfully!';
                $color = 'success';
                return redirect('agency/users')->with([
                    'notification' => $notification,
                    'color' => $color,
                ]);
            }

            if ($request->approve == 'approve') {
                $agent->update([
                    'isWaitingApproval' => 0, // 0 for approved
                    'approver_id' => $user_id,
                ]);
                $notification = 'Agent has been approved successfully!';
                $color = 'success';
                return redirect('agency/users')->with([
                    'notification' => $notification,
                    'color' => $color,
                ]);
            }
            $this->auditLog(Auth::user()->id, 'Approve/Reject Agent', 'Agency Banking', $notification, $request->ip());
            return redirect('agency/users')->with([
                'notification' => $notification,
                'color' => $color,
            ]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->id, 'Approve/Reject Agent', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("AGENT-APPROVE-EXCEPTION: " . $e->getMessage());
            Log::error("AGENT-APPROVE-EXCEPTION: " . $e->getTraceAsString());
            // Handle errors and exceptions
            return redirect('agency/users')->with([
                'notification' => 'Something went wrong!',
                'color' => 'danger',
            ]);
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

    public function approveOperator(Request $request, $id)
    {
        $operator = Operator::where('operator_id', $id)->first();
        $agent = TblAgent::find($operator->agent_id);
        $agent_name = $agent->agent_full_name;
        $agentservices = TblAgentService::all();
        $devices = TblAgentDevice::where('agent_id', $operator->agent_id)
            ->where('device_id', $operator->device_id)->get();
        return view('agency.operators.approve_operator', compact('operator', 'agent_name', 'id', 'devices', 'agentservices'));
    }

    public function approveOperatorAct(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'reject' => ['nullable', 'string', 'in:reject'],
            'approve' => ['nullable', 'string', 'in:approve'],
        ]);

        try {
            $user_id = Auth::id();
            $password = random_int(1000, 9999);
            $operator = Operator::where('operator_id', $id)->first();
            if (!$operator) {
                $notification = 'Operator does not exist!';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            if ($request->reject == 'reject') {
                $update = $operator->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
                if ($update) {
                    $notification = 'Operator has been rejected successfully!';
                    $color = 'success';
                } else {
                    $notification = 'Failed to reject operator account!';
                    $color = 'danger';
                }
                $this->auditLog(Auth::user()->getAuthIdentifier(), 'Reject Operator', 'Agency Banking', $notification, $request->ip());
                return redirect('agency/operators/' . $operator->agent_id)->with(['notification' => $notification, 'color' => $color]);
            }

            if ($request->approve == 'approve') {
                $enc_password = Hash::make($password);
                $update = $operator->update(['operator_password' => $enc_password, 'isWaitingApproval' => 0, 'approver_id' => $user_id]);
                if ($update) {
                    $notification = 'Operator has been approved successfully!';
                    $color = 'success';
                    $msg = "Your Agency Banking PIN is $password, please keep it safe and dont share it with anyone.";
                    $op_mobile = $operator->operator_msisdn;
                    SmsJob::dispatch($msg, $op_mobile);
                } else {
                    $notification = 'Failed to approve operator account!';
                    $color = 'danger';
                }
                $this->auditLog(Auth::user()->getAuthIdentifier(), 'Approve Operator', 'Agency Banking', $notification, $request->ip());
                return redirect('agency/operators/' . $operator->agent_id)->with(['notification' => $notification, 'color' => $color]);
            }
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Approve/Reject Operator', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("OPERATOR-APPROVE-EXCEPTION: " . $e->getMessage());
            Log::error("OPERATOR-APPROVE-EXCEPTION: " . $e->getTraceAsString());
            return back()->with(['notification' => 'Failed to approve operator account!', 'color' => 'danger']);
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
        $agent = TblAgent::where('agent_id', $id)->first();
        $unassigneddevices = Devices::where('device_status', 0)->get();
        $tradingacs = TblABBankAccounts::where('agent_id', $id)->where('account_type_id', '1')->where('account_status', '1')->get();
        $commisionacs = TblABBankAccounts::where('agent_id', $id)->where('account_type_id', '2')->where('account_status', '1')->get();

        return view('agency.devices.index', compact('unassigneddevices', 'agent', 'devices', 'id', 'tradingacs', 'commisionacs'));
    }

    public function approveDevice(Request $request, $id)
    {
        $device = TblAgentDevice::findOrFail($id);
        $agent = TblAgent::where('agent_id', $device->agent_id)->first();
        $unassigneddevices = Devices::where('device_status', 0)->get();
        $approvedevices = Devices::where('device_status', 1)->get();

        $connecteddevice = Devices::where('device_id', $device->device_id)->first();
        $tradingacs = TblABBankAccounts::where('agent_id', $device->agent_id)->where('account_type_id', '1')->where('account_status', '1')->get();
        $commisionacs = TblABBankAccounts::where('agent_id', $device->agent_id)->where('account_type_id', '2')->where('account_status', '1')->get();
        $agentdevices = TblAgentDevice::where('agent_id', $device->agent_id)->get();

        return view('agency.devices.approve_device', compact('device', 'agent', 'approvedevices', 'unassigneddevices', 'tradingacs', 'commisionacs', 'connecteddevice'));
    }

    public function approveDeviceAct(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'reject' => ['nullable', 'string', 'in:reject'],
            'approve' => ['nullable', 'string', 'in:approve'],
        ]);

        try {
            $user_id = Auth::id();

            $device = TblAgentDevice::where('id', $id)->first();

            if (!$device) {
                $notification = 'Device does not exist!';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            if ($request->reject == 'reject') {
                $update = $device->update(['isWaitingApproval' => 2, 'approver_id' => $user_id]);
                if ($update) {
                    $notification = 'Device has been rejected successfully!';
                    $color = 'success';
                } else {
                    $notification = 'Failed to reject device!';
                    $color = 'danger';
                }
                $this->auditLog(Auth::user()->getAuthIdentifier(), 'Reject Device', 'Agency Banking', $notification, $request->ip());
                return redirect('agency/devices/' . $device->agent_id)->with(['notification' => $notification, 'color' => $color]);
            } elseif ($request->approve == 'approve') {
                $update = $device->update(['isWaitingApproval' => 0, 'approver_id' => $user_id]);
                if ($update) {
                    $notification = 'Device has been approved successfully!';
                    $color = 'success';
                } else {
                    $notification = 'Failed to approve device!';
                    $color = 'danger';
                }
                $this->auditLog(Auth::user()->getAuthIdentifier(), 'Approve Device', 'Agency Banking', $notification, $request->ip());
                return redirect('agency/devices/' . $device->agent_id)->with(['notification' => $notification, 'color' => $color]);
            }

            $notification = 'Failed, Invalid action!';
            $color = 'danger';
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Approve/Reject Device', 'Agency Banking', $notification, $request->ip());
            return back()->with(['notification' => $notification, 'color' => $color]);

        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Approve/Reject Device', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("DEVICE-APPROVE-EXCEPTION: " . $e->getMessage());
            Log::error("DEVICE-APPROVE-EXCEPTION: " . $e->getTraceAsString());
            $notification = 'Something went wrong!';
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }

    }

    public function resetOperatorPIN(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'operator_id' => ['required', 'regex:/^\d+$/'],
        ]);

        try {
            $operator_id = $request->operator_id;
            $operator = Operator::where('operator_id', $operator_id)->first();
            if (!$operator) {
                $notification = "Operator does not exist!";
                $color = "danger";
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }

            $operator_msisdn = $operator->operator_msisdn;

            $pin = random_int(1000, 9999);
            $status = 2;
            $reset = $operator->update([
                'operator_password' => Hash::make($pin),
                'operator_status' => $status,
                'login_counts' => 0
            ]);

            if ($reset) {
                $notification = "Operator PIN reset successfully!";
                $color = "success";
                //send the sms
                $msg = "PIN reset successfully, your new PIN is $pin, if this was not you please report it here.";
                SmsJob::dispatch($msg, $operator_msisdn);
            } else {
                $notification = "Failed to reset operator PIN!";
                $color = "danger";
            }
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Reset Operator PIN', 'Agency Banking', $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Reset Operator PIN', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("OPERATOR-RESET-EXCEPTION: " . $e->getMessage());
            Log::error("OPERATOR-RESET-EXCEPTION: " . $e->getTraceAsString());
            $notification = 'Something went wrong!';
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function statusOperator(Request $r)
    {
        // Validate the incoming request
        $r->validate([
            'operator_id' => ['required', 'regex:/^\d+$/'], // Must be a numeric value
            'status' => ['required', 'in:1,2'], // Must be either 1 or 2
        ]);

        $operator_id = $r->operator_id;
        $status = $r->status;

        // Fetch the operator
        $operator = Operator::where('operator_id', $operator_id)->first();

        // If the operator doesn't exist, return an error
        if (!$operator) {
            return redirect()->back()->with([
                'notification' => 'Operator does not exist!',
                'color' => 'danger'
            ]);
        }

        try {
            // Update operator status based on the given status
            $operator->update(['operator_status' => $status]);

            // Success notification
            $message = $status == 1
                ? 'Operator activated successfully!'
                : 'Operator deactivated successfully!';

            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Activate/Deactivate Operator', 'Agency Banking', $message, $r->ip());
            return redirect()->back()->with([
                'notification' => $message,
                'color' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Activate/Deactivate Operator', 'Agency Banking', 'Exception Occurred', $r->ip());
            // Log the error for debugging
            Log::error('Failed to update operator status: ' . $e->getMessage());
            Log::error('Failed to update operator status: ' . $e->getTraceAsString());

            // Return failure notification
            return redirect()->back()->with([
                'notification' => 'Failed to update operator status, please try again later!',
                'color' => 'danger'
            ]);
        }
    }

    public function editOperator($id)
    {
        $operator = Operator::where('operator_id', $id)->first();
        $agent_id = $operator->agent_id;
        $devices = TblAgentDevice::where('agent_id', $agent_id)->get();
        $agentservices = TblAgentService::all();
        return view('agency.operators.edit', compact('operator', 'devices', 'agentservices'));
    }

    public function storeOperator(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'phone' => 'required|tanzanian_mobile',
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'location' => 'required|regex:/^[a-zA-Z\s]+$/|max:30',
            'agent_service' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['BI', 'DC', 'WC', 'FT', 'AO', 'AS', 'MS', 'UP', 'BW'];
                    foreach ($value as $item) {
                        if (!in_array($item, $allowedValues)) {
                            $fail("The {$attribute} contains an invalid item: {$item}.");
                        }
                    }
                },
            ],
            'device' => 'required|string|regex:/^[1-9][0-9]*$/',
        ], ['device.regex' => 'Please select device to proceed!']);

        if ($validate->fails()) {
            $notification = $validate->errors()->first();
            $color = "danger";
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
        $action = "Add Operator";
        try {
            $op_mobile = preg_replace("/^\+?0/", "255", $request->phone);

            if (Operator::where('operator_msisdn', $op_mobile)->exists()) {
                return redirect()->back()->with(['notification' => 'Operator already exists', 'color' => 'danger']);
            }

            $operator = new Operator();
            $operator->operator_fullname = $request['full_name'];
            $operator->operator_msisdn = $op_mobile;
            $operator->location = $request['location'];
            $operator->device_id = $request['device'];
            $operator->agent_id = $id;
            $operator->operator_status = 1;
            $operator->operator_menu = implode("~", $request->agent_service);
            $operator->operator_password = Hash::make(random_int(1000, 9999));
            $operator->initiator_id = Auth::id();
            $operator->isWaitingApproval = 1;
            $operator->approver_id = 0;

            if ($operator->save()) {
                $notification = 'Operator added successfully!';
                $color = 'success';
            } else {
                $notification = 'Failed add the operator, please try again later!';
                $color = 'danger';
            }
            $this->auditLog(Auth::user()->getAuthIdentifier(), $action, 'Agency Banking', $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), $action, 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("Error adding operator: " . $e->getMessage());
            $notification = 'Failed add the operator, please try again later!';
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }


    public function updateOperator(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'operator_id' => ['required', 'regex:/^\d+$/'],
            'agent_service' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['BI', 'DC', 'WC', 'FT', 'AO', 'AS', 'MS', 'UP', 'BW'];
                    foreach ($value as $item) {
                        if (!in_array($item, $allowedValues)) {
                            $fail("The {$attribute} contains an invalid item: {$item}.");
                        }
                    }
                },
            ],
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'location' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'phone' => 'required|tanzanian_mobile',
        ]);

        try {
            $operator_id = $request->operator_id;
            //implode agent menu
            $service_menu = implode("~", $request->agent_service);
            $op_mobile = preg_replace(['/^\+/', '/^0/'], ['', '255'], $request->phone);
            $operator = Operator::where('operator_id', $operator_id)->first();

            if (!$operator) {
                $notification = 'Operator does not exist!';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            $update = $operator->update([
                'operator_fullname' => $request->full_name,
                'operator_msisdn' => $op_mobile,
                'location' => $request->location,
                'operator_menu' => $service_menu,
                'isWaitingApproval' => 1,
                'approver_id' => 0]);

            if ($update) {
                $notification = 'Operator updated successfully!';
                $color = 'success';

            } else {
                $notification = 'There was a problem trying to update the operator, please try again later.!';
                $color = 'danger';
            }
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Update Operator', 'Agency Banking', $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $this->auditLog(Auth::user()->getAuthIdentifier(), 'Update Operator', 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("OPERATOR-UPDATE-EXCEPTION: " . $e->getMessage());
            Log::error("OPERATOR-UPDATE-EXCEPTION: " . $e->getTraceAsString());
            return back()->with(['notification' => 'Failed to update operator account!', 'color' => 'danger']);
        }
    }

    public function deleteOperatorApproval(Request $request, $id)
    {

        $operator = Operator::where('operator_id', $id)->first();
        $agent_id = $operator->agent_id;
        $agent_name = TblAgent::where('agent_id', $agent_id)->first()->agent_full_name;
        $devices = TblAgentDevice::where('agent_id', $agent_id)->get();
        $agentservices = TblAgentService::all();
        return view('agency.operators.delete_operator_approval', compact('operator', 'devices', 'agentservices', 'agent_name'));
    }

    public function deleteOperator($id)
    {
        $user_id = Auth::id();
        $agent_id = Operator::where('operator_id', $id)->first()->agent_id;
        Operator::where(['operator_id' => $id])->update(['isWaitingApproval' => 1, 'approver_id' => 0, 'deletedBy_id' => $user_id, 'isDeleted' => 1]);
        return redirect()->route('agency.operators', $agent_id)->with(['notification' => 'Operator delete request sent for approval', 'color' => 'success']);
    }

    public function deleteOperatorActApproval(Request $request, $id)
    {
        $user_id = Auth::id();
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
            $log->auditTrail("Assigned Device", "AB", 'Device asssigned to agent successfully', 'agency/devices', Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Device asssigned to agent successfully', 'color' => 'success']);
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with(['notification' => 'Device was not assigned to agent. Error:' . $ex, 'color' => 'danger']);
        }
    }


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
                $log->auditTrail("Blocked Device", "AB", $notification, 'agency/devices', Auth::user()->getAuthIdentifier());
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
        $uid = Auth::user()->id;
        //updated by Evance Nganyaga
        $request->validate([
            'service' => 'required'
        ]);

        //add the three rate values
        $bank_rate = $request->bank_rate;
        $agent_rate = $request->agent_rate;
        $third_party_rate = $request->third_party_rate;
        $rate_sum = $bank_rate + $agent_rate + $third_party_rate;


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
        $log->auditTrail("Added Commission", "AB", 'Commission added successfully', 'agency/agentcommissions', Auth::user()->getAuthIdentifier());
        return redirect()->back()->with(['notification' => 'Commission added successfully', 'color' => 'success']);
    }


    public function editCommission($id)
    {
        $services = BankingAgentService::all();
        $commission = CommissionDistribution::where('commision_id', $id)->first();

        return view('agency.commissions.edit', compact('commission'));
    }

    public function updateCommission(Request $request)
    {
        $uid = Auth::user()->id;
        //add the three rate values
        $bank_rate = $request->bank_rate;
        $agent_rate = $request->agent_rate;
        $third_party_rate = $request->third_party_rate;
        $rate_sum = $bank_rate + $agent_rate + $third_party_rate;

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
        abort(404);
        $services = BankingAgentService::all();
        $parties = AbDistributionParties::all();
        $commissions = CommissionDistribution::orderBy('commision_id', 'DESC')->get();

        return view('agency.commissions.commissions', compact('commissions', 'services', 'parties'));
    }

    public function getTransactions(Request $request)
    {
        // Get the transaction ID from the request
        $transactionId = $request->input('transaction_id');

        // Get start and end dates from the request, defaulting to null if not provided
        $startDate = $request->input('start_date') ? $request->input('start_date') . " 00:00:00" : null;
        $endDate = $request->input('end_date') ? $request->input('end_date') . " 23:59:59" : null;

        // Initialize the query
        $query = TblTransaction::query()->whereNotIn('trxn_name', ['BALANCE_INQUIRY', 'BALANCE_INQUIRY_CARD']);


        // Check if transaction ID is present
        if ($transactionId) {
            // Filter by the transaction ID
            $transactions = $query->where('transactionID', trim($transactionId))
                ->orderBy('txn_id', 'DESC')
                ->get();
        } elseif ($startDate && $endDate) {
            // Filter by the date range
            $transactions = $query->whereBetween('trans_datetime', [$startDate, $endDate])
                ->orderBy('txn_id', 'DESC')
                ->limit(300)
                ->get();
        } else {
            // If no filters are applied, return the latest 20 transactions
            $transactions = $query->orderBy('txn_id', 'DESC')
                ->limit(20)
                ->get();
        }

        return view('agency.transactions.transactions', compact('transactions'));
    }

    public function viewTransaction($txn_id)
    {
        $transaction = TblTransaction::where('txn_id', $txn_id)->first();


        return view('agency.transactions.view', compact('transaction'));
    }

    public function reverseTransaction(Request $request)
    {
        $url = "http://172.29.1.108:18984/mkombozi/request/process/ag";
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
            "accountID" => $request->accountID,
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

        $accountInfo = $res->getBody();
        $accountDetail = json_decode($accountInfo);
        $responseCode = $accountDetail->responseCode;
        $responseMessage = $accountDetail->responseMessage;
        $transactionTimestamp = $accountDetail->transactionTimestamp;
        $transactionId = $accountDetail->transactionId;
        if ($responseMessage == "SUCCCESS") {
            $responseMessage = "Transaction with ID " . $transactionId . " succesfully reversed";
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


    public function editAccountServices($id)
    {
        $account = BankServiceAccount::on('sqlsrv4')->where('bank_account_id', $id)->first();
        return view('agency.account_services.edit', compact('id', 'account'));
    }


    public function updateAccountService(Request $request)
    {

        $request->validate([
            'id' => 'required'
        ]);


        $account = BankServiceAccount::where('bank_account_id', $request->id)
            ->update([
                'agency_expenses_deposit' => $request->agency_expenses_deposit,

                /* 'colection_account' => $request->colection_account,
                 'disbursement_account' => $request->disbursement_account,
                 'agency_commision_account' => $request->agency_commision_account,
                 'agency_payable_commision_account' => $request->agency_payable_commision_account,
                 'agency_expenses_deposit' => $request->agency_expenses_deposit,
                 'agency_deposit_commision_account' => $request->agency_deposit_commision_account*/
            ]);
        if ($account) {
            $log = new Helper();
            $log->auditTrail("Updated Service Account", "AB", 'Added Service Account', 'agency/account/service', Auth::user()->getAuthIdentifier());
            return redirect('https://admin.acbtz.com:6003/agency/account/service')->with(['notification' => 'Service Account updated successfully!', 'color' => 'success']);
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
        $agent = TblAgent::where('agent_id', $id)->first();
        $accounts = TblABBankAccounts::orderBy('account_id', 'DESC')->where('agent_id', $id)->get();
        $accounttypes = TblABBankAccountTypes::all();
        $accountstatuses = AbStatus::all();
        return view('agency.agent.index_accounts', compact('agent', 'accounts', 'accounttypes', 'accountstatuses'));
    }

    //change
    public function storeAgentAccount(Request $request)
    {
        $uid = Auth::user()->id;

        $validation = Validator::make($request->all(), [
            'account_number' => 'required|string|regex:/^\d{12}$/',
            'account_type_id' => 'required|string|regex:/^\d+$/|in:1,2',
            'account_status' => 'required|string|regex:/^\d+$/|in:1,2,3,4',
            'agent_id' => 'required|regex:/^\d+$/'
        ], ['account_type_id.regex' => 'Account Type is required']);

        if ($validation->fails()) {
            $notification = $validation->errors()->first();
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }

        //Validate account number to be unique
        $accountNumberExist = TblABBankAccounts::select(['tbl_agency_banking_agent_bank_accounts.bank_account'])
            ->where('bank_account', $request->account_number)
            ->exists();

        if ($accountNumberExist) {
            $notification = "Account already exist!";
            $color = "danger";
            //change redirect url by James
            return redirect('agency/accounts/' . $request->agent_id)->with('notification', $notification)->with('color', $color);
        }
        $action = 'Add agent account';
        try {
            //get agent clientId number in database to be compared with account clientId
            $agent = TblAgent::select('clientId')->where('agent_id', $request->agent_id)->first();

            $this->verifyAccount($request);

            if ($agent && $agent->clientId != session()->get('clientId')) {
                $notification = "Account does not belong to this agent";
                $color = "danger";
                return redirect('agency/accounts/' . $request->agent_id)->with('notification', $notification)->with('color', $color);
            }

            $responseCode = session()->get('responseCode');

            if ($responseCode == '200') {
                $account = new TblABBankAccounts();
                $account->bank_account = $request->account_number;
                $account->account_type_id = $request->account_type_id;
                $account->account_status = $request->account_status;
                $account->registration_status = 2;
                $account->initiator_id = $uid;
                $account->agent_id = $request->agent_id;
                $account->approver_id = 0;
                $account->isWaitingApproval = 1;
                $account->branchId = session()->get('branchId');
                $account->clientId = session()->get('clientId');
                $account->clientName = session()->get('clientName');
                $account->currencyID = session()->get('currencyID');
                $account->productID = session()->get('productID');
                $account->productName = session()->get('productName');
                $account->accountName = session()->get('accountName');
                $account->address = session()->get('address');
                $account->city = session()->get('city');
                $account->countryID = session()->get('countryID');
                $account->countryName = session()->get('countryName');
                $account->mobile = session()->get('mobile');
                $account->emailID = session()->get('emailID');
                $account->aCStatus = session()->get('aCStatus');
                $account->createdOn = session()->get('createdOn');
                $account->updateCount = session()->get('updateCount');
                $account->branchName = session()->get('branchName');

                if ($account->save()) {
                    $notification = "Agent account added successfully!";
                    $color = "success";
                } else {
                    $notification = "Failed to add agent account!";
                    $color = "danger";
                }

                $this->auditLog(Auth::user()->getAuthIdentifier(), $action, 'Agency Banking', $notification, $request->ip());
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);

            } else {
                return redirect()->back()->with(['notification' => session()->get('responseMessage'), 'color' => 'success']);

            }

        } catch (\Exception $e) {
            session()->forget('clientName');
            session()->forget('phone');
            session()->forget('clientId');
            session()->forget('accountCategory');
            session()->put('accountID', $request->account_number);
            $this->auditLog(Auth::user()->getAuthIdentifier(), $action, 'Agency Banking', 'Exception Occurred', $request->ip());
            Log::error("Store Agent Account Exception: " . $e->getMessage());
            Log::error("Store Agent Account Exception: " . $e->getTraceAsString());
            return redirect()->back()->with(['notification' => 'Failed to add agent account', 'color' => 'danger']);
        }
    }

    public function storeInstitutionAccount(Request $request)
    {
        $uid = Auth::user()->id;
        $request->validate([
            'account_number' => 'required',
            'account_type_id' => 'required'
        ]);

        //Validate account number to be unique
        $account_number = TblABInstitutionAccounts::where('account_number', $request->account_number)
            ->where('account_type_id', $request->account_type_id)
            ->get();

        if (count($account_number) > 0) {
            $notification = "Account already exist!";
            $color = "danger";
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
        $log->auditTrail("Added new Account", "AB", 'Added Account', 'agency/institution_accounts', Auth::user()->getAuthIdentifier());
        return redirect()->back()->with(['notification' => 'Account added successfully!', 'color' => 'success']);
    }

    public function approveInstitutionAccount(Request $r)
    {
        $uid = Auth::user()->id;
        $account_id = $r->account_id;
        $op = $r->op;
        if ($op == 1) {
            //approve
            $dual_status = 1;
            $notification = "Institution account approved successfully!";
        } else {
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
        return view('agency.account_services.edit_institution_accounts', compact('account_types', 'accounts', 'account', 'auditlogs'));

    }

    public function updateInstitutionAccount(Request $request)
    {
        $uid = Auth::user()->id;
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
            $log->auditTrail("Updated Account", "AB", 'Account updated successfully!', 'agency/institutionaccounts/edit/' . $request->id, Auth::user()->getAuthIdentifier());
            return redirect('agency/institutionaccounts/edit/' . $request->id)->with(['notification' => 'Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect('agency/institutionaccounts/edit/' . $request->id)->with(['notification' => 'Account updated un successfully!', 'color' => 'danger']);
        }
    }


    public function approveAgentAccount(Request $r)
    {
        $uid = Auth::user()->id;
        $account_id = $r->account_id;
        $op = $r->op;
        if ($op == 1) {
            //approve
            $dual_status = 1;
            $notification = "Agent account approved successfully!";
        } else {
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
        $account = TblABBankAccounts::where('account_id', $id)->first();
        $accounttypes = TblABBankAccountTypes::all();
        $accountstatuses = AbStatus::all();
        return view('agency.agent.edit_accounts', compact('account', 'accounttypes', 'accountstatuses'));
    }

    //account update
    public function updateAgentAccount(Request $request)
    {
        $uid = Auth::user()->id;
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
            $log->auditTrail("Updated Account", "AB", 'Account updated successfully!', 'agency/accounts/' . $request->agent_id, Auth::user()->getAuthIdentifier());
            // return redirect('agency/accounts/'.$request->agent_id)->with(['notification' => 'Account updated successfully!', 'color' => 'success']);
        } else {
            return redirect('agency/accounts/' . $request->agent_id)->with(['notification' => 'Account updated un successfully!', 'color' => 'danger']);
        }
    }


    public function storeAccountService(Request $request)
    {


        $uid = Auth::user()->id;
        $request->validate([
            'bank_service_ID' => 'required'
        ]);

        DB::beginTransaction();
        try {

            $account = new BankServiceAccount();
            $account->bank_service_ID = $request->bank_service_ID;
            $account->agency_expenses_deposit = $request->agency_expenses_deposit;

            $account->colection_account = 'null';
            $account->disbursement_account = 'null';
            $account->agency_commision_account = 'null';
            $account->agency_payable_commision_account = 'null';
            $account->agency_deposit_commision_account = 'null';

            $account->initiator_id = $uid;
            $account->isWaitingApproval = 1;
            $account->save();
            DB::commit();
            $log = new Helper();
            $log->auditTrail("Added Account", "AB", 'Added Account', 'agency/accounts', Auth::user()->getAuthIdentifier());
            return redirect()->back()->with(['notification' => 'Account added successfully!', 'color' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['notification' => 'Account added un successfully! Error' . $e, 'color' => 'danger']);
        }
    }


    public function approveAccountService(Request $r)
    {
        $uid = Auth::user()->id;
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
        $uid = Auth::user()->id;
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
            $log->auditTrail("Added Service Charge", "AB", $notification, 'agency/charges', Auth::user()->getAuthIdentifier());
        } else {
            $notification = 'Oops something went wrong!';
            $color = 'danger';
        }
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function editCharges($id)
    {
        $charge = TblCharge::where('charge_id', $id)->first();
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
                $log->auditTrail("Updated Service Charge", "AB", $notification, 'agency/charges', Auth::user()->getAuthIdentifier());
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

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function verifyAccount(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'account_number' => 'required|string|regex:/^\d{12}$/',
        ]);

        if ($validation->fails()) {
            $notification = $validation->errors()->first();
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }

        $account = $request->account_number;
        $url = env('AGENT_ACCOUNT_INQUIRY') . $account;

        Log::info("AGENT-ACCOUNT-INQUIRY-REQUEST: " . $url);
        try {
            $username = env('AGENT_INQUIRY_USERNAME');
            $password = env('AGENT_INQUIRY_PASSWORD');
            $client = new Client;
            $result = $client->request('GET', $url, [
                'auth' => [$username, $password],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $accountDetail = json_decode($result->getBody());
            Log::info("AGENT-ACCOUNT-INQUIRY-RESPONSE: " . json_encode($accountDetail));

            if (isset($accountDetail->account_name) && isset($accountDetail->account_id)) {
                $clientName = $accountDetail->account_name;
                $phoneNumber = $accountDetail->mobile;
                $accountCategory = $accountDetail->product;
                $clientId = $accountDetail->client_id;

                session()->put('responseCode', '200');
                session()->put('responseMessage', 'Success');
                session()->put('clientName', $clientName);
                session()->put('phone', $phoneNumber);
                session()->put('clientId', $clientId);
                session()->put('accountCategory', $accountCategory);
                session()->put('accountID', $request->account_number);
                return back();
            } else {
                session()->forget('clientName');
                session()->forget('phone');
                session()->forget('clientId');
                session()->forget('accountCategory');
                session()->put('accountID', $request->account_number);
                return redirect()->back()->with(['notification' => "Account Not found for Account Number: $request->account_number", 'color' => 'danger']);
            }
        } catch (\Exception $e) {
            session()->forget('clientName');
            session()->forget('phone');
            session()->forget('clientId');
            session()->forget('accountCategory');
            session()->put('accountID', $request->account_number);
            throw new \Exception("Agent Inquiry Exception => " . $e->getTraceAsString());
        }
    }

}
