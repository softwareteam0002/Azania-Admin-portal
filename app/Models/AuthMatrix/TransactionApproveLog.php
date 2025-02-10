<?php

namespace App\Models\AuthMatrix;

use Illuminate\Database\Eloquent\Model;
use App\IbUser;

class TransactionApproveLog extends Model
{
    protected $connection="sqlsrv2";
    public $timestamps = true;
    protected $fillable = ['transactionable_id', 'user_id', 'my_turn', 'is_sequencial', 'approve_status', 'approve_comment', 'transactionable_type', 'created_at', 'matrix_role_id', 'matrix_role_service_id'];
    protected $table = 'tbl_transaction_approve_log';
    protected $with = ['user', 'transactionable'];



    public function transactionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(IbUser::class, 'user_id', 'id');
    }

    public function matrixRoleService()
    {
        return $this->belongsTo(MatrixRoleService::class, 'matrix_role_service_id', 'id');
    }

    public function matrixRole()
    {
        return $this->belongsTo(MatrixRole::class, 'matrix_role_id', 'id');
    }

}
