<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated())->refresh();

        return $this->tokenResponse($this->guard()->login($user), $user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! $token = $this->guard()->attempt($request->validated())) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        return $this->tokenResponse($token, $this->guard()->user());
    }

    /** Exchanges a valid (or recently expired) token for a new one. */
    public function refresh(): JsonResponse
    {
        try {
            $token = $this->guard()->refresh();
        } catch (JWTException) {
            return response()->json(['message' => 'Token cannot be refreshed'], 401);
        }

        return $this->tokenResponse($token);
    }

    public function logout(): Response
    {
        $this->guard()->logout();

        return response()->noContent();
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    private function guard(): JWTGuard
    {
        return auth('api');
    }

    private function tokenResponse(string $token, ?User $user = null, int $status = 200): JsonResponse
    {
        return response()->json(array_filter([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $user ? new UserResource($user) : null,
        ]), $status);
    }
}
