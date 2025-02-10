<?php

namespace App\Exports;

use App\TblTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ABReportsExport implements FromView{
    public $transactions;
    public $from_date;
    public $to_date;

    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('agency.reports.regular', [
            'transactions' => $this->transactions,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            's_title' => "Reports"
        ]);
    }


}