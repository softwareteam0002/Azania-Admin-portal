<?php

namespace App\Http\Controllers\IB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BulkTransactionController extends Controller
{
    public function index(){
        return view("ib.bulk.index");
    }
}
