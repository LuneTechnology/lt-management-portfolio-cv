<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(
            Tag::with('stacks')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tag,name',
        ]);

        $tag = Tag::create($validated);

        return response()->json($tag, 201);
    }

    public function show(Tag $tag)
    {
        return response()->json(
            $tag->load('stacks')
        );
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tag,name,' . $tag->id_tag . ',id_tag',
        ]);

        $tag->update($validated);

        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'Tag berhasil dihapus',
        ]);
    }
}