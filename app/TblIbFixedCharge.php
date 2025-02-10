<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbFixedCharge extends Model
{
    protected $table="tbl_admin_charges_fixed";

    public $timestamps=false;
    public $incrementing = false;
    protected $primaryKey = 'charges_fixed_id';
    protected $connection="sqlsrv";

    protected $guarded = [];

    protected $with = ['chargeType','service'];

    function chargeType(){
        return $this->belongsTo(TblIbChargeType::class, 'charge_type_id','charges_type_id' );
    }

    function service(){
        return $this->belongsTo(TblIbService::class, 'service_id','agent_serviceID' );
    }
}
