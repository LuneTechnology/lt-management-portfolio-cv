<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    // READ - semua work type
    public function index()
    {
        return response()->json(WorkType::all(), 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $workType = WorkType::create($validated);

        return response()->json($workType, 201);
    }

    // READ - satu work type
    public function show(WorkType $workType)
    {
        return response()->json($workType, 200);
    }

    // UPDATE
    public function update(Request $request, WorkType $workType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
        ]);

        $workType->update($validated);

        return response()->json($workType, 200);
    }

    // DELETE
    public function destroy(WorkType $workType)
    {
        $workType->delete();

        return response()->json(['message' => 'Work type deleted successfully'], 200);
    }
}