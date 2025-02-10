<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbStatus extends Model
{
    protected $table="tbl_agency_banking_status";

    public $timestamps=false;

    protected $connection="sqlsrv4";

}
