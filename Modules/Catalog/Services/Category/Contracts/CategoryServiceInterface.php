<?php

namespace Modules\Catalog\Services\Category\Contracts;

use Modules\Catalog\Models\Category;
use Modules\Catalog\DTOs\Category\CategoryData;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Category;

    public function create(CategoryData $data): Category;

    public function update(int $id, CategoryData $data): bool;

    public function delete(int $id): bool;
}
