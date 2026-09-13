<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\PositionTypeController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\ExperienceController;

Route::apiResource('roles', RoleController::class);
Route::apiResource('works', WorkController::class);
Route::apiResource('position-types', PositionTypeController::class);
Route::apiResource('work-types', WorkTypeController::class);
Route::apiResource('experiences', ExperienceController::class);