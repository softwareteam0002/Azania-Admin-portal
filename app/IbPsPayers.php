<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPsPayers extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_institution_payers";

    public $timestamps=false;

    public function levels()
    {
        return $this->belongsTo(IbPsLevels::class,'service_level_id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id');
    }

    public function services()
    {
        return $this->belongsTo(IbPsServices::class,'service_id');
    }

}
