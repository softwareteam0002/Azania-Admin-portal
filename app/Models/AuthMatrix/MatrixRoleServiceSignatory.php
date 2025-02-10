<?php

namespace App\Models\AuthMatrix;

use Illuminate\Database\Eloquent\Model;
use App\IbTransferType;
use App\IbUser;

class MatrixRoleServiceSignatory extends Model
{    
    protected $connection="sqlsrv2";
    public $timestamps = true;
    protected $table    = 'tbl_matrix_role_service_signatories';
    protected $fillable = ['matrix_role_service_id', 'int_level', 'user_id', 'created_at'];
    protected $with = ['user'];
    
    public function roleService()
    {
        return $this->belongsTo(MatrixRoleService::class, 'matrix_role_service_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(IbUser::class, 'user_id', 'id');
    }
}