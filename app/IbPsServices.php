<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPsServices extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_institution_services";

    public $timestamps=false;

    public function types()
    {
        return $this->belongsTo(IbPsServices::class,'service_type_id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id');
    }

    public function status()
    {

    }
}
