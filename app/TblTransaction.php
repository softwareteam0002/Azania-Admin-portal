<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblTransaction extends Model
{
    //
    protected $table="tbl_agency_banking_transactions";

    protected $connection="sqlsrv4";
    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'operator_id');
    }
}
