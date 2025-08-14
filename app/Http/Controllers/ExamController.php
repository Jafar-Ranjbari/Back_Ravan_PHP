<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Exam",
 *     required={"title", "question_count", "duration_minutes"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Mathematics Entrance Test"),
 *     @OA\Property(property="question_count", type="integer", example=50),
 *     @OA\Property(property="price", type="number", format="float", nullable=true, example=99.5),
 *     @OA\Property(property="link", type="string", nullable=true, example="https://example.com/exam/1"),
 *     @OA\Property(property="duration_minutes", type="integer", example=120),
 *     @OA\Property(property="discount_percent", type="number", nullable=true, example=15),
 *     @OA\Property(property="quiz_type", type="string", nullable=true, example="multiple-choice"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Annual entrance exam"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/q.jpg"),
 *     @OA\Property(property="describtion", type="string", nullable=true, example="Extra exam note"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ExamController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/exams",
     *     tags={"Exams"},
     *     summary="List all exams",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Exam"))
     *     )
     * )
     */
    public function index()
    {
        return Exam::all();
    }

    /**
     * @OA\Post(
     *     path="/api/exams",
     *     tags={"Exams"},
     *     summary="Create an exam",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "question_count", "duration_minutes"},
     *             @OA\Property(property="title", type="string", example="New Final Exam"),
     *             @OA\Property(property="question_count", type="integer", example=60),
     *             @OA\Property(property="price", type="number", format="float", example=49.99),
     *             @OA\Property(property="link", type="string", nullable=true, example="https://example.com/exam/page"),
     *             @OA\Property(property="duration_minutes", type="integer", example=50),
     *             @OA\Property(property="discount_percent", type="number", nullable=true, example=5),
     *             @OA\Property(property="quiz_type", type="string", nullable=true, example="single-choice"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Science summer assessment"),
     *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/exam.jpg"),
     *             @OA\Property(property="describtion", type="string", nullable=true, example="For eligible students only"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Exam created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Exam")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string',
            'question_count'   => 'required|integer',
            'price'            => 'nullable|numeric',
            'link'             => 'nullable|string',
            'duration_minutes' => 'required|integer',
            'discount_percent' => 'nullable|numeric',
            'quiz_type'        => 'nullable|string',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|string',
            'describtion'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);
        // Default is_active to true if not sent
        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }
        $exam = Exam::create($validated);
        return response()->json($exam, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/exams/{id}",
     *     tags={"Exams"},
     *     summary="Get an exam by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200, description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Exam")
     *     ),
     *     @OA\Response(response=404, description="Exam not found")
     * )
     */
    public function show($id)
    {
        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }
        return $exam;
    }

    /**
     * @OA\Put(
     *     path="/api/exams/{id}",
     *     tags={"Exams"},
     *     summary="Update an exam",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Exam"),
     *             @OA\Property(property="question_count", type="integer", example=100),
     *             @OA\Property(property="price", type="number", format="float", example=70.50),
     *             @OA\Property(property="link", type="string", example="https://example.com/new-link"),
     *             @OA\Property(property="duration_minutes", type="integer", example=80),
     *             @OA\Property(property="discount_percent", type="number", example=0),
     *             @OA\Property(property="quiz_type", type="string", example="essay"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="image_url", type="string", example="https://example.com/updated.jpg"),
     *             @OA\Property(property="describtion", type="string", example="Some staff update note"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200, description="Exam updated",
     *         @OA\JsonContent(ref="#/components/schemas/Exam")
     *     ),
     *     @OA\Response(response=404, description="Exam not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }

        $validated = $request->validate([
            'title'            => 'sometimes|required|string',
            'question_count'   => 'sometimes|required|integer',
            'price'            => 'nullable|numeric',
            'link'             => 'nullable|string',
            'duration_minutes' => 'sometimes|required|integer',
            'discount_percent' => 'nullable|numeric',
            'quiz_type'        => 'nullable|string',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|string',
            'describtion'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);

        $exam->update($validated);

        return response()->json($exam);
    }

    /**
     * @OA\Delete(
     *     path="/api/exams/{id}",
     *     tags={"Exams"},
     *     summary="Delete an exam",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Exam deleted"),
     *     @OA\Response(response=404, description="Exam not found")
     * )
     */
    public function destroy($id)
    {
        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }
        $exam->delete();
        return response()->json(null, 204);
    }
}
