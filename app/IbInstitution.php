<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbInstitution extends Model
{

    protected $table='tbl_institutions';

    public $timestamps=false;

    protected $connection='sqlsrv2';


    public function users()
    {
        return $this->hasMany(IbUser::class,'institute_id','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
    public function institution_types()
    {
        return $this->belongsTo(IbInstitutionType::class,'institution_type','id');
    }
}
