<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    /**
     * Display a listing of the achievements (search, filter, sort, pagination).
     */
    public function index(Request $request)
    {
        $achievements = Achievement::with('category')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->category, fn ($q, $c) => $q->where('id_category', $c))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderBy('date', $request->sort === 'oldest' ? 'asc' : 'desc')
            ->paginate($request->get('per_page', 5));

        return response()->json([
            'success' => true,
            'data'    => $achievements->items(),
            'meta'    => [
                'current_page' => $achievements->currentPage(),
                'last_page'    => $achievements->lastPage(),
                'per_page'     => $achievements->perPage(),
                'total'        => $achievements->total(),
                'from'         => $achievements->firstItem(),
                'to'           => $achievements->lastItem(),
            ],
            'stats'   => [
                'total'          => Achievement::count(),
                'new_this_month' => Achievement::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
            ],
        ], 200);
    }

    /**
     * Display the specified achievement.
     */
    public function show($id)
    {
        $achievement = Achievement::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $achievement
        ], 200);
    }

    /**
     * Store a newly created achievement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'place'       => 'required|string|max:255',
            'date'        => 'required|date',
            'id_project'  => 'nullable|exists:projects,id_project',
            'id_category' => 'required|exists:categories,id_category',
            'description' => 'nullable|string|max:255',
            'type'        => 'required|in:Award,Training',
            'status'      => 'required|in:Verified,Completed',
            'logo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('achievements', 'public');
        }

        $validated['id_user'] = $request->user()->id_user;

        $achievement = Achievement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Achievement created successfully',
            'data'    => $achievement->load('category')
        ], 201);
    }

    /**
     * Update the specified achievement in storage.
     */
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'place'       => 'required|string|max:255',
            'date'        => 'required|date',
            'id_project'  => 'nullable|exists:projects,id_project',
            'id_category' => 'required|exists:categories,id_category',
            'description' => 'nullable|string|max:255',
            'type'        => 'required|in:Award,Training',
            'status'      => 'required|in:Verified,Completed',
            'logo'        => 'nullable|image|max:2048',
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
            'data'    => $achievement->load('category')
        ], 200);
    }

    /**
     * Remove the specified achievement from storage.
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
            'message' => 'Achievement deleted successfully'
        ], 200);
    }
}