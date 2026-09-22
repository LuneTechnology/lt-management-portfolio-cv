<?php

namespace App\Http\Controllers;

use App\Models\ExperienceStack;
use Illuminate\Http\Request;

class ExperienceStackController extends Controller
{
    public function index()
    {
        return response()->json(
            ExperienceStack::with([
                'experience.project',
                'stack',
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_experience' => 'required|integer|exists:experiences,id_experience',
            'id_stack' => 'required|integer|exists:stack,id_stack',
        ]);

        $experienceStack = ExperienceStack::create($validated);

        return response()->json(
            $experienceStack->load([
                'experience',
                'stack',
            ]),
            201
        );
    }

    public function show($id_experience, $id_stack)
    {
        $experienceStack = ExperienceStack::where('id_experience', $id_experience)
            ->where('id_stack', $id_stack)
            ->firstOrFail();

        return response()->json(
            $experienceStack->load([
                'experience',
                'stack',
            ])
        );
    }

    public function destroy($id_experience, $id_stack)
    {
        $deleted = ExperienceStack::where('id_experience', $id_experience)
            ->where('id_stack', $id_stack)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Experience Stack tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Stack berhasil dihapus dari Experience',
        ]);
    }
}