<?php

namespace App\Http\Controllers;

use App\Models\PositionType;
use Illuminate\Http\Request;

class PositionTypeController extends Controller
{
    public function index()
    {
        $positionTypes = PositionType::all();

        return response()->json([
            'success' => true,
            'data'    => $positionTypes
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $positionType = PositionType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Position Type created successfully',
            'data'    => $positionType
        ], 201);
    }

    public function show(PositionType $positionType)
    {
        return response()->json([
            'success' => true,
            'data'    => $positionType
        ], 200);
    }

    public function update(Request $request, PositionType $positionType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $positionType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Position Type updated successfully',
            'data'    => $positionType
        ], 200);
    }

    public function destroy(PositionType $positionType)
    {
        $positionType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Position Type deleted successfully'
        ], 200);
    }
}