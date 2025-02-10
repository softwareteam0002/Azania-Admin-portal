<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbBillerGroup extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_billers_group";

    protected $primaryKey="biller_group_id";

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
