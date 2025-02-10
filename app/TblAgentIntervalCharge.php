<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblAgentIntervalCharge extends Model
{
    protected $table="tbl_agency_banking_charges_interval";

    public $timestamps=false;

    protected $connection="sqlsrv4";

    protected $primaryKey = 'interval_id';

    protected $guarded = [];

    protected $with = ['chargeType','service'];

    function chargeType(){
        return $this->belongsTo(TblAgentChargeType::class, 'charge_type_id','charges_type_id' );
    }

    function service(){
        return $this->belongsTo(TblAgentService::class, 'service_id','agent_serviceID' );
    }
}
