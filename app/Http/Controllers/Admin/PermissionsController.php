<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PermissionsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('um_permissions_access'), 403);
        $permissions = Permission::orderBy('id', 'DESC')->get();
        return view('admin.permissions.index', compact('permissions'));
    }


    public function store(Request $request)
    {
        abort_unless(\Gate::allows('um_permissions_create'), 403);

        try {
            $permission = new Permission();
            $permission->title = $request->title;
            $permission->name = $request->name;

            if ($permission->save()) {
                $notification = "Permission created successfully!";
                $color = "success";
            } else {
                $notification = "Permission created unsuccessfully!";
                $color = "danger";
            }

            $new_details = Permission::where('id', $permission->id)->get()[0];
            $request = $this->prepareAuditData($request, "Update Permission", null, $new_details);

            $log = new Helper();
            $log->auditTrack($request, "Permission updated successfully", "success");
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            Log::error("Store Permission Exception: ", ['message' => $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile()]);
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => 'danger']);
        }
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('um_permissions_edit'), 403);
        $permission = Permission::where('id', $id)->get()->first();
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request)
    {
        abort_unless(\Gate::allows('um_permissions_edit'), 403);

        try {
            $id = $request->permission_id;
            $update = Permission::where('id', $id)
                ->update([
                    'title' => $request->title,
                    'name' => $request->name,
                ]);

            if ($update) {
                $notification = "Permission updated successfully!";
                $color = "success";
            } else {
                $notification = "Permission updated unsuccessfully!";
                $color = "danger";
            }

            $new_details = Permission::where('id', $id)->get()->first();
            $request = $this->prepareAuditData($request, "Update Permission", null, $new_details);

            $log = new Helper();
            $log->auditTrack($request, "Permission updated successfully", "success");
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            Log::error("Update Permission Exception: ", ['message' => $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile()]);
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => 'danger']);
        }
    }


    public function destroy(Request $request)
    {
        abort_unless(\Gate::allows('um_permissions_edit'), 403);
        try {
            $id = $request->permission_id;
            $delete = Permission::where('id', $id)->delete();

            if ($delete) {
                $notification = "Permission deleted successfully!";
                $color = "success";
            } else {
                $notification = "Permission deleted unsuccessfully!";
                $color = "danger";
            }

            $new_details = Permission::where('id', $id)->get()->first();
            $request = $this->prepareAuditData($request, "Delete Permission", $new_details, $new_details);
            $log = new Helper();
            $log->auditTrack($request, "Permission deleted successfully", "success");
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            Log::error("Delete Permission Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => 'danger']);
        }
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
