<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = User::with('roles')->find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = User::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return User::with('roles')->find($id);
    }

    public function all()
    {
        return User::with('roles')->get();
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function findByProvider(string $provider, string $socialId)
    {
        return User::where('social_provider', $provider)
            ->where('social_id', $socialId)
            ->first();
    }
}
