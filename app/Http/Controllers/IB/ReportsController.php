<?php

namespace App\Http\Controllers\IB;

use App\AtmCard;
use App\Exports\IBCustomersExport;
use App\Exports\IBReportsExport;
use App\Http\Controllers\Controller;
use App\IBTransaction;
use App\IbTransferType;
use App\IbUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

//TODO: Deploy on server
class ReportsController extends Controller
{
    private function convertDate($today)
    {
        $format = "Y-m-d H:i:s";
        $d = strtotime($today);
        return date($format, $d) . "";
    }

    public function index()
    {
        $transferTypes = IbTransferType::whereNotIn('id', [10, 17, 20, 25, 26, 27, 28, 29, 30, 31, 32, 33])->get();
        $transactions = IBTransaction::all();
        $users = IbUser::all();
        $customer_users = IbUser::where('institute_id', "=", null)->orderBy('id', 'desc')->get();
        $cooperate_users = IbUser::where('institute_id', "!=", null)->orderBy('id', 'desc')->get();
        //usage  report payloads

        //card request payloads
        $cards = AtmCard::all();
        $requested_cards = AtmCard::where('status_id', "=", 1)->orderBy('id', 'desc')->get();
        $accepted_cards = AtmCard::where('status_id', "=", 2)->orderBy('id', 'desc')->get();
        $processed_cards = AtmCard::where('status_id', "=", 3)->orderBy('id', 'desc')->get();
        $collected_cards = AtmCard::where('status_id', "=", 4)->orderBy('id', 'desc')->get();

        $today = date("Y-m-d");

        //return the payload
        return view('ib.reports.index', compact(
            'transactions',
            'today',
            'users',
            'cooperate_users',
            'customer_users',
            'cards',
            'requested_cards',
            'accepted_cards',
            'processed_cards',
            'collected_cards',
            'transferTypes'
        ));
    }

    public function export(Request $r)
    {
        try {
            $service_id = $r->service;
            $from_date = $this->convertDate($r->from_date);
            $to_date = $this->convertDate($r->to_date);

            // Initialize sorted title
            $s_title = "Sorted by ";

            // Check if customer type is set and query accordingly
            if (isset($r->customer_type)) {
                // Query users only for customer reports
                $type = $this->determineCustomerType($r, $s_title);
                $users = $this->getUsers($from_date, $to_date, $type);

                // Generate user report
                return $this->generateUserReport($r, $from_date, $to_date, $users,$type);
            } else {
                // Query transactions only for general reports
                $transactions = $this->getTransactions($r, $from_date, $to_date, $s_title);

                // Filter transactions based on status
                $this->filterTransactionsByStatus($r, $transactions, $s_title);

                // Execute queries
                $transactions = $transactions->get();

                // Generate transaction report
                return $this->generateTransactionReport($r, $from_date, $to_date, $s_title, $transactions);
            }
        } catch (\Exception $e) {
            Log::error("IB-REPORT-EXCEPTION: " . json_encode($e->getMessage()));
            return back()->with(['notification' => "Something went wrong!", 'color' => 'danger']);
        }
    }

    private function getTransactions($r, $from_date, $to_date, &$s_title)
    {
        if (isset($r->service) && $r->service != 'all') {
            $s_title .= "Services ";
            if ($r->service == 16) {
                return DB::connection('sqlsrv2')->table('tbl_bulk_payments')
                    ->select(
                        'tbl_institutions.institute_name',
                        'batch_number',
                        'tbl_bulk_payments.institution_id',
                        'account_number',
                        'tbl_bulk_payments.description',
                        'tbl_bulk_payments.created_at',
                        'account_id',
                        'tbl_users.display_name',
                        'tbl_bulk_payment_payees.payee_account',
                        'tbl_bulk_payments.amount',
                        DB::raw('tbl_bulk_payment_payees.amount as payee_amount'),
                        'tbl_bulk_payment_payees.responseCode',
                        'tbl_bulk_payment_payees.responseMessage',
                        'tbl_bulk_payment_payees.serialID',
                        'tbl_bulk_payment_payees.batchID',
                        DB::raw('tbl_bulk_payments.amount as batch_total_amount')
                    )
                    ->leftJoin('tbl_institutions', 'tbl_institutions.id', '=', 'tbl_bulk_payments.institution_id')
                    ->leftJoin('tbl_account', 'tbl_account.id', '=', 'tbl_bulk_payments.account_id')
                    ->leftJoin('tbl_bulk_payment_payees', 'tbl_bulk_payment_payees.bulk_payment_id', '=', 'tbl_bulk_payments.id')
                    ->leftJoin('tbl_users', 'tbl_bulk_payments.created_by', '=', 'tbl_users.id')
                    ->orderBy('tbl_bulk_payment_payees.id', 'desc')
                    ->whereDate('tbl_bulk_payments.created_at', '>=', $from_date)
                    ->whereDate('tbl_bulk_payments.created_at', '<=', $to_date);
            } elseif ($r->service == 18) {
                return DB::connection('sqlsrv2')->table('tbl_eft_bulk_payments')
                    ->select(
                        'tbl_institutions.institute_name',
                        'account_number',
                        'batch_number',
                        'tbl_eft_bulk_payments.description',
                        'tbl_eft_bulk_payments.created_at',
                        'tbl_eft_bulk_payment_payees.payee_name',
                        'tbl_eft_bulk_payment_payees.payee_account',
                        'tbl_bank.name as payee_bank_name',
                        DB::raw('tbl_eft_bulk_payment_payees.amount as payee_amount'),
                        'tbl_eft_bulk_payment_payees.responseCode',
                        'tbl_eft_bulk_payment_payees.responseMessage',
                        'tbl_eft_bulk_payment_payees.serialID',
                        'tbl_eft_bulk_payment_payees.batchID',
                        DB::raw('tbl_eft_bulk_payments.amount as batch_total_amount')
                    )
                    ->leftJoin('tbl_institutions', 'tbl_institutions.id', '=', 'tbl_eft_bulk_payments.institution_id')
                    ->leftJoin('tbl_account', 'tbl_account.id', '=', 'tbl_eft_bulk_payments.account_id')
                    ->leftJoin('tbl_eft_bulk_payment_payees', 'tbl_eft_bulk_payment_payees.bulk_payment_id', '=', 'tbl_eft_bulk_payments.id')
                    ->leftJoin('tbl_bank', 'tbl_eft_bulk_payment_payees.payee_bank_id', '=', 'tbl_bank.id')
                    ->orderBy('tbl_eft_bulk_payment_payees.id', 'desc')
                    ->whereDate('tbl_eft_bulk_payments.created_at', '>=', $from_date)
                    ->whereDate('tbl_eft_bulk_payments.created_at', '<=', $to_date);
            } else {
                return DB::connection('sqlsrv')->table('ib_report_view')
                    ->whereBetween('CREATED AT', [$from_date, $to_date])
                    ->where('TRANSFER TYPE ID', $r->service);
            }
        }

        return DB::connection('sqlsrv')->table('ib_report_view')->whereBetween('CREATED AT', [$from_date, $to_date]);
    }

    private function determineCustomerType($r, $s_title)
    {
        if ($r->customer_type == "Retail") {
            $s_title .= "Retail Customers ";
            return 0; // Retail type
        } else {
            $s_title .= "Corporate Customers ";
            return 1; // Corporate type
        }
    }

    private function getUsers($from_date, $to_date, $type)
    {
        return IbUser::whereBetween("created_at", [$from_date, $to_date])
            ->where("institute_id", $type == 0 ? null : '!=', null)
            ->get();
    }

    private function generateUserReport($r, $from_date, $to_date, $users, $type)
    {
        // Logic to generate user report
        if ($r->format == "xls") {
            $xls = new IBCustomersExport();
            $xls->users = $users;
            $xls->from_date = $from_date;
            $xls->to_date = $to_date;
            $xls->type = $type;

            return Excel::download($xls, "IB-User-Report: $from_date - $to_date.xlsx");
        }

        //PDF format
        return redirect()->back()->with(['notification' => "Registration report is only available in excel format", 'color' => 'danger']);
    }

    private function filterTransactionsByStatus($r, $transactions, &$s_title)
    {
        switch ($r->status) {
            case 'success':
                $s_title .= "Transaction Success ";
                return $transactions->where('STATUS', 'SUCCESS');
            case 'onprogress':
                $s_title .= "Transaction On Progress ";
                return $transactions->where('STATUS', "ON-PROGRESS");
            case 'failed':
                $s_title .= "Transaction Failed ";
                return $transactions->where('STATUS', 'FAILED');
            default:
                $s_title .= "Transaction Status ";
                break;
        }
    }


    private function generateTransactionReport($r, $from_date, $to_date, $s_title, $transactions)
    {
        // Handle report generation for transactions
        if ($r->format == "pdf") {
            $pdf = PDF::loadView('ib.reports.regular', compact('from_date', 'to_date', 's_title', 'transactions'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("IB-Report: $from_date - $to_date.pdf");
        } elseif ($r->format == "xls") {
            $xls = new IBReportsExport();
            $xls->transactions = $transactions;
            $xls->from_date = $from_date;
            $xls->to_date = $to_date;
            return Excel::download($xls, "IB-Report: $from_date - $to_date.xlsx");
        }
    }

}
