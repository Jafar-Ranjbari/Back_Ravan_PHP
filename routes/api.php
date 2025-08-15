<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\LessonApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===============================
// Public Routes
// ===============================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ===============================
// API Routes (No Auth Required for Testing)
// ===============================

Route::middleware(['api'])->group(function () {
    // User routes
    Route::get('/user', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    
    // Users CRUD
    Route::apiResource('users', UserController::class);
    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    
    // Other resources
    Route::apiResource('lessons', LessonApiController::class);
    Route::apiResource('exams', \App\Http\Controllers\ExamController::class);
    Route::apiResource('exam-results', \App\Http\Controllers\ExamResultController::class);
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('user-exams', \App\Http\Controllers\UserExamController::class);
    Route::apiResource('institute-exams', \App\Http\Controllers\InstituteExamController::class);
    Route::apiResource('institutes', \App\Http\Controllers\InstituteController::class);
});

// Fallback
Route::fallback(function () {
    return response()->json(['message' => 'Endpoint not found.'], 404);
});
