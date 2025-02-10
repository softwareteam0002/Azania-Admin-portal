<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABInstitutionAccountTypes extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_bank_account_types";
}



