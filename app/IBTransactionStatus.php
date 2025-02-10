<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBTransactionStatus extends Model
{
    protected $table = 'tbl_transaction_statuses';

    protected $connection='sqlsrv2';
}
