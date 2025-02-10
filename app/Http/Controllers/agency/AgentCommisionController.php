<?php

namespace App\Http\Controllers\Agency;


use App\TblTransaction;

use App\TblABCommissionPaymentsBatch;
use App\TblABCommissionPayments;
use App\TblABServiceCommision;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//added by Evance Nganyaga
use App\AuditTrailLogs;
use App\Exports\ABCommisionDistributionExport;
use Excel;

use App\TblABBankAccounts;

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
        $uid =  Auth::user()->id;
        $from_date = $r->from_date;
        $to_date = $r->to_date;
                //replace the - in dates
         $from_date = str_replace('-', '', $from_date) . "000000";
         $to_date = str_replace('-', '', $to_date) . "235959";
        $db = DB::connection('sqlsrv4');

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

        $commissions =  collect($commissions);

        //return $commissions;

        //count the commissions and add the data to the commission paymentes table
        if (count($commissions) > 0) {
            //create a new batch
            $batch = new TblABCommissionPaymentsBatch();
            $batch->issued_date = date('d-m-Y');
            $batch->from_date = $r->from_date;
            $batch->to_date =  $r->to_date;
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
                $db =  new TblABCommissionPayments();
                $db->agent_id = $agent_id;
                $db->commission_account = $commission_account;
                $db->amount = $amount;
                $db->number_of_transaction = $number_of_transactions;
                $db->posting_date = date('d-m-Y');
                $db->batch_id = $batch_id;
                $db->save();
                $payment_id = $db->payment_id;
            }
            $notification = "Batch created successfully!";
            $color = "success";
        } else {
            $notification = "Batch created unsuccessfully!";
            $color = "danger";
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);


        //$batches = TblABCommissionPaymentsBatch::all();
        //return view("agency.commissions.commissionspayments", compact('batches'))->with(['notification' => $notification, 'color' => $color]);
        //return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions'))->with(['notification' => "Batch disapproved successfully!", 'color' => 'success']);

        /**
         * 
         * 
            {
                "txn_id": "20",
                "operator_id": "100004",
                "trans_agent_terminal_id": "MKCB00002",
                "trxn_name": "AGENT_BALANCE_INQUIR",
                "trxn_type": "BI",
                "amount": "00",
                "charges": "NA",
                "trans_datetime": "2020-07-10-09:13:26",
                "transactionID": "AB.59276724.15943616",
                "date": "2020-07-10-09:13:26",
                "response_code": "",
                "responseMessage": "",
                "trxn_status": "",
                "customer_account": "00110100830301",
                "trxn_start_time": "2020-07-10-09:13:26",
                "trxn_end_time": "",
                "transaction_reversed": "1",
                "initiator_id": null,
                "approver_id": null,
                "reprinting_value": null,
                "serialID": null,
                "stan": null,
                "rowIdCharge": null,
                "serialIDCharge": null,
                "batchID": null,
                "batchIDCharge": null,
                "rowId": null,
                "third_party_response": null
            }

            SELECT TOP (1000) [batch_id]
                ,[issued_date]
                ,[from_date]
                ,[to_date]
                ,[status]
                ,[initiator_id]
                ,[approver_id]
                ,[total_amount]
            FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_commission_payments_batch]

            SELECT TOP (1000) [payment_id]
                ,[agent_id]
                ,[commission_account]
                ,[amount]
                ,[number_of_transaction]
                ,[posting_date]
                ,[batch_id]
            FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_commission_payments]
         
            

            [{
                "commission_id": 1,
                "commission_amount": "200",
                "agent_amount": "60.0",
                "bank_amount": "60.0",
                "third_party_amount": "80.0",
                "agent_id": null,
                "is_reversed": null,
                "is_paid": "1",
                "transactionID": "AB.59296274.15945573",
                "initiator_id": "5",
                "approver_id": "5",
                "payment_id": null
            }]

            [{
                "agent_id": "10004",
                "agent_msisdn": "255746085566",
                "agent_language": "1",
                "agent_date_registered": null,
                "agent_username": "0746085566",
                "agent_password": "6523",
                "agent_valid_id_number": null,
                "agent_full_name": "Hebron Henry",
                "agent_business_license_number": "123455",
                "business_certificate_registration_number": "123456",
                "agent_status": "1",
                "agent_bank_id": "1",
                "agent_reg_source": "Mikocheni",
                "agent_init": "1",
                "agent_appr": "1",
                "agent_address": "P.O BOX 152 Dar",
                "agent_location": "Mikocheni",
                "agent_float_limit": "1000000",
                "agent_daily_limit": "10000000",
                "branch_id": "2",
                "agent_menu": "BI~DC~WC~FT~AS~MS~UP~BW~SC~IP",
                "is_iniator": "1",
                "is_approver": "0"
            }]

            SELECT u.UserName, SUM(t.Value) AS Balance
            FROM Transactions t
            JOIN Users u ON u.ID = t.UserID
            GROUP BY u.UserName

            select 
                agent.agent_id,
                sum(cast(service_commission.agent_amount as money)) as commission_amount,
                count(service_commission.transactionID) as transactions_count,
            from 
                tbl_agency_banking_service_commission as service_commission,
                tbl_agency_banking_agents as agent,
                tbl_agency_banking_transactions as transactions
            where
                transaction.transactionID = service_commission.transactionID and
                agent.aget_id = service_commission.agent_id and
                service_commission.is_paid = 0 and
                service_commission.payment_id = null and
                transaction.trans_datetime between(date1, date2)
            group by agent.agent_id

                
         */
    }

    //function to perform operations on a commission batch
    public function commisionBatchOperations(Request $r)
    {
        $uid =  Auth::user()->id;
        $batch_id = $r->batch_id;
        if (isset($r->op)) {
            $op = intval($r->op);
        } else {
            return redirect()->back()->with(['notification' => "There is no operation specified", 'color' => 'warning']);
        }

        //parse the operations performed
        switch ($op) {
            case 1:
                //this is approve the batch and pay
                //get all the batch transactions
                $batch = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)->get()[0];
                $from_date = $batch->from_date;
                $to_date = $batch->to_date;
                //replace the - in dates
                $from_date = str_replace('-', '', $from_date) . "000000";
                $to_date = str_replace('-', '', $to_date) . "235959";

                $commissions = TblABCommissionPayments::where('batch_id', $batch_id)->get();
                //[TODO] - bulk post the data to CBS Payments
                foreach ($commissions as $commission) {
                    //update the posting date on commission payment
                    $update = TblABCommissionPayments::where('payment_id', $commission->payment_id)
                        ->update([
                            'posting_date' => date('d-m-Y')
                        ]);

                    //update the service commission, by date between 
                    $update = TblABServiceCommision::where('payment_id', $commission->payment_id)
                        ->update([
                            'is_paid' => 1
                        ]);
                }

                //update all the batches
                $status = 2;
                $update = TblABCommissionPaymentsBatch::where('batch_id', $batch_id)
                    ->update([
                        'status' => 2
                    ]);


                //update all the commissions set to paid
                $update = TblABServiceCommision::whereBetween('date', [$from_date, $to_date])
                    ->update([
                        'is_paid' => 1
                    ]);


                if ($update == true) {
                    return redirect()->back()->with(['notification' => "Batch approved successfully!", 'color' => 'success']);
                } else {
                    return redirect()->back()->with(['notification' => "Batch approved unsuccessfully!", 'color' => 'danger']);
                }
                break;

            case 2:
                //this is disapprove the batch
                //update all the batches
                $status = 3;
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
                $payment_id = $payments[0]->payment_id;
                $commissions = TblABServiceCommision::whereBetween('date', [$from_date, $to_date])->get();
                //return $payments;


                if ($update == true) {
                    return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions'))->with(['notification' => "Batch disapproved successfully!", 'color' => 'success']);
                    //return redirect()->back()->with(['notification' => "Batch disapproved successfully!", 'color' => 'success']);
                } else {
                    return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions'))->with(['notification' => "Batch disapproved unsuccessfully!", 'color' => 'danger']);
                }
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
        }
    }

    //get the commission batch details
    public function indexCommssionBatch($id) {
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
        $payment_id = $payments[0]->payment_id;
        $commissions = TblABServiceCommision::whereHas('transaction', function($q) {
    $q->where('trxn_type', 'DC');
})->whereBetween('date', [$from_date, $to_date])->get();
foreach($commissions as $commission)
{
$total +=$commission->commission_amount;
}
$noOfTrans = count($commissions);
        return view("agency.commissions.commissionspayments_details", compact('batches', 'batch', 'payments', 'commissions', 'noOfTrans', 'total'));
    }
}
