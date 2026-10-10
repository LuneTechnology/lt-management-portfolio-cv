<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\DashboardStatsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ProjectImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\PositionTypeController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StackController;
use App\Http\Controllers\ExperienceStackController;
use App\Http\Controllers\StackTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkTagController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/dashboard/stats', [DashboardStatsController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Master data
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('works', WorkController::class);
    Route::apiResource('work-tags', WorkTagController::class);
    Route::apiResource('position-types', PositionTypeController::class);
    Route::apiResource('work-types', WorkTypeController::class);
    Route::apiResource('stack-types', StackTypeController::class);
    Route::apiResource('stacks', StackController::class);

    // Portfolio management
    Route::get('projects/{project}/images', [ProjectImageController::class, 'index']);
    Route::post('projects/{project}/images', [ProjectImageController::class, 'store']);
    Route::put('project-images/{projectImage}', [ProjectImageController::class, 'update']);
    Route::delete('project-images/{projectImage}', [ProjectImageController::class, 'destroy']);

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('link', LinkController::class);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('educations', EducationController::class);
    Route::apiResource('achievements', AchievementController::class);

    // Composite-key pivot routes; apiResource would generate incompatible single-key show/destroy routes.
    Route::get('experience-stacks', [ExperienceStackController::class, 'index']);
    Route::post('experience-stacks', [ExperienceStackController::class, 'store']);
    Route::get('experience-stacks/{id_experience}/{id_stack}', [ExperienceStackController::class, 'show']);
    Route::delete('experience-stacks/{id_experience}/{id_stack}', [ExperienceStackController::class, 'destroy']);

    Route::apiResource('users', UserController::class);
});
