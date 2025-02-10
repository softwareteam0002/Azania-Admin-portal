<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankServiceAccount extends Model
{
    protected $primaryKey = 'bank_account_id';
    protected $connection="sqlsrv4";
    //protected $table="tbl_agency_banking_agent_service";
    protected $table = "tbl_agency_banking_bank_service_accounts";
    public $timestamps =false;

    public function services()
    {
        return $this->belongsTo(TblAgentService::class, 'bank_service_ID', 'agent_serviceID');
    }

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
    
    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
