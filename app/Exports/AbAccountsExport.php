<?php

namespace App\Exports;

use App\InstantAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AbAccountsExport implements FromView{
    public $accounts;
    public $from_date;
    public $to_date;

    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('agency.reports.accounts', [
            'accounts' => $this->accounts,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            's_title' => "Account Opening Report"
        ]);
    }


}