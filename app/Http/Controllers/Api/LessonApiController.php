<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LessonApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/lessons
     */
    public function index(): JsonResponse
    {
        try {
            $lessons = Lesson::all();
            
            return response()->json([
                'success' => true,
                'message' => 'لیست دروس با موفقیت دریافت شد',
                'data' => $lessons
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت لیست دروس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/lessons
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $lesson = Lesson::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'درس با موفقیت ایجاد شد',
                'data' => $lesson
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در اعتبارسنجی داده‌ها',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد درس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/lessons/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'جزئیات درس با موفقیت دریافت شد',
                'data' => $lesson
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'درس مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت جزئیات درس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/lessons/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($id);
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $lesson->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'درس با موفقیت به‌روزرسانی شد',
                'data' => $lesson->fresh()
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'درس مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در اعتبارسنجی داده‌ها',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی درس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/lessons/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($id);
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'درس با موفقیت حذف شد'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'درس مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف درس',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
