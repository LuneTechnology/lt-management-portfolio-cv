<?php

namespace App\Http\Controllers;

use App\Models\ExperienceStack;
use Illuminate\Http\Request;

class ExperienceStackController extends Controller
{
    public function index()
    {
        return response()->json(
            ExperienceStack::all()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_experience' => 'required|integer|exists:experiences,id_experience',
            'id_stack' => 'required|integer|exists:stack,id_stack',
        ]);

        $exists = ExperienceStack::where(
            'id_experience',
            $validated['id_experience']
        )
        ->where(
            'id_stack',
            $validated['id_stack']
        )
        ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Stack sudah terhubung dengan experience',
            ], 409);
        }

        $experienceStack = ExperienceStack::create($validated);

        return response()->json($experienceStack, 201);
    }

    public function destroy(
        int $id_experience,
        int $id_stack
    ) {
        $deleted = ExperienceStack::where(
            'id_experience',
            $id_experience
        )
        ->where(
            'id_stack',
            $id_stack
        )
        ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Relasi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Stack berhasil dilepas dari experience',
        ]);
    }
}