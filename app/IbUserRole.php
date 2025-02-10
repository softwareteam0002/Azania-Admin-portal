<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbUserRole extends Model
{
    protected $connection="sqlsrv2";

    public $timestamps=false;

    protected $table="role_user";
}
