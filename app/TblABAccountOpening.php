<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblABAccountOpening extends Model
{
    protected $connection = "sqlsrv4";

    public $timestamps = false;

    protected $table = "tbl_agency_banking_account_openning";
	
	public function agent(){
        return $this->belongsTo(TblAgent::class,'agent_id','agent_id');
    }
	 public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'operator_id');
    }
}
