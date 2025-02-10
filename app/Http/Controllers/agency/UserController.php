<?php

namespace App\Http\Controllers\agency;

use App\AbStatus;
use App\Http\Controllers\Controller;
use App\TblAgent;
use App\TblAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentNumber = trim($request->input('agent_number'));
        $name = trim($request->input('name'));
        $mobile = trim($request->input('mobile_number'));

        // Replace leading 0 with 255 in mobile number
        if (strpos($mobile, '0') === 0) {
            $mobile = '255' . substr($mobile, 1);
        }

        // Building the query dynamically
        $query = TblAgent::query();

        if ($agentNumber) {
            $query->where('agent_id', $agentNumber);
        }

        if ($mobile) {
            $query->where('agent_msisdn', $mobile);
        }

        if ($name) {
            $query->where('agent_full_name', 'like', "%$name%");
        }

        // Limit to the most recent 20 agents if no filters are applied
        $agents = $query->orderBy('agent_id', 'DESC')->limit(20)->get();

        $languages = DB::connection("sqlsrv4")->table('tbl_agency_banking_agent_language')->select('language_id', 'language_name')->get();
        $statuses = AbStatus::where('tbl_status_id', '<', 3)->select('tbl_status_id', 'status')->get();
        $regions = DB::table('regions')->get();
        $districts = DB::table('districts')->get();

        $agentservices = TblAgentService::select('agent_serviceName', 'short_name')->get();

        return view("agency.agent.index", compact('agents', 'languages', 'statuses', 'agentservices', 'regions', 'districts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $initiator = Auth::user()->id;

        $insert = TblAgent::insert([
            'agent_msisdn' => $request->mobile_number,
            'agent_language' => $request->language_id,
            'agent_valid_id_number' => $request->agent_number,
            'agent_full_name' => $request->agent_name,
            'agent_business_license_number' => $request->business_licence,
            'business_certificate_registration_number' => $request->registration_number,
            'agent_status' => $request->status_id,
            'agent_reg_source' => $request->location,
            'initiator_id' => $initiator,
            'isWaitingApproval' => 1,
            'approver_id' => 0,
            'agent_address' => $request->address,
            'agent_location' => $request->location,
            'agent_float_limit' => $request->float_limit,
            'agent_daily_limit' => $request->daily_limit,
            'branch_id' => $request->branch_id,
        ]);

        if ($insert == true) {
            $notification = 'Agent Added Successfully!';
            $color = 'success';
        } else {
            $notification = 'Something went wrong!';
            $color = 'danger';
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
