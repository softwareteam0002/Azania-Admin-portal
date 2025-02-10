<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbServiceCharges extends Model
{
    public $timestamps=false;

    protected $table="tbl_fees";

    protected $connection="sqlsrv2";

    public function approvers()
    {
        return $this->belongsTo('App\IbUser','approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo('App\IbUser','initiator_id','id');
    }
}
