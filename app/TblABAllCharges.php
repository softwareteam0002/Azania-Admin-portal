<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblABAllCharges extends Model
{
    protected $connection = "sqlsrv4";

    protected $table = "tbl_agency_banking_all_charges";

    protected $primaryKey = "charge_id";

   protected $fillable = ['service_id', 'charge_type', 'from_account', 'to_account', 'amount', 'amount_percent', 'batch_id', 'status', 'payee'];


    public $timestamps = false;

    public function services(){
        return $this->belongsTo('App\TblAgentService', 'service_id');
    }

    public function chargetypes(){
        return $this->belongsTo('App\TblAgentChargeType',  'charge_type');
    }

    public function batch(){
        return $this->belongsTo(TblABChargesBatch::class, 'batch_id', 'batch_id');
    }
}
