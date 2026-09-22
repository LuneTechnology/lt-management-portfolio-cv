<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::all();

        return response()->json([
            'success' => true,
            'data'    => $works
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'place' => 'nullable|string|max:255',
        ]);

        $work = Work::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work created successfully',
            'data'    => $work
        ], 201);
    }

    public function show(Work $work)
    {
        return response()->json([
            'success' => true,
            'data'    => $work
        ], 200);
    }

    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'place' => 'nullable|string|max:255',
        ]);

        $work->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work updated successfully',
            'data'    => $work
        ], 200);
    }

    public function destroy(Work $work)
    {
        $work->delete();

        return response()->json([
            'success' => true,
            'message' => 'Work deleted successfully'
        ], 200);
    }
}