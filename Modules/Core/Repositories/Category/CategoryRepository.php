<?php

namespace Modules\Core\Repositories\Category;

use Modules\Core\Repositories\Category\Contracts\CategoryRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
