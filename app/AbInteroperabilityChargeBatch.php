<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbInteroperabilityChargeBatch extends Model
{
    //use HasFactory;
    protected $connection="sqlsrv4";
    protected $table = 'dbo.tbl_agency_banking_interoperability_charge_batches';
    protected $guarded = [];

    public function initiator(){
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

}
