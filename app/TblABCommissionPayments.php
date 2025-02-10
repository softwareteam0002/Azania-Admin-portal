<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TblABCommissionPayments extends Model{
    /**
     
SELECT TOP (1000) [payment_id]
      ,[agent_id]
      ,[commission_account]
      ,[amount]
      ,[status]
      ,[number_of_transaction]
      ,[from_date]
      ,[to_date]
      ,[posting_date]
      ,[initiator_id]
      ,[approver_id]
  FROM [AgencyBankingTransaction].[dbo].[tbl_agency_banking_commission_payments]

     */
    protected $connection = "sqlsrv4";
    protected $table = "tbl_agency_banking_commission_payments";
    protected $primaryKey = "payment_id";
    public $timestamps = false;

     public function batch(){
         return $this->belongsTo(TblABChargesBatch::class, 'batch_id', 'batch_id');
     }
     public function agent(){
         return $this->belongsTo(TblAgent::class, 'agent_id', 'agent_id');
     }
}
