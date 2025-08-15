<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
 *     @OA\Property(property="role_id", type="integer", nullable=true, example=2),
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
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter by active status",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['role', 'institute']);
            
            // Filter by active status if provided
            if ($request->has('active')) {
                $isActive = $request->boolean('active');
                $query = $isActive ? $query->active() : $query->inactive();
            }
            
            $perPage = $request->get('per_page', 15);
            $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100
            
            $users = $query->paginate($perPage);
            
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت لیست کاربران',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Creates a new user with the provided information.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name","email","password"},
     *             @OA\Property(property="full_name", type="string", maxLength=255, example="احمد محمدی"),
     *             @OA\Property(property="email", type="string", format="email", example="ahmad@example.com"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="09123456789"),
     *             @OA\Property(property="sex", type="string", enum={"male","female","other"}, example="male"),
     *             @OA\Property(property="age", type="integer", minimum=1, maximum=120, example=28),
     *             @OA\Property(property="password", type="string", minLength=8, example="password123"),
     *             @OA\Property(property="role_id", type="integer", description="Optional", example=2),
     *             @OA\Property(property="institute_id", type="integer", description="Optional", example=1),
     *             @OA\Property(property="describtion", type="string", description="توضیحات اضافی", example="توسعه‌دهنده فرانت‌اند"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="کاربر با موفقیت ایجاد شد"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="خطا در اعتبارسنجی داده‌ها"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name'    => 'required|string|max:255|min:2',
                'email'        => 'required|string|email|max:255|unique:users,email',
                'mobile'       => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
                'sex'          => 'nullable|in:male,female,other',
                'age'          => 'nullable|integer|min:1|max:120',
                'password'     => 'required|string|min:8',
                'role_id'      => 'nullable|integer|exists:roles,id',
                'institute_id' => 'nullable|integer|exists:institutes,id',
                'describtion'  => 'nullable|string|max:1000',
                'is_active'    => 'nullable|boolean',
            ], [
                'full_name.required' => 'نام و نام خانوادگی الزامی است',
                'full_name.min' => 'نام و نام خانوادگی باید حداقل 2 کاراکتر باشد',
                'email.required' => 'ایمیل الزامی است',
                'email.email' => 'فرمت ایمیل صحیح نیست',
                'email.unique' => 'این ایمیل قبلاً ثبت شده است',
                'password.required' => 'رمز عبور الزامی است',
                'password.min' => 'رمز عبور باید حداقل 8 کاراکتر باشد',
                'mobile.regex' => 'فرمت شماره موبایل صحیح نیست',
                'age.min' => 'سن باید حداقل 1 سال باشد',
                'age.max' => 'سن باید حداکثر 120 سال باشد',
                'role_id.exists' => 'نقش انتخابی وجود ندارد',
                'institute_id.exists' => 'موسسه انتخابی وجود ندارد',
                'describtion.max' => 'توضیحات نباید بیش از 1000 کاراکتر باشد',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'full_name'    => $request->full_name,
                'email'        => $request->email,
                'mobile'       => $request->mobile,
                'sex'          => $request->sex,
                'age'          => $request->age,
                'password'     => Hash::make($request->password),
                'role_id'      => $request->role_id,
                'institute_id' => $request->institute_id,
                'describtion'  => $request->describtion,
                'is_active'    => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]);

            // Load relationships for response
            $user->load(['role', 'institute']);

            return response()->json([
                'message' => 'کاربر با موفقیت ایجاد شد',
                'user' => $user
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در ایجاد کاربر',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = User::with(['role', 'institute'])->findOrFail($id);
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'کاربر یافت نشد'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت اطلاعات کاربر',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *             @OA\Property(property="full_name", type="string", maxLength=255, example="احمد محمدی"),
     *             @OA\Property(property="email", type="string", format="email", example="ahmad_new@example.com"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="09987654321"),
     *             @OA\Property(property="sex", type="string", enum={"male","female","other"}, example="other"),
     *             @OA\Property(property="age", type="integer", minimum=1, maximum=120, example=30),
     *             @OA\Property(property="password", type="string", minLength=8, example="newpassword123"),
     *             @OA\Property(property="role_id", type="integer", description="Optional", example=2),
     *             @OA\Property(property="institute_id", type="integer", description="Optional", example=3),
     *             @OA\Property(property="describtion", type="string", description="توضیحات", example="متخصص React و Next.js"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="کاربر با موفقیت به‌روزرسانی شد"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'full_name'    => 'sometimes|required|string|max:255|min:2',
                'email'        => [
                    'sometimes',
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'mobile'       => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
                'sex'          => 'nullable|in:male,female,other',
                'age'          => 'nullable|integer|min:1|max:120',
                'password'     => 'sometimes|required|string|min:8|confirmed',
                'role_id'      => 'nullable|integer|exists:roles,id',
                'institute_id' => 'nullable|integer|exists:institutes,id',
                'describtion'  => 'nullable|string|max:1000',
                'is_active'    => 'nullable|boolean',
            ], [
                'full_name.required' => 'نام و نام خانوادگی الزامی است',
                'full_name.min' => 'نام و نام خانوادگی باید حداقل 2 کاراکتر باشد',
                'email.required' => 'ایمیل الزامی است',
                'email.email' => 'فرمت ایمیل صحیح نیست',
                'email.unique' => 'این ایمیل قبلاً ثبت شده است',
                'password.required' => 'رمز عبور الزامی است',
                'password.min' => 'رمز عبور باید حداقل 8 کاراکتر باشد',
                'password.confirmed' => 'تأیید رمز عبور مطابقت ندارد',
                'mobile.regex' => 'فرمت شماره موبایل صحیح نیست',
                'age.min' => 'سن باید حداقل 1 سال باشد',
                'age.max' => 'سن باید حداکثر 120 سال باشد',
                'role_id.exists' => 'نقش انتخابی وجود ندارد',
                'institute_id.exists' => 'موسسه انتخابی وجود ندارد',
                'describtion.max' => 'توضیحات نباید بیش از 1000 کاراکتر باشد',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
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

            // Hash password if provided
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->load(['role', 'institute']);

            return response()->json([
                'message' => 'کاربر با موفقیت به‌روزرسانی شد',
                'user' => $user
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'کاربر یافت نشد'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در به‌روزرسانی کاربر',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="کاربر با موفقیت حذف شد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json([
                'message' => 'کاربر با موفقیت حذف شد'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'کاربر یافت نشد'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در حذف کاربر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}/toggle-status",
     *     tags={"Users"},
     *     summary="Toggle user active status",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="وضعیت کاربر تغییر کرد"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => !$user->is_active]);
            $user->load(['role', 'institute']);
            
            return response()->json([
                'message' => 'وضعیت کاربر تغییر کرد',
                'user' => $user
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'کاربر یافت نشد'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تغییر وضعیت کاربر',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
