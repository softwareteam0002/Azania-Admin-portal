<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Operator extends Authenticatable implements JWTSubject
{
    public $timestamps = false;
    protected $table = 'tbl_agency_banking_operator';
    protected $connection = 'sqlsrv4';
	protected $primaryKey = 'operator_id';
	

	protected $guarded = [];
	/*protected $visible = [
        'operator_id', 'operator_fullname', 'operator_msisdn',
		'device_id', 'agent_id', 'operator_status',
    ];*/
    protected $hidden = [
        'operator_password',
        'remember_token',
    ];

    public function agent(){
        return $this->belongsTo('App\TblAgent', 'agent_id');
    }
    
    public function device(){
        return $this->belongsTo('App\Devices', 'device_id');
    }

    public function initiators(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approvers(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
	
	 /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
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
}
