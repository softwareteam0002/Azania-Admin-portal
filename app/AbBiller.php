<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbBiller extends Model
{
    protected $connection = "sqlsrv4";

    public $timestamps = false;

    protected $table = "tbl_agency_banking_billers";

    protected $primaryKey = "id";
    protected $guarded = [];

    public function billergroups()
    {
        return $this->belongsTo(AbBillerGroup::class, 'biller_group', 'biller_group_id');
    }

    public function status()
    {
        return $this->belongsTo(AbStatus::class, 'biller_status', 'tbl_status_id');
    }

    public function initiator()
    {
        return $this->belongsTo(AdminUser::class, 'initiator_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(AdminUser::class, 'approver_id', 'id');
    }
}
