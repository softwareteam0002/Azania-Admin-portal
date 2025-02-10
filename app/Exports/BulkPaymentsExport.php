<?php

namespace App\Exports;

use App\IbUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class BulkPaymentsExport implements FromView, ShouldAutoSize
{
    public $transactions;
    public $service_id;
    public $from_date;
    public $to_date;
    public $type;

    public function view(): View{
        return view('ib.reports.bulk_payments_excel', [
            'transactions' => $this->transactions,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'service_id' =>$this->service_id,
        ]);
    }
}
