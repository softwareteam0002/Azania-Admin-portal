<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankingAgentService extends Model
{
    protected $primaryKey = 'agent_serviceID';
    protected $table = 'tbl_agency_banking_agent_service';
    protected $connection='sqlsrv4';
}
