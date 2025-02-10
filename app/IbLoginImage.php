<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbLoginImage extends Model
{
    //
    protected $table="tbl_login_page_images";

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
