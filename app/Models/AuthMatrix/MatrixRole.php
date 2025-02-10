<?php

namespace App\Models\AuthMatrix;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MatrixRole extends Model
{    
    protected $connection="sqlsrv2";
    public $timestamps = true;
    protected $table    = 'tbl_matrix_roles';
    protected $fillable = ['name', 'is_sequencial', 'is_range', 'min_amount', 'max_amount', 'created_by', 'created_at'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
}
