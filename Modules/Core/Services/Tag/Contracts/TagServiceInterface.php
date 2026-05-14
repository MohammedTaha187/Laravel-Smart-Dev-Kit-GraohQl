<?php

namespace Modules\Core\Services\Tag\Contracts;

use Modules\Core\Models\Tag;
use Modules\Core\DTOs\Tag\TagData;
use Illuminate\Database\Eloquent\Collection;

interface TagServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Tag;

    public function create(TagData $data): Tag;

    public function update(string $id, TagData $data): bool;

    public function delete(string $id): bool;
}
