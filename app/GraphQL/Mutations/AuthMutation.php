<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Services\Api\V1\Auth\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthMutation
{
    public function __construct(protected AuthService $authService) {}

    /**
     * Handle user registration.
     */
    public function register(mixed $_, array $args)
    {
        return $this->authService->register($args);
    }

    /**
     * Handle user login.
     */
    public function login(mixed $_, array $args)
    {
        try {
            return $this->authService->login($args);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(mixed $_, array $args)
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();
        $this->authService->logout($user);

        return [
            'message' => 'Logged out successfully',
        ];
    }

    /**
     * Handle token refresh.
     */
    public function refresh(mixed $_, array $args)
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();
        return $this->authService->refresh($user);
    }

    /**
     * Handle profile update.
     */
    public function updateProfile(mixed $_, array $args)
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();
        $this->authService->updateProfile($user, $args);

        return $user->fresh();
    }
}
