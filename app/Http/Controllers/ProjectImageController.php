<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectImageController extends Controller
{
    public function index(Project $project)
    {
        return response()->json(
            $project->images()->orderBy('sort_order')->orderBy('id_project_image')->get()
        );
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:100000'],
        ]);

        $path = $request->file('image')->store('project-images/' . $project->id_project, 'public');
        $isFirstImage = !$project->images()->exists();
        $makePrimary = (bool) ($validated['is_primary'] ?? false) || $isFirstImage;

        try {
            $image = DB::transaction(function () use ($project, $validated, $path, $makePrimary) {
                if ($makePrimary) {
                    $project->images()->update(['is_primary' => false]);
                }

                return $project->images()->create([
                    'path' => $path,
                    'alt_text' => $validated['alt_text'] ?? null,
                    'is_primary' => $makePrimary,
                    'sort_order' => $validated['sort_order'] ?? ((int) $project->images()->max('sort_order') + 1),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return response()->json($image, 201);
    }

    public function update(Request $request, ProjectImage $projectImage)
    {
        $validated = $request->validate([
            'alt_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:100000'],
        ]);

        DB::transaction(function () use ($projectImage, $validated) {
            if (($validated['is_primary'] ?? false) === true) {
                $projectImage->project->images()->where('id_project_image', '!=', $projectImage->getKey())->update(['is_primary' => false]);
            }

            $projectImage->update($validated);
        });

        return response()->json($projectImage->fresh());
    }

    public function destroy(ProjectImage $projectImage)
    {
        $wasPrimary = $projectImage->is_primary;
        $project = $projectImage->project;
        $path = $projectImage->path;

        DB::transaction(fn () => $projectImage->delete());
        Storage::disk('public')->delete($path);

        if ($wasPrimary) {
            $nextImage = $project->images()->orderBy('sort_order')->orderBy('id_project_image')->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Gambar project berhasil dihapus.']);
    }
}
