<?php

namespace Modules\Catalog\Repositories\Product;

use Modules\Catalog\Repositories\Product\Contracts\ProductRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Catalog\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
