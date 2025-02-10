<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbPsMembers extends Model
{
    protected $connection="sqlsrv2";

    protected $table="tbl_institution_members";

    public $timestamps=false;

    public function services()
    {
        return $this->belongsTo(IbPsLevels::class,'service_level_id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id');
    }

    public function users()
    {
        return $this->belongsTo(IbUser::class,'added_by');
    }
}
