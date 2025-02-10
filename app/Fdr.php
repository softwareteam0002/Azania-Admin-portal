<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fdr extends Model
{
    //
    protected $table = "tbl_fixed_deposit_rates";

    protected $connection = "sqlsrv2";

    public $timestamps=false;

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
