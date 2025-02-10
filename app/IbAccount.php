<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\AuthMatrix\MatrixRoleService;

class IbAccount extends Model
{
    //
    protected $table="tbl_account";

    protected $connection="sqlsrv2";

    public function branches()
    {
        return $this->belongsTo(IbBranch::class,'branchId','id');
    }

    public function institutions()
    {
        return $this->belongsTo(IbInstitution::class,'institution_id','id');
    }

    public function types()
    {
        return $this->belongsTo(IbAccountType::class,'account_type_id','id');
    }

    public function status()
    {
        return $this->belongsTo(IbAccountStatus::class,'aCStatus','id','tbl_stat');
    }

    public function users()
    {
        return $this->belongsTo(IbUser::class, 'user_id', 'id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function matrixRoleService()
    {
        return $this->hasMany(MatrixRoleService::class,'account_id','id');
    }
}
