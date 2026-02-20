<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthController extends Controller
{
    public function __construct(private FirebaseAuth $firebase)
    {
    }

    public function firebaseLogin(Request $request): JsonResponse
    {
        $request->validate([
            'firebase_id_token' => 'required|string',
        ]);

        try {
            // Verify Firebase token
            $verifiedToken = $this->firebase->verifyIdToken($request->firebase_id_token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid Firebase token'], 401);
        }

        $firebaseUid = $verifiedToken->claims()->get('sub');
        $email = $verifiedToken->claims()->get('email');

        // Find teacher by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        // Update firebase_uid if not set
        if (!$user->firebase_uid) {
            $user->firebase_uid = $firebaseUid;
            $user->save();
        }

        // Issue Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
}