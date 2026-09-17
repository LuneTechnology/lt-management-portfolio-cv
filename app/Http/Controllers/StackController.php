<?php

namespace App\Http\Controllers;

use App\Models\Stack;
use Illuminate\Http\Request;

class StackController extends Controller
{
    public function index()
    {
        return response()->json(
            Stack::with('experiences')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tag' => 'required|string|max:50',
        ]);

        $stack = Stack::create($validated);

        return response()->json($stack, 201);
    }

    public function show(Stack $stack)
    {
        return response()->json(
            $stack->load('experiences')
        );
    }

    public function update(Request $request, Stack $stack)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tag' => 'required|string|max:50',
        ]);

        $stack->update($validated);

        return response()->json($stack);
    }

    public function destroy(Stack $stack)
    {
        $stack->delete();

        return response()->json([
            'message' => 'Stack berhasil dihapus',
        ]);
    }
}