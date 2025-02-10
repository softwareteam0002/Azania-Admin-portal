<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{

    public $timestamps = false;

    protected $table = 'tbl_agency_banking_device';

    protected $connection="sqlsrv4";

    protected $primaryKey = 'device_id';
    protected $guarded=[];

    //Relating to user
    public function users()
    {
        return $this->belongsTo(AdminUser::class,'registered_by','id');
    }

    public function branches()
    {
        return $this->belongsTo(AbBranch::class,'branch_id','id');
    }

    public function tradingac(){
        return $this->belongsTo(TblABBankAccounts::class, 'trading_account_id', 'account_id');
    }

    public function commisionac(){
        return $this->belongsTo(TblABBankAccounts::class, 'commision_account_id', 'account_id');
    }
}
