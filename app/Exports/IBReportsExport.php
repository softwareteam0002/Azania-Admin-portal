<?php

namespace App\Exports;

use App\TblTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IBReportsExport implements FromView{
    public $transactions;
    public $from_date;
    public $to_date;

    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('ib.reports.excel', [
            'transactions' => $this->transactions,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            's_title' => "Reports"
        ]);
    }


}