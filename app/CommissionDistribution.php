<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionDistribution extends Model
{
    protected $table = 'tbl_agency_banking_commission_distribution';
    public $timestamps = false;
    protected $connection = "sqlsrv4";

    public function service(){
        return $this->belongsTo('App\BankingAgentService', 'service_name_id');
    }
/*
    public function parties()
    {
        return $this->belongsTo(AbDistributionParties::class, 'party_id', 'id');
    }
*/

    public function initiator(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approver(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

}
