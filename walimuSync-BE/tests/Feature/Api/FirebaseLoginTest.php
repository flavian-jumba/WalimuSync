<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken;

uses(RefreshDatabase::class);

it('returns unauthorized when firebase token is invalid', function (): void {
    $firebaseAuth = Mockery::mock(FirebaseAuth::class);
    $firebaseAuth
        ->shouldReceive('verifyIdToken')
        ->once()
        ->with('invalid-token')
        ->andThrow(new RuntimeException('Invalid token'));

    app()->instance(FirebaseAuth::class, $firebaseAuth);

    $response = $this->postJson('/api/firebase-login', [
        'firebase_id_token' => 'invalid-token',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid Firebase token',
        ]);
});

it('logs in a known user with valid firebase token and issues sanctum token', function (): void {
    $user = User::factory()->create([
        'email' => 'teacher@example.com',
        'firebase_uid' => null,
    ]);

    $verifiedToken = Mockery::mock(UnencryptedToken::class);
    $verifiedToken
        ->shouldReceive('claims')
        ->andReturn(new DataSet([
            'sub' => 'firebase-user-123',
            'email' => 'teacher@example.com',
            'name' => 'Jane Teacher',
            'picture' => null,
        ], 'encoded-claims'));

    $firebaseAuth = Mockery::mock(FirebaseAuth::class);
    $firebaseAuth
        ->shouldReceive('verifyIdToken')
        ->once()
        ->with('valid-token')
        ->andReturn($verifiedToken);

    app()->instance(FirebaseAuth::class, $firebaseAuth);

    $response = $this->postJson('/api/firebase-login', [
        'firebase_id_token' => 'valid-token',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Login successful')
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.email', 'teacher@example.com');

    expect($response->json('token'))->toBeString()->not->toBe('');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'firebase_uid' => 'firebase-user-123',
    ]);

    expect($user->fresh()->tokens()->count())->toBe(1);
});

it('auto-registers a new teacher via google oauth when email not found', function (): void {
    $verifiedToken = Mockery::mock(UnencryptedToken::class);
    $verifiedToken
        ->shouldReceive('claims')
        ->andReturn(new DataSet([
            'sub' => 'google-uid-456',
            'email' => 'newteacher@school.co.ke',
            'name' => 'New Teacher',
            'picture' => 'https://lh3.googleusercontent.com/avatar.jpg',
        ], 'encoded-claims'));

    $firebaseAuth = Mockery::mock(FirebaseAuth::class);
    $firebaseAuth
        ->shouldReceive('verifyIdToken')
        ->once()
        ->with('google-oauth-token')
        ->andReturn($verifiedToken);

    app()->instance(FirebaseAuth::class, $firebaseAuth);

    $response = $this->postJson('/api/firebase-login', [
        'firebase_id_token' => 'google-oauth-token',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Account created')
        ->assertJsonPath('user.email', 'newteacher@school.co.ke')
        ->assertJsonPath('user.name', 'New Teacher');

    expect($response->json('token'))->toBeString()->not->toBe('');

    $this->assertDatabaseHas('users', [
        'email' => 'newteacher@school.co.ke',
        'firebase_uid' => 'google-uid-456',
        'avatar_url' => 'https://lh3.googleusercontent.com/avatar.jpg',
    ]);
});

it('returns 422 when firebase token has no email', function (): void {
    $verifiedToken = Mockery::mock(UnencryptedToken::class);
    $verifiedToken
        ->shouldReceive('claims')
        ->andReturn(new DataSet([
            'sub' => 'uid-no-email',
            'email' => null,
            'name' => 'No Email User',
            'picture' => null,
        ], 'encoded-claims'));

    $firebaseAuth = Mockery::mock(FirebaseAuth::class);
    $firebaseAuth
        ->shouldReceive('verifyIdToken')
        ->once()
        ->andReturn($verifiedToken);

    app()->instance(FirebaseAuth::class, $firebaseAuth);

    $this->postJson('/api/firebase-login', [
        'firebase_id_token' => 'token-without-email',
    ])->assertStatus(422)
        ->assertJsonPath('message', 'Email not available from provider');
});

it('updates avatar url when existing user logs in with different picture', function (): void {
    $user = User::factory()->create([
        'email' => 'existing@school.co.ke',
        'firebase_uid' => 'existing-uid',
        'avatar_url' => 'https://old-picture.jpg',
    ]);

    $verifiedToken = Mockery::mock(UnencryptedToken::class);
    $verifiedToken
        ->shouldReceive('claims')
        ->andReturn(new DataSet([
            'sub' => 'existing-uid',
            'email' => 'existing@school.co.ke',
            'name' => 'Existing Teacher',
            'picture' => 'https://new-picture.jpg',
        ], 'encoded-claims'));

    $firebaseAuth = Mockery::mock(FirebaseAuth::class);
    $firebaseAuth
        ->shouldReceive('verifyIdToken')
        ->once()
        ->andReturn($verifiedToken);

    app()->instance(FirebaseAuth::class, $firebaseAuth);

    $this->postJson('/api/firebase-login', [
        'firebase_id_token' => 'valid-refresh-token',
    ])->assertOk()
        ->assertJsonPath('message', 'Login successful');

    expect($user->fresh()->avatar_url)->toBe('https://new-picture.jpg');
});
