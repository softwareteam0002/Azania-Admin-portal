<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use App\Models\AuthMatrix\TransactionApproveLog;

class PaymentService extends Model
{
    //
    protected $connection = 'sqlsrv2';
    
    protected $table    = 'tbl_transactions';

    protected $fillable = ["reference_number", "mobile_number", "meter_number", "amount"];

    public function approveLogs()
    {
        return $this->morphMany(TransactionApproveLog::class, 'transactionable');
    }
}
