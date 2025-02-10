<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditTrailLogs extends Model
{
    protected $table="tbl_audit_trail_logs";

    public $timestamps=false;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
