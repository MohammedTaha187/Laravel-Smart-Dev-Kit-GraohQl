<?php

namespace Modules\Core\Services\Tag;

use Modules\Core\Services\Tag\Contracts\TagServiceInterface;
use Modules\Core\Repositories\Tag\Contracts\TagRepositoryInterface;
use Modules\Core\Models\Tag;
use Modules\Core\DTOs\Tag\TagData;

use Illuminate\Database\Eloquent\Collection;

class TagService implements TagServiceInterface
{
    public function __construct(
        private readonly TagRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(string $id): ?Tag
    {
        return $this->repo->find($id);
    }

    public function create(TagData $data): Tag
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(string $id, TagData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
