<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(
            Task::with('experience')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_experience' => 'required|integer|exists:experiences,id_experience',
            'name' => 'required|string|max:50',
        ]);

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json(
            $task->load('experience')
        );
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'id_experience' => 'required|integer|exists:experiences,id_experience',
            'name' => 'required|string|max:50',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task berhasil dihapus',
        ]);
    }
}