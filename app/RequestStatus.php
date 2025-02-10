<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestStatus extends Model
{
    protected  $table="tbl_requests_status";

    protected $connection="sqlsrv";

}
