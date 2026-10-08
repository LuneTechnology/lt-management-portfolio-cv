<?php

namespace App\Http\Controllers;

use App\Models\WorkTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class WorkTagController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage work tags.'
        );
    }

    public function index()
    {
        return response()->json(
            WorkTag::orderBy('id_work_tag', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:work_tags,name',
        ]);

        $workTag = WorkTag::create($validated);

        return response()->json($workTag, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            WorkTag::findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();

        $workTag = WorkTag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:work_tags,name,' . $id . ',id_work_tag',
        ]);

        $workTag->update($validated);

        return response()->json($workTag);
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        $workTag = WorkTag::findOrFail($id);

        try {
            $workTag->delete();

            return response()->json([
                'message' => 'Work tag deleted successfully.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'This work tag cannot be deleted because it is still being used by Work.',
            ], 409);
        }
    }
}