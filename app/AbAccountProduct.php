<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbAccountProduct extends Model
{
    //
    protected $table="tbl_agency_banking_account_product";

    protected $connection="sqlsrv4";

    public $timestamps=false;

   /* public function approvers()
    {
        return $this->belongsTo(AdminUser::class,'approver_id','id');
    }

    public function initiators()
    {
        return $this->belongsTo(AdminUser::class,'initiator_id','id');
    }
*/

}