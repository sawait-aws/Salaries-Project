<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    // Login method
    public function login(Request $request)
    {
        // Validate request data
        $request->validate([
            'user_id' => 'required|numeric',  // Assuming user_id is numeric
            'password' => 'required|string',
        ]);

        // Find the user by user_id (assuming you're using user_id as the unique identifier)
        $user = User::where('user_id', $request->user_id)->first();

        // If user does not exist or password is incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create the Sanctum token
        $token = $user->createToken('authToken')->plainTextToken;

        // Return success response with token
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    // Logout method
    public function logout(Request $request)
    {
        // Revoke the token for the current user
        $request->user()->currentAccessToken()->delete();

        // Return a response indicating successful logout
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
