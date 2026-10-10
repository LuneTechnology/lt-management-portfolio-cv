<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\ExperienceStack;
use Illuminate\Http\Request;

class ExperienceStackController extends Controller
{
    private function isSuperAdmin($user): bool
    {
        return (int) ($user?->id_role) === 2;
    }

    private function accessibleExperience(Request $request, int|string $id): Experience
    {
        $query = Experience::query();
        if (!$this->isSuperAdmin($request->user())) {
            $query->where('id_user', $request->user()->id_user);
        }
        return $query->findOrFail($id);
    }

    public function index(Request $request)
    {
        $query = ExperienceStack::with(['experience.project', 'stack']);
        if (!$this->isSuperAdmin($request->user())) {
            $query->whereHas('experience', fn ($experience) => $experience->where('id_user', $request->user()->id_user));
        }
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_experience' => ['required', 'integer', 'exists:experiences,id_experience'],
            'id_stack' => ['required', 'integer', 'exists:stack,id_stack'],
        ]);
        $this->accessibleExperience($request, $validated['id_experience']);

        $pivot = ExperienceStack::firstOrCreate($validated);
        return response()->json($pivot->load(['experience', 'stack']), 201);
    }

    public function show(Request $request, string $id_experience, string $id_stack)
    {
        $this->accessibleExperience($request, $id_experience);
        $pivot = ExperienceStack::where('id_experience', $id_experience)
            ->where('id_stack', $id_stack)
            ->firstOrFail();
        return response()->json($pivot->load(['experience', 'stack']));
    }

    public function destroy(Request $request, string $id_experience, string $id_stack)
    {
        $this->accessibleExperience($request, $id_experience);
        $deleted = ExperienceStack::where('id_experience', $id_experience)
            ->where('id_stack', $id_stack)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Experience Stack tidak ditemukan.'], 404);
        }

        return response()->json(['message' => 'Stack berhasil dihapus dari Experience.']);
    }
}
