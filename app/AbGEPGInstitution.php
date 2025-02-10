<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbGEPGInstitution extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_gepg_institution";

    protected $primaryKey="institution_id";

    public function gepgStatus()
    {
        return $this->belongsTo(AbStatus::class,'gepginstitution_status','tbl_status_id');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
