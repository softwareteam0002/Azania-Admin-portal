<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbTransferType extends Model
{    
    protected $connection="sqlsrv2";

    protected $table    = 'tbl_transfer_types';
    protected $fillable = ['name','description'];
    
}
