<?php

namespace Modules\Catalog\Repositories\Category;

use Modules\Catalog\Repositories\Category\Contracts\CategoryRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Catalog\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
