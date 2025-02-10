<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TIPSReportsExport implements FromView,ShouldAutoSize
 {
    public $transactions;
    public $from_date;
    public $to_date;

    public function view(): View{

        return view('tips.reports.excel', [
            'transactions' => $this->transactions,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            's_title' => "Reports"
        ]);
    }


}