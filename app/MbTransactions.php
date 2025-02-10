<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MbTransactions extends Model
{
    protected $connection="sqlsrv3";

    public $timestamps=false;

    protected $table="tbl_esb_transaction";
}
