<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBTransactionType extends Model
{
    protected $table = 'tbl_transaction_type';

    protected $connection='sqlsrv2';
}
