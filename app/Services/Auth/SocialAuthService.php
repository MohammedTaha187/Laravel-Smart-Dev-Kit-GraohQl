<?php

namespace App\Services\Auth;

use App\Events\UserRegistered;
use App\Repositories\User\Contracts\UserRepositoryInterface;

class SocialAuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    /**
     * Handle social login (Google, Facebook, Apple, etc.)
     * This acts as both Register and Login.
     */
    public function handleSocialLogin(array $data): array
    {
        $user = $this->userRepository->findByProvider($data['social_provider'], $data['social_id']);

        if (! $user) {
            $user = $this->userRepository->findByEmail($data['email']);

            if ($user) {
                $this->userRepository->update($user->id, [
                    'social_provider' => $data['social_provider'],
                    'social_id' => $data['social_id'],
                ]);
            } else {
                $user = $this->userRepository->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => null,
                    'avatar' => $data['avatar'] ?? null,
                    'social_provider' => $data['social_provider'],
                    'social_id' => $data['social_id'],
                    'gender' => $data['gender'] ?? 'male',
                    'language_preference' => $data['language_preference'] ?? 'ar',
                ]);

                $user->assignRole('customer');

                event(new UserRegistered($user));
            }
        }

        $token = auth('api')->login($user);

        return ['user' => $user, 'token' => $token];
    }
}
