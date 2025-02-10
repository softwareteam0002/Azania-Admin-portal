<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsbTransactions extends Model
{
    //
    protected $table='tbl_esb_transaction';

    public $timestamps=false;

    protected $connection='sqlsrv3';


}
