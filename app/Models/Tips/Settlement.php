<?php

namespace App\Models\Tips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = 'sqlsrv5';
    public function scopeSearch($query,$value)
    {

        if ( ! is_null($value)) {

            return $query->where(function ($query) use ($value) {
                $query->where('settlementWindow_id', 'like', '%'.$value.'%')
                    ->orWhere('settlementWindow_date', 'like', '%'.$value.'%')
                    ->orWhere('settlementWindow_description', 'like', '%'.$value.'%')
                    ->orWhere('outgoingTransactions_currency', 'like', '%'.$value.'%')
                    ->orWhere('incomingTransactions_currency', 'like', '%'.$value.'%')
                    ->orWhere('position_type', 'like', '%'.$value.'%')
                    ->orWhere('position_currency', 'like', '%'.$value.'%')
                    ->orWhere('position_ledger_name', 'like', '%'.$value.'%')
                    ->orWhere('type_fee', 'like', '%'.$value.'%')
                    ->orWhere('fee_ledger_name_interchange', 'like', '%'.$value.'%')
                    ->orWhere('fee_ledger_name_processing', 'like', '%'.$value.'%')
                    ->orWhere('fee_currency', 'like', '%'.$value.'%');
            });
        }
    }
}
