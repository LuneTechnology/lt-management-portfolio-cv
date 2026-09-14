<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EducationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\PositionTypeController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\ExperienceController;

Route::apiResource('roles', RoleController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('link', LinkController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('educations', EducationController::class);
Route::apiResource('works', WorkController::class);
Route::apiResource('position-types', PositionTypeController::class);
Route::apiResource('work-types', WorkTypeController::class);
Route::apiResource('experiences', ExperienceController::class);
