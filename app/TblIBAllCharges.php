<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblIBAllCharges extends Model
{
    protected $connection = "sqlsrv2";

    protected $table = "tbl_all_charges";

    protected $primaryKey = "charge_id";
    
    protected $fillable = ['charge_id', 'service_id', 'charge_type', 'from_account', 'to_account', 'amount', 'amount_percent', 'batch_id', 'status', 'payee'];

    public $timestamps = false;

    public function services(){
        return $this->belongsTo(IbTransferType::class, 'service_id');
    }

    public function chargetypes(){
        return $this->belongsTo(TblIbChargeType::class,  'charge_type');
    }

    public function batch(){
        return $this->belongsTo(TblIBChargesBatch::class, 'batch_id', 'batch_id');
    }
}
