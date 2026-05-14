<?php

namespace Modules\Core\Repositories\Tag;

use Modules\Core\Repositories\Tag\Contracts\TagRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Tag;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }
}
