<?php

namespace App\Http\Controllers\ESB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(){
        $sql = "SELECT * FROM `users`";
        $subscribers = DB::connection('mysql2')->select($sql);
        return view("esb.users.index", compact('subscribers'));
    }
}
