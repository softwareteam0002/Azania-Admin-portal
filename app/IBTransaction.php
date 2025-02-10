<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBTransaction extends Model
{
    protected $table = 'tbl_transactions';

    protected $connection='sqlsrv2';

    public function transactionType(){
        return $this->belongsTo('App\IBTransactionType', 'transaction_type_id');
    }

    public function accounts()
    {
        return $this->belongsTo('App\IbAccount','account_id');
    }

    public function users()
    {
        return $this->belongsTo(IbUser::class,'user_id','id');
    }

    public function transfers()
    {
        return $this->belongsTo(IbTransfer::class,'transfer_type_id','id');
    }

    public function types()
    {
        return $this->belongsTo(IBTransactionType::class,'transaction_type_id','id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id','id');
    }

    public function status()
    {
        return $this->belongsTo(IBTransactionStatus::class,'status_id','id');
    }


}
