<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    protected $table = 'password_histories';
    protected $connection = "sqlsrv";
    protected $guarded = [];
}
