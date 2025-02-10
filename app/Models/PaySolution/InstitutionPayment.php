<?php

namespace App\Models\PaySolution;

use Illuminate\Database\Eloquent\Model;

use App\Models\AuthMatrix\TransactionApproveLog;

class InstitutionPayment extends Model
{
    protected $table = 'tbl_institution_payments';

    protected $connection = 'sqlsrv2';

    public function approveLogs()
    {
        return $this->morphMany(TransactionApproveLog::class, 'transactionable');
    }

}
