<?php

namespace App\Http\Controllers\ESB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BulkTransactionController extends Controller
{
    public function index(){
        return view("esb.bulk.index");
    }
}
