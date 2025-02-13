<?php

namespace App\Http\Controllers\agency;

use App\Exports\ABServiceChargeExport;
use App\Http\Controllers\Controller;
use App\Imports\ServiceChargesABImport;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('ab_service_charges_view'), 403);

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

            // adopt the new charges structure
            $charges = TblABAllCharges::all();
            $batches = TblABChargesBatch::all();

            $activebatch = TblABChargesBatch::where('batch_status', 1)->first();
            $activebatch_id = $activebatch ? $activebatch->batch_id : null;

            // these charges are hard coded, need to be changed to dynamics
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
        } catch (\Exception $e) {
            Log::error("Error retrieving data from the database: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with('notification', 'An error occurred! ' . $e->getMessage())->with('color', 'danger');
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make(
            [
                'file' => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
            ],
            [
                'file' => 'required|file',
                'extension' => 'in:xlsx,xls',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->all());
        }

        DB::beginTransaction();

        try {
            $batch_id = TblABChargesBatch::create([
                'batch_status' => 2,
                'from_date' => now(),
                'file_path' => './'
            ])->batch_id;

            Excel::import(new ServiceChargesABImport($batch_id), $request->file('file'));

            DB::commit();

            return redirect()
                ->route('agency.charges.index')
                ->with(['notification' => 'Charges Uploaded successfully!', 'color' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error importing data', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()
                ->route('agency.charges.index')
                ->with(['notification' => 'An error occurred!', 'color' => 'danger']);
        }
    }

    public function indexServiceChargesByBatch($id)
    {
        abort_unless(\Gate::allows('ab_service_charges_view'), 403);

        try {
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

            $charges = TblABAllCharges::all();
            $batches = TblABChargesBatch::all();

            if (!isset($id)) {
                $activebatch = TblABChargesBatch::where('batch_status', 1)->first();
                $activebatch_id = $activebatch ? $activebatch->batch_id : null;
            } else {
                $activebatch = TblABChargesBatch::find($id);
                $activebatch_id = $activebatch ? $activebatch->batch_id : null;
            }

            if (!$activebatch_id) {
                return redirect()->back()->with('notification', 'Active batch not found!')->with('color', 'danger');
            }

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
        } catch (\Exception $e) {
            Log::error("Error fetching service charges by batch: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with('notification', 'An error occurred! ' . $e->getMessage())->with('color', 'danger');
        }
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('ab_service_charges_create'), 403);
        try {
            $batch_id = $request->batch_id;
            $charge_type_id = $request->charge_type_id;
            $agent_service_id = $request->agent_service_id;
            $charge_amount = $request->charge_amount;
            $from_amount = $request->from_amount;
            $to_amount = $request->to_amount;
            $charge_percent = $request->charge_percent;
            $payee = $request->payee;

            if ($payee === "0") {
                $payee = "customer";
            } elseif ($payee === "1") {
                $payee = "bank";
            } else {
                $payee = "thirdparty";
            }

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
                return redirect()->back()->with('notification', "Charge added successfully!")->with('color', "success");
            } else {
                return redirect()->back()->with('notification', "Failed to add charge!")->with('color', "danger");
            }
        } catch (\Exception $e) {
            Log::error('Error adding charge', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('notification', "An error occurred while adding the charge!")->with('color', "danger");
        }
    }

    //batch download
    public function batchDownload(Request $r)
    {
        abort_unless(\Gate::allows('ab_service_charges_export'), 403);

        try {
            //download the batch
            if (isset($r->batch_id)) {
                $batch_id = $r->batch_id;
                $batch = TblABChargesBatch::where("batch_id", $batch_id)->get();
                $charges = TblABAllCharges::where("batch_id", $batch_id)->get();
            } else {
                return redirect()->back()->with('notification', "Please specify a batch!")->with('color', "danger");
            }
            return view('agency.charges.download', compact('batch', 'charges'));
        } catch (\Exception $e) {
            Log::error("Error downloading batch: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with('notification', 'An error occurred while downloading the batch!')->with('color', 'danger');
        }
    }


    //added by Evance Nganyaga
    public function batchOperations(Request $r)
    {
        $uid = Auth::user()->id;
        try {
            //validate the operation
            if (isset($r->op)) {
                $op = $r->op;
                //validate the batch id
                if (isset($r->batch_id)) {
                    $batch_id = $r->batch_id;
                    switch ($op) {
                        case '1':
                            $batch = TblABChargesBatch::create([
                                'batch_status' => 2,
                                'from_date' => null,
                                'to_date' => null,
                                'file_path' => "./",
                                'initiator_id' => $uid,
                            ]);

                            if ($batch) {
                                return redirect()->back()->with('notification', "Batch created successfully!")->with('color', "success");
                            }

                            return redirect()->back()->with('notification', "Batch created unsuccessfully!")->with('color', "danger");
                            break;
                        case '2':
                            //this is duplicate batch operation
                            $duplicate = TblABChargesBatch::create([
                                'batch_status' => 2,
                                'from_date' => null,
                                'to_date' => null,
                                'file_path' => "./",
                                'initiator_id' => $uid
                            ]);

                            if ($duplicate) {
                                $batch_charges = TblABAllCharges::where('batch_id', $batch_id)->get();
                                $charges = $batch_charges->map(function ($bc) use ($duplicate, $uid) {
                                    return [
                                        'service_id' => $bc->service_id,
                                        'charge_type' => $bc->charge_type,
                                        'from_amount' => $bc->from_amount,
                                        'to_amount' => $bc->to_amount,
                                        'amount' => $bc->amount,
                                        'payee' => $bc->payee,
                                        'amount_percentage' => $bc->amount_percentage,
                                        'status' => 1,
                                        'batch_id' => $duplicate->batch_id,
                                        'initiator_id' => $uid
                                    ];
                                });

                                TblABAllCharges::insert($charges->toArray());

                                return redirect()->back()
                                    ->with('notification', "Batch #$batch_id and Charges duplicated to Batch #{$duplicate->batch_id} successfully!")
                                    ->with('color', "success");
                            }

                            return redirect()->back()
                                ->with('notification', "Batch and Charges duplicated unsuccessfully!")
                                ->with('color', "danger");

                            break;
                        case '3':
                            //this is download batch operation
                            $batch = TblABChargesBatch::where("batch_id", $batch_id)->get()[0];
                            $charges = TblABAllCharges::where("batch_id", $batch_id)->get();

                            $xls = new ABServiceChargeExport();
                            $xls->batch = $batch;
                            $xls->charges = $charges;
                            return Excel::download($xls, "Batch #" . $batch->batch_id . " Service Charges.xls");
                            break;
                        case '4':
                            $batch = TblABChargesBatch::find($batch_id);
                            if (!$batch) {
                                return redirect()->back()->with('notification', "Batch not found!")->with('color', "danger");
                            }

                            if ($uid === $batch->initiator_id) {
                                return redirect()->back()->with('notification', "You cannot activate the batch, you have initiated it!")->with('color', "danger");
                            }

                            TblABChargesBatch::where('batch_id', '!=', $batch_id)
                                ->update(['batch_status' => 2]);

                            $batch->update([
                                'batch_status' => 1,
                                'from_date' => now()->format('d-m-Y'),
                                'approver_id' => $uid,
                                'to_date' => null
                            ]);

                            return redirect()->back()->with('notification', "Batch has been activated successfully!")->with('color', "success");
                            break;
                        case '5':
                            $batch = TblABChargesBatch::find($batch_id);

                            if ($batch) {
                                TblABChargesBatch::where('batch_id', '!=', $batch_id)
                                    ->where('batch_status', 2)
                                    ->update(['batch_status' => 1]);

                                $batch->update([
                                    'batch_status' => 2,
                                    'to_date' => now()->format('d-m-Y')
                                ]);

                                return redirect()->back()->with('notification', "Batch has been deactivated successfully!")->with('color', "success");
                            }

                            return redirect()->back()->with('notification', "Batch not found!")->with('color', "danger");
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
        } catch (\Exception $e) {
            Log::error('Error in batch operation', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return redirect()->back()
                ->with('notification', 'An error occurred during the batch operation! ' . $e->getMessage())
                ->with('color', 'danger');
        }
    }

    //added by Evance Nganyaga
    public function deleteServiceCharge(Request $r)
    {
        abort_unless(\Gate::allows('ab_service_charges_export'), 403);

        try {
            $charge_id = $r->charge_id;
            $delete = TblABAllCharges::where('charge_id', $charge_id)->delete();
            if ($delete) {
                return redirect()->back()->with('notification', "Service charge removed successfully from batch!")->with('color', "success");
            }

            return redirect()->back()->with('notification', "Service charge removed unsuccessfully from batch!")->with('color', "danger");
        } catch (\Exception $e) {
            Log::error('Error deleting service charge', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('notification', 'An error occurred while removing the service charge!')->with('color', "danger");
        }
    }


}

