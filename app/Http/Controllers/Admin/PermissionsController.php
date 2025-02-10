<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPermissionRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Permission;
use App\User;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(){
        abort_unless(\Gate::allows('um_permissions_access'), 403);
        $permissions = Permission::orderBy('id', 'DESC')->get();
        return view('admin.permissions.index', compact('permissions'));
    }


    public function store(Request $request){
        abort_unless(\Gate::allows('um_permissions_create'), 403);
        $permission = new Permission();
        $permission->title = $request->title;
        $permission->name =  $request->name;
        //$permission->sections_id =  $request->sections_id;

        if($permission->save() == TRUE){
            $notification = "Permission created successfully!";
            $color = "success";
        }else{
            $notification = "Permission created un successfully!";
            $color = "danger";
        }

        $new_details = Permission::where('id',$permission->id)->get()[0];
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Update Permission";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']="NULL";
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Permission updated successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function edit($id){
        abort_unless(\Gate::allows('um_permissions_edit'), 403);
        $permission = Permission::where('id', $id)->get()->first();
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request){
        abort_unless(\Gate::allows('um_permissions_edit'), 403);
        $id = $request->permission_id;
        $update = Permission::where('id', $id)
        ->update([
            'title'=>$request->title,
            'name'=>$request->name,

        ]);

        if($update == TRUE){
            $notification = "Permission updated successfully!";
            $color = "success";
        }else{
            $notification = "Permission updated un successfully!";
            $color = "danger";
        }

        $new_details = Permission::where('id',$id)->get()->first();
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Update Permission";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']="NULL";
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Permission updated successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }


    public function destroy(Request $request){
        abort_unless(\Gate::allows('um_permissions_edit'), 403);
        $id = $request->permission_id;
        $delete = Permission::where('id', $id)->delete();

        if($delete == TRUE){
            $notification = "Permission deleted successfully!";
            $color = "success";
        }else{
            $notification = "Permission deleted un successfully!";
            $color = "danger";
        }

        $new_details = Permission::where('id',$id)->get()->first();
        $request['user_id']=Auth::user()->getAuthIdentifier();
        $request['module']="ESB Portal";
        $request['action']="Permission Deleted";
        $request['action_time']=now();
        $request['reason']="NULL";
        $request['old_details']=$new_details;
        $request['new_details']=$new_details;

        $log = new Helper();
        $log->auditTrack($request,"Permission deleted successfully","success");
        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

   
}
