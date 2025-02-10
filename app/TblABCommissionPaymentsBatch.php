<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblABCommissionPaymentsBatch extends Model{
    /**
     TblABCommissionPaymentsBatch
    SELECT TOP (1000) [batch_id]
        ,[issued_date]
        ,[from_date]
        ,[to_date]
        ,[status]
        ,[initiator_id]
        ,[approver_id]
        ,[total_amount]
    FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_commission_payments_batch]

     */
    protected $connection = "sqlsrv4";
    protected $table = "tbl_agency_banking_commission_payments_batch";
    protected $primaryKey = "batch_id";
    public $timestamps = false;

    public function agent(){
        return $this->belongsTo(TblAgent::class,'agent_id','agent_id');
    }
    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
    
    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
