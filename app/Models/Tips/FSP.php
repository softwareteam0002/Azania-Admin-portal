<?php

namespace App\Models\Tips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FSP extends Model
{
    use HasFactory;
    protected $table='fsps';
    public $timestamps = false;
    protected $connection = 'sqlsrv5';
}
