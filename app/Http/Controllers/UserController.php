<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"full_name", "email", "password"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="mobile", type="string", nullable=true, example="+1234567890"),
 *     @OA\Property(property="sex", type="string", enum={"male", "female", "other"}, nullable=true, example="male"),
 *     @OA\Property(property="age", type="integer", nullable=true, example=28),
 *     @OA\Property(property="role_id", type="integer", example=2),
 *     @OA\Property(property="institute_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="describtion", type="string", nullable=true, example="Frontend developer"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="List all users",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Register a new user",
     *     description="Creates a new user and assigns a role (default is User).",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name","email","password"},
     *             @OA\Property(property="full_name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="sex", type="string", enum={"male","female","other"}, example="male"),
     *             @OA\Property(property="age", type="integer", minimum=1, example=28),
     *             @OA\Property(property="password", type="string", minLength=6, example="secret123"),
     *             @OA\Property(property="role_id", type="integer", description="Optional, defaults to User", example=4),
     *             @OA\Property(property="institute_id", type="integer", description="Optional", example=1),
     *             @OA\Property(property="describtion", type="string", description="Bio or extra note", example="Frontend developer"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'mobile'       => 'nullable|string|max:20',
            'sex'          => 'nullable|in:male,female,other',
            'age'          => 'nullable|integer',
            'password'     => 'required|string|min:6',
            'role_id'      => 'nullable|exists:roles,id',
            'institute_id' => 'nullable|exists:institutes,id',
            'describtion'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleId = $request->role_id ?? 4; // Default to User

        $user = User::create([
            'full_name'    => $request->full_name,
            'email'        => $request->email,
            'mobile'       => $request->mobile,
            'sex'          => $request->sex,
            'age'          => $request->age,
            'password'     => Hash::make($request->password),
            'role_id'      => $roleId,
            'institute_id' => $request->institute_id,
            'describtion'  => $request->describtion,
            'is_active'    => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Get user by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="full_name", type="string", maxLength=255, example="John Updated"),
     *             @OA\Property(property="email", type="string", format="email", example="john_new@example.com"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="+987654321"),
     *             @OA\Property(property="sex", type="string", enum={"male","female","other"}, example="other"),
     *             @OA\Property(property="age", type="integer", minimum=1, example=30),
     *             @OA\Property(property="password", type="string", minLength=6, example="updated123"),
     *             @OA\Property(property="role_id", type="integer", description="Optional", example=2),
     *             @OA\Property(property="institute_id", type="integer", description="Optional", example=3),
     *             @OA\Property(property="describtion", type="string", description="Bio or extra note", example="React & Nextjs expert"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full_name'    => 'sometimes|required|string|max:255',
            'email'        => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password'     => 'sometimes|required|string|min:6',
            'role_id'      => 'sometimes|required|exists:roles,id',
            'institute_id' => 'nullable|exists:institutes,id',
            'mobile'       => 'nullable|string|max:20',
            'sex'          => 'nullable|in:male,female,other',
            'age'          => 'nullable|integer|min:1',
            'describtion'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'full_name',
            'email',
            'mobile',
            'sex',
            'age',
            'role_id',
            'institute_id',
            'describtion',
            'is_active'
        ]);

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
