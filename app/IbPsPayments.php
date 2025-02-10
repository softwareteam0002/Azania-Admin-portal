<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPsPayments extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_institution_payments";

    public $timestamps=false;

    public function payers()
    {
        return $this->belongsTo(IbPsPayers::class,'tbl_institution_payer_id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institute_id');
    }

    public function services()
    {
        return$this->belongsTo(IbPsServices::class,'service_id');
    }
}
