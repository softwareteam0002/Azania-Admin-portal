<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblABServiceCommision extends Model
{
    protected $connection = "sqlsrv4";
    protected $table = "tbl_agency_banking_service_commission";
    protected $primaryKey = "commission_id";
    public $timestamps = false;


    public function agent() {
        return $this->belongsTo(TblAgent::class, 'agent_id', 'agent_id');
    }

    public function transaction() {
        return $this->belongsTo(TblTransaction::class, 'transactionID', 'transactionID');
    }

    public function commissionpayments() {
        return $this->belongsTo(TblABServiceCommision::class, 'payment_id', 'payment_id');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

}
