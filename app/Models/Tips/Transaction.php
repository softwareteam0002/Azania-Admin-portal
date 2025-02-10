<?php

namespace App\Models\Tips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];
    protected $connection = 'sqlsrv5';
    public function scopeSearch($query,$value)
    {

        if ( ! is_null($value)) {

            return $query->where(function ($query) use ($value) {
                $query->where('payerRef', 'like', '%'.$value.'%')
                    ->orWhere('payer_identifier', 'like', '%'.$value.'%')
                    ->orWhere('payer_fspId', 'like', '%'.$value.'%')
                    ->orWhere('payer_fullName', 'like', '%'.$value.'%')
                    ->orWhere('payee_identifier', 'like', '%'.$value.'%')
                    ->orWhere('payeeRef', 'like', '%'.$value.'%')
                    ->orWhere('payee_identity_value', 'like', '%'.$value.'%')
                    ->orWhere('reversalRef', 'like', '%'.$value.'%')
                    ->orWhere('payee_fspId', 'like', '%'.$value.'%')
                    ->orWhere('completedTimestamp', 'like', '%'.$value.'%')
                    ->orWhere('transferState', 'like', '%'.$value.'%')
                    ->orWhere('payee_fullName', 'like', '%'.$value.'%');
            });
        }
    }
}
