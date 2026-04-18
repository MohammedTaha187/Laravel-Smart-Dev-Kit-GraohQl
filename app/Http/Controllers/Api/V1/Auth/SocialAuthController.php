<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Services\Api\V1\Auth\SocialAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    public function __construct(protected SocialAuthService $socialAuthService) {}

    public function handleSocialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'social_provider' => 'required|string',
            'social_id' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'avatar' => 'nullable|string',
        ]);

        $result = $this->socialAuthService->handleSocialLogin($request->all());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }
}
