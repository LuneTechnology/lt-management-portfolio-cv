<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function isSuperAdmin($user): bool
    {
        return (int) ($user?->id_role) === 2;
    }

    private function authorizeExperience(Request $request, int|string $experienceId): Experience
    {
        $query = Experience::query();
        if (!$this->isSuperAdmin($request->user())) {
            $query->where('id_user', $request->user()->id_user);
        }
        return $query->findOrFail($experienceId);
    }

    private function authorizeTask(Request $request, Task $task): void
    {
        $this->authorizeExperience($request, $task->id_experience);
    }

    public function index(Request $request)
    {
        $query = Task::with('experience');
        if (!$this->isSuperAdmin($request->user())) {
            $query->whereHas('experience', fn ($experience) => $experience->where('id_user', $request->user()->id_user));
        }
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_experience' => ['required', 'integer', 'exists:experiences,id_experience'],
            'desc' => ['required', 'string', 'max:255'],
        ]);
        $this->authorizeExperience($request, $validated['id_experience']);
        $task = Task::create($validated);
        return response()->json($task->load('experience'), 201);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        return response()->json($task->load('experience'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $validated = $request->validate([
            'id_experience' => ['required', 'integer', 'exists:experiences,id_experience'],
            'desc' => ['required', 'string', 'max:255'],
        ]);
        $this->authorizeExperience($request, $validated['id_experience']);
        $task->update($validated);
        return response()->json($task->fresh()->load('experience'));
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->delete();
        return response()->json(['message' => 'Task berhasil dihapus.']);
    }
}
