<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LetterCredit extends Model
{
    protected $table = 'tbl_letter_of_credit';

    protected $connection = 'sqlsrv2';

    public $timestamps=false;

    public function status(){
        return $this->belongsTo(RequestStatus::class,'status_id','id');
    }

    public function accounts()
    {
        return $this->belongsTo('App\IbAccount','account_id','id');
    }

    public function users()
    {
        return $this->belongsTo('App\IbUser','created_by','id');
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
