<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPaymentServiceStatus extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_payment_services_statuses";

    public $timestamps=false;
}
