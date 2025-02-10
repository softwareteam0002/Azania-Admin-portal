<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    //
    protected $table="tbl_loan_types";

    protected $connection="sqlsrv2";

    public $timestamps=false;

    public function approvers()
    {
       return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
}
