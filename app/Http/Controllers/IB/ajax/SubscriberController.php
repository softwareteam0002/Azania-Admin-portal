<?php

namespace App\Http\Controllers\IB\ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use \Crypt;

class SubscriberController extends Controller
{
    public function index($user_id){


        $decrypted_id = Crypt::decrypt($user_id);
        $sql = "SELECT * FROM `users` WHERE id=".$decrypted_id;
        $subscriber = DB::connection('mysql')->select($sql);
        return response()->json(['success'=>'Data is successfully selected',
            'data'=>\GuzzleHttp\json_encode($subscriber)]);
    }

}
