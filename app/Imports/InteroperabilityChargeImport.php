<?php

namespace App\Imports;

use App\AbInteroperabilityCharge;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InteroperabilityChargeImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function __construct($batch_number, $charge_type)
    {
        $this->batch_number = $batch_number;
        $this->charge_type = $charge_type;
    }

    public function model(array $row)
    {
        return new AbInteroperabilityCharge([
            'from_amount' => @$row["from_amount"],
            'to_amount' => @$row["to_amount"],
            'charge' => @$row["charge"],
            'batch_number' => $this->batch_number,
            'added_by' => Auth::user()->id,
            'charge_type' => $this->charge_type,
            'uuid' => Str::uuid(),
        ]);
    }

    public function rules(): array
    {
        return [
            'from_amount' => 'required|numeric',
            'to_amount' => 'required|numeric',
            'charge' => 'required|numeric',
        ];
    }
}
