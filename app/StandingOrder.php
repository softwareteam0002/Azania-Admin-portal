<?php

namespace App;

use App\PaymentServiceStatus;
use Illuminate\Database\Eloquent\Model;

class StandingOrder extends Model
{
    //
    protected $table = 'tbl_standing_order';
	protected $connection = 'sqlsrv2';
    protected $fillable = ['transactionId', 'serviceType', 'credited_account', 'start_date', 'interval', 'repetition', 'amount', 'account_id', 'user_id', 'isVerified'];

    public function accounts()
    {
        return $this->belongsTo('\App\Models\Web\Account', 'account_id');
    } 
	public function approver()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(InstitutionRequestApproval::class, 'request_id');
    }
    public function statuses()
    {
        return $this->belongsTo(PaymentServiceStatus::class, "status_id");
    }
}
