<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    public $timestamps=false;

    protected $table="users";

    protected $connection="sqlsrv";

    public function actions()
    {
        return $this->belongsTo(TblAdminActionLevel::class,'action_id','id');
    }

}
