<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABBankAccounts extends Model
{
    protected $connection = "sqlsrv4";

    public $timestamps = false;

    protected $table = "tbl_agency_banking_agent_bank_accounts";

    protected $primaryKey = "account_id";
    protected $guarded=[];

    public function accountType()
    {
        return $this->belongsTo(TblABBankAccountTypes::class, 'account_type_id', 'type_id');
    }
	public function agent(){
        return $this->belongsTo(AdminUser::class,'agent_id','agent_id');
    }

    public function accountStatus()
    {
        return $this->belongsTo(AbStatus::class, 'account_status', 'tbl_status_id');
    }

    public function registrationStatus()
    {
        return $this->belongsTo(AbStatus::class, 'registration_status', 'tbl_status_id');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
