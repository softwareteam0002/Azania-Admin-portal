<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//TODO: Task 1
class CheckBookStatus extends Model
{
    protected $table="tbl_cheque_book_request_statuses";

    protected $connection="sqlsrv2";

}
