<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ABServiceChargeExport implements FromView{
    public $batch;
    public $charges;

    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('agency.charges.download', [
            'batch' => $this->batch,
            'charges' => $this->charges
        ]);
    }


}