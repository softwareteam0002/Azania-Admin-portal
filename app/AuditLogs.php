<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model
{
    protected $table="tbl_audit_action_logs";

    public $timestamps=false;

}
