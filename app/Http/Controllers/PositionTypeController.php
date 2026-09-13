<?php

namespace App\Http\Controllers;

use App\Models\PositionType;
use Illuminate\Http\Request;

class PositionTypeController extends Controller
{
    // READ - semua position type
    public function index()
    {
        return response()->json(PositionType::all(), 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $positionType = PositionType::create($validated);

        return response()->json($positionType, 201);
    }

    // READ - satu position type
    public function show(PositionType $positionType)
    {
        return response()->json($positionType, 200);
    }

    // UPDATE
    public function update(Request $request, PositionType $positionType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
        ]);

        $positionType->update($validated);

        return response()->json($positionType, 200);
    }

    // DELETE
    public function destroy(PositionType $positionType)
    {
        $positionType->delete();

        return response()->json(['message' => 'Position type deleted successfully'], 200);
    }
}