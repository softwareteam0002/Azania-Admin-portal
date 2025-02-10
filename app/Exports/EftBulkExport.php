<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class EftBulkExport implements FromView, ShouldAutoSize
{
    public $transactions;
    public $from_date;
    public $to_date;
    public $type;
    public $service_id;

    public function view(): View{
        return view('ib.reports.eft_bulk_excel', [
            'service_id' => $this->service_id,
            'transactions' => $this->transactions,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);
    }

}
