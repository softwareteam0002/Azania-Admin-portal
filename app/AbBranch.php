<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbBranch extends Model
{
    //
    protected $table="tbl_agency_branches";

    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $guarded=[];

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }


}
