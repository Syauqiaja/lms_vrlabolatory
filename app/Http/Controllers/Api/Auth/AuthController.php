<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam name string required The full name of the user. Example: John Doe
     * @bodyParam email string required The email address of the user. Must be unique. Example: johndoe@example.com
     * @bodyParam password string required The password of the user. Minimum 6 characters. Example: secret123
     * @bodyParam password_confirmation string required Must match the password. Example: secret123
     *
     * @response 201 {
     *   "status": true,
     *   "message": "User registered successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "johndoe@example.com"
     *   },
     *   "token": "1|pCkTqQj..."
     * }
     */
    public function register(Request $request)
    {
       try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'status'  => false,
                'message' => $firstError,
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'data'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login user and get token.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam email string required The user's email. Example: johndoe@example.com
     * @bodyParam password string required The user's password. Example: secret123
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Login successful",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "johndoe@example.com"
     *   },
     *   "token": "1|pCkTqQj..."
     * }
     * @response 401 {
     *   "status": false,
     *   "message": "Invalid login credentials"
     * }
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'status'  => false,
                'message' => $firstError,
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'data'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Successfully logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get authenticated user profile.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "johndoe@example.com"
     *   }
     * }
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $request->user(),
        ]);
    }
}
