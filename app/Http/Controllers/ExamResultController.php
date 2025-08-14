<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="ExamResult",
 *     required={"user_id", "exam_id", "result_html"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=12),
 *     @OA\Property(property="exam_id", type="integer", example=9),
 *     @OA\Property(property="result_html", type="string", example="<p>Score: 8/10</p>"),
 *     @OA\Property(property="descriptionShort", type="string", nullable=true, example="Passed"),
 *     @OA\Property(property="descriptionLong", type="string", nullable=true, example="Excellent performance"),
 *     @OA\Property(property="expert_comment", type="string", nullable=true, example="Keep up the good work!"),
 *     @OA\Property(property="describtion", type="string", nullable=true, example="Internal note"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ExamResultController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/exam-results",
     *     tags={"ExamResults"},
     *     summary="List all exam results",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ExamResult"))
     *     )
     * )
     */
    public function index()
    {
        return ExamResult::all();
    }

    /**
     * @OA\Post(
     *     path="/api/exam-results",
     *     tags={"ExamResults"},
     *     summary="Create an exam result",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "exam_id", "result_html"},
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="exam_id", type="integer", example=1),
     *             @OA\Property(property="result_html", type="string", example="<p>Score: 10/10</p>"),
     *             @OA\Property(property="descriptionShort", type="string", example="Passed"),
     *             @OA\Property(property="descriptionLong", type="string", example="Excellent result"),
     *             @OA\Property(property="expert_comment", type="string", example="Great performance!"),
     *             @OA\Property(property="describtion", type="string", example="Staff feedback or meta"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Exam result created",
     *         @OA\JsonContent(ref="#/components/schemas/ExamResult")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'exam_id'          => 'required|exists:exams,id',
            'result_html'      => 'required|string',
            'descriptionShort' => 'nullable|string',
            'descriptionLong'  => 'nullable|string',
            'expert_comment'   => 'nullable|string',
            'describtion'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);
        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }
        $result = ExamResult::create($validated);
        return response()->json($result, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/{id}",
     *     tags={"ExamResults"},
     *     summary="Get an exam result by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExamResult")
     *     ),
     *     @OA\Response(response=404, description="Exam result not found")
     * )
     */
    public function show($id)
    {
        $result = ExamResult::find($id);
        if (!$result) {
            return response()->json(['message' => 'Exam result not found'], 404);
        }
        return $result;
    }

    /**
     * @OA\Put(
     *     path="/api/exam-results/{id}",
     *     tags={"ExamResults"},
     *     summary="Update an exam result",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="result_html", type="string", example="<p>Updated Score: 10/10</p>"),
     *             @OA\Property(property="descriptionShort", type="string", example="Retaken and Passed"),
     *             @OA\Property(property="descriptionLong", type="string", example="Improved performance"),
     *             @OA\Property(property="expert_comment", type="string", example="Much better!"),
     *             @OA\Property(property="describtion", type="string", example="Admin change note"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exam result updated",
     *         @OA\JsonContent(ref="#/components/schemas/ExamResult")
     *     ),
     *     @OA\Response(response=404, description="Exam result not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $result = ExamResult::find($id);
        if (!$result) {
            return response()->json(['message' => 'Exam result not found'], 404);
        }

        $validated = $request->validate([
            'result_html'      => 'sometimes|string',
            'descriptionShort' => 'nullable|string',
            'descriptionLong'  => 'nullable|string',
            'expert_comment'   => 'nullable|string',
            'describtion'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);
        $result->update($validated);

        return response()->json($result);
    }

    /**
     * @OA\Delete(
     *     path="/api/exam-results/{id}",
     *     tags={"ExamResults"},
     *     summary="Delete an exam result",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Exam result deleted"),
     *     @OA\Response(response=404, description="Exam result not found")
     * )
     */
    public function destroy($id)
    {
        $result = ExamResult::find($id);
        if (!$result) {
            return response()->json(['message' => 'Exam result not found'], 404);
        }
        $result->delete();
        return response()->json(null, 204);
    }
}
