<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class TblAgent extends Authenticatable implements JWTSubject
{

    protected  $table="tbl_agency_banking_agents";

    protected $connection = 'sqlsrv4';
    protected $guarded=[];

    public $timestamps=false;
	protected $hidden = [
        'agent_password',
        //'remember_token',
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /*public function accounts(){
        return $this->belongsTo(TblABBankAccounts::class,'agent_id','agent_id');
    }*/
	  public function accounts(){
        return $this->hasMany(TblABBankAccounts::class,'agent_id','agent_id');
    }
    public function operators(){
        return $this->hasMany(Operator::class,'agent_id','agent_id');
    }
    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $primaryKey = 'agent_id';
}
