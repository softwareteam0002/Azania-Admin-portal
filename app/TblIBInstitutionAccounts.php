<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIBInstitutionAccounts extends Model
{
    protected $connection = "sqlsrv2";

    public $timestamps = false;

    protected $table = "tbl_bank_institution_accounts";
    
    public function accountType()
    {
        return $this->belongsTo(TblIBInstitutionAccountTypes::class, 'account_type_id', 'id');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
    
    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
