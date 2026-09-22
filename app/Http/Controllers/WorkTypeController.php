<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    public function index()
    {
        $workTypes = WorkType::all();

        return response()->json([
            'success' => true,
            'data'    => $workTypes
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workType = WorkType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work Type created successfully',
            'data'    => $workType
        ], 201);
    }

    public function show(WorkType $workType)
    {
        return response()->json([
            'success' => true,
            'data'    => $workType
        ], 200);
    }

    public function update(Request $request, WorkType $workType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work Type updated successfully',
            'data'    => $workType
        ], 200);
    }

    public function destroy(WorkType $workType)
    {
        $workType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Work Type deleted successfully'
        ], 200);
    }
}