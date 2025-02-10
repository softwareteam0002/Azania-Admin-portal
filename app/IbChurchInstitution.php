<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbChurchInstitution extends Model
{

    protected $table='tbl_parishes';

    public $timestamps=false;

    protected $connection='sqlsrv2';

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
