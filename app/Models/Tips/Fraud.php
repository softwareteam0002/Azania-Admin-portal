<?php

namespace App\Models\Tips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fraud extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv5';
    public $timestamps = false;
    public function scopeSearch($query,$value)
    {

        if ( ! is_null($value)) {

            return $query->where(function ($query) use ($value) {
                $query->where('fspId', 'like', '%'.$value.'%')
                    ->orWhere('identifier', 'like', '%'.$value.'%')
                    ->orWhere('identifierType', 'like', '%'.$value.'%')
                    ->orWhere('fullName', 'like', '%'.$value.'%')
                    ->orWhere('identityType', 'like', '%'.$value.'%')
                    ->orWhere('identityValue', 'like', '%'.$value.'%')
                    ->orWhere('reasons', 'like', '%'.$value.'%')
                    ->orWhere('status', 'like', '%'.$value.'%')
                    ->orWhere('date', 'like', '%'.$value.'%')
                    ->orWhere('participantId', 'like', '%'.$value.'%')
                    ->orWhere('createdDate', 'like', '%'.$value.'%')
                    ->orWhere('fraudRegisterId', 'like', '%'.$value.'%');
            });
        }
    }
}
