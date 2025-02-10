<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblServices extends Model
{
    protected $table="tbl_services";

    protected $primaryKey="agent_serviceID";

    public $timestamps=false;
}
