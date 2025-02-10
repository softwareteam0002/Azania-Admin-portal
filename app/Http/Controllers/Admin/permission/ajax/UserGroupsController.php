<?php

namespace App\Http\Controllers\Admin\permission\ajax;

use App\Permission;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use \Crypt;

class UserGroupsController extends Controller
{

    public function updateRoleUser(Request $request){

        $user_id = Crypt::decrypt($request->user);
        $newRole = $request->role;
        $user = User::find($user_id);
        $result = User::findOrFail($user_id)->roles()->sync($newRole);

        if ($result){
            return response()->json(['success'=>'Data is successfully inserted to database',
                'data'=>\GuzzleHttp\json_encode($result),
                'status'=>'00']);
        }else{
            return response()->json(['failure'=>'Data was not inserted into the database',
                'data'=>\GuzzleHttp\json_encode($result),
                'status'=>'01']);
        }

    }

    public function getRolebasedOnUser($user_id){

                $decrypted_id = Crypt::decrypt($user_id);
                $user = User::find($decrypted_id);
                $userRoles = $user->roles;
                $allRoles = Role::all();

                if ($userRoles){
                    return response()->json(['success'=>'Selected list of roles',
                        'userRoles'=>$userRoles->toJson(),
                        'allRoles'=>$allRoles->toJson(),
                        'status'=>'00']);
                }else{
                    return response()->json(['failure'=>'Failed to fetch roles',
                        'userRoles'=>$userRoles->toJson(),
                        'allRoles'=>$allRoles->toJson(),
                        'status'=>'01']);
                }

            }
}
