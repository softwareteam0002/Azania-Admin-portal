<?php

namespace App\Exports;

use App\IbUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IBCustomersExport implements FromView{
    public $users;
    public $from_date;
    public $to_date;
    public $type;

    public function view(): View{
        //$transactions = TblTransaction::all();
        return view('ib.reports.customers', [
            'users' => $this->users,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'type'=> $this->type,
            's_title' => "Cusyomers Report"
        ]);
    }


}