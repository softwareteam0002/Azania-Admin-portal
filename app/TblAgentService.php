<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblAgentService extends Model
{
    protected $table="tbl_agency_banking_agent_service";

    protected $connection="sqlsrv4";

    protected $primaryKey="agent_serviceID";

    public $timestamps=false;
}




