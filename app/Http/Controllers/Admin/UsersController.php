<?php

namespace App\Http\Controllers\Admin;

use App\AbBranch;
use App\Devices;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Mail\EmailNotification;
use App\Role;
use App\TblAdminActionLevel;
use App\User;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Random\RandomException;
use Str;

class UsersController extends Controller
{

    public function index()
    {
        abort_unless(\Gate::allows('um_users_access'), 403);
        $users = User::orderBy('id', 'DESC')->latest()->get();
        $roles = Role::all();
        $actions = TblAdminActionLevel::all();
        return view('admin.users.index', compact('users', 'roles', 'actions'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('um_users_create'), 403);
        $roles = Role::all();
        $actions = TblAdminActionLevel::all();
        return view('admin.users.create', compact('roles', 'actions'));
    }

    public function store(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'fname' => 'required|regex:/^[A-Za-z][A-Za-z\-\' ]{1,49}$/',
            'lname' => 'required|regex:/^[A-Za-z][A-Za-z\-\' ]{1,49}$/',
            'action_id' => 'required|regex:/^\d$/',
        ]);

        if ($validator->fails()) {
            return back()->with([
                'notification' => $validator->errors()->first(),
                'color' => 'danger',
            ]);
        }

        DB::beginTransaction();
        try {
            $notification = "Failed to add user!";
            $color = 'danger';
            // Create a new user
            $user = User::create([
                'name' => "{$request->fname} {$request->lname}",
                'email' => $request->email,
                'action_id' => $request->action_id,
                'status' => 0,
                'initiator_id' => Auth::id(),
                'first_login' => 1,
                'password' => Hash::make(Str::random(10)),
                'approver_id' => 0,
                'isWaitingApproval' => 1,
                'isDeleted' => 0,
            ]);

            if ($user) {
                // Attach roles to the user
                $user->roles()->attach($request->roles);
                DB::commit();
                $notification = "User added successfully!";
                $color = 'success';
            }
            DB::rollBack();
            $this->auditLog(Auth::id(), 'Add User', 'User Management', $notification, $request->ip());
            return back()->with(['notification' => $notification, 'color' => $color]);

        } catch (\Exception $ex) {
            $notification = "Something went wrong, Try again later!";
            $color = 'danger';
            Log::error("Add User Exception: " . $ex->getMessage());
            DB::rollBack();
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }


    public function edit(User $user)
    {
        abort_unless(\Gate::allows('um_users_edit'), 403);
        $roles = Role::all();
        $user_roles = $user->roles()->pluck('id', 'id')->toArray();
        $actions = TblAdminActionLevel::all();
        return view('admin.users.edit', compact('roles', 'user', 'actions', 'user_roles'));
    }

    public function update(Request $request)
    {
        abort_unless(\Gate::allows('um_users_edit'), 403);
        DB::beginTransaction();
        try {
            $old_details = User::where('id', Crypt::decrypt($request->id))->get()[0];
            $decryptedId = Crypt::decrypt($request->id);
            $user = User::find($decryptedId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->action_id = $request->action_id;
            $user->initiator_id = Auth::user()->id;
            $user->isWaitingApproval = 1;
            $user->approver_id = 0;
            $user->update();
            // Sync roles
            $user->roles()->sync($request['roles']);
            $new_details = User::where('id', Crypt::decrypt($request->id))->get()[0];

            $request['user_id'] = Auth::user()->getAuthIdentifier();
            $request['module'] = "ESB Portal";
            $request['action'] = "Update user";
            $request['action_time'] = now();
            $request['reason'] = "NULL";
            $request['old_details'] = $old_details;
            $request['new_details'] = $new_details;

            $log = new Helper();
            DB::commit();
            return $log->auditTrack($request, "User updated successfully", "success");
        } catch (\Exception $e) {
            Log::error("Update User Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            DB::rollBack();
            return back()->with([
                'notification' => 'Something went wrong, try again later!',
                'color' => 'danger',
            ]);
        }
    }

    public function show(Request $r)
    {
        $user = User::where('id', $r->user_id)->get()[0];
        abort_unless(\Gate::allows('um_users_view'), 403);
        $user->load('roles');
        $actions = TblAdminActionLevel::all();
        $roles = Role::all()->pluck('title', 'id');

        return view('admin.users.show', compact('user', 'actions', 'roles'));
    }

    public function approveUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $user_roles = $user->roles()->pluck('id', 'id')->toArray();
        $actions = TblAdminActionLevel::all();
        return view('admin.users.approve_user', compact('user', 'roles', 'user_roles', 'actions'));
    }

    /**
     * @throws RandomException
     */

    public function approveUserAct(Request $request, $id)
    {
        try {
            // Find the user or throw a 404 error
            $user = User::findOrFail($id);
            $userId = Auth::id();

            // Handle rejection
            if ($request->reject === 'reject') {
                $user->isWaitingApproval = 2;
                $user->approver_id = $userId;

                if ($user->update()) {
                    $this->auditLog($userId, 'Reject User', 'User Management', 'User rejected successfully',
                        $request->ip());
                    return redirect()
                        ->route('admin.users.index')
                        ->with([
                            'notification' => 'User rejected successfully!',
                            'color' => 'success',
                        ]);
                }

                return redirect()
                    ->route('admin.users.index')
                    ->with([
                        'notification' => 'Failed to reject user!',
                        'color' => 'danger',
                    ]);
            }

            // Handle approval
            if ($request->approve === 'approve') {
                // Generate and hash password
                $password = Helper::generatePassword();
                $user->isWaitingApproval = 0; // Approved state
                $user->approver_id = $userId;
                $user->password = Hash::make($password);

                if ($user->update()) {
                    // Prepare and queue email notification
                    $message = "Dear **{$user->name}**,  \n\nYour Password to access Admin Portal is **{$password}**";
                    Mail::to($user->email)->queue(new EmailNotification($message));
                    $this->auditLog($userId, 'Approve User', 'User Management', 'User approved successfully',
                        $request->ip());
                    return redirect()
                        ->route('admin.users.index')
                        ->with([
                            'notification' => 'User approved successfully!',
                            'color' => 'success',
                        ]);
                }

                return redirect()
                    ->route('admin.users.index')
                    ->with([
                        'notification' => 'Failed to approve user!',
                        'color' => 'danger',
                    ]);
            }

            // Handle invalid actions
            return redirect()
                ->route('admin.users.index')
                ->with([
                    'notification' => 'Invalid action!',
                    'color' => 'danger',
                ]);

        } catch (\Exception $ex) {
            // Log the exception for debugging purposes
            Log::error('Approve User Exception: ' . $ex->getMessage());

            return back()->with([
                'notification' => 'Something went wrong, try again later!',
                'color' => 'danger',
            ]);
        }
    }


    public function deleteUserApproval(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $roles = Role::all();
        $user_roles = $user->roles()->pluck('id', 'id')->toArray();
        $actions = TblAdminActionLevel::all();
        return view('admin.users.delete_user_approval', compact('user', 'roles', 'user_roles', 'actions'));
    }

    public function deleteUser($id)
    {
        $user_id = Auth::id();
        User::where(['id' => $id])->update(['isWaitingApproval' => 1, 'approver_id' => 0, 'deletedBy_id' => $user_id, 'isDeleted' => 1]);
        return redirect()->route('admin.users.index')->with(['notification' => 'User delete request sent for approval', 'color' => 'success']);
    }

    public function deleteUserActApproval(Request $request, $id)
    {
        $user_id = Auth::id();
        try {
            if ($request->reject === 'reject') {
                User::where(['id' => $id])->update(['isWaitingApproval' => 0, 'approver_id' => $user_id, 'isDeleted' => 0]);
                return redirect()->route('admin.users.index')->with(['notification' => 'User deleting has been rejected successfully', 'color' => 'success']);
            }

            if ($request->approve === 'approve') {
                User::where(['id' => $id])->delete();
                return redirect()->route('admin.users.index')->with(['notification' => 'User deleting has been approved successfully', 'color' => 'success']);
            }
        } catch (\Exception $e) {
            Log::error("Delete User Approval Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->route('admin.users.index')->with(['notification' => 'Something went wrong, try again later!', 'color' => 'danger']);
        }
    }

    public function getDevices()
    {
        $devices = Devices::orderBy('device_id', 'DESC')->get();
        $branches = AbBranch::all();
        return view('admin.devices.index', compact('devices', 'branches'));
    }

    //Activates agent device
    public function activateDevice(Request $request)
    {
        $status = "1";
        $request->validate([
            'device_id' => 'required'
        ]);

        try {
            $update = Devices::where('device_id', $request->device_id)
                ->update([
                    'device_status' => $status
                ]);

            if ($update) {
                $notification = "Device activated successfully";
                $color = "success";
            } else {
                $notification = "Oops something went wrong!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error("Device Activation Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "Something went wrong, try again later!";
            $color = "danger";
        }

        return redirect('admin/devices')->with('notification', $notification)->with('color', $color);
    }

    //Deactivates device
    public function deactivateDevice(Request $request)
    {
        $status = "2";
        $request->validate([
            'device_id' => 'required'
        ]);

        try {
            $update = Devices::where('device_id', $request->device_id)
                ->update([
                    'device_status' => $status
                ]);

            if ($update) {
                $notification = "Device de-activated successfully";
                $color = "success";
            } else {
                $notification = "Oops something went wrong!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error("Device Deactivation Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "Something went wrong, try again later!";
            $color = "danger";
        }

        return redirect('admin/devices')->with('notification', $notification)->with('color', $color);
    }

    public function deviceCreateView()
    {
        return view('admin.devices.create');
    }

    public function editDevice($id)
    {
        //updated by Evance Nganyaga
        $device = Devices::on('sqlsrv4')->where('device_id', $id)->get()[0];
        $branches = AbBranch::all();
        return view('admin.devices.edit', compact('device', 'branches'));
    }

    //added by Evance Nganyaga
    public function updateDevice(Request $request)
    {
        //then this is a normal request
        $request->validate([
            'device_imei1' => 'required',
            'branch_id' => 'required',
            'terminal' => 'required'
        ]);

        //Validation imei1 and imei2 should not be the same
        if ($request->device_imei1 === $request->device_imei2) {
            return redirect()->back()->with(['notification' => 'IMEI Number 1 should not Match IMEI Number 2!', 'color' => 'danger']);
        }

        try {
            $update = Devices::where('device_id', $request->device_id)
                ->update([
                    'device_imei1' => $request->device_imei1,
                    'device_imei2' => $request->device_imei2,
                    'branch_id' => $request->branch_id,
                    'terminal_ID' => $request->terminal,
                ]);

            if ($update) {
                $notification = "Device updated successfully!";
                $color = "success";
            } else {
                $notification = "There was a problem trying to update the device!";
                $color = "danger";
            }
        } catch (\Exception $e) {
            Log::error("Update Device Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "Something went wrong, try again later!";
            $color = "danger";
        }

        return redirect()->back()->with('notification', $notification)->with('color', $color);
    }

    public function storeDevice(Request $request)
    {
        try {
            //updated by Evance Nganyaga
            if ($request->bulk_upload && $request->hasFile('devices_file')) {
                //this is a bulk file upload request
                $devices_file = $request->file('devices_file');
                //validate the file
                if ($devices_file->getClientOriginalExtension() !== "csv" && $devices_file->getMimeType() !== "text/plain") {
                    return redirect()->back()->with(['notification' => 'Invalid file format, please download and use the provided template.', 'color' => 'danger']);
                }
                if ($devices_file->getSize() > 2000000) {
                    return redirect()->back()->with(['notification' => 'File is large than 2 MB, please split and upload in portions.', 'color' => 'danger']);
                }

                //loop through the file
                $c = 0;
                $insert_count = 0;
                foreach (file($devices_file) as $line) {
                    // loop with $line for each line of yourfile.txt
                    if ($c == 0) {
                        //heading
                        $c++;
                        continue;
                    } else {
                        //real data
                        $data = explode(",", $line);
                        $terminal_ID = $data[0];
                        $device_imei1 = $data[1];
                        $device_imei2 = $data[2];

                        //validate the data
                        if ($request->branch_id) {
                            return redirect()->back()->with(['notification' => 'Please assign a branch to devices.', 'color' => 'danger']);
                        }
                        if ($device_imei1 === $device_imei2) {
                            return redirect()->back()->with(['notification' => 'One or more devices has the same imei numbers.', 'color' => 'danger']);
                        }

                        $device_imei = Devices::where('device_imei1', $device_imei1)->orWhere('device_imei2', $device_imei1)->get();
                        if (count($device_imei) > 0) {
                            return redirect()->back()->with(['notification' => 'Device with imei ' . $device_imei1 . ', ' . $device_imei2 . ' already exist.', 'color' => 'danger']);
                        }

                        if ($device_imei2 !== "null\r\n") {
                            $device_imei = Devices::where('device_imei1', $device_imei2)->orWhere('device_imei2', $device_imei2)->get();
                            if (count($device_imei) > 0) {
                                return redirect()->back()->with(['notification' => 'Device with imei ' . $device_imei1 . ', ' . $device_imei2 . ' already exist.', 'color' => 'danger']);
                            }
                        } else {
                            $device_imei2 = null;
                        }

                        $terminal_id = Devices::where('terminal_ID', $terminal_ID)->get();
                        if (count($terminal_id) > 0) {
                            return redirect()->back()->with(['notification' => 'One or more devices have the same Terminal ID ' . $terminal_ID . ' already in use', 'color' => 'danger']);
                        }

                        $device = new Devices();
                        $device->setConnection('sqlsrv4');
                        $device->device_imei1 = $device_imei1;
                        $device->device_imei2 = $device_imei2;
                        $device->terminal_ID = $terminal_ID;
                        $device->branch_id = $request['branch_id'];
                        $device->device_status = 0;
                        $device->registered_by = auth()->user()->id;
                        $device->save();
                        $insert_count++;
                    }
                }
                return redirect()->back()->with(['notification' => $insert_count . ' Device(s) added successfully!', 'color' => 'success']);
            }

            //then this is a normal request
            $request->validate([
                'device_imei1' => 'required',
                'branch_id' => 'required',
                'terminal' => 'required'
            ]);

            //Validation imei1 and imei2 should not be the same
            if ($request->device_imei1 === $request->device_imei2) {
                return redirect()->back()->with(['notification' => 'Imei Number 1 should not Match Imei Number 2', 'color' => 'danger']);
            }

            $device_imei = Devices::where('device_imei1', $request->device_imei1)->get();

            if (count($device_imei) > 0) {
                return redirect()->back()->with(['notification' => 'Device already exists', 'color' => 'danger']);
            }

            $terminal_id = Devices::where('terminal_ID', $request->terminal)->get();

            if (count($terminal_id) > 0) {
                return redirect()->back()->with(['notification' => 'Terminal Id already in use', 'color' => 'danger']);
            }

            //check  to see if there is device imei 2 in the request
            if ($request->device_imei2) {
                $imei2 = $request->device_imei2;
            } else {
                $imei2 = null;
            }

            $device = new Devices();
            $device->setConnection('sqlsrv4');
            $device->device_imei1 = $request['device_imei1'];
            $device->device_imei2 = $imei2;
            $device->terminal_ID = $request['terminal'];
            $device->branch_id = $request['branch_id'];
            $device->device_status = 0;
            $device->registered_by = auth()->user()->id;
            $device->save();

            return redirect()->back()->with(['notification' => 'Device added successfully', 'color' => 'success']);
        } catch (\Exception $e) {
            Log::error("Store Device Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->back()->with(['notification' => 'Something went wrong, try again later!', 'color' => 'danger']);
        }
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        try {
            $user->delete();
            return back()->with(['notification' => 'User deleted successfully', 'color' => 'success']);
        } catch (\Exception $e) {
            Log::error("User Deletion Exception: " ,['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return back()->with(['notification' => 'Failed to delete user, please try again later!', 'color' => 'danger']);
        }
    }

    /**
     * @throws RandomException
     */
    public function reset(Request $request)
    {
        try {
            $password = Helper::generatePassword();
            $user = User::where('id', $request->user_id)->first();
            $user->password = Hash::make($password);
            $user->first_login = 1;
            $user->status = 0;
            $passwd_reset = $user->update();
            if ($passwd_reset) {
                $message = "Dear **{$user->name}**,  \n\nYour Password to access Admin Portal is **{$password}**";
                Cache::forget('attempt_by_' . $user->id);
                // Queue the email notification
                Mail::to($user->email)->queue(new EmailNotification($message));
                $this->auditLog($user->id, 'Password Reset', 'User Management', 'password reset successfully',
                    $request->ip());
                return back()->with(['notification' => 'Password reset successfully', 'color' => 'success']);
            } else {
                return back()->with(['notification' => 'Password reset failed', 'color' => 'danger']);
            }
        } catch (\Exception $exception) {
            Log::error("Admin Reset Password Exception: " . $exception->getMessage());
            return back()->with(['notification' => 'Something went wrong. Try again later!', 'color' => 'danger']);
        }

    }
}
