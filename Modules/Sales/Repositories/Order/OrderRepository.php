<?php

namespace Modules\Sales\Repositories\Order;

use Modules\Sales\Repositories\Order\Contracts\OrderRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Sales\Models\Order;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
