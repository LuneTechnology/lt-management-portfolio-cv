<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::apiResource('roles', RoleController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('link', LinkController::class);