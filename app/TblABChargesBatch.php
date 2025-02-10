<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblABChargesBatch extends Model
{
    protected $connection = "sqlsrv4";
    protected $table = "tbl_agency_banking_charges_batch";
    protected $primaryKey = "batch_id";
    public $timestamps = false;


    public function status() {
        return $this->belongsTo(AbStatus::class, 'batch_status', 'tbl_status_id');
    }

    public function initiator()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }


}
