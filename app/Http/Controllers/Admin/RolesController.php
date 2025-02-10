<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(){
        //abort_unless(\Gate::allows('um_roles_access'), 403);
        $roles = Role::orderBy('id', 'DESC')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request){
        //abort_unless(\Gate::allows('um_roles_create'), 403);

        $role = new Role();
        $role->title = $request->title;
        if($role->save() == true){
            $notification = "Role added successfully!";
            $color = "success";
            $role->permissions()->sync($request->input('permissions', []));
        }else{
            $notification = "Role added un successfully!";
            $color = "danger";
        }

        $new_details = Role::where('id',$role->id)->get()[0];
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Store Role";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']="NULL";
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Role created successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function edit($id){
        //abort_unless(\Gate::allows('um_roles_edit'), 403);
        $permissions = Permission::all();
        $role = Role::find($id);
        //$role->load('permissions');
        $rolePermissions = $role->permissions->pluck('id', 'id')->toArray();
        return view('admin.roles.edit', compact('permissions', 'role', 'rolePermissions'));
    }

    public function update(Request $request){
        //abort_unless(\Gate::allows('um_roles_edit'), 403);
        $id = $request->role_id;
        $old_detail = Role::where('id',$id)->get()[0];

        $role = Role::find($request->role_id);
	$role->title = $request->title;
        $role->update();
	$role->permissions()->sync($request->input('permissions', []));
	$notification = "Role updated successfully!";
        $color = "success";
           
        $new_details = Role::where('id',$id)->get()[0];
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Update Role";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']=$old_detail;
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Role updated successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }


    public function destroy(Request $request){
        //abort_unless(\Gate::allows('um_roles_edit'), 403);
        $id = $request->role_id;
        $delete = Role::where('id', $id)->delete();
        if($delete == true){
            $notification = "Role deleted successfully!";
            $color = "success";
        }else{
            $notification = "Role deleted un successfully!";
            $color = "danger";
        }

        $new_details = Role::where('id',$id)->get()->first();
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Delete Role";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']=$new_details;
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Role deleted successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }
}
