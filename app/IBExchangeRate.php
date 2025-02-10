<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IBExchangeRate extends Model
{
    // 

protected $connection = "sqlsrv2";    
protected $table    = 'tbl_exchange_rates';    
protected $fillable = ['foreign_currency','currency_code','mean_rate','selling_price', 'buying_price', 'date'];
}
