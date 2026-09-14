<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // READ - semua Category
    public function index()
    {
        return response()->json(Category::all());
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    // READ - satu Category
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // UPDATE
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    // DELETE
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category berhasil dihapus'
        ]);
    }
}
