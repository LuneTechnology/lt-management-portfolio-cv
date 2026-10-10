<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Link::query();

        if ($request->filled('id_project')) {
            $query->where('id_project', $request->integer('id_project'));
        }

        return response()->json(
            $query->orderBy('sort_order')->orderBy('id_link')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_project' => ['required', 'integer', 'exists:project,id_project'],
            'name' => ['required', 'string', 'max:100'],
            'link' => ['required', 'url', 'max:2048'],
            'type' => ['required', 'in:live_demo,source_code,video,documentation,article,other'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:100000'],
        ]);

        $link = Link::create($validated);

        return response()->json($link, 201);
    }

    public function show(Link $link)
    {
        return response()->json($link);
    }

    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'id_project' => ['sometimes', 'required', 'integer', 'exists:project,id_project'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'link' => ['sometimes', 'required', 'url', 'max:2048'],
            'type' => ['sometimes', 'required', 'in:live_demo,source_code,video,documentation,article,other'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:100000'],
        ]);

        $link->update($validated);

        return response()->json($link->fresh());
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return response()->json(['message' => 'Link project berhasil dihapus.']);
    }
}
