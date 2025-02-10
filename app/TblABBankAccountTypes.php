<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABBankAccountTypes extends Model
{
    protected $connection="sqlsrv4";

    public $timestamps=false;

    protected $table="tbl_agency_banking_agent_account_type";

    protected $primaryKey="type_id";
}



