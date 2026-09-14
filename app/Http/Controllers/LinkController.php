<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Link::all());
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'nullable|string|max:50',
            'link'   => 'nullable|string|max:50',
        ]);

        $link = Link::create($validated);

        return response()->json($link, 201);
    }

    // READ - satu link
    public function show(Link $link)
    {
        return response()->json($link);
    }

    // UPDATE
    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'name'      => 'nullable|string|max:50',
            'link'   => 'nullable|string|max:50',
        ]);

        $link->update($validated);

        return response()->json($link);
    }

    // DELETE
    public function destroy(Link $link)
    {
        $link->delete();

        return response()->json([
            'message' => 'Link berhasil dihapus'
        ]);
    }
}
