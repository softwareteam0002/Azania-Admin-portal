<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbBranches extends Model
{
    //
    protected $table="tbl_agency_banking_bank_branches";

    protected $connection="sqlsrv4";

    protected $primaryKey="branch_id";


}
