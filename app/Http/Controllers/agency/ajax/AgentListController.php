<?php

namespace App\Http\Controllers\agency\ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Crypt;

class AgentListController extends Controller
{
    public function test($user_id){


        $decrypted_id = Crypt::decrypt($user_id);
        $users = User::find($decrypted_id);
        return response()->json(['success'=>'Data is successfully selected',
            'data'=>\GuzzleHttp\json_encode($users)]);
    }
}
