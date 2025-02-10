<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AbInteroperabilityExport implements FromView
{
    public $transactions;

    public function view(): View
    {
        return view('agency.reports.interoperability_report', ['transactions' => $this->transactions]);
    }
}
