<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbSadakaDigital extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_sadaka_digital_details";

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
