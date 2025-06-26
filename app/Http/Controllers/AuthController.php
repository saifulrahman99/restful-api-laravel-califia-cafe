<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // REGISTER
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = $user->createToken('browser')->plainTextToken;

        return ApiResponse::commonResponse([
            'token' => $token,
            'user' => $user
        ], ResponseMessage::SUCCESS, 201);
    }

    // LOGIN

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->get('email'))->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $user->tokens()->delete();
        $token = $user->createToken('browser', ['*'], now()->addDays(7))->plainTextToken;

        return ApiResponse::commonResponse([
            'token' => $token,
            'user' => $user
        ]);
    }

    // LOGOUT
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return ApiResponse::commonResponse(['status' => 200], 'Logged out successfully.');
    }

    // GET USER DATA
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::commonResponse($request->user());
    }

    public function checkToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::commonResponse(null, "Token Tidak Ditemukan", 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return ApiResponse::commonResponse(null, "Token Tidak Valid", 401);
        }

        if ($accessToken->expires_at && Carbon::now()->greaterThan($accessToken->expires_at)) {
            return ApiResponse::commonResponse(null, "Token sudah kedaluwarsa", 401);
        }

        return ApiResponse::commonResponse(null, "token valid");
    }
}
