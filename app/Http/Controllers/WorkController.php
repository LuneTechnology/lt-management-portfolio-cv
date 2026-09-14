<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    // READ - semua work
    public function index()
    {
        return response()->json(Work::all(), 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'place' => 'required|string|max:50',
        ]);

        $work = Work::create($validated);

        return response()->json($work, 201);
    }

    // READ - satu work
    public function show(Work $work)
    {
        return response()->json($work, 200);
    }

    // UPDATE
    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:50',
            'place' => 'sometimes|required|string|max:50',
        ]);

        $work->update($validated);

        return response()->json($work, 200);
    }

    // DELETE
    public function destroy(Work $work)
    {
        $work->delete();

        return response()->json(['message' => 'Work deleted successfully'], 200);
    }
}