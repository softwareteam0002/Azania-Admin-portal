<?php

namespace App\Http\Controllers\agency;

use App\Http\Controllers\Controller;
use App\TblABAllCharges;
use App\TblABChargesBatch;
use App\TblAgentChargeType;
use App\TblAgentFixedCharge;
use App\TblAgentIntervalCharge;
use App\TblAgentIntervalPercentCharge;
use App\TblAgentPercentCharge;
use App\TblAgentService;
use App\TblCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


//adopt the new charges structure


class BatchController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Retrieves all values on the table
            $services = TblCharge::all();
            $agentservices = TblAgentService::all();
            $types = TblAgentChargeType::all();

            $tblFixedCharge = TblAgentFixedCharge::all();
            $tblAgentPercentCharge = TblAgentPercentCharge::all();
            $tblAgentIntervalCharge = TblAgentIntervalCharge::all();
            $tblAgentIntervalPercent = TblAgentIntervalPercentCharge::all();

            $datas = $tblFixedCharge->concat($tblAgentIntervalCharge)
                ->concat($tblAgentIntervalPercent)
                ->concat($tblAgentPercentCharge);

            // Adopt the new charges structure
            $charges = TblABAllCharges::all();
            $batches = TblABChargesBatch::all();

            return view('agency.charges.charges', compact(
                'datas',
                'services',
                'agentservices',
                'types',
                'charges',
                'batches'
            ));
        } catch (\Exception $e) {
            Log::error("Error retrieving data: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with([
                'notification' => 'An error occurred while retrieving data. Please try again later.',
                'color' => 'danger'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = TblAgentService::all();
        $types = TblAgentChargeType::all();

        return view('agency.charges.create', compact('services', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->charge_type === 2 || $request->charge_type === 5) {
                $request->validate([
                    'charge_percent' => 'required',
                ]);

                if ((int)$request->charge_percent > 100 || (int)$request->charge_percent < 0 || !isset($request->charge_percent)) {
                    $notification = 'Percentage range between 0 to 100!';
                    $color = 'danger';
                    return redirect()->back()->with('notification', $notification)->with('color', $color);
                }
            }

            //Validating range of amount if select charge type is an interval
            if ($request->charge_type === 3 || $request->charge_type === 5) {
                $request->validate([
                    'from_amount' => 'required',
                    'to_amount' => 'required',
                ]);
                if (!isset($request->from_amount, $request->to_amount)) {
                    $notification = 'Must set amount range before submitting!';
                    $color = 'danger';
                    return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
                }
            }

            //Validating charge amount , if not inserted
            if ($request->charge_type === 1 || $request->charge_type === 3) {
                $request->validate([
                    'charge_amount' => 'required',
                ]);
                if (!isset($request->charge_amount)) {
                    $notification = 'Please enter charge amount!';
                    $color = 'danger';
                    return redirect()->back()->with('notification', $notification)->with('color', $color);
                }
            }

            $request->validate([
                'service_name' => 'required',
                'charge_type' => 'required'
            ]);

            $data = $this->recordAmountByType($request);

            return redirect()->back()->with(['notification' => $data['message'], 'color' => $data['color']]);
        } catch (\Exception $e) {
            Log::error("Error storing data: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = 'An error occurred while processing your request. Please try again later.';
            $color = 'danger';
            return redirect()->back()->with('notification', $notification)->with(['color' => $color]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int $charge
     * @return \Illuminate\Http\Response
     */
    public function show($charge)
    {
        $charge = TblCharge::where('charge_id', $charge)->get()->first();
        $service = TblAgentService::all();
        $types = TblAgentChargeType::all();
        return view('agency.charges.edit', compact('charge', 'service', 'types'));
    }


    public function edit($charge)
    {
        try {
            $data = explode('-', $charge);

            $charge_id = $data[0];
            $id = $data[1];

            $services = TblCharge::all();
            $agentservices = TblAgentService::all();
            $types = TblAgentChargeType::all();
            switch ($charge_id) {
                case "1":
                    $service = TblAgentFixedCharge::where('charges_fixed_id', $id)
                        ->get()->first();
                    break;
                case "2":
                    $service = TblAgentPercentCharge::where('percentage_id', $id)
                        ->get()->first();

                    break;
                case "3":
                    $service = TblAgentIntervalCharge::where('interval_id', $id)
                        ->get()->first();

                    break;
                case "5":
                    $service = TblAgentIntervalPercentCharge::where('interval_id', $id)
                        ->get()->first();
                    break;
                default:
                    $service = null;
                    break;
            }

            return view('agency.charges.edit', compact('agentservices', 'services', 'types', 'service'));
        } catch (\Exception $e) {
            Log::error("Error in editing charge: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with([
                'notification' => 'An error occurred while editing the charge. Please try again later.',
                'color' => 'danger',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $charge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            if ($request->charge_type === 2 || $request->charge_type === 5) {
                $request->validate([
                    'charge_percent' => 'required|numeric',
                ]);

                if ((int)$request->charge_percent > 100 || (int)$request->charge_percent < 0 || !isset($request->charge_percent)) {
                    $notification = 'Percentage range between 0 to 100!';
                    $color = 'danger';
                    return redirect()->back()->with('notification', $notification)->with('color', $color);
                }
            }

            //Validating range of amount if select charge type is an interval
            if ($request->charge_type === 3 || $request->charge_type === 5) {
                $request->validate([
                    'from_amount' => 'required|numeric',
                    'to_amount' => 'required|numeric',
                ]);
                if (!isset($request->from_amount, $request->to_amount)) {
                    $notification = 'Must set amount range before submitting!';
                    $color = 'danger';
                    return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
                }
            }

            //Validating charge amount , if not inserted
            if ($request->charge_type === 1) {
                $request->validate([
                    'charge_amount' => 'required|numeric',
                ]);
                if (!isset($request->charge_amount)) {
                    $notification = 'Please enter charge amount!';
                    $color = 'danger';
                    return redirect()->back()->with('notification', $notification)->with('color', $color);
                }
            }

            $request->validate([
                'service_name' => 'required',
                'charge_type' => 'required'
            ]);

            $data = $this->recordAmountByType($request);

            return redirect()->back()->with(['notification' => $data['message'], 'color' => $data['color']]);
        } catch (\Exception $e) {
            Log::error("Error updating data: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = 'An error occurred while updating the charge. Please try again later.';
            $color = 'danger';
            return redirect()->back()->with('notification', $notification)->with(['color' => $color]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $charge
     * @return \Illuminate\Http\Response
     */
    public function destroy($charge)
    {
        //
    }

    public function recordAmountByType(Request $request)
    {
        try {
            switch ($request->charge_type) {
                case "1":
                    $check = TblAgentFixedCharge::where('charge_type_id', $request->charge_type)
                        ->where('service_id', $request->service_name)
                        ->get();
                    if (count($check) >= 1) {
                        $update = $check->first();
                        $update->charge_amount = $request->charge_amount;
                        $update->save();

                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';

                        return ['message' => $notification, 'color' => $color];
                    }

                    $insert = TblAgentFixedCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charge_amount' => $request->charge_amount,
                        'charge_id' => 1
                    ]);

                    if ($insert) {
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];
                    }

                    $notification = 'Oops something went wrong!';
                    $color = 'danger';
                    return ['message' => $notification, 'color' => $color];

                case "2":
                    $check = TblAgentPercentCharge::where('charge_type_id', $request->charge_type)
                        ->where('service_id', $request->service_name)
                        ->get();
                    if (count($check) >= 1) {
                        $update = $check->first();
                        $update->percentage_value = $request->charge_percent;
                        $update->save();

                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';

                        return ['message' => $notification, 'color' => $color];
                    }

                    $insert = TblAgentPercentCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'percentage_value' => $request->charge_percent,
                        'charge_id' => 1
                    ]);

                    if ($insert) {
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];
                    }

                    $notification = 'Oops something went wrong!';
                    $color = 'danger';
                    return ['message' => $notification, 'color' => $color];

                case "3":
                    $check = TblAgentIntervalCharge::where('charge_type_id', $request->charge_type)
                        ->where('service_id', $request->service_name)
                        ->get();
                    if (count($check) >= 1) {
                        $update = $check->first();
                        $update->charge_amount = $request->charge_amount;
                        $update->from_amount = $request->from_amount;
                        $update->to_amount = $request->to_amount;
                        $update->save();

                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';

                        return ['message' => $notification, 'color' => $color];
                    }

                    $insert = TblAgentIntervalCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charge_amount' => $request->charge_amount,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount,
                        'charge_id' => 1
                    ]);

                    if ($insert) {
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];
                    }

                    $notification = 'Oops something went wrong!';
                    $color = 'danger';
                    return ['message' => $notification, 'color' => $color];

                case "5":
                    $check = TblAgentIntervalPercentCharge::where('charge_type_id', $request->charge_type)
                        ->where('service_id', $request->service_name)
                        ->get();
                    if (count($check) >= 1) {
                        $update = $check->first();
                        $update->charges_percent = $request->charge_percent;
                        $update->from_amount = $request->from_amount;
                        $update->to_amount = $request->to_amount;
                        $update->save();

                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';

                        return ['message' => $notification, 'color' => $color];
                    }

                    $insert = TblAgentIntervalPercentCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charges_percent' => $request->charge_percent,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount,
                        'charge_id' => 1
                    ]);

                    if ($insert) {
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];
                    }

                    $notification = 'Oops something went wrong!';
                    $color = 'danger';
                    return ['message' => $notification, 'color' => $color];

                default:
                    return ['message' => 'Invalid charge type provided.', 'color' => 'danger'];
            }
        } catch (\Exception $e) {
            Log::error("Error in recording amount by type: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return ['message' => 'An error occurred while processing your request.', 'color' => 'danger'];
        }
    }

    public function storeBatch(Request $r)
    {
        try {
            $from_date = $r->from_date;
            $to_date = $r->to_date;
            $batch_status = 2;
            $file_path = "path"; //default path
            $db_action = TblABChargesBatch::insert([
                'batch_status' => $batch_status,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'file_path' => $file_path
            ]);

            if ($db_action) {
                $notification = "Service Charge Batch added successfully!";
                $color = "success";
            } else {
                $notification = "Service Charge Batch added unsuccessfully!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error("Error in storeBatch: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "An error occurred while adding the Service Charge Batch.";
            $color = "danger";
        }

        return redirect()->back()->with('notification', $notification)->with('color', $color);
    }
    
}
