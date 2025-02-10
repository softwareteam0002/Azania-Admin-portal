<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbTransfer extends Model
{
    protected $table = 'tbl_transfers';

    protected $connection='sqlsrv2';

}
