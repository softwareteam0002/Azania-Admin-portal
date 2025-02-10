<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBServiceAccount extends Model
{
    //
   protected $connection = 'sqlsrv2';
   protected $table = 'tbl_service_account';
   protected $fillable = ['service_name','account_number'];

     public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
}
