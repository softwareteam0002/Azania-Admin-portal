<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbCardRequestStatus extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'tbl_atm_card_request_status';

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
}
