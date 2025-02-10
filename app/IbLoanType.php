<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbLoanType extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_loan_types";

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
