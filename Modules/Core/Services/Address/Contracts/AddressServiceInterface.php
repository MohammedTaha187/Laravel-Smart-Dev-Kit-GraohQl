<?php

namespace Modules\Core\Services\Address\Contracts;

use Modules\Core\Models\Address;
use Modules\Core\DTOs\Address\AddressData;
use Illuminate\Database\Eloquent\Collection;

interface AddressServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Address;

    public function create(AddressData $data): Address;

    public function update(string $id, AddressData $data): bool;

    public function delete(string $id): bool;
}
