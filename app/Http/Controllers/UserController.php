<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * READ SEMUA USER
     */
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    /**
     *CREATED
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'username' => 'nullable|string|max:50',
            'photo'    => 'nullable|string|max:255',
            'contact'  => 'nullable|string|max:255',
            'aboutme'  => 'nullable|string',
            'id_role'  => 'nullable|exists:roles,id_role',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * READ SATU USER
     */
    public function show(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email'    => 'sometimes|email|unique:users,email,' . $id . ',id_user',
            'password' => 'nullable|min:6',
            'username' => 'nullable|string|max:50',
            'photo'    => 'nullable|string|max:255',
            'contact'  => 'nullable|string|max:255',
            'aboutme'  => 'nullable|string',
            'id_role'  => 'nullable|exists:roles,id_role',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * DELETE
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}