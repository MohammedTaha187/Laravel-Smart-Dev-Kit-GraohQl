<?php

namespace Modules\Core\Services\Category;

use Modules\Core\Services\Category\Contracts\CategoryServiceInterface;
use Modules\Core\Repositories\Category\Contracts\CategoryRepositoryInterface;
use Modules\Core\Models\Category;
use Modules\Core\DTOs\Category\CategoryData;

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

    public function getById(string $id): ?Category
    {
        return $this->repo->find($id);
    }

    public function create(CategoryData $data): Category
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(string $id, CategoryData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
