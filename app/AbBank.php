<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbBank extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_banks";

    protected $primaryKey="bank_id";
	protected $guarded = [];


    public function bankStatus()
    {
        return $this->belongsTo(AbStatus::class, 'bank_status', 'tbl_status_id');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

}
