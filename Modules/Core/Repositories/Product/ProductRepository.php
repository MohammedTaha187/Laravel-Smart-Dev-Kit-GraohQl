<?php

namespace Modules\Core\Repositories\Product;

use Modules\Core\Repositories\Product\Contracts\ProductRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
