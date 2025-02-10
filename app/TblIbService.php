<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblIbService extends Model
{

    protected $connection="sqlsrv2";

    protected $table="tbl_services_config";

    protected $primaryKey="agent_serviceID";

    public $timestamps=false;
}
