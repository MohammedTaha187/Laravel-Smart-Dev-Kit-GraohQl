<?php

namespace App\Repositories\User\Contracts;

interface UserRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function find(string $id);

    public function all();

    public function findByEmail(string $email);

    public function findByProvider(string $provider, string $socialId);
}
