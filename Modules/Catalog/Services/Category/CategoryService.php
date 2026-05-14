<?php

namespace Modules\Catalog\Services\Category;

use Modules\Catalog\Services\Category\Contracts\CategoryServiceInterface;
use Modules\Catalog\Repositories\Category\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Models\Category;
use Modules\Catalog\DTOs\Category\CategoryData;

use Illuminate\Database\Eloquent\Collection;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(int $id): ?Category
    {
        return $this->repo->find($id);
    }

    public function create(CategoryData $data): Category
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(int $id, CategoryData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
