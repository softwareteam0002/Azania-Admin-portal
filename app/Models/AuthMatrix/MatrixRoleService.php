<?php

namespace App\Models\AuthMatrix;

use Illuminate\Database\Eloquent\Model;
use App\IbTransferType;
use App\User;

class MatrixRoleService extends Model
{    
    protected $connection="sqlsrv2";
    public $timestamps = true;
    protected $table    = 'tbl_matrix_role_service';
    protected $fillable = ['account_id', 'account_number', 'service_id', 'matrix_role_id','created_by', 'created_at'];
    
    protected $with = ['service', 'signatories', 'matrixRole', 'creator'];

    public function matrixRole()
    {
        return $this->belongsTo(MatrixRole::class, 'matrix_role_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(IbTransferType::class, 'service_id', 'id');
    }

    public function signatories()
    {
        return $this->hasMany(MatrixRoleServiceSignatory::class, 'matrix_role_service_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}