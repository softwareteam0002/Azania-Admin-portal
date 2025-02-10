<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordPolicy extends Model
{
    protected $table = 'password_policies';
    protected $connection = "sqlsrv";
    protected $guarded = [];

    public function initiator()
    {
        return $this->belongsTo(AdminUser::class, 'initiator_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(AdminUser::class, 'approver_id', 'id');
    }
}
