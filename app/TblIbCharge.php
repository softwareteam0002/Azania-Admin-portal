<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbCharge extends Model
{


    protected $connection="sqlsrv";

    protected $table="tbl_admin_charges";

    protected $primaryKey="charge_id";

    public $timestamps=false;

    public function services()
    {
        return $this->belongsTo('App\TblIbService','service_id');
    }

    public function charge_types()
    {
        return $this->belongsTo('App\TblIbChargeType','charge_type_id');
    }

    public function fixed_charges()
    {
        return $this->belongsTo(TblIbFixedCharge::class,'charge_type_id','charge_type_id');
    }

    public function interval_charges()
    {
        return $this->belongsTo(TblIbIntervalCharge::class,'charge_type_id','charge_type_id');
    }

    public function interval_percent_charges()
    {
        return $this->belongsTo(TblIbIntervalPercentCharge::class,'charge_type_id','charge_type_id');
    }

    public function percent_charges()
    {
        return $this->belongsTo(TblIbPercentCharge::class,'charge_type_id','charge_type_id');
    }
}
