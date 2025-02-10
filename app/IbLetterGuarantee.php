<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbLetterGuarantee extends Model
{
    //
    protected $table="tbl_letter_of_guarantees";

    public $timestamps=false;

    protected $connection='sqlsrv2';

    protected $guarded=['id'];

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id');
    }

    public function accounts()
    {
        return $this->belongsTo(IbAccount::class,'account_id');
    }

    public function status()
    {
        return $this->belongsTo(RequestStatus::class,'status_id','id');
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
