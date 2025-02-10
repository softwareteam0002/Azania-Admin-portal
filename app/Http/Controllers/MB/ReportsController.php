<?php

namespace App\Http\Controllers\MB;

use App\Http\Controllers\Controller;
use App\MbTransactions;
use Illuminate\Http\Request;
use Excel;
use PDF;
use App\Exports\MBReportsExport;

class ReportsController extends Controller{

    //format the date to create a format to select between
    private function convertDate($today){
        $format = "D M d H:i:s T Y";
        $d = strtotime($today);
        return date($format, $d) . "";
    }

    public function index(){
        abort_unless(\Gate::allows('mb_view_transactions_reports'), 403);
        $transactions = MbTransactions::where('transaction_channel','MB')->get();
        $today = date("Y-m-d"); 
        //return the payload
        return view('mobile.reports.index', compact(
            'today',
            'transactions',
        ));
    }

    public function export(Request $r){
        abort_unless(\Gate::allows('mb_view_transactions_reports'), 403);
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        // replace the - in dates
        $from_date = $from_date . " 00:00:00";
        $to_date = $to_date . " 23:59:59";

        //reformat the date to the corresponding format in the database
        $from_date = $this->convertDate($from_date);
        $to_date = $this->convertDate($to_date);

        $transactions = MbTransactions::where('transaction_channel', 'MB')->whereBetween('transaction_date', [$from_date, $to_date]);

        $s_title = "Sorted by ";
        //parse the services
        if(isset($r->service)){
            $s_title .= "Services ";
            $transactions = $transactions->whereIn('transaction_name', $r->service);
        }

        //pasrse the status to sort
        switch($r->status){
            case 'success':
                $s_title .= "Transaction Success ";
                $transactions = $transactions->where('transaction_status', '200');
            break;
            case 'failed':
                $s_title .= "Transaction Failed ";
                $transactions = $transactions->where('transaction_status', "");
            break;
            default:
                $s_title .= "Transaction Status ";
            break;
        }
        
        $transactions = $transactions->get();

        //revett the dates back to their old values
        $from_date = $r->from_date;
        $to_date = $r->to_date;

        //create the appropriate format
        if($r->format == "pdf"){
            //return the PDF
            $pdf =  PDF::loadView('mobile.reports.regular', compact(
                'from_date',
                'to_date',
                's_title',
                'transactions'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("MB-Report: $from_date - $to_date.pdf");

        }elseif($r->format == "xls"){
            $xls = new MBReportsExport();
            $xls->transactions = $transactions;
            $xls->from_date = $from_date;
            $xls->to_date = $to_date;

            return Excel::download($xls, "MB-Report: $from_date - $to_date.xlsx");
        }else{
            return redirect()->back()->with(['notification' => "Please specify a report format.", 'color' => 'danger']);
        }
    }

}
