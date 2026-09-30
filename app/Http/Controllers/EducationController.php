<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    /**
     * READ SEMUA EDUCATION
     *
     * Admin      → hanya education miliknya
     * Super Admin → semua education
     */
    public function index()
    {
        $user = Auth::guard('web')->user();

        if ($user->id_role == 2) {
            // Super Admin
            $educations = Education::with('user')->get();
        } else {
            // Admin
            $educations = Education::where(
                'id_user',
                $user->id_user
            )
            ->with('user')
            ->get();
        }

        return response()->json($educations);
    }

    /**
     * CREATE EDUCATION
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:50',
            'major'    => 'required|string|max:50',
            'place'    => 'required|string|max:50',
            'level'    => 'required|string|max:20',
            'date_in'  => 'required|date',
            'date_out' => 'nullable|date|after_or_equal:date_in',
            'gpa'      => 'nullable|numeric|min:0|max:9.99',
        ]);

        /*
         * Admin:
         * ID user otomatis menggunakan user yang sedang login.
         */
        if ($user->id_role == 1) {
            $validated['id_user'] = $user->id_user;
        }

        /*
         * Super Admin:
         * boleh memilih id_user.
         */
        if ($user->id_role == 2) {
            $request->validate([
                'id_user' => 'required|exists:users,id_user',
            ]);

            $validated['id_user'] = $request->id_user;
        }

        $education = Education::create($validated);

        return response()->json(
            $education->load('user'),
            201
        );
    }

    /**
     * READ SATU EDUCATION
     */
    public function show(string $id)
    {
        $user = Auth::guard('web')->user();

        /*
         * Super Admin bisa melihat semua.
         */
        if ($user->id_role == 2) {
            $education = Education::with('user')
                ->findOrFail($id);

            return response()->json($education);
        }

        /*
         * Admin hanya bisa melihat education miliknya.
         */
        $education = Education::where('id_education', $id)
            ->where('id_user', $user->id_user)
            ->with('user')
            ->firstOrFail();

        return response()->json($education);
    }

    /**
     * UPDATE EDUCATION
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::guard('web')->user();

        /*
         * Cari education sesuai role.
         */
        if ($user->id_role == 2) {
            // Super Admin
            $education = Education::findOrFail($id);
        } else {
            // Admin
            $education = Education::where('id_education', $id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();
        }

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:50',
            'major'    => 'sometimes|required|string|max:50',
            'place'    => 'sometimes|required|string|max:50',
            'level'    => 'sometimes|required|string|max:20',
            'date_in'  => 'sometimes|required|date',
            'date_out' => 'nullable|date|after_or_equal:date_in',
            'gpa'      => 'nullable|numeric|min:0|max:9.99',
        ]);

        /*
         * Hanya Super Admin yang boleh memindahkan
         * education ke user lain.
         */
        if ($user->id_role == 2 && $request->has('id_user')) {
            $request->validate([
                'id_user' => 'required|exists:users,id_user',
            ]);

            $validated['id_user'] = $request->id_user;
        }

        $education->update($validated);

        return response()->json(
            $education->load('user')
        );
    }

    /**
     * DELETE EDUCATION
     */
    public function destroy(string $id)
    {
        $user = Auth::guard('web')->user();

        /*
         * Super Admin bisa menghapus semua.
         */
        if ($user->id_role == 2) {
            $education = Education::findOrFail($id);
        } else {
            /*
             * Admin hanya bisa menghapus miliknya.
             */
            $education = Education::where('id_education', $id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();
        }

        $education->delete();

        return response()->json(null, 204);
    }
}