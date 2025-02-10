<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbTv extends Model
{
    protected $table="tbl_tv_service_provider";

    public $timestamps=false;

    protected $connection="sqlsrv2";

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
