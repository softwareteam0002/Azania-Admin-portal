<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbChargeType extends Model
{
    protected $table="tbl_ib_charges_type";

    protected $connection="sqlsrv2";

    protected $primaryKey="charges_type_id";
}
