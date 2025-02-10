<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbInstitutionPayment extends Model
{
    public $timestamps=false;

    protected $connection="sqlsrv2";

    protected $table="tbl_institution_payments";

}
