<?php

namespace Modules\Catalog\Services\Product\Contracts;

use Modules\Catalog\Models\Product;
use Modules\Catalog\DTOs\Product\ProductData;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Product;

    public function create(ProductData $data): Product;

    public function update(int $id, ProductData $data): bool;

    public function delete(int $id): bool;
}
