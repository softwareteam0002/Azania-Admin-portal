<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsbParishAccount extends Model
{
    //
    protected $table='tbl_parish_accounts';

    public $timestamps=false;

    protected $connection='sqlsrv3';
	
	public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

}
