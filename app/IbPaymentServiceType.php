<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPaymentServiceType extends Model
{
    public $timestamps = false;

    protected $table = "tbl_payment_service_types";

    protected $connection = "sqlsrv2";

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
