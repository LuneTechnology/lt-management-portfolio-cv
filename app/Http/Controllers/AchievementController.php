<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    /**
     * Display achievements with search, filters, sorting, pagination, and stats.
     */
    public function index(Request $request)
    {
        $achievements = Achievement::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('id_category', $request->category))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->orderBy('date', $request->input('sort') === 'oldest' ? 'asc' : 'desc')
            ->paginate((int) $request->input('per_page', 5));

        return response()->json([
            'success' => true,
            'data' => $achievements->items(),
            'meta' => [
                'current_page' => $achievements->currentPage(),
                'last_page' => $achievements->lastPage(),
                'per_page' => $achievements->perPage(),
                'total' => $achievements->total(),
                'from' => $achievements->firstItem(),
                'to' => $achievements->lastItem(),
            ],
            'stats' => [
                'total' => Achievement::count(),
                'new_this_month' => Achievement::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
        ]);
    }

    /**
     * Display a single achievement.
     */
    public function show($id)
    {
        $achievement = Achievement::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $achievement,
        ]);
    }

    /**
     * Store a new achievement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'date' => 'required|date',
            // This project uses the singular table name `project`.
            'id_project' => 'nullable|exists:project,id_project',
            // This project uses the singular table name `category`.
            'id_category' => 'nullable|exists:category,id_category',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:Award,Training',
            'status' => 'required|in:Verified,Completed',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('achievements', 'public');
        }

        $validated['id_user'] = $request->user()->id_user;

        $achievement = Achievement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Achievement created successfully',
            'data' => $achievement->load('category'),
        ], 201);
    }

    /**
     * Update an existing achievement.
     */
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'date' => 'required|date',
            // This project uses the singular table name `project`.
            'id_project' => 'nullable|exists:project,id_project',
            // This project uses the singular table name `category`.
            'id_category' => 'nullable|exists:category,id_category',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:Award,Training',
            'status' => 'required|in:Verified,Completed',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($achievement->logo) {
                Storage::disk('public')->delete($achievement->logo);
            }

            $validated['logo'] = $request->file('logo')->store('achievements', 'public');
        }

        $achievement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Achievement updated successfully',
            'data' => $achievement->load('category'),
        ]);
    }

    /**
     * Delete an achievement.
     */
    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);

        if ($achievement->logo) {
            Storage::disk('public')->delete($achievement->logo);
        }

        $achievement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Achievement deleted successfully',
        ]);
    }
}
