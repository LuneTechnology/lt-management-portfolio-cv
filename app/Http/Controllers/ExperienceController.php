<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display all experiences.
     */
    public function index()
    {
        $experiences = Experience::with([
            'user',
            'work',
            'positionType',
            'workType',
            'project',
        ])
        ->orderByDesc('id_experience')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $experiences,
        ]);
    }

    /**
     * Store a new experience.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => [
                'required',
                'integer',
                'exists:users,id_user',
            ],

            'id_work' => [
                'required',
                'integer',
                'exists:works,id_work',
            ],

            'id_position_type' => [
                'required',
                'integer',
                'exists:position_types,id_position_type',
            ],

            'id_work_type' => [
                'required',
                'integer',
                'exists:work_types,id_work_type',
            ],

            'id_project' => [
                'required',
                'integer',
                'exists:projects,id_project',
            ],
        ]);

        $experience = Experience::create(
            $validated
        );

        // Return with all relations
        $experience->load([
            'user',
            'work',
            'positionType',
            'workType',
            'project',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Experience created successfully.',
            'data' => $experience,
        ], 201);
    }

    /**
     * Display a specific experience.
     */
    public function show($id)
    {
        $experience = Experience::with([
            'user',
            'work',
            'positionType',
            'workType',
            'project',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $experience,
        ]);
    }

    /**
     * Update an experience.
     */
    public function update(
        Request $request,
        $id
    ) {
        $experience =
            Experience::findOrFail($id);

        $validated = $request->validate([
            'id_user' => [
                'required',
                'integer',
                'exists:users,id_user',
            ],

            'id_work' => [
                'required',
                'integer',
                'exists:works,id_work',
            ],

            'id_position_type' => [
                'required',
                'integer',
                'exists:position_types,id_position_type',
            ],

            'id_work_type' => [
                'required',
                'integer',
                'exists:work_types,id_work_type',
            ],

            'id_project' => [
                'required',
                'integer',
                'exists:projects,id_project',
            ],
        ]);

        $experience->update(
            $validated
        );

        $experience->load([
            'user',
            'work',
            'positionType',
            'workType',
            'project',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Experience updated successfully.',
            'data' => $experience,
        ]);
    }

    /**
     * Delete an experience.
     */
    public function destroy($id)
    {
        $experience =
            Experience::findOrFail($id);

        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience deleted successfully.',
        ]);
    }
}