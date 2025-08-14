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
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
*/

// ===============================
// Public Routes
// ===============================

// User Registration
Route::post('/register', [AuthController::class, 'register']);

// User Login
Route::post('/login', [AuthController::class, 'login']);

// ===============================
// Protected Routes
// ===============================

// Everything below requires authentication (Sanctum token)
// 'auth:sanctum'
Route::middleware([])->group(function () {
    // User Profile
    Route::get('/user', [UserController::class, 'profile']);

    // User Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Refresh Token
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Example: Get all users
    Route::get('/users', [UserController::class, 'index']);

    // Exam CRUD (resource routes)
    Route::apiResource('exams', \App\Http\Controllers\ExamController::class);

    // ExamResultController CRUD (resource routes)
    Route::apiResource('exam-results', \App\Http\Controllers\ExamResultController::class);

    // اضافه کردن route در قسمت protected routes
Route::apiResource('lessons', LessonApiController::class);


    // RolesController CRUD (resource routes)
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    // UserExams CRUD (resource routes)
    Route::apiResource('user-exams', \App\Http\Controllers\UserExamController::class);
    // InstituteExams CRUD (resource routes)
    Route::apiResource('institute-exams', \App\Http\Controllers\InstituteExamController::class);
});
// InstituteController CRUD (resource routes)
Route::apiResource('institutes', \App\Http\Controllers\InstituteController::class);
// ===============================
// Fallback Route
// ===============================

Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found.',
    ], 404);
});
