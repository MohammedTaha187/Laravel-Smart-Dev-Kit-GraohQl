<?php

namespace Modules\Core\Services\Vendor;

use Modules\Core\Services\Vendor\Contracts\VendorServiceInterface;
use Modules\Core\Repositories\Vendor\Contracts\VendorRepositoryInterface;
use Modules\Core\Models\Vendor;
use Modules\Core\DTOs\Vendor\VendorData;
use EasyDev\Laravel\Services\FileService;
use Illuminate\Database\Eloquent\Collection;

class VendorService implements VendorServiceInterface
{
    public function __construct(
        private readonly VendorRepositoryInterface $repo,
        private readonly FileService $fileService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(string $id): ?Vendor
    {
        return $this->repo->find($id);
    }

    public function create(VendorData $data): Vendor
    {
        $payload = $data->toArray();
        if ($data->logo) {
            $payload['logo'] = $this->fileService->upload($data->logo, 'vendors');
        }

        return $this->repo->create($payload);
    }

    public function update(string $id, VendorData $data): bool
    {
        $payload = $data->toArray();
        if ($data->logo) {
            $old = $this->getById($id);
            if ($old && $old->logo) {
                $this->fileService->delete($old->logo);
            }
            $payload['logo'] = $this->fileService->upload($data->logo, 'vendors');
        }

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
