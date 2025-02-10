<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbClass extends Model
{

    protected $table='tbl_authorization_classes';

    public $timestamps=false;

    protected $connection='sqlsrv2';
    
 public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
