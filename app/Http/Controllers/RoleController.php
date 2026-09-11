<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // READ - semua role
    public function index()
    {
        return Role::all();
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role = Role::create($validated);

        return response()->json($role, 201);
    }

    // READ - satu role
    public function show(Role $role)
    {
        return response()->json($role);
    }

    // UPDATE
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    // DELETE
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus',
        ]);
    }
}