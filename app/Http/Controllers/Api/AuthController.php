<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", format="int64", description="User ID"),
 *   @OA\Property(property="name", type="string", description="User's name"),
 *   @OA\Property(property="email", type="string", description="User's email"),
 *   @OA\Property(property="role", type="string", description="User's role")
 * )
 */

/**
 * @OA\Schema(
 *   schema="AuthResponse",
 *   type="object",
 *   @OA\Property(property="user", ref="#/components/schemas/User"),
 *   @OA\Property(property="access_token", type="string", description="Access token"),
 *   @OA\Property(property="token_type", type="string", description="Token type")
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", description="User's name"),
     *             @OA\Property(property="email", type="string", description="User's email"),
     *             @OA\Property(property="password", type="string", description="User's password"),
     *             @OA\Property(property="role", type="string", description="User's role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AuthRegister")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */

     /**
     * @OA\Schema(
     *   schema="AuthRegister",
     *   type="object",
     *   @OA\Property(property="user", ref="#/components/schemas/User"),
     *   @OA\Property(property="access_token", type="string", description="Access token"),
     *   @OA\Property(property="token_type", type="string", description="Token type")
     * )
     */
    #register function for users
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Admin,Librarian,Member',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'=> $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AuthLogin")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login details"
     *     )
     * )
     */

     /**
     * @OA\Schema(
     *   schema="AuthLogin",
     *   type="object",
     *   @OA\Property(property="email", type="string", description="User's email"),
     *   @OA\Property(property="password", type="string", description="User's password")
     * )
     */
    #login function for user
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'=> $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Logout a user",
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    #logout function for authenticated user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
