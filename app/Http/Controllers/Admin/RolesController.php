<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('um_roles_access'), 403);
        $roles = Role::orderBy('id', 'DESC')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('um_roles_create'), 403);

        try {
            $role = new Role();
            $role->title = $request->title;
            if ($role->save()) {
                $notification = "Role added successfully!";
                $color = "success";
                $role->permissions()->sync($request->input('permissions', []));
            } else {
                $notification = "Role added un successfully!";
                $color = "danger";
            }

            $new_details = Role::where('id', $role->id)->first();
            $request = $this->prepareAuditData($request, "Store Role", null, $new_details);

            $log = new Helper();
            $log->auditTrack($request, "Role created successfully", "success");
        } catch (\Exception $e) {
            Log::error('Error storing role: ', ['message' => $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile()]);
            $notification = $e->getMessage();
            $color = "danger";
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('um_roles_edit'), 403);

        try {
            $permissions = Permission::all();
            $role = Role::find($id);
            $rolePermissions = $role->permissions->pluck('id', 'id')->toArray();
            return view('admin.roles.edit', compact('permissions', 'role', 'rolePermissions'));
        } catch (\Exception $e) {
            Log::error('Error editing role: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with(['notification' => 'An error occurred while editing the role.', 'color' => 'danger']);
        }
    }

    public function update(Request $request)
    {
        abort_unless(\Gate::allows('um_roles_edit'), 403);

        try {
            $id = $request->role_id;
            $old_detail = Role::where('id', $id)->get()[0];

            $role = Role::find($request->role_id);
            $role->title = $request->title;
            $role->update();
            $role->permissions()->sync($request->input('permissions', []));
            $notification = "Role updated successfully!";
            $color = "success";

            $new_details = Role::where('id', $id)->get()[0];
            $request = $this->prepareAuditData($request, "Update Role", $old_detail, $new_details);

            $log = new Helper();
            $log->auditTrack($request, "Role updated successfully", "success");
        } catch (\Exception $e) {
            Log::error('Error updating role: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "An error occurred while updating the role.";
            $color = "danger";
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }


    public function destroy(Request $request)
    {
        abort_unless(\Gate::allows('um_roles_edit'), 403);

        try {
            $id = $request->role_id;
            $delete = Role::where('id', $id)->delete();
            if ($delete) {
                $notification = "Role deleted successfully!";
                $color = "success";
            } else {
                $notification = "Role deleted un successfully!";
                $color = "danger";
            }

            $new_details = Role::where('id', $id)->get()->first();

            $request = $this->prepareAuditData($request, "Delete Role", $new_details, $new_details);
            $log = new Helper();
            $log->auditTrack($request, "Role deleted successfully", "success");
        } catch (\Exception $e) {
            Log::error('Error deleting role: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "An error occurred while deleting the role.";
            $color = "danger";
        }

        return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
    }

    private function prepareAuditData(Request $request, $action, $oldDetails = null, $newDetails = null, $reason = null)
    {
        $request['user_id'] = Auth::user()->getAuthIdentifier();
        $request['module'] = "User Management";
        $request['action'] = $action;
        $request['action_time'] = now();
        $request['reason'] = $reason;
        $request['old_details'] = $oldDetails;
        $request['new_details'] = $newDetails;
        return $request;
    }
}
