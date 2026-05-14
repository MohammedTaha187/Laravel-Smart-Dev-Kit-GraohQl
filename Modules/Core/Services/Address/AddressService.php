<?php

namespace Modules\Core\Services\Address;

use Modules\Core\Services\Address\Contracts\AddressServiceInterface;
use Modules\Core\Repositories\Address\Contracts\AddressRepositoryInterface;
use Modules\Core\Models\Address;
use Modules\Core\DTOs\Address\AddressData;

use Illuminate\Database\Eloquent\Collection;

class AddressService implements AddressServiceInterface
{
    public function __construct(
        private readonly AddressRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(string $id): ?Address
    {
        return $this->repo->find($id);
    }

    public function create(AddressData $data): Address
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(string $id, AddressData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
