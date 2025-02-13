<?php

namespace App\Http\Controllers\agency;

use App\Exports\AbAccountsExport;
use App\Exports\AbAgentsExport;
use App\Exports\ABCommisionDistributionExport;
use App\Exports\ABReportsExport;
use App\Http\Controllers\Controller;
use App\TblABBankAccounts;
use App\TblABServiceCommision;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function index()
    {
        try {
            $accounts = TblABBankAccounts::where('account_type_id', 1)->get();
            $today = date("Y-m-d");
            //return the payload
            return view('agency.reports.index', compact(
                'today',
                'accounts'
            ));
        } catch (\Exception $e) {
            Log::error('INDEX-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => 'Failed to load reports page.', 'color' => 'danger']);
        }
    }

    public function export(Request $r)
    {
        try {
            if ($r->report_type === 'pdf') {
                return back()->with(['notification' => "PDF Format is currently not available!", 'color' => 'warning']);
            }

            $from_date = $r->from_date . " 00:00:00";
            $to_date = $r->to_date . " 23:59:59";

            if (isset($r->customer_type)) {

                if ($r->customer_type === 'Agents') {
                    $agents = DB::connection('sqlsrv4')->table('agency_agents_registration_report_view')
                        ->whereBetween('REGISTRATION DATE', [$from_date, $to_date])
                        ->get();

                    if (count($agents) === 0) {
                        return back()->with(['notification' => "No data found for the provided date range", 'color' => 'danger']);
                    }

                    $xls = new AbAgentsExport();
                    $xls->agents = $agents;
                } elseif ($r->customer_type === 'Accounts') {
                    $accounts = DB::connection('sqlsrv4')->table('agency_account_opening_report_view')
                        ->whereBetween('DATE', [$from_date, $to_date])
                        ->get();

                    if (count($accounts) === 0) {
                        return back()->with(['notification' => "No data found for the provided date range", 'color' => 'danger']);
                    }

                    $xls = new AbAccountsExport();
                    $xls->accounts = $accounts;
                }

                $xls->from_date = $from_date;
                $xls->to_date = $to_date;

                return Excel::download($xls, "AB-{$r->customer_type}-Report: $r->from_date - $r->to_date.xlsx");

            }


            $transactions = DB::connection('sqlsrv4')->table('agency_transactions_report_view')
                ->whereBetween('TRANSACTION DATE', [$from_date, $to_date]);

            $s_title = "Sorted by ";

            if (isset($r->service)) {
                $s_title .= "Services ";
                $transactionType = $this->getTransactionType($r->service);
                $transactions = $transactions->whereIn('TRANSACTION NAME', $transactionType);
            }

            if (isset($r->status)) {
                $transactions = $transactions->where('STATUS', $r->status);
            }

            $transactions = $transactions->get();

            if (count($transactions) === 0) {
                return back()->with(['notification' => "No data found for the provided date range", 'color' => 'danger']);
            }

            if ($r->report_type === "xls") {
                $xls = new ABReportsExport();
                $xls->transactions = $transactions;
                $xls->from_date = $from_date;
                $xls->to_date = $to_date;

                return Excel::download($xls, "AB-Report: $r->from_date - $r->to_date.xls");
            }

            return redirect()->back()->with(['notification' => "Please specify a report format.", 'color' => 'danger']);
           
        } catch (\Exception $e) {
            Log::error('EXPORT-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "Failed to generate report", 'color' => 'danger']);
        }
    }

    public function exportCommissionDistribution(Request $r)
    {
        try {
            $commissions = TblABServiceCommision::where('is_paid', 0)->get();
            $accounts = TblABBankAccounts::where('account_type_id', 1)->get();
            $xls = new ABCommisionDistributionExport();
            $xls->commissions = $commissions;
            $xls->accounts = $accounts;

            return Excel::download($xls, "Unpaid Commision Distribution.xlsx");
        } catch (\Exception $e) {
            Log::error('EXPORT-COMMISSION-DISTRIBUTION-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => "Failed to export commission distribution", 'color' => 'danger']);
        }
    }

    public function approveCommissionDistribution(Request $r)
    {
        try {
            $uid = Auth::user()->id;
            $op = $r->op;
            try {
                if ($op === 1) {
                    $is_paid = 1;
                    $update = TblABServiceCommision::where('is_paid', 0)
                        ->update([
                            'approver_id' => $uid,
                            'is_paid' => $is_paid
                        ]);
                    $notification = "Unpaid commision initiated  successfully!";
                } else {
                    $is_paid = 0;
                    $update = TblABServiceCommision::where('is_paid', 0)
                        ->update([
                            'initiator_id' => $uid,
                            'is_paid' => $is_paid
                        ]);
                    $notification = "Unpaid commision approved unsuccessfully!";
                }

                if ($update) {
                    return redirect()->back()->with(['notification' => $notification, 'color' => 'success']);
                }

                return redirect()->back()->with(['notification' => 'Agent commision distribution approved/disapproved unsuccessfully!', 'color' => 'danger']);
            } catch (\Exception $e) {
                Log::error('UPDATE-COMMISSION-DISTRIBUTION-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
                return redirect()->back()->with(['notification' => 'An error occurred during commission update.', 'color' => 'danger']);
            }
        } catch (\Exception $e) {
            Log::error('APPROVE-COMMISSION-DISTRIBUTION-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with(['notification' => 'An error occurred while processing the commission distribution.', 'color' => 'danger']);
        }
    }

    public function getTransactionType($service)
    {
        try {
            if (is_array($service)) {
                $results = [];
                foreach ($service as $item) {
                    $results = $this->getTransactionTypeForSingleService($item);
                }
                return $results;
            }

            return $this->getTransactionTypeForSingleService($service);
        } catch (\Exception $e) {
            Log::error('GET-TRANSACTION-TYPE-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return null;
        }
    }

    private function getTransactionTypeForSingleService($service): ?array
    {
        try {
            switch ($service) {
                case 'BI':
                    return ['BALANCE_INQUIRY', 'BALANCE_INQUIRY_CARD'];
                case 'B2W':
                    return ['B2W_TRANSFER'];
                case 'DC':
                    return ['DEPOSIT_CARD', 'DEPOSIT'];
                case 'WC':
                    return ['WITHDRAWAL_CARD'];
                case 'FT':
                    return ['FUND_TRANSFER'];
                case 'MS':
                    return ['MINISTATEMENT'];
                case 'PAY':
                    return ['UTILITY'];
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('GET-TRANSACTION-TYPE-SINGLE-SERVICE-EXCEPTION:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return null;
        }
    }
}
