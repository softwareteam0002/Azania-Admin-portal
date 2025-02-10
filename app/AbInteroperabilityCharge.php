<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbInteroperabilityCharge extends Model
{
    //use HasFactory;
    protected $connection="sqlsrv4";
    protected $table = 'dbo.tbl_agency_banking_interoperability_charges';
    protected $guarded = [];

    public function initiator(){
        return $this->belongsTo(AdminUser::class, 'added_by');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

}
