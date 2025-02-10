<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbImage extends Model
{

    protected $table="tbl_dashboard_images";

    protected $connection="sqlsrv2";

    public $timestamps=false;

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
