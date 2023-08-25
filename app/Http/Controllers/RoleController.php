<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return $roles;
    }

    public function store(Request $request)
    {
        Role::create($request->all());
        return response()->json([
            'message' => 'Role created successfully',
            'role' => $request->all()
        ], 201);
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $role->update($request->all());
        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ], 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([
            'message' => 'Role deleted successfully'
        ], 200);
    }

 }
