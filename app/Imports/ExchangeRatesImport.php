<?php

namespace App\Imports;

use App\IBExchangeRate;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
class ExchangeRatesImport implements ToModel, WithHeadingRow,WithValidation
{
   

    /**
     * BulkPaymentsImport constructor.
     * @param $salary_processing_id
     */
    public function __construct()
    {

    }


    /**
    * @param array $row
    * @return Model|null
    */
    public function model(array $row)
    {
        return new IBExchangeRate([

            'date'              => Carbon::now()->format('Y-m-d'),
            "foreign_currency"  => $row["foreign_currency"],
            "currency_code"     =>$row["currency_code"],
            "mean_rate"         => $row["mean_rate"],
            "buying_price"      =>$row["buying_price"],
            "selling_price"     =>$row["selling_price"]
        ]);
    }

    /**
     * @inheritDoc 
     */
    public function rules(): array
    {
        return  [
            'foreign_currency'=>'required|max:40',
            'currency_code'   => 'required|max:3',
            'mean_rate'=>'required|numeric|between:0.0,100000000.0',
            'buying_price'=>'required|numeric|between:0.0,100000000.0',
            'buying_price'=>'required|numeric|between:0.0,100000000.0'
        ];
    }
}

