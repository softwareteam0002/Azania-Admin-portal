<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblCharge extends Model
{
    protected $connection="sqlsrv4";

    protected $table="tbl_agency_banking_charges";

    protected $primaryKey="charge_id";

    public $timestamps=false;

    public function services()
    {
       return $this->belongsTo('App\TblAgentService','service_id');
    }

    public function charge_types()
    {
        return $this->belongsTo('App\TblAgentChargeType','charge_type_id');
    }

    public function fixed_charges()
    {
        return $this->belongsTo(TblAgentFixedCharge::class,'charge_type_id','charge_type_id');
    }

    public function interval_charges()
    {
        return $this->belongsTo(TblAgentIntervalCharge::class,'charge_type_id','charge_type_id');
    }

    public function interval_percent_charges()
    {
        return $this->belongsTo(TblAgentIntervalPercentCharge::class,'charge_type_id','charge_type_id');
    }

    public function percent_charges()
    {
        return $this->belongsTo(TblAgentPercentCharge::class,'charge_type_id','charge_type_id');
    }

}
