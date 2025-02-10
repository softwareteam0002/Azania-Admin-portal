<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbTransactionClass extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_transaction_classes";

    public $timestamps=false;


public function transfer_types()
{
 return $this->belongsTo(IbTransferType::class,'transfer_type_id','id');
}


}
