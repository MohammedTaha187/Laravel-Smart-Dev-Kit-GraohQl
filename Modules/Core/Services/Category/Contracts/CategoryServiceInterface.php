<?php

namespace Modules\Core\Services\Category\Contracts;

use Modules\Core\Models\Category;
use Modules\Core\DTOs\Category\CategoryData;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Category;

    public function create(CategoryData $data): Category;

    public function update(string $id, CategoryData $data): bool;

    public function delete(string $id): bool;
}
