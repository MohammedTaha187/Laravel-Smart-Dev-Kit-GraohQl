<?php

namespace Modules\Core\Services\Product\Contracts;

use Modules\Core\Models\Product;
use Modules\Core\DTOs\Product\ProductData;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Product;

    public function create(ProductData $data): Product;

    public function update(string $id, ProductData $data): bool;

    public function delete(string $id): bool;
}
