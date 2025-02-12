<?php

namespace App\Http\Controllers\agency;


use App\Exports\ABCommisionDistributionExport;
use App\Http\Controllers\Controller;
use App\TblABBankAccounts;
use App\TblABCommissionPayments;
use App\TblABCommissionPaymentsBatch;
use App\TblABServiceCommision;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentCommisionController extends Controller
{

    public function index()
    {
        $today = date("Y-m-d");
        $batches = TblABCommissionPaymentsBatch::all();
        return view("agency.commissions.commissionspayments", compact('batches', 'today'));
    }


    //function to generate commsion batch
    public function generateCommssionBatches(Request $r)
    {
        $uid = Auth::user()->id;
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        //replace the - in dates
        $from_date = str_replace('-', '', $from_date) . "000000";
        $to_date = str_replace('-', '', $to_date) . "235959";
        $db = DB::connection('sqlsrv4');

        try {
            $commissions = $db->select("
                    select
                        ag.agent_id,
                        sum(cast(cm.commission_amount as money)) as commission_total,
                        sum(cast(cm.agent_amount as money)) as agent_commision,
                        sum(cast(cm.bank_amount as money)) as bank_commision,
                        sum(cast(cm.third_party_amount as money)) as third_party_commission,
                        count(cm.transactionID) as transactions
                    from
                        tbl_agency_banking_service_commission as cm
                        join
                            tbl_agency_banking_agents as ag
                        on
                            ag.agent_id = cm.agent_id
                    where
                        cm.date between '$from_date' and '$to_date'
                    group by ag.agent_id
                ");

            $commissions = collect($commissions);

            //count the commissions and add the data to the commission paymentes table
            if (count($commissions) > 0) {
                //create a new batch
                $batch = new TblABCommissionPaymentsBatch();
                $batch->issued_date = date('d-m-Y');
                $batch->from_date = $r->from_date;
                $batch->to_date = $r->to_date;
                $batch->status = 1;
                $batch->initiator_id = $uid;
                $batch->save();
                $batch_id = $batch->batch_id;

                //there are unpaid commissions, add them to the database
                foreach ($commissions as $commission) {
                    //add to the payment details
                    $agent_id = $commission->agent_id;
                    //get the commission account
                    $accounts = TblABBankAccounts::where('agent_id', $agent_id)->where('account_type_id', 2)->get()[0];
                    $commission_account = $accounts->bank_account;
                    $amount = $commission->agent_commision;
                    $number_of_transactions = $commission->transactions;

                    //add the batch to payments
                    $db = new TblABCommissionPayments();
                    $db->agent_id = $agent_id;
                    $db->commission_account = $commission_account;
                    $db->amount = $amount;
                    $db->number_of_transaction = $number_of_transactions;
                    $db->posting_date = date('d-m-Y');
                    $db->batch_id = $batch_id;
                    $db->save();
                }
                $notification = "Batch created successfully!";
                $color = "success";
            } else {
                $notification = "Batch created unsuccessfully!";
                $color = "danger";
            }

        } catch (\Exception $e) {
            Log::error('AGENCY-COMMISSION-EXCEPTION: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "An error occurred while creating the batch: " . $e->getMessage();
            $color = "danger";
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    //function to perform operations on a commission batch
    public function commisionBatchOperations(Request $r)
    {
        $uid = Auth::user()->id;
        $batch_id = $r->batch_id;
        if (isset($r->op)) {
            $op = (int)$r->op;
        } else {
            return redirect()->back()->with(['notification' => "There is no operation specified", 'color' => 'warning']);
        }

        try {
            //parse the operations performed
            switch ($op) {
                case 1:
                    $batch = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)->get()[0];
                    $from_date = $batch->from_date;
                    $to_date = $batch->to_date;
                    $from_date = str_replace('-', '', $from_date) . "000000";
                    $to_date = str_replace('-', '', $to_date) . "235959";

                    $commissions = TblABCommissionPayments::where('batch_id', $batch_id)->get();
                    foreach ($commissions as $commission) {
                        //update the posting date on commission payment
                        $commissionPayments = TblABCommissionPayments::where('payment_id', $commission->payment_id)
                            ->update([
                                'posting_date' => date('d-m-Y')
                            ]);

                        //update the service commission, by date between
                        $serviceCommission = TblABServiceCommision::where('payment_id', $commission->payment_id)
                            ->update([
                                'is_paid' => 1
                            ]);
                    }

                    $commissionPaymentsBatch = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)
                        ->update([
                            'status' => 2
                        ]);

                    //update all the commissions set to paid
                    $serviceCommissionDate = TblABServiceCommision::whereBetween('date', [$from_date, $to_date])
                        ->update([
                            'is_paid' => 1
                        ]);

                    if ($commissionPayments && $serviceCommissionDate && $commissionPaymentsBatch && $serviceCommission) {
                        return redirect()->back()->with(['notification' => "Batch approved successfully!", 'color' => 'success']);
                    }

                    return redirect()->back()->with(['notification' => "Batch approved unsuccessfully!", 'color' => 'danger']);
                    break;

                case 2:
                    $update = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)
                        ->update([
                            'status' => 3,
                            'approver_id' => $uid
                        ]);

                    $batches = TblABCommissionPaymentsBatch::all();
                    $batch = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)->get()[0];
                    $from_date = $batch->from_date;
                    $to_date = $batch->to_date;
                    //prepare the commission transactions date
                    $from_date = str_replace('-', '', $from_date) . "000000";
                    $to_date = str_replace('-', '', $to_date) . "235959";
                    $payments = TblABCommissionPayments::where('batch_id', $batch_id)->get();
                    $commissions = TblABServiceCommision::whereBetween('date', [$from_date, $to_date])->get();
                    if ($update) {
                        return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions'))->with(['notification' => "Batch disapproved successfully!", 'color' => 'success']);
                    }
                    return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions'))->with(['notification' => "Batch disapproved unsuccessfully!", 'color' => 'danger']);
                    break;

                case 4:
                    //get the data
                    $batch = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)->get()[0];
                    $from_date = $batch->from_date;
                    $to_date = $batch->to_date;
                    //prepare the commission transactions date
                    $from_date = str_replace('-', '', $from_date) . "000000";
                    $to_date = str_replace('-', '', $to_date) . "235959";

                    $commissions = TblABServiceCommision::whereBetween('date', [$from_date, $to_date])->get();
                    //this is export the batch
                    $xls = new ABCommisionDistributionExport();
                    $xls->commissions = $commissions;
                    return Excel::download($xls, " Batch #$batch_id Commission Distribution " . $batch->from_date . " - " . $batch->to_date . ".xlsx");
                    break;
                default:
                    return redirect()->back()->with(['notification' => "There is no operation specified", 'color' => 'danger']);
            }
        } catch (\Exception $e) {
            Log::error('COMMISSION-BATCH-EXCEPTION: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with(['notification' => "An error occurred: " . $e->getMessage(), 'color' => 'danger']);
        }
    }

    //get the commission batch details
    public function indexCommssionBatch($id)
    {
        try {
            //this is view details of the batch
            $total = 0;
            $batches = TblABCommissionPaymentsBatch::all();
            $batch = TblABCommissionPaymentsBatch::where('batch_id', $id)->get()->first();
            $from_date = $batch->from_date;
            $to_date = $batch->to_date;
            //prepare the commission transactions date
            $from_date = str_replace('-', '', $from_date) . "000000";
            $to_date = str_replace('-', '', $to_date) . "235959";
            $payments = TblABCommissionPayments::where('batch_id', $id)->get();
            $commissions = TblABServiceCommision::whereHas('transaction', function ($q) {
                $q->where('trxn_type', 'DC');
            })->whereBetween('date', [$from_date, $to_date])->get();
            foreach ($commissions as $commission) {
                $total += $commission->commission_amount;
            }
            $noOfTrans = count($commissions);
            return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions', 'noOfTrans', 'total'));
        } catch (\Exception $e) {
            Log::error('INDEX-COMMISSION-BATCH-EXCEPTION: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with(['notification' => "An error occurred while fetching batch details ", 'color' => 'danger']);
        }
    }
}
