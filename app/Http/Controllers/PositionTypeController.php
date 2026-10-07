<?php

namespace App\Http\Controllers;

use App\Models\PositionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class PositionTypeController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::guard('web')->user();

        abort_unless(
            $user && $user->id_role == 2,
            403,
            'Only Super Admin can manage position types.'
        );
    }

    /**
     * Display all position types.
     */
    public function index()
    {
        return response()->json(
            PositionType::orderBy('name')->get()
        );
    }

    /**
     * Store new position type.
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:position_types,name',
            ],
        ]);

        $positionType = PositionType::create($validated);

        return response()->json(
            $positionType,
            201
        );
    }

    /**
     * Display specific position type.
     */
    public function show(string $id)
    {
        return response()->json(
            PositionType::findOrFail($id)
        );
    }

    /**
     * Update position type.
     */
    public function update(
        Request $request,
        string $id
    ) {
        $this->authorizeSuperAdmin();

        $positionType = PositionType::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:position_types,name,'
                    . $id
                    . ',id_position_type',
            ],
        ]);

        $positionType->update($validated);

        return response()->json(
            $positionType
        );
    }

    /**
     * Delete position type.
     */
    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        $positionType = PositionType::findOrFail($id);

        if ($positionType->experiences()->exists()) {
            return response()->json([
                'message' =>
                    'This Position Type is still being used by Experience data.',
            ], 409);
        }

        try {
            $positionType->delete();

            return response()->json([
                'message' =>
                    'Position Type deleted successfully.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' =>
                    'This Position Type cannot be deleted because it is still being used.',
            ], 409);
        }
    }
}