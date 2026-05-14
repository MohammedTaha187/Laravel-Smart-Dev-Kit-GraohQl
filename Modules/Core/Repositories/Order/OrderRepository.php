<?php

namespace Modules\Core\Repositories\Order;

use Modules\Core\Repositories\Order\Contracts\OrderRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Order;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
