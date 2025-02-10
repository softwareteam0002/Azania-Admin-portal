<?php

namespace App\Models\Tips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv5';
    public $timestamps = false;
    public function scopeSearch($query,$value)
    {

        if ( ! is_null($value)) {

            return $query->where(function ($query) use ($value) {
                $query->where('sender_reference', 'like', '%'.$value.'%')
                    ->orWhere('sender_fspId', 'like', '%'.$value.'%')
                    ->orWhere('sender_user', 'like', '%'.$value.'%')
                    ->orWhere('recipients_fspId', 'like', '%'.$value.'%')
                    ->orWhere('recipients_user', 'like', '%'.$value.'%')
                    ->orWhere('subject', 'like', '%'.$value.'%')
                    ->orWhere('body', 'like', '%'.$value.'%')
                    ->orWhere('notificationType', 'like', '%'.$value.'%')
                    ->orWhere('status', 'like', '%'.$value.'%')
                    ->orWhere('flag', 'like', '%'.$value.'%')
                    ->orWhere('created_at', 'like', '%'.$value.'%');
            });
        }
    }
}
