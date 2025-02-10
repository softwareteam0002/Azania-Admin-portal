<?php

namespace App\Http\Controllers\ESB;

use App\Http\Controllers\Controller;
use App\EsbTransactions;
use Illuminate\Http\Request;
use Excel;
use PDF;
use App\Exports\ESBReportsExport;

class ReportsController extends Controller{


    //format the date to create a format to select between
    private function convertDate($today){
        $format = "D M d H:i:s T Y";
        $d = strtotime($today);
        return date($format, $d)."";
    }

    public function index(){
        $transactions = EsbTransactions::all();
        $today = date("Y-m-d");
        //return the payload
        return view('esb.reports.index', compact(
            'today',
            'transactions',
        ));
    }

    public function export(Request $r){

        $from_date = $r->from_date;
        $to_date = $r->to_date;
        // replace the - in dates
        $from_date = $from_date." 00:00:00";
        $to_date = $to_date." 23:59:59";

        //reformat the date to the corresponding format in the database
        $from_date = $this->convertDate($from_date);
        $to_date = $this->convertDate($to_date);

        //$transactions = EsbTransactions::whereBetween('transaction_date', [$from_date, $to_date]);
        //format the date to properly fomat the date
        $transactions = EsbTransactions::whereBetween('transaction_date', [$from_date, $to_date]);

        //return $r->channels;

        //parse the channelss to sort
        $s_title = "Sorted by ";
        if(isset($r->channels)){
            $s_title .= "Services ";
            $transactions = $transactions->whereIn('transaction_channel', $r->channels);
        }

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
            $pdf =  PDF::loadView('esb.reports.regular', compact(
                'from_date',
                'to_date',
                's_title',
                'transactions'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("ESB-Report: $from_date - $to_date.pdf");
        }elseif($r->format == "xls"){
            $xls = new ESBReportsExport();
            $xls->transactions = $transactions;
            $xls->from_date = $from_date;
            $xls->to_date = $to_date;

            return Excel::download($xls, "ESB-Report: $from_date - $to_date.xlsx");
        }else{
            return redirect()->back()->with(['notification' => "Please specify a report format.", 'color' => 'danger']);
        }

        

        
        
        
        // return count($transactions);
        // $r->to_date = $to_date; 
        // $r->from_date = $from_date; 
        // return $r;
    }

}
