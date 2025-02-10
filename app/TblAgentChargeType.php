<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblAgentChargeType extends Model
{
    protected $table="tbl_agency_banking_charges_type";

    protected $connection="sqlsrv4";

    protected $primaryKey="charges_type_id";

}
