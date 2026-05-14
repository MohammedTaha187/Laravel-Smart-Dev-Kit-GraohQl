<?php

namespace Modules\Catalog\Services\Product;

use Modules\Catalog\Services\Product\Contracts\ProductServiceInterface;
use Modules\Catalog\Repositories\Product\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Models\Product;
use Modules\Catalog\DTOs\Product\ProductData;

use Illuminate\Database\Eloquent\Collection;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(int $id): ?Product
    {
        return $this->repo->find($id);
    }

    public function create(ProductData $data): Product
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(int $id, ProductData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
