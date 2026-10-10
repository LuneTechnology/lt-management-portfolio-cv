<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    private function isSuperAdmin($user): bool
    {
        return (int) ($user?->id_role) === 2;
    }

    private function contributorQueryFor($user, $query): void
    {
        if (!$this->isSuperAdmin($user)) {
            $query->where('id_user', $user->id_user);
        }

        $query->with(['user', 'work', 'positionType', 'workType', 'tasks', 'stacks']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $projects = Project::query()
            ->with(['category', 'work', 'links', 'images', 'achievements'])
            ->with(['experiences' => function ($query) use ($user) {
                $this->contributorQueryFor($user, $query);
            }])
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'date_in' => ['required', 'date'],
            'date_out' => ['nullable', 'date', 'after_or_equal:date_in'],
            'id_category' => ['required', 'integer', 'exists:category,id_category'],
            'id_work' => ['required', 'integer', 'exists:works,id_work'],
        ]);

        $project = Project::create($validated);

        return response()->json(
            $project->load(['category', 'work', 'experiences.user', 'links', 'images', 'achievements']),
            201
        );
    }

    public function show(Request $request, Project $project)
    {
        $user = $request->user();
        $project->load(['category', 'work', 'links', 'images', 'achievements']);

        $experiences = $project->experiences();
        if (!$this->isSuperAdmin($user)) {
            $experiences->where('id_user', $user->id_user);
        }

        $project->setRelation(
            'experiences',
            $experiences->with(['user', 'work', 'positionType', 'workType', 'tasks', 'stacks'])->get()
        );

        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'date_in' => ['required', 'date'],
            'date_out' => ['nullable', 'date', 'after_or_equal:date_in'],
            'id_category' => ['required', 'integer', 'exists:category,id_category'],
            'id_work' => ['required', 'integer', 'exists:works,id_work'],
        ]);

        DB::transaction(function () use ($project, $validated) {
            $project->update($validated);

            // Work belongs to the Project; keep the legacy experiences.id_work in sync.
            $project->experiences()->update(['id_work' => $validated['id_work']]);
        });

        return response()->json(
            $project->fresh()->load(['category', 'work', 'experiences.user', 'links', 'images', 'achievements'])
        );
    }

    public function destroy(Project $project)
    {
        if ($project->experiences()->exists()) {
            return response()->json([
                'message' => 'Project tidak dapat dihapus karena masih memiliki contributor.',
            ], 422);
        }

        try {
            $project->delete();

            return response()->json(['message' => 'Project berhasil dihapus.']);
        } catch (QueryException $exception) {
            return response()->json([
                'message' => 'Project tidak dapat dihapus karena masih digunakan oleh data lain.',
            ], 422);
        }
    }
}
