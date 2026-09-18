<?php

namespace App\Http\Controllers;

use App\Models\Stack;
use Illuminate\Http\Request;

class StackController extends Controller
{
    public function index()
    {
        return response()->json(
            Stack::with('tag')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_tag' => 'required|integer|exists:tag,id_tag',
        ]);

        $stack = Stack::create($validated);

        return response()->json(
            $stack->load('tag'),
            201
        );
    }

    public function show(Stack $stack)
    {
        return response()->json(
            $stack->load(['tag', 'experiences'])
        );
    }

    public function update(Request $request, Stack $stack)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_tag' => 'required|integer|exists:tag,id_tag',
        ]);

        $stack->update($validated);

        return response()->json(
            $stack->load('tag')
        );
    }

    public function destroy(Stack $stack)
    {
        $stack->delete();

        return response()->json([
            'message' => 'Stack berhasil dihapus',
        ]);
    }
}