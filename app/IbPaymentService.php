<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPaymentService extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_payment_services";

    public $timestamps=false;

    public function status()
    {
        return $this->belongsTo('App\IbPaymentServiceStatus','status_id','id');
    }

    public function types()
    {
        return $this->belongsTo(IbPaymentServiceType::class, 'type_id', 'id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
}
