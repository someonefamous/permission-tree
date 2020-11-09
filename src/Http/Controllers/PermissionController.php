<?php

namespace SomeoneFamous\PermissionTree\Http\Controllers;

use SomeoneFamous\PermissionTree\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        return view('sf_permissions::permissions.index', ['permissions' => $permissions]);

    }

    public function show(Permission $permission)
    {
        return view('sf_permissions::permissions.show', ['permission' => $permission]);
    }
}
