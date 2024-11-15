<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);

        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // Retrieve the authenticated user
            $user = Auth::user();
            $role = $user->role;

            // Generate a Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return a JSON response with the token and user role
            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'role' => $role,
                'user' => $user,
                'message' => 'Login successful'
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);

    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Return a JSON response confirming the logout
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
