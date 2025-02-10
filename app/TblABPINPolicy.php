<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABPINPolicy extends Model
{
    protected $connection = "sqlsrv4";

    public $timestamps = false;

    protected $table = "tbl_agency_banking_pin_policy";

    protected $primaryKey = "id";

}
