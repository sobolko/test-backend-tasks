<?php

use Illuminate\Support\Facades\Route;

// Аутентификация Sanctum
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');

// CRUD задачи и статистика (только для аутентифицированных)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);
    Route::get('tasks-stats', [\App\Http\Controllers\TaskController::class, 'stats']);
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->middleware('role:admin');
    Route::get('/external-data', [\App\Http\Controllers\ExternalController::class, 'getCatFact']);
});