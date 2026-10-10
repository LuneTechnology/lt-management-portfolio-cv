<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceController extends Controller
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

    private function rules(bool $creating, bool $superAdmin): array
    {
        $rules = [
            'id_project' => ['required', 'integer', 'exists:project,id_project'],
            'id_position_type' => ['required', 'integer', 'exists:position_types,id_position_type'],
            'id_work_type' => ['required', 'integer', 'exists:work_types,id_work_type'],
            'tasks' => ['sometimes', 'array'],
            'tasks.*' => ['required', 'string', 'max:255'],
            'stack_ids' => ['sometimes', 'array'],
            'stack_ids.*' => ['required', 'integer', 'distinct', 'exists:stack,id_stack'],
        ];

        if ($superAdmin) {
            $rules['id_user'] = [$creating ? 'required' : 'sometimes', 'integer', 'exists:users,id_user'];
        }

        return $rules;
    }

    private function relations(): array
    {
        return ['user', 'work', 'positionType', 'workType', 'project.work', 'tasks', 'stacks.stackType'];
    }

    public function index(Request $request)
    {
        $query = Experience::with($this->relations())->orderByDesc('id_experience');

        if (!$this->isSuperAdmin($request->user())) {
            $query->where('id_user', $request->user()->id_user);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $superAdmin = $this->isSuperAdmin($user);
        $validated = $request->validate($this->rules(true, $superAdmin));
        $project = Project::findOrFail($validated['id_project']);

        if (!$project->id_work) {
            return response()->json([
                'message' => 'Project harus memiliki Work sebelum Contributor ditambahkan.',
            ], 422);
        }

        $experience = DB::transaction(function () use ($validated, $project, $user, $superAdmin, $request) {
            $experience = Experience::create([
                'id_user' => $superAdmin ? $validated['id_user'] : $user->id_user,
                'id_work' => $project->id_work,
                'id_position_type' => $validated['id_position_type'],
                'id_work_type' => $validated['id_work_type'],
                'id_project' => $project->id_project,
            ]);

            if ($request->exists('tasks')) {
                foreach ($validated['tasks'] ?? [] as $description) {
                    $experience->tasks()->create(['desc' => trim($description)]);
                }
            }

            if ($request->exists('stack_ids')) {
                $experience->stacks()->sync($validated['stack_ids'] ?? []);
            }

            return $experience;
        });

        return response()->json([
            'success' => true,
            'message' => 'Contributor berhasil ditambahkan.',
            'data' => $experience->load($this->relations()),
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $experience = $this->accessibleExperience($request, $id)->load($this->relations());
        return response()->json(['success' => true, 'data' => $experience]);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $superAdmin = $this->isSuperAdmin($user);
        $experience = $this->accessibleExperience($request, $id);
        $validated = $request->validate($this->rules(false, $superAdmin));
        $project = Project::findOrFail($validated['id_project']);

        if (!$project->id_work) {
            return response()->json([
                'message' => 'Project harus memiliki Work sebelum Contributor disimpan.',
            ], 422);
        }

        DB::transaction(function () use ($experience, $validated, $project, $superAdmin, $request) {
            $updates = [
                'id_work' => $project->id_work,
                'id_position_type' => $validated['id_position_type'],
                'id_work_type' => $validated['id_work_type'],
                'id_project' => $project->id_project,
            ];

            if ($superAdmin && array_key_exists('id_user', $validated)) {
                $updates['id_user'] = $validated['id_user'];
            }

            $experience->update($updates);

            if ($request->exists('tasks')) {
                $experience->tasks()->delete();
                foreach ($validated['tasks'] ?? [] as $description) {
                    $experience->tasks()->create(['desc' => trim($description)]);
                }
            }

            if ($request->exists('stack_ids')) {
                $experience->stacks()->sync($validated['stack_ids'] ?? []);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Contributor berhasil diperbarui.',
            'data' => $experience->fresh()->load($this->relations()),
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $experience = $this->accessibleExperience($request, $id);
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contributor berhasil dihapus.',
        ]);
    }
}
