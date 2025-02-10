<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIBInstitutionAccountTypes extends Model
{
    protected $connection="sqlsrv2";

    public $timestamps=false;

    protected $table="tbl_bank_account_types";
}



