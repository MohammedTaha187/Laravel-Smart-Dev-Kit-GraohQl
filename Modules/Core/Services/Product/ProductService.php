<?php

namespace Modules\Core\Services\Product;

use Modules\Core\Services\Product\Contracts\ProductServiceInterface;
use Modules\Core\Repositories\Product\Contracts\ProductRepositoryInterface;
use Modules\Core\Models\Product;
use Modules\Core\DTOs\Product\ProductData;

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

    public function getById(string $id): ?Product
    {
        return $this->repo->find($id);
    }

    public function create(ProductData $data): Product
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(string $id, ProductData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
