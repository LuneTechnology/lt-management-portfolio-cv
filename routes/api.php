<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\PositionTypeController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StackController;
use App\Http\Controllers\ExperienceStackController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::apiResource('roles', RoleController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('link', LinkController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('educations', EducationController::class);
Route::apiResource('works', WorkController::class);
Route::apiResource('position-types', PositionTypeController::class);
Route::apiResource('work-types', WorkTypeController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('experience-stacks', ExperienceStackController::class);
Route::apiResource('tasks', TaskController::class);
Route::apiResource('stacks', StackController::class);
Route::apiResource('tags', TagController::class);


// Authentication routes
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});