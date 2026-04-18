<?php

namespace App\Services\Auth;

use App\Events\UserRegistered;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'male',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'language_preference' => $data['language_preference'] ?? 'ar',
        ]);

        $user->assignRole('patient');

        event(new UserRegistered($user));

        $token = auth('api')->login($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function login(array $data): array
    {
        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if (! $token = auth('api')->attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout(User $user): void
    {
        auth('api')->logout();
    }

    public function refresh(User $user): array
    {
        $token = auth('api')->refresh();

        return [
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['new_password']),
        ]);
    }

    public function forgotPassword(array $data): void
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if (! $user) {
            throw new \Exception('User not found');
        }

        // Generate a 6-digit numeric token
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store/Update token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Send Email
        Mail::to($user->email)->send(new PasswordResetMail($token));
    }

    public function resetPassword(array $data): void
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->first();

        if (! $resetRecord) {
            throw new \Exception('Invalid or expired token.');
        }

        // Check if token is older than 60 minutes
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
            throw new \Exception('Token has expired.');
        }

        // Verify token
        if (! Hash::check($data['token'], $resetRecord->token)) {
            throw new \Exception('Invalid token.');
        }

        // Update password
        $user = $this->userRepository->findByEmail($data['email']);
        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['password']),
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
    }

    public function updateProfile(User $user, array $data): void
    {
        $this->userRepository->update($user->id, $data);
    }
}
