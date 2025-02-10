<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AtmCard extends Model
{
    protected $table = 'tbl_atm_card_request';

    protected $connection = "sqlsrv2";

    public function user(){
        return $this->belongsTo('App\IbUser', 'user_id');
    }
    public function account(){
        return $this->belongsTo('App\Account');
    }
    public function status(){
        return $this->belongsTo('App\IbCardRequestStatus', 'status_id');
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
