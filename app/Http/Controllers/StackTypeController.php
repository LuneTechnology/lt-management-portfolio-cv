<?php

namespace App\Http\Controllers;

use App\Models\StackType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class StackTypeController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage Stack Type.'
        );
    }

    public function index()
    {
        return response()->json(
            StackType::all()
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stack_types,name',
        ]);

        $stackType = StackType::create($validated);

        return response()->json($stackType, 201);
    }

    public function show(StackType $stackType)
    {
        return response()->json($stackType);
    }

    public function update(Request $request, StackType $stackType)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stack_types,name,' .
                $stackType->id_stack_type . ',id_stack_type',
        ]);

        $stackType->update($validated);

        return response()->json($stackType);
    }

    public function destroy(StackType $stackType)
    {
        $this->authorizeSuperAdmin();

        try {
            $stackType->delete();

            return response()->json([
                'message' => 'Stack Type berhasil dihapus',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Stack Type tidak dapat dihapus karena masih digunakan oleh Stack.',
            ], 409);
        }
    }
}