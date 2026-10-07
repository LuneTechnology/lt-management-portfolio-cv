<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkTypeController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage work type data.'
        );
    }

    public function index()
    {
        return response()->json(
            WorkType::orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:work_types,name',
            ],
        ]);

        $workType = WorkType::create($validated);

        return response()->json($workType, 201);
    }

    public function show($id)
    {
        $workType = WorkType::findOrFail($id);

        return response()->json($workType);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $workType = WorkType::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('work_types', 'name')
                    ->ignore($workType->id_work_type, 'id_work_type'),
            ],
        ]);

        $workType->update($validated);

        return response()->json($workType);
    }

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        $workType = WorkType::findOrFail($id);

        if ($workType->experiences()->exists()) {
            return response()->json([
                'message' => 'Work Type tidak dapat dihapus karena masih digunakan oleh Experience.',
            ], 409);
        }

        try {
            $workType->delete();

            return response()->json([
                'message' => 'Work Type berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Work Type tidak dapat dihapus karena masih digunakan.',
            ], 409);
        }
    }
}