<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbInteroperabilityTransaction extends Model
{
    //use HasFactory;

    protected $connection = "sqlsrv4";
    protected $table = 'dbo.tbl_agency_banking_interoperability_transactions';
    protected $guarded = [];

}
