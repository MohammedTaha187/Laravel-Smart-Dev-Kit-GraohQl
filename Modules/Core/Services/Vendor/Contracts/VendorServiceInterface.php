<?php

namespace Modules\Core\Services\Vendor\Contracts;

use Modules\Core\Models\Vendor;
use Modules\Core\DTOs\Vendor\VendorData;
use Illuminate\Database\Eloquent\Collection;

interface VendorServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Vendor;

    public function create(VendorData $data): Vendor;

    public function update(string $id, VendorData $data): bool;

    public function delete(string $id): bool;
}
