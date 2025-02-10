<?php

namespace App\Exports;

use App\TblTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ABCommisionDistributionExport implements FromView{
    public $commissions;
    public $accounts;
    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('agency.commissions.commissionspayments_download', [
            'commissions' => $this->commissions
        ]);
    }


}