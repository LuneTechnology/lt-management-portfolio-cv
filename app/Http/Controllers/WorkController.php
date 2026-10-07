<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class WorkController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage work data.'
        );
    }

    public function index()
    {
        return response()->json(
            Work::with('workTag')
                ->orderBy('id_work', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'place' => 'required|string|max:50',
            'id_work_tag' => 'required|integer|exists:work_tags,id_work_tag',
        ]);

        $work = Work::create($validated);

        return response()->json(
            $work->load('workTag'),
            201
        );
    }

    public function show(string $id)
    {
        return response()->json(
            Work::with('workTag')->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();

        $work = Work::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'place' => 'required|string|max:50',
            'id_work_tag' => 'required|integer|exists:work_tags,id_work_tag',
        ]);

        $work->update($validated);

        return response()->json(
            $work->load('workTag')
        );
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        $work = Work::findOrFail($id);

        try {
            $work->delete();

            return response()->json([
                'message' => 'Work deleted successfully.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'This work cannot be deleted because it is still being used by Education or Experience.',
            ], 409);
        }
    }
}