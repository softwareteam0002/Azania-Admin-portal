<?php

namespace App\Imports;

use App\TblABAllCharges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ServiceChargesABImport implements ToModel, WithHeadingRow, WithValidation
{
  
  private $batch_id;
  public function __construct($batch_id) {
      $this->batch_id = $batch_id;
  }    
  public function model (array $row) {
    return new TblABAllCharges([

            'service_id' => $row['service_id'],
            'charge_type' => $row['charge_type'],
            'from_amount' => $row['from_amount'],
            'to_amount' => $row['to_amount'],
            'amount' => $row['amount'],
            'amount_percentage' => $row['amount_percentage'],
            'status' => 1,
            'payee' => $row['payee'],
            'batch_id' => $this->batch_id

    ]);
       
  }

  public function rules (): array 
   {

          return  [

            'amount'=>'required|numeric|between:0.0,100000000.0',
          ];
  } 
}
