<?php

namespace App\Http\Controllers\agency;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\TblAgentChargeType;
use App\TblAgentFixedCharge;
use App\TblAgentIntervalCharge;
use App\TblAgentIntervalPercentCharge;
use App\TblAgentPercentCharge;
use App\TblAgentService;
use App\TblCharge;
use App\Imports\ServiceChargesABImport;


//adopt the new charges structure
use App\TblABAllCharges;
use App\TblABChargesBatch;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


//use Excel;
use PDF;

use App\Exports\ABServiceChargeExport;

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
        abort_unless(\Gate::allows('ab_service_charges_view'), 403);
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
        
        $activebatch = TblABChargesBatch::where('batch_status', 1)->first();
        $activebatch_id = null;

        if($activebatch)
        {
            $activebatch_id = $activebatch->batch_id;
        }

        //these charges are hard codded, need to be changed to dynamics
        $fixedcharges = TblABAllCharges::where('charge_type', 1)->where('batch_id', $activebatch_id)->get();
        $percentagecharges = TblABAllCharges::where('charge_type', 2)->where('batch_id', $activebatch_id)->get();
        $intervalcharges = TblABAllCharges::where('charge_type', 3)->where('batch_id', $activebatch_id)->get();
        $intervalpercentagecharges = TblABAllCharges::where('charge_type', 4)->where('batch_id', $activebatch_id)->get();


        return view('agency.charges.charges', compact(
            'datas',
            'services',
            'agentservices',
            'types',
            'charges',
            'activebatch',
            'batches',
            'fixedcharges',
            'percentagecharges',
            'intervalcharges',
            'intervalpercentagecharges'
        ));
    }

    public function import(Request $request) {
           // $validator = Validator::make(request()->all(), ['file' => 'required|mimes:xlsx, xls']);
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
               $batch_id = TblABChargesBatch::query()->insertGetId(
                            [  
                               'batch_status' => 2,
                               'from_date' => date('Y-m-d H:i:s'),
                               'file_path'    => './'
                            ] 
               );
           
           Excel::import(new ServiceChargesABImport($batch_id), request()->file('file'));
          
	   $notification = 'Charges Uploaded successfully!';
           $color = 'success';	
            return redirect()->route('agency.charges.index')->with('notification', $notification)->with('color', $color);   
            } 
            catch (\ValidationException $e) {
                return redirect()->back();
		}
            catch(Exception | Error $e){
            alert()->error($e->getMessage(), 'An error occurred!')->autoclose(40000);
            DB::rollBack();

        }
    }

    public function indexServiceChargesByBatch($id)
    {
        abort_unless(\Gate::allows('ab_service_charges_view'), 403);
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
        if (!isset($id)) {
            $activebatch = TblABChargesBatch::where('batch_status', 1)->get()[0];
            $activebatch_id = $activebatch->batch_id;
        } else {
            $activebatch_id = $id;
            $activebatch = TblABChargesBatch::where('batch_id', $activebatch_id)->get()[0];
        }


        //these charges are hard codded, need to be changed to dynamics
        $fixedcharges = TblABAllCharges::where('charge_type', 1)->where('batch_id', $activebatch_id)->get();
        $percentagecharges = TblABAllCharges::where('charge_type', 2)->where('batch_id', $activebatch_id)->get();
        $intervalcharges = TblABAllCharges::where('charge_type', 3)->where('batch_id', $activebatch_id)->get();
        $intervalpercentagecharges = TblABAllCharges::where('charge_type', 4)->where('batch_id', $activebatch_id)->get();


        return view('agency.charges.charges', compact(
            'datas',
            'services',
            'agentservices',
            'types',
            'charges',
            'activebatch',
            'batches',
            'fixedcharges',
            'percentagecharges',
            'intervalcharges',
            'intervalpercentagecharges'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('ab_service_charges_create'), 403);
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
    public function store(Request $request){

        abort_unless(\Gate::allows('ab_service_charges_create'), 403);
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


        /**

        SELECT TOP (1000) [charge_id]
        ,[service_id]
        ,[charge_type]
        ,[from_amount]
        ,[to_amount]
        ,[amount]
        ,[amount_percentage]
        ,[batch_id]
        ,[status]
        FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_all_charges]

         *
         */



        $charge = new TblABAllCharges;
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


    //added by Evance Nganyaga
    public function storeBatch(Reuest $r)
    {
        abort_unless(\Gate::allows('ab_service_charges_create'), 403);
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        $batch_status = 1;
        $db_action = TblABChargesBatch::insert([
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


    //batch download
    public function batchDownload(Request $r)
    {
        abort_unless(\Gate::allows('ab_service_charges_export'), 403);
        //download the batch
        if (isset($r->batch_id)) {
            $batch_id = $r->batch_id;
            $batch = TblABChargesBatch::where("batch_id", $batch_id)->get();
            $charges = TblABAllCharges::where("batch_id", $batch_id)->get();
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
                        $batch = new TblABChargesBatch();
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
                        $duplicate = new TblABChargesBatch();
                        $duplicate->batch_status = 2;
                        $duplicate->from_date = null;
                        $duplicate->to_date = null;
                        $duplicate->file_path = "./";
                        $duplicate->initiator_id = $uid;
                        //insert a new batch
                        if ($duplicate->save()) {
                            $batch_charges = TblABAllCharges::where('batch_id', $batch_id)->get();
                            $iflag = 0;
                            foreach ($batch_charges as $bc) {
                                //prepare population

                                /**

                                    SELECT TOP (1000) [charge_id]
                                    ,[service_id]
                                    ,[charge_type]
                                    ,[from_amount]
                                    ,[to_amount]
                                    ,[amount]
                                    ,[amount_percentage]
                                    ,[batch_id]
                                    ,[status]
                                    FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_all_charges]

                                 *
                                 */



                                $charge = new TblABAllCharges;
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
                        $batch = TblABChargesBatch::where("batch_id", $batch_id)->get()[0];
                        $charges = TblABAllCharges::where("batch_id", $batch_id)->get();

                        $xls = new ABServiceChargeExport();
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
                        $batch  = TblABChargesBatch::where('batch_id', $batch_id)->get()[0];
                        //dd($batch);
                        if ($uid == $batch->initiator_id) {
                            //this is the same user so no activation
                            return redirect()->back()->with('notification', "You can not activate the batch, you have initiated it!")->with('color', "danger");
                        } else {
                            //deactivate all then activate this batch
                            $deactivateall = TblABChargesBatch::where('batch_id', '!=', $batch_id)->update([
                                'batch_status' => 2,
                            ]);
                            $update = TblABChargesBatch::where('batch_id', $batch_id)
                                    ->update([
                                        'batch_status' => 1,
                                        'from_date' => date('d-m-Y'),
                                        'approver_id' => $uid,
                                        'to_date' => null
                                    ]);
                            return redirect()->back()->with('notification', "Batch has been activated successfully!")->with('color', "success");

                        }

                        break;
                    case '5':
                        //this is deactivate batch operation
                        $batch  = TblABChargesBatch::where('batch_id', $batch_id);
                        
                        $deactivateall = TblABChargesBatch::where('batch_id', '!=', $batch_id)
                                                            ->where('batch_status', 2)->first();
                        $deactivateall->batch_status = 1;
                        $deactivateall->save();

                        $update = TblABChargesBatch::where('batch_id', $batch_id)
                            ->update([
                                'batch_status' => 2,
                                'to_date' => date('d-m-Y')
                            ]);
                        
                        return redirect()->back()->with('notification', "Batch has been deactivated successfully!")->with('color', "success");

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
        abort_unless(\Gate::allows('ab_service_charges_export'), 403);
        $charge_id = $r->charge_id;
        $delete = TblABAllCharges::where('charge_id', $charge_id)->delete();
        if ($delete == true) {
            return redirect()->back()->with('notification', "Service charge removed successfully from batch!")->with('color', "success");
        } else {
            return redirect()->back()->with('notification', "Service charge removed unsuccessfully from batch!")->with('color', "danger");
        }
    }
  
    

}
 
