<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbIntervalPercentCharge extends Model
{
    protected $table="tbl_admin_charges_interval_percentage";


    public $timestamps=false;

    protected $connection="sqlsrv";

    protected $primaryKey = 'interval_id';
    protected $guarded = [];

    protected $with = ['chargeType','service'];

    function chargeType(){
        return $this->belongsTo(TblIbChargeType::class, 'charge_type_id','charges_type_id' );
    }

    function service(){
        return $this->belongsTo(TblIbService::class, 'service_id','agent_serviceID' );
    }
}
