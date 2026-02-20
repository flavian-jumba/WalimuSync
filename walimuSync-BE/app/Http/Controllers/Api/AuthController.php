<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthController extends Controller
{
    public function __construct(private FirebaseAuth $firebase) {}

    public function firebaseLogin(Request $request): JsonResponse
    {
        $request->validate([
            'firebase_id_token' => 'required|string',
        ]);

        try {
            $verifiedToken = $this->firebase->verifyIdToken($request->firebase_id_token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid Firebase token'], 401);
        }

        $firebaseUid = $verifiedToken->claims()->get('sub');
        $email = $verifiedToken->claims()->get('email');
        $name = $verifiedToken->claims()->get('name') ?? '';
        $picture = $verifiedToken->claims()->get('picture');

        if (! $email) {
            return response()->json(['message' => 'Email not available from provider'], 422);
        }

        // Find or create teacher by email (Google OAuth auto-registration)
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name ?: Str::before($email, '@'),
                'email' => $email,
                'firebase_uid' => $firebaseUid,
                'password' => bcrypt(Str::random(32)),
                'avatar_url' => $picture,
            ]);
        } else {
            // Update firebase_uid and profile info if changed
            $updates = [];

            if (! $user->firebase_uid) {
                $updates['firebase_uid'] = $firebaseUid;
            }

            if ($picture && $user->avatar_url !== $picture) {
                $updates['avatar_url'] = $picture;
            }

            if ($name && ! $user->name) {
                $updates['name'] = $name;
            }

            if (! empty($updates)) {
                $user->update($updates);
            }
        }

        // Revoke old tokens and issue a fresh one
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => $user->wasRecentlyCreated ? 'Account created' : 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
