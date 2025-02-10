<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChequeBook extends Model
{
    protected $table = 'tbl_cheque_book_request';

    protected $connection='sqlsrv2';

    public function account(){
        return $this->belongsTo('App\Account');
    }

    public function status(){
        return $this->belongsTo(RequestStatus::class,'status_id','id');
    }

    public function currency(){
        return $this->belongsTo('App\IbCurrency','currency_id','id');
    }

    public function users(){
        return$this->belongsTo('App\IbUser','user_id','id');
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
