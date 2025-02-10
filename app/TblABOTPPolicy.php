<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABOTPPolicy extends Model
{
    protected $connection = "sqlsrv4";

    public $timestamps = false;

    protected $table = "tbl_agency_banking_otp_policy";

    protected $primaryKey = "id";

}
