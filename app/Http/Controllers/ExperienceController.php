<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    // READ - semua experience beserta relasinya
    public function index()
    {
        $experiences = Experience::with([
            'user',
            'work',
            'positionType',
            'workType',
            'project'
        ])->get();

        return response()->json([
            'success' => true,
            'data'    => $experiences
        ], 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user'          => 'required|exists:users,id_user',
            'id_work'          => 'required|exists:works,id_work',
            'id_position_type' => 'required|exists:position_types,id_position_type',
            'id_work_type'     => 'required|exists:work_types,id_work_type',
            'id_project'       => 'required|exists:projects,id_project',
        ]);

        $experience = Experience::create($validated);
        $experience->load(['user', 'work', 'positionType', 'workType', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'Experience created successfully',
            'data'    => $experience
        ], 201);
    }

    // READ - satu experience
    public function show(Experience $experience)
    {
        $experience->load(['user', 'work', 'positionType', 'workType', 'project']);

        return response()->json([
            'success' => true,
            'data'    => $experience
        ], 200);
    }

    // UPDATE
    public function update(Request $request, Experience $experience)
    {
        $validated = $request->validate([
            'id_user'          => 'sometimes|required|exists:users,id_user',
            'id_work'          => 'sometimes|required|exists:works,id_work',
            'id_position_type' => 'sometimes|required|exists:position_types,id_position_type',
            'id_work_type'     => 'sometimes|required|exists:work_types,id_work_type',
            'id_project'       => 'sometimes|required|exists:projects,id_project',
        ]);

        $experience->update($validated);
        $experience->load(['user', 'work', 'positionType', 'workType', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'Experience updated successfully',
            'data'    => $experience
        ], 200);
    }

    // DELETE
    public function destroy(Experience $experience)
    {
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience deleted successfully'
        ], 200);
    }
}