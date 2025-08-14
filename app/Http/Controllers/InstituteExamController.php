<?php

namespace App\Http\Controllers;

use App\Models\InstituteExam;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="InstituteExam",
 *     required={"institute_id", "exam_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="institute_id", type="integer", example=10),
 *     @OA\Property(property="exam_id", type="integer", example=3),
 *     @OA\Property(property="assigned_at", type="string", format="date-time", example="2025-08-06T12:00:00Z"),
 *     @OA\Property(property="describtion", type="string", example="Math 101 assignment"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class InstituteExamController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/institute-exams",
     *     tags={"InstituteExams"},
     *     summary="List all institute-exam assignments",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of InstituteExam",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/InstituteExam"))
     *     )
     * )
     */
    public function index()
    {
        return InstituteExam::all();
    }

    /**
     * @OA\Post(
     *     path="/api/institute-exams",
     *     tags={"InstituteExams"},
     *     summary="Create a new institute-exam assignment",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"institute_id", "exam_id"},
     *             @OA\Property(property="institute_id", type="integer", example=1),
     *             @OA\Property(property="exam_id", type="integer", example=2),
     *             @OA\Property(property="assigned_at", type="string", format="date-time", example="2025-08-06T12:00:00Z"),
     *             @OA\Property(property="describtion", type="string", example="For Summer 2025"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="InstituteExam created",
     *         @OA\JsonContent(ref="#/components/schemas/InstituteExam")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'exam_id'      => 'required|exists:exams,id',
            'assigned_at'  => 'nullable|date',
            'describtion'  => 'nullable|string',
            'is_active'    => 'nullable|boolean'
        ]);
        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }
        $instituteExam = InstituteExam::create($validated);

        return response()->json($instituteExam, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/institute-exams/{id}",
     *     tags={"InstituteExams"},
     *     summary="Get one institute-exam assignment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="InstituteExam found",
     *         @OA\JsonContent(ref="#/components/schemas/InstituteExam")
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = InstituteExam::find($id);
        if (!$item) return response()->json(['message' => 'InstituteExam not found'], 404);
        return response()->json($item);
    }

    /**
     * @OA\Put(
     *     path="/api/institute-exams/{id}",
     *     tags={"InstituteExams"},
     *     summary="Update an institute-exam assignment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="assigned_at", type="string", format="date-time"),
     *             @OA\Property(property="describtion", type="string"),
     *             @OA\Property(property="is_active", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="InstituteExam updated",
     *         @OA\JsonContent(ref="#/components/schemas/InstituteExam")
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = InstituteExam::find($id);
        if (!$item) return response()->json(['message' => 'InstituteExam not found'], 404);

        $validated = $request->validate([
            'assigned_at' => 'nullable|date',
            'describtion' => 'nullable|string',
            'is_active'   => 'nullable|boolean'
        ]);
        $item->update($validated);

        return response()->json($item);
    }

    /**
     * @OA\Delete(
     *     path="/api/institute-exams/{id}",
     *     tags={"InstituteExams"},
     *     summary="Delete an institute-exam assignment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="InstituteExam deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="InstituteExam deleted")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy($id)
    {
        $item = InstituteExam::find($id);
        if (!$item) return response()->json(['message' => 'InstituteExam not found'], 404);
        $item->delete();
        return response()->json(['message' => 'InstituteExam deleted']);
    }
}
