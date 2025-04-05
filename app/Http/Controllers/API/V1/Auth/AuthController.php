<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Auth;

use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse|Response
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request): JsonResponse|Response
    {
        if (!Auth::attempt($request->toArray())) {
            return response([
                'error' => __('errors.the_provided_credentials_are_not_correct'),
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => __('errors.authentication_failed'),
            ], 401);
        }

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            /** @var PersonalAccessToken $token */
            $token = $user->currentAccessToken();
            $token->delete();
        }

        return response()->json();
    }
}
