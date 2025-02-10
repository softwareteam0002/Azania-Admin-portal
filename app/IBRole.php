<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBRole extends Model
{
    public $timestamps = false;

    protected $table = 'roles';

    protected $connection="sqlsrv2";
}
