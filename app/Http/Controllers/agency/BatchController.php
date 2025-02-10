<?php

namespace App\Http\Controllers\agency;

use App\Http\Controllers\Controller;
use App\TblAgentChargeType;
use App\TblAgentFixedCharge;
use App\TblAgentIntervalCharge;
use App\TblAgentIntervalPercentCharge;
use App\TblAgentPercentCharge;
use App\TblAgentService;
use App\TblCharge;


//adopt the new charges structure
use App\TblABAllCharges;
use App\TblABChargesBatch;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    private $charge_id;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Retrieves all values on the table
        $services = TblCharge::all();
        $agentservices = TblAgentService::all();
        $types = TblAgentChargeType::all();

        $tblFixedCharge = TblAgentFixedCharge::all();
        $tblAgentPercentCharge = TblAgentPercentCharge::all();
        $tblAgentIntervalCharge = TblAgentIntervalCharge::all();
        $tblAgentIntervalPercent = TblAgentIntervalPercentCharge::all();

        $datas = collect();
        $datas = $tblFixedCharge->concat($tblAgentIntervalCharge)
            ->concat($tblAgentIntervalPercent)
            ->concat($tblAgentPercentCharge);

        
        //adopt the new charges structure

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        //Validating percent to range between 0 to 100 for service charges by percent
        if ($request->charge_type == 2 || $request->charge_type == 5) {
            $request->validate([
                'charge_percent' => 'required',
            ]);

            if (intval($request->charge_percent) > 100 || intval($request->charge_percent) < 0 || !isset($request->charge_percent)) {
                $notification = 'Percentage range between 0 to 100!';
                $color = 'danger';
                return redirect()->back()->with('notification', $notification)->with('color', $color);
            }
        }

        //Validating range of amount if select charge type is an interval
        if ($request->charge_type == 3 || $request->charge_type == 5) {
            $request->validate([
                'from_amount' => 'required',
                'to_amount' => 'required',
            ]);
            if ((!isset($request->from_amount) || !isset($request->to_amount))) {
                $notification = 'Must set amount range before submitting!';
                $color = 'danger';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }
        }

        //Validating charge amount , if not inserted
        if ($request->charge_type == 1 || $request->charge_type == 3) {
            $request->validate([
                'charge_amount' => 'required',
            ]);
            if ((!isset($request->charge_amount) || !isset($request->charge_amount))) {
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
    }


    /**
     * Display the specified resource.
     *
     * @param  int $charge
     * @return \Illuminate\Http\Response
     */
    public function show($charge)
    {

//        $charges_fixed =
//            DB::table('tbl_agency_banking_charges')->where('tbl_agency_banking_charges.charge_type_id','1')
//                ->join('tbl_agency_banking_charges_fixed', 'tbl_agency_banking_charges_fixed.charge_type_id', '=', 'tbl_agency_banking_charges.charge_type_id')
//                ->join('tbl_agency_banking_charges_fixed', 'tbl_agency_banking_charges_fixed.service_id', '=', 'tbl_agency_banking_charges.service_id')
//                ->select('tbl_agency_banking_charges_fixed.charge_amount')
//                ->get();
//
//        $charges_percentage =
//            DB::table('tbl_agency_banking_charges')->where('tbl_agency_banking_charges.charge_type_id','2')
//                ->join('tbl_agency_banking_charges_percentage', 'tbl_agency_banking_charges_percentage.charge_type_id', '=', 'tbl_agency_banking_charges.charge_type_id')
//                ->join('tbl_agency_banking_charges_percentage', 'tbl_agency_banking_charges_percentage.service_id', '=', 'tbl_agency_banking_charges.service_id')
//                ->select('tbl_agency_banking_charges_percentage.percentage_value')
//                ->get();
//
//        $charges_interval =
//            DB::table('tbl_agency_banking_charges')->where('tbl_agency_banking_charges.charge_type_id','3')
//                ->join('tbl_agency_banking_charges_interval', 'tbl_agency_banking_charges_interval.charge_type_id', '=', 'tbl_agency_banking_charges.charge_type_id')
//                ->join('tbl_agency_banking_charges_interval', 'tbl_agency_banking_charges_interval.service_id', '=', 'tbl_agency_banking_charges.service_id')
//                ->select('tbl_agency_banking_charges_interval.from_amount','tbl_agency_banking_charges_interval.to_amount','tbl_agency_banking_charges_interval.charge_amount')
//                ->get();
//
//        $charge =
//                $charges_fixed
//                ->union($charges_interval)
//                ->union($charges_percentage)
//                ->get();

//        $charge = DB::table('tbl_agency_banking_charges')
//            ->select('articles.id as articles_id' )
//            ->join('tbl_agency_banking_agent_service', 'tbl_agency_banking_agent_service.agent_serviceID', '=', 'categories.id')
//            ->join('users', 'articles.user_id', '=', 'user.id')
//            ->join('users', 'articles.user_id', '=', 'user.id')
//            ->join('users', 'articles.user_id', '=', 'user.id')
//            ->get();


        $charge = TblCharge::where('charge_id', $charge)->get()->first();

        $service = TblAgentService::all();
        $types = TblAgentChargeType::all();


        return view('agency.charges.edit', compact('charge', 'service', 'types'));
    }


    public function edit($charge)
    {
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
                //Nothing will be done if id is not as specified above
                break;





        }


        return view('agency.charges.edit', compact('agentservices', 'services', 'types', 'service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $charge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $charge)
    {
        //Validating percent to range between 0 to 100 for service charges by percent

        if ($request->charge_type == 2 || $request->charge_type == 5) {

            $request->validate([
                'charge_percent' => 'required|numeric',
            ]);

            if (intval($request->charge_percent) > 100 || intval($request->charge_percent) < 0 || !isset($request->charge_percent)) {
                $notification = 'Percentage range between 0 to 100!';
                $color = 'danger';
                return redirect()->back()->with('notification', $notification)->with('color', $color);
            }
        }

        //Validating range of amount if select charge type is an interval
        if ($request->charge_type == 3 || $request->charge_type == 5) {
            $request->validate([
                'from_amount' => 'required|numeric',
                'to_amount' => 'required|numeric',
            ]);
            if ((!isset($request->from_amount) || !isset($request->to_amount))) {
                $notification = 'Must set amount range before submitting!';
                $color = 'danger';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }
        }

        //Validating charge amount , if not inserted
        if ($request->charge_type == 1 ) {
            $request->validate([
                'charge_amount' => 'required|numeric',
            ]);
            if ((!isset($request->charge_amount) || !isset($request->charge_amount))) {
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
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $charge
     * @return \Illuminate\Http\Response
     */
    public function destroy($charge)
    {
        //
    }

    public function recordAmountByType(Request $request)
    {

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
                } else {
                    $insert = TblAgentFixedCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charge_amount' => $request->charge_amount,
                        'charge_id' => 1


                    ]);

                    if ($insert == true) {
                        //Add the amount on the table based on charge type selected (Fixed, Interval or Percentage)
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];

                    } else {
                        $notification = 'Oops something went wrong!';
                        $color = 'danger';
                        return ['message' => $notification, 'color' => $color];

                    }
                }
                break;
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
                } else {

                    $insert = TblAgentPercentCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'percentage_value' => $request->charge_percent,
                        'charge_id' => 1
                    ]);

                    if ($insert == true) {
                        //Add the amount on the table based on charge type selected (Fixed, Interval or Percentage)
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];

                    } else {
                        $notification = 'Oops something went wrong!';
                        $color = 'danger';
                        return ['message' => $notification, 'color' => $color];

                    }
                }

                break;
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
                } else {

                    $insert = TblAgentIntervalCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charge_amount' => $request->charge_amount,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount,
                        'charge_id' => 1
                    ]);

                    if ($insert == true) {
                        //Add the amount on the table based on charge type selected (Fixed, Interval or Percentage)
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];

                    } else {
                        $notification = 'Oops something went wrong!';
                        $color = 'danger';
                        return ['message' => $notification, 'color' => $color];

                    }
                }

                break;
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
                } else {

                    $insert = TblAgentIntervalPercentCharge::insert([
                        'charge_type_id' => $request->charge_type,
                        'service_id' => $request->service_name,
                        'charges_percent' => $request->charge_percent,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount,
                        'charge_id' => 1
                    ]);

                    if ($insert == true) {
                        //Add the amount on the table based on charge type selected (Fixed, Interval or Percentage)
                        $notification = 'Service Charge Added Successfully!';
                        $color = 'success';
                        return ['message' => $notification, 'color' => $color];

                    } else {
                        $notification = 'Oops something went wrong!';
                        $color = 'danger';
                        return ['message' => $notification, 'color' => $color];

                    }
                }

                break;
            default:
                //Nothing will be done if id is not as specified above
                break;

        }
    }

    public function updateAmountByType(Request $request, $charge){

        switch ($request->charge_type) {
            case "1":

                $insert = TblAgentFixedCharge::where('charge_id', $charge)
                    ->update([
                        'charge_amount' => $request->charge_amount
                    ]);


                if ($insert == true) {
                    return true;
                } else {
                    return false;
                }

                break;
            case "2":

                $insert = TblAgentPercentCharge::where('charge_id', $charge)
                    ->update([
                        'percentage_value' => $request->charge_percent
                    ]);

                if ($insert == true) {
                    return true;
                } else {
                    return false;
                }

                break;
            case "3":

                $insert = TblAgentIntervalCharge::where('charge_id', $charge)
                    ->update([
                        'charge_amount' => $request->charge_amount,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount
                    ]);

                if ($insert == true) {
                    return true;
                } else {
                    return false;
                }

                break;
            case "5":

                $insert = TblAgentIntervalPercentCharge::where('charge_id', $charge)
                    ->update([
                        'charges_percent' => $request->charge_percent,
                        'from_amount' => $request->from_amount,
                        'to_amount' => $request->to_amount
                    ]);

                if ($insert == true) {
                    return true;
                } else {
                    return false;
                }

                break;
            default:
                //Nothing will be done if id is not as specified above
                return false;
                break;

        }
    }




    //added by Evance Nganyaga
    public function storeBatch(Request $r){
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        $batch_status = 2;
        $file_path = "path";//default path
        $db_action = TblABChargesBatch::insert([
            'batch_status'=>$batch_status,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
            'file_path'=>$file_path
        ]);

        if($db_action==true)
        {
            $notification="Service Charge Batch added successfully!";
            $color="success";
        }
        else{
            $notification="Service Charge Batch added un successfully!";
            $color="danger";
        }

        return redirect()->back()->with('notification',$notification)->with('color',$color);
    
    }

    //added by Evance Nganyaga
    public function deleteBatch($id){
        return redirect()->back()->with('notification',"Batch deleted successfully!")->with('color',"success");
    }
}
