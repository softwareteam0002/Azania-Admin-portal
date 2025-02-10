<?php

namespace App\Http\Controllers\Admin\permission\ajax;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use \Crypt;

class PermissionController extends Controller
{
    public function addRole(Request $request){

        $role = Role::create($request->all());

        if ($role){
            return response()->json(['success'=>'Data is successfully inserted to database',
                'data'=>\GuzzleHttp\json_encode($role),
                'status'=>'00']);
        }else{
            return response()->json(['failure'=>'Data was not inserted into the database',
                'data'=>\GuzzleHttp\json_encode($role),
                'status'=>'01']);
        }

    }
    public function updateRolePermission(Request $request){

        $role_id = Crypt::decrypt($request->role);
        $permission = $request->permission;
        $role = Role::find($role_id);
        $role->permissions()->sync($request->input('permission', []));

        if ($role){
            return response()->json(['success'=>'Data is successfully inserted to database',
                'data'=>\GuzzleHttp\json_encode($role),
                'status'=>'00']);
        }else{
            return response()->json(['failure'=>'Data was not inserted into the database',
                'data'=>\GuzzleHttp\json_encode($role),
                'status'=>'01']);
        }

    }
    public function listRoles(){

        $role = Role::all()->pluck('title', 'id');

        if ($role){
            return response()->json(['success'=>'Data is successfully selected',
                'data'=>$role->toArray(),
                'status'=>'00']);
        }else{
            return response()->json(['failure'=>'Data was not inserted into the database',
                'data'=>$role.toArray(),
                'status'=>'01']);
        }

    }
    public function getRolePermissions($role_id){

        $decrypted_id = Crypt::decrypt($role_id);
        $role = Role::find($decrypted_id);
        $rolePermissions = $role->permissions;
        $permissions = Permission::all();

        if ($role){
            return response()->json(['success'=>'Selected list of permissions',
                'role'=>$role->toJson(),
                'permission'=>$permissions->toJson(),
                'rolePermission'=>$rolePermissions->toJson(),
                'status'=>'00']);
        }else{
            return response()->json(['failure'=>'Data was not inserted into the database',
                'role'=>$role->toJson(),
                'permission'=>$permissions->toJson(),
                'status'=>'01']);
        }

    }
}
