<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'tbl_account';

    protected $connection ='sqlsrv2';

    public function branchs()
    {
        return $this->belongsTo(IbBranch::class,'branchId','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

}
