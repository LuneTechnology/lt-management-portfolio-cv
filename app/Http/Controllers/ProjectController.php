<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Project::all());
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:50',
            'date_in'   => 'required|date',
            'date_out'  => 'nullable|date|after_equal|date_in',
        ]);

        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    // READ - satu project
    public function show(Project $project)
    {
        return response()->json($project);
    }

    // UPDATE
    public function update(Request $request, project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'date_in'   => 'required|date',
            'date_out'  => 'nullable|date|after_equal|date_in',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    // DELETE
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus'
        ]);
    }
}
