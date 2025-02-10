<?php

namespace App\Imports;

use App\AbBank;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BanksImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */

    public function model(array $row)
    {
        return new AbBank([
            'bank_name' => @$row["bank_name"],
            'bank_code' => @$row["bank_code"],
            'initiator_id' => Auth::user()->id,
			'bank_status' => 1,
            'isWaitingApproval' => 1,
            'isDeleted' => 0,
            'approver_id' => 0
        ]);

    }

    public function rules(): array
    {
        return  [
            'bank_name' => 'required',
            'bank_code' =>  'required|unique:sqlsrv4.tbl_agency_banking_Banks',
        ];
    }
}
