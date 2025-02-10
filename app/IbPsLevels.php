<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPsLevels extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_institution_service_levels";

    public $timestamps=false;

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id');
    }

    public function status()
    {
        return $this->belongsTo(IbAccountStatus::class,'status');
    }
}
