<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     *READ SEMUA EDUCATION
     */
    public function index()
    {
        $educations = Education::all();

        return response()->json($educations);
    }

    /**
     * CREATED
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:50',
            'major'      => 'required|string|max:50',
            'place'      => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'gpa'        => 'nullable|numeric|min:0|max:9.99',
            'id_user'    => 'required|exists:users,id_user',
        ]);

        $education = Education::create($validated);

        return response()->json($education, 201);
    }

    /**
     * READ SATU EDUCATION
     */
    public function show(string $id)
    {
        $education = Education::findOrFail($id);

        return response()->json($education);
    }

    /**
     *UPDATE
     */
    public function update(Request $request, string $id)
    {
        $education = Education::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'sometimes|required|string|max:50',
            'major'      => 'sometimes|required|string|max:50',
            'place'      => 'sometimes|required|string|max:50',
            'start_date' => 'sometimes|required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'gpa'        => 'nullable|numeric|min:0|max:9.99',
            'id_user'    => 'sometimes|required|exists:users,id_user',
        ]);

        $education->update($validated);

        return response()->json($education);
    }

    /**
     * DELETE
     */
    public function destroy(string $id)
    {
        $education = Education::findOrFail($id);
        $education->delete();

        return response()->json(null, 204);
    }
}