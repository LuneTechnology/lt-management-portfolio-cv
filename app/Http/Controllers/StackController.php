<?php

namespace App\Http\Controllers;

use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class StackController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage Stack.'
        );
    }

    public function index()
    {
        return response()->json(
            Stack::with('stackType')->get()
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_stack_type' => 'required|integer|exists:stack_types,id_stack_type',
        ]);

        $stack = Stack::create($validated);

        return response()->json(
            $stack->load('stackType'),
            201
        );
    }

    public function show(Stack $stack)
    {
        return response()->json(
            $stack->load('stackType')
        );
    }

    public function update(Request $request, Stack $stack)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_stack_type' => 'required|integer|exists:stack_types,id_stack_type',
        ]);

        $stack->update($validated);

        return response()->json(
            $stack->load('stackType')
        );
    }

    public function destroy(Stack $stack)
    {
        $this->authorizeSuperAdmin();

        try {
            $stack->delete();

            return response()->json([
                'message' => 'Stack berhasil dihapus',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Stack tidak dapat dihapus karena masih digunakan oleh Experience.',
            ], 409);
        }
    }
}