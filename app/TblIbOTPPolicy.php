<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbOTPPolicy extends Model{


    protected $connection="sqlsrv2";
    protected $table="tbl_otp_policy";
    protected $primaryKey="id";
    public $timestamps=false;
}
