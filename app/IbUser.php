<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class IbUser extends Model
{
    protected $table = 'tbl_users';

    protected $connection = 'sqlsrv2';

    public function institutions()
    {
        return $this->belongsTo('App\IbInstitution','institute_id','id');
    }
    public function classes()
    {
        return $this->belongsTo(IbClass::class,'class_id','id');
    }

    public function options()
    {
        return $this->belongsTo('App\OtpOption','otp_options_id','id');
    }

    public function roles()
    {
        return $this->belongsTo('App\IBRole','role_id','id');
    }
     public function actions()
    {
        return $this->belongsTo(TblAdminActionLevel::class,'action_id','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id');
    }
    public function accounts()
    {
        return $this->hasMany(IbAccount::class, 'user_id', 'id');
    }


}
