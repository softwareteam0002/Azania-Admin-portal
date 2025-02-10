<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsbParishTransactionType extends Model
{
    //
    protected $table='tbl_parish_transaction_type';

    public $timestamps=false;

    protected $connection='sqlsrv3';
	
	public function parish()
    {
        return $this->belongsTo(EsbParishAccount::class,'parish_account_id','id');
    }

    public function type()
    {
        return $this->belongsTo(EsbSadakaTranType::class,'sadaka_tran_type_id','id');
    }
	public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
