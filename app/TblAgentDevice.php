<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblAgentDevice extends Model
{

    public $timestamps = false;

    protected $table="tbl_agency_banking_agent_devices";

    protected $connection="sqlsrv4";
    protected $guarded=[];

    public function device(){
        return $this->belongsTo('App\Devices', 'device_id');
    }

    public function status()
    {
        return $this->belongsTo(AbStatus::class, 'operator_status', 'tbl_status_id');
    }

     public function initiators(){
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }

    public function approvers(){
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }
}
