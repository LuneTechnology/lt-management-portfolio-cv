<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Pastikan hanya Super Admin yang boleh mengakses User Management.
     */
    private function authorizeSuperAdmin()
    {
        $user = Auth::guard('web')->user();

        if (!$user || $user->id_role != 2) {
            abort(403, 'Hanya Super Admin yang dapat mengakses User Management.');
        }

        return $user;
    }


    /**
     * GET /api/users
     */
    public function index()
    {
        $this->authorizeSuperAdmin();

        $users = User::with('role')
            ->orderBy('id_user', 'desc')
            ->get();

        return response()->json($users);
    }


    /**
     * POST /api/users
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],

            'username' => [
                'nullable',
                'string',
                'max:50',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'contact' => [
                'nullable',
                'string',
                'max:255',
            ],

            'aboutme' => [
                'nullable',
                'string',
            ],

            'id_role' => [
                'required',
                'integer',
                'exists:role,id_role',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        $validated['password'] =
            Hash::make($validated['password']);


        /*
        |--------------------------------------------------------------------------
        | Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            $validated['photo'] =
                $request->file('photo')
                    ->store('users', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $user = User::create($validated);

        return response()->json(
            $user->load('role'),
            201
        );
    }


    /**
     * GET /api/users/{user}
     */
    public function show(string $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::with('role')
            ->where('id_user', $id)
            ->firstOrFail();

        return response()->json($user);
    }


    /**
     * PUT/PATCH /api/users/{user}
     */
    public function update(
        Request $request,
        string $id
    ) {
        $currentUser =
            $this->authorizeSuperAdmin();

        $user = User::where(
            'id_user',
            $id
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                'unique:users,email,' .
                    $user->id_user .
                    ',id_user',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'max:255',
            ],

            'username' => [
                'nullable',
                'string',
                'max:50',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'contact' => [
                'nullable',
                'string',
                'max:255',
            ],

            'aboutme' => [
                'nullable',
                'string',
            ],

            'id_role' => [
                'sometimes',
                'required',
                'integer',
                'exists:role,id_role',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (
            isset($validated['password']) &&
            $validated['password'] !== ''
        ) {

            $validated['password'] =
                Hash::make(
                    $validated['password']
                );

        } else {

            unset($validated['password']);

        }


        /*
        |--------------------------------------------------------------------------
        | Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            if (
                $user->photo &&
                Storage::disk('public')->exists(
                    $user->photo
                )
            ) {
                Storage::disk('public')
                    ->delete($user->photo);
            }


            $validated['photo'] =
                $request->file('photo')
                    ->store('users', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $user->update($validated);

        return response()->json(
            $user->fresh()->load('role')
        );
    }


    /**
     * DELETE /api/users/{user}
     */
    public function destroy(string $id)
    {
        $currentUser =
            $this->authorizeSuperAdmin();

        $user = User::where(
            'id_user',
            $id
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Prevent deleting yourself
        |--------------------------------------------------------------------------
        |
        | Supaya Super Admin tidak tidak sengaja menghapus
        | akun yang sedang digunakan untuk login.
        |
        */

        if (
            $user->id_user ===
            $currentUser->id_user
        ) {
            return response()->json([
                'message' =>
                    'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Photo
        |--------------------------------------------------------------------------
        */

        if (
            $user->photo &&
            Storage::disk('public')->exists(
                $user->photo
            )
        ) {
            Storage::disk('public')
                ->delete($user->photo);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete User
        |--------------------------------------------------------------------------
        */

        $user->delete();

        return response()->json([
            'message' =>
                'User berhasil dihapus.',
        ]);
    }
}