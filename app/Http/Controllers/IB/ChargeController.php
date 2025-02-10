<?php

namespace App\Http\Controllers\IB;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\IbBank;
use App\TblIbCharge;
use App\TblIbChargeType;
use App\TblIbFixedCharge;
use App\TblIbIntervalCharge;
use App\TblIbIntervalPercentCharge;
use App\TblIbPercentCharge;
use App\TblIbService;
use App\TblIbAllService;
use App\Imports\ServiceChargesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
//adopt the new charges structure
use App\TblIBAllCharges;
use App\TblIBChargesBatch;
use App\IbTransferType;
use Excel;
use PDF;
use App\Exports\IBServiceChargeExport;


class ChargeController extends Controller
{
    private $charge_id;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //adopt the new charges structure
        $charges = TblIBAllCharges::all();
        $batches = TblIBChargesBatch::all();
        $services = IbTransferType::all();
        $agentservices = IbTransferType::all();

        if (!isset($id)) {
            //check to see if there is an active batch
            if(count(TblIBChargesBatch::where('batch_status', "1")->get()) > 0){
                $activebatch = TblIBChargesBatch::where('batch_status', "1")->get()[0];  
                $activebatch_id = $activebatch->batch_id;				
            }else{
				//dd(TblIBChargesBatch::get());
                $activebatch = TblIBChargesBatch::get();  				
				$activebatch_id = $id;
            }
            //$activebatch_id = $activebatch->batch_id;
        } else {
            $activebatch_id = $id;
            $activebatch = TblIBChargesBatch::where('batch_id', $activebatch_id)->get()[0];
        }

        //these charges are hard codded, need to be changed to dynamics
        $fixedcharges = TblIBAllCharges::where('charge_type', 1)->where('batch_id', $activebatch_id)->orderBy('charge_id', 'DESC')->get();
        $percentagecharges = TblIBAllCharges::where('charge_type', 2)->where('batch_id', $activebatch_id)->orderBy('charge_id', 'DESC')->get();
        $intervalcharges = TblIBAllCharges::where('charge_type', 3)->where('batch_id', $activebatch_id)->orderBy('charge_id', 'DESC')->get();
        $intervalpercentagecharges = TblIBAllCharges::where('charge_type', 4)->where('batch_id', $activebatch_id)->orderBy('charge_id', 'DESC')->get();


        return view('ib.charges.charges', compact(
            'services',
            'agentservices',
            'charges',
            'batches',
            'activebatch',
            'fixedcharges',
            'percentagecharges',
            'intervalcharges',
            'intervalpercentagecharges'
        ));
    }

    public function indexServiceChargesByBatch($id)
    {
        //adopt the new charges structure
        $charges = TblIBAllCharges::all();
        $batches = TblIBChargesBatch::all();
        $services = IbTransferType::all();
        $agentservices = IbTransferType::all();

        if (!isset($id)) {
            $activebatch = TblIBChargesBatch::where('batch_status', 1)->get()[0];
            $activebatch_id = $activebatch->batch_id;
        } else {
            $activebatch_id = $id;
            $activebatch = TblIBChargesBatch::where('batch_id', $activebatch_id)->get()[0];
        }

        //these charges are hard codded, need to be changed to dynamics
        $fixedcharges = TblIBAllCharges::where('charge_type', 1)->where('batch_id', $activebatch_id)->get();
        $percentagecharges = TblIBAllCharges::where('charge_type', 2)->where('batch_id', $activebatch_id)->get();
        $intervalcharges = TblIBAllCharges::where('charge_type', 3)->where('batch_id', $activebatch_id)->get();
        $intervalpercentagecharges = TblIBAllCharges::where('charge_type', 4)->where('batch_id', $activebatch_id)->get();


        return view('ib.charges.charges', compact(
            'services',
            'agentservices',
            'charges',
            'batches',
            'activebatch',
            'batches',
            'fixedcharges',
            'percentagecharges',
            'intervalcharges',
            'intervalpercentagecharges'
        ));
    }


    public function create()
    {
        //$services = TblIbService::all();
        $services = TblIbAllService::all();
        $types = TblIbChargeType::all();

        return view('ib.charges.create', compact('services', 'types'));
    }


    public function store(Request $request)
    {


        $this->validate(
            $request, 
            ['from_amount' => 'max:9']
        );
        //updated by Evance Nganyaga
        $batch_id = $request->batch_id;
        $charge_type_id = $request->charge_type_id;
        $agent_service_id = $request->agent_service_id;
        $charge_amount = $request->charge_amount;
        $from_amount = $request->from_amount;
        $to_amount = $request->to_amount;
        $charge_percent = $request->charge_percent;
        $payee = $request->payee;

        if ($payee == "0") {
            $payee = "customer";
        } elseif ($payee == "1") {
            $payee = "bank";
        } else {
            $payee = "thirdparty";
        }


        $charge = new TblIBAllCharges;
        $charge->service_id = $agent_service_id;
        $charge->charge_type = $charge_type_id;
        $charge->from_amount = $from_amount;
        $charge->to_amount = $to_amount;
        $charge->amount = $charge_amount;
        $charge->payee = $payee;
        $charge->amount_percentage = $charge_percent;
        $charge->status = 1;
        $charge->batch_id = $batch_id;
        if ($charge->save()) {
            //charge has been added successfully
            return redirect()->back()->with('notification', "Charge added successfully!")->with('color', "success");
        } else {
            return redirect()->back()->with('notification', "Charge added successfully!")->with('color', "danger");
        }
        //end the shit here

    }



    public function show($charge)
    {

        $charge = TblIbCharge::where('charge_id', $charge)->get()[0];
        //$services = TblIbService::all();
        $services = TblIbAllService::all();
        $types = TblIbChargeType::all();

        return view('ib.charges.edit', compact('charge', 'services', 'types'));
    }




    //added by Evance Nganyaga
    public function storeBatch(Reuest $r)
    {  dd(request()->all());
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        $batch_status = 1;
        $db_action = TblIBChargesBatch::insert([
            'batch_status' => $r->biller_short_name,
            'from_date' => $r->biller_description,
            'to_date' => $r->biller_status
        ]);

        if ($db_action == true) {
            $notification = "Service Batch added successfully!";
            $color = "success";
        } else {
            $notification = "Service Batch added un successfully!";
            $color = "danger";
        }

        return redirect('agency/view_biller')->with('notification', $notification)->with('color', $color);
    }


    public function edit($charge)
    {
        $data = explode('-', $charge);

        $charge_id = $data[0];
        $id = $data[1];

        $services = TblIbCharge::all();
        //$agentservices = TblIbService::all();
        $services = TblIbAllService::all();
        $types = TblIbChargeType::all();
        switch ($charge_id) {
            case "1":
                $service = TblIbFixedCharge::where('charges_fixed_id', $id)
                    ->get()->first();
                break;
            case "2":
                $service = TblIbPercentCharge::where('percentage_id', $id)
                    ->get()->first();

                break;
            case "3":
                $service = TblIbIntervalCharge::where('interval_id', $id)
                    ->get()->first();

                break;
            case "5":
                $service = TblIbIntervalPercentCharge::where('interval_id', $id)
                    ->get()->first();
                break;
            default:
                //Nothing will be done if id is not as specified above
                break;
        }


        return view('ib.charges.edit', compact('agentservices', 'services', 'types', 'service'));
    }


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
        if ($request->charge_type == 1) {
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


    //batch download
    public function batchDownload(Request $r)
    {
        //download the batch
        if (isset($r->batch_id)) {
            $batch_id = $r->batch_id;
            $batch = TblIBChargesBatch::where("batch_id", $batch_id)->get();
            $charges = TblIBAllCharges::where("batch_id", $batch_id)->get();
        } else {
            return redirect()->back()->with('notification', "Please specify a batch!")->with('color', "danger");
        }
        return view('agency.charges.download', compact('batches', 'charges'));
    }


    //added by Evance Nganyaga
    public function batchOperations(Request $r)
    {
        $uid =  Auth::user()->id;
        //validate the operation
        if (isset($r->op)) {
            $op = $r->op;
            //validate the batch id
            if (isset($r->batch_id)) {
                $batch_id = $r->batch_id;
                switch ($op) {
                    case '1':
                        //this is add a new batch operation
                        $batch = new TblIBChargesBatch();
                        $batch->batch_status = 2;
                        $batch->from_date = null;
                        $batch->to_date = null;
                        $batch->file_path = "./";
                        //initiator and approver algorithm
                        $batch->initiator_id = $uid;
                        //insert a new batch
                        if ($batch->save()) {
                            return redirect()->back()->with('notification', "Batch created successfully!")->with('color', "success");
                        } else {
                            return redirect()->back()->with('notification', "Batch created unsuccessfully!")->with('color', "danger");
                        }
                        break;
                    case '2':
                        //this is duplicate batch operation
                        $duplicate = new TblIBChargesBatch();
                        $duplicate->batch_status = 2;
                        $duplicate->from_date = null;
                        $duplicate->to_date = null;
                        $duplicate->file_path = "./";
                        $duplicate->initiator_id = $uid;
                        //insert a new batch
                        if ($duplicate->save()) {
                            $batch_charges = TblIBAllCharges::where('batch_id', $batch_id)->get();
                            $iflag = 0;
                            foreach ($batch_charges as $bc) {
                                //prepare population
                                $charge = new TblIBAllCharges;
                                $charge->service_id = $bc->service_id;
                                $charge->charge_type = $bc->charge_type;
                                $charge->from_amount = $bc->from_amount;
                                $charge->to_amount = $bc->to_amount;
                                $charge->amount = $bc->amount;
                                $charge->payee = $bc->payee;
                                $charge->amount_percentage = $bc->amount_percentage;
                                $charge->status = 1;
                                $charge->batch_id = $duplicate->batch_id;
                                $charge->initiator_id = $uid;
                                $charge->save();
                            }
                            return redirect()->back()->with('notification', "Batch #" . $batch_id . " and Charges duplicated to Batch #" .  $duplicate->batch_id . " successfully!")->with('color', "success");
                        } else {
                            //failed to create a duplicate batch
                            return redirect()->back()->with('notification', "Batch and Charges duplicated unsuccessfully!")->with('color', "danger");
                        }

                        break;
                    case '3':
                        //this is download batch operation
                        $batch = TblIBChargesBatch::where("batch_id", $batch_id)->get()[0];
                        $charges = TblIBAllCharges::where("batch_id", $batch_id)->get();

                        $xls = new IBServiceChargeExport();
                        $xls->batch = $batch;
                        $xls->charges = $charges;
                        return Excel::download($xls, "Batch #" . $batch->batch_id . " Service Charges.xls");



                        //$pdf =  PDF::loadView('agency.charges.download', compact('batch','charges'));
                        //$pdf->setPaper('A4', 'landscape');
                        //return $pdf->download("Batch #". $batch->batch_id." Service Charges.pdf");

                        //return view('agency.charges.download', compact('batch','charges'));
                        break;
                    case '4':
                        //this is activate batch operation
                        $batch  = TblIBChargesBatch::where('batch_id', $batch_id)->get()[0];
                        //dd($batch);
                        if ($uid == $batch->initiator_id) {
                            //this is the same user so no activation
                            return redirect()->back()->with('notification', "You can not activate the batch, you have initiated it!")->with('color', "danger");
                        } else {
                            //deactivate all then activate this batch
                            $deactivateall = TblIBChargesBatch::where('batch_id', '!=', $batch_id)->update([
                                'batch_status' => 2,
                            ]);
                            if ($deactivateall == true) {
                                $update = TblIBChargesBatch::where('batch_id', $batch_id)
                                    ->update([
                                        'batch_status' => 1,
                                        'from_date' => date('d-m-Y'),
                                        'approver_id' => $uid,
                                        'to_date' => null
                                    ]);
                                $update = TblIBAllCharges::where(['batch_id' => $batch_id])->update(['status' => 1]);    
                                if ($update == TRUE) {
                                    return redirect()->back()->with('notification', "Batch has been activated successfully!")->with('color', "success");
                                } else {
                                    return redirect()->back()->with('notification', "Batch has been activated unsuccessfully!")->with('color', "danger");
                                }
                            } else {
                                return redirect()->back()->with('notification', "Batch has been activated unsuccessfully, failed to deactivate other batches!")->with('color', "danger");
                            }
                        }

                        break;
                    case '5':
                        //this is deactivate batch operation
                        $batch  = TblIBChargesBatch::where('batch_id', $batch_id);

                        $update = TblIBChargesBatch::where('batch_id', $batch_id)
                            ->update([
                                'batch_status' => 2,
                                'to_date' => date('d-m-Y')
                            ]);
                        if ($update == TRUE) {
                            return redirect()->back()->with('notification', "Batch has been deactivated successfully!")->with('color', "success");
                        } else {
                            return redirect()->back()->with('notification', "Batch has been deactivated unsuccessfully!")->with('color', "danger");
                        }
                        break;
                    default:
                        //there is no operation
                        return redirect()->back()->with('notification', "Please specify the bacth operation!")->with('color', "danger");
                        break;
                }
            } else {
                //there is no batch id attached
                return redirect()->back()->with('notification', "Please specify the bacth to perform operations!")->with('color', "danger");
            }
        } else {
            //there is no specified operations sent
            return redirect()->back()->with('notification', "Please specify the operation to be performed on batch!")->with('color', "danger");
        }
    }

    //added by Evance Nganyaga
    public function deleteServiceCharge(Request $r)
    {
        $charge_id = $r->charge_id;
        $delete = TblIBAllCharges::where('charge_id', $charge_id)->delete();
        if ($delete == true) {
            return redirect()->back()->with('notification', "Service charge removed successfully from batch!")->with('color', "success");
        } else {
            return redirect()->back()->with('notification', "Service charge removed unsuccessfully from batch!")->with('color', "danger");
        }
    }

    //added by me

    public function import(Request $request) {
            //$validator = Validator::make(request()->all(), ['file' => 'required|mimes:xlsx, xls']);
	  	   $validator = Validator::make(
  [
      'file'      => $request->file,
      'extension' => strtolower($request->file->getClientOriginalExtension()),
  ],
  [
      'file'          => 'required',
      'extension'      => 'required|in:xlsx,xls',
  ]
);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return redirect()->back()->withErrors($errors);
            }
            DB::beginTransaction();
            try{
               $batch_id = TblIBChargesBatch::query()->insertGetId(
                            [  
                               'batch_status' => 2,
                               'from_date' => date('Y-m-d H:i:s'),
                               'file_path'    => './'
                            ] 
               );
           
           Excel::import(new ServiceChargesImport($batch_id), request()->file('file'));
           $notification = 'Charges Uploaded successfully!';
           $color = 'success';  
            return redirect()->route('ib.charges.index')->with('notification', $notification)->with('color', $color);   
            } 
            catch (\ValidationException $e) {
                return redirect()->back();
            }
            catch(Exception | Error $e){
            alert()->error($e->getMessage(), 'An error occurred!')->autoclose(40000);
            DB::rollBack();

        }
    }

}
