<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbDistributionParties extends Model
{
    public $timestamps=false;

    protected $table="tbl_agency_banking_commission_distribution_parties";

    protected $connection="sqlsrv4";
}
