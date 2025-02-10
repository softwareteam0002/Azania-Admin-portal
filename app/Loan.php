<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table="tbl_loans";

    protected  $connection="sqlsrv2";

    public function type(){
        return $this->belongsTo('App\LoanType');
    }

    public function branch()
    {
        return $this->belongsTo('App\IbBranch','branch_id','id');
    }

    public function users()
    {
        return $this->belongsTo('App\IbUser','user_id','id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institute_id');
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
