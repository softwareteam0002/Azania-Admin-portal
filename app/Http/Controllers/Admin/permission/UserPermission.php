<?php

namespace App\Http\Controllers\Admin\permission;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Support\Facades\Auth;
class UserPermission extends Controller
{
    public function index()
    {

//        abort_unless(\Gate::allows('role_edit'), 403);

        $permissions = Permission::all()->pluck('name', 'id');
        $allPermissions = Permission::all();

        $user = Auth::user();
        $roles = $user->roles;
        $userRoles = $user->roles[0];
        $users = User::all();
        foreach ($roles as $role) {
            $role->load('permissions');
        }

        $roles = Role::all();


        return view('admin.permissions.user.index', compact('permissions', 'role', 'roles', 'users', 'userRoles', 'allPermissions'));
    }
}
