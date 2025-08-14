<?php

namespace App\Http\Controllers;

use App\Models\UserExam;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="UserExam",
 *     required={"user_id", "exam_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=12),
 *     @OA\Property(property="exam_id", type="integer", example=3),
 *     @OA\Property(property="started_at", type="string", format="date-time", example="2025-08-06T10:00:00Z"),
 *     @OA\Property(property="finished_at", type="string", format="date-time", example="2025-08-06T11:30:00Z"),
 *     @OA\Property(property="total_time_seconds", type="integer", example=5400),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="answers", type="string", example="Answers in JSON format"),
 *     @OA\Property(property="describtion", type="string", example="First attempt"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class UserExamController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-exams",
     *     tags={"UserExams"},
     *     summary="List all user exams",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user exams",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserExam"))
     *     )
     * )
     */
    public function index()
    {
        return UserExam::all();
    }

    /**
     * @OA\Post(
     *     path="/api/user-exams",
     *     tags={"UserExams"},
     *     summary="Create a user exam",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "exam_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="exam_id", type="integer", example=1),
     *             @OA\Property(property="started_at", type="string", format="date-time", example="2025-08-06T10:00:00Z"),
     *             @OA\Property(property="finished_at", type="string", format="date-time", example="2025-08-06T11:30:00Z"),
     *             @OA\Property(property="total_time_seconds", type="integer", example=4500),
     *             @OA\Property(property="status", type="string", example="started"),
     *             @OA\Property(property="answers", type="string", example="Answers in JSON format"),
     *             @OA\Property(property="describtion", type="string", example="Initial attempt"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User exam created",
     *         @OA\JsonContent(ref="#/components/schemas/UserExam")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'exam_id'            => 'required|exists:exams,id',
            'started_at'         => 'nullable',
            'finished_at'        => 'nullable',
            'total_time_seconds' => 'nullable|integer',
            'status'             => 'nullable',
            'answers'            => 'nullable',
            'describtion'        => 'nullable',
            'is_active'          => 'nullable|boolean'
        ]);

        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }

        $userExam = UserExam::create($validated);

        return response()->json($userExam, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-exams/{id}",
     *     tags={"UserExams"},
     *     summary="Show a user exam",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User exam found",
     *         @OA\JsonContent(ref="#/components/schemas/UserExam")
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $userExam = UserExam::find($id);

        if (!$userExam) {
            return response()->json(['message' => 'UserExam not found'], 404);
        }

        return response()->json($userExam);
    }

    /**
     * @OA\Put(
     *     path="/api/user-exams/{id}",
     *     tags={"UserExams"},
     *     summary="Update a user exam",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="started_at", type="string", format="date-time"),
     *             @OA\Property(property="finished_at", type="string", format="date-time"),
     *             @OA\Property(property="total_time_seconds", type="integer"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="answers", type="string", example="Answers in JSON format"),
     *             @OA\Property(property="describtion", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User exam updated",
     *         @OA\JsonContent(ref="#/components/schemas/UserExam")
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $userExam = UserExam::find($id);

        if (!$userExam) {
            return response()->json(['message' => 'UserExam not found'], 404);
        }

        $validated = $request->validate([
            'started_at'         => 'nullable|date',
            'finished_at'        => 'nullable|date',
            'total_time_seconds' => 'nullable|integer',
            'status'             => 'nullable|string',
            'answers'            => 'nullable|string',
            'describtion'        => 'nullable|string',
            'is_active'          => 'nullable|boolean'
        ]);

        $userExam->update($validated);

        return response()->json($userExam);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-exams/{id}",
     *     tags={"UserExams"},
     *     summary="Delete a user exam",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User exam deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="UserExam deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy($id)
    {
        $userExam = UserExam::find($id);

        if (!$userExam) {
            return response()->json(['message' => 'UserExam not found'], 404);
        }

        $userExam->delete();

        return response()->json(['message' => 'UserExam deleted successfully']);
    }
}
