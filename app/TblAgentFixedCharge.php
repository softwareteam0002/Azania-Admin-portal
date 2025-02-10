<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblAgentFixedCharge extends Model
{
    protected $table="tbl_agency_banking_charges_fixed";

    public $timestamps=false;
    public $incrementing = false;
protected $primaryKey = 'charges_fixed_id';
    protected $connection="sqlsrv4";

    protected $guarded = [];

    protected $with = ['chargeType','service'];

     function chargeType(){
         return $this->belongsTo(TblAgentChargeType::class, 'charge_type_id','charges_type_id' );
     }

     function service(){
         return $this->belongsTo(TblAgentService::class, 'service_id','agent_serviceID' );
     }
}
