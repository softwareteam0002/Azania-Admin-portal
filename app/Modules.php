<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{

    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function sections()
    {
        return $this->belongsToMany(Sections::class);
    }
}
