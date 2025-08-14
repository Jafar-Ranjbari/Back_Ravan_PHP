<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Institute",
 *     required={"name", "username", "password"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Green Academy"),
 *     @OA\Property(property="address", type="string", nullable=true, example="123 Main St"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://example.com/logo.png"),
 *     @OA\Property(property="start_date", type="string", format="date", nullable=true, example="2025-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", nullable=true, example="2026-01-01"),
 *     @OA\Property(property="mobile", type="string", nullable=true, example="09112223344"),
 *     @OA\Property(property="username", type="string", example="greenacademy"),
 *     @OA\Property(property="password", type="string", example="hashedpw"),
 *     @OA\Property(property="describtion", type="string", nullable=true, example="Another note or meta for internal use"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */
class InstituteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/institutes",
     *     tags={"Institute"},
     *     summary="List all institutes",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Institute"))
     *     )
     * )
     */
    public function index()
    {
        return Institute::all();
    }

    /**
     * @OA\Post(
     *     path="/api/institutes",
     *     tags={"Institute"},
     *     summary="Create a new institute",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","username","password"},
     *             @OA\Property(property="name", type="string", example="Green Academy"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="logo_url", type="string", example="https://example.com/logo.png"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2026-01-01"),
     *             @OA\Property(property="mobile", type="string", example="09112223344"),
     *             @OA\Property(property="username", type="string", example="greenacademy"),
     *             @OA\Property(property="password", type="string", example="secret123"),
     *             @OA\Property(property="describtion", type="string", example="Institute notes"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Institute created",
     *         @OA\JsonContent(ref="#/components/schemas/Institute")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'address'     => 'nullable|string',
            'logo_url'    => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'mobile'      => 'nullable|string',
            'username'    => 'required|string|unique:institutes,username',
            'password'    => 'required|string|min:6',
            'describtion' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);
        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }
        $validated['password'] = bcrypt($validated['password']);
        $institute = Institute::create($validated);

        return response()->json($institute, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/institutes/{id}",
     *     tags={"Institute"},
     *     summary="Get an institute by id",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Institute")
     *     ),
     *     @OA\Response(response=404, description="Institute not found")
     * )
     */
    public function show($id)
    {
        $institute = Institute::find($id);
        if (!$institute) {
            return response()->json(['message' => 'Institute not found'], 404);
        }
        return $institute;
    }

    /**
     * @OA\Put(
     *     path="/api/institutes/{id}",
     *     tags={"Institute"},
     *     summary="Update an institute",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="logo_url", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="mobile", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="describtion", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Institute updated",
     *         @OA\JsonContent(ref="#/components/schemas/Institute")
     *     ),
     *     @OA\Response(response=404, description="Institute not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $institute = Institute::find($id);
        if (!$institute) {
            return response()->json(['message' => 'Institute not found'], 404);
        }
        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'address'     => 'nullable|string',
            'logo_url'    => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'mobile'      => 'nullable|string',
            'password'    => 'nullable|string|min:6',
            'describtion' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }
        $institute->update($validated);
        return response()->json($institute);
    }

    /**
     * @OA\Delete(
     *     path="/api/institutes/{id}",
     *     tags={"Institute"},
     *     summary="Delete an institute",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Institute deleted"),
     *     @OA\Response(response=404, description="Institute not found")
     * )
     */
    public function destroy($id)
    {
        $institute = Institute::find($id);
        if (!$institute) {
            return response()->json(['message' => 'Institute not found'], 404);
        }
        $institute->delete();
        return response()->json(null, 204);
    }
}
