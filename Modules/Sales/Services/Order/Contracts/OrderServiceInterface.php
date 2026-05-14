<?php

namespace Modules\Sales\Services\Order\Contracts;

use Modules\Sales\Models\Order;
use Modules\Sales\DTOs\Order\OrderData;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Order;

    public function create(OrderData $data): Order;

    public function update(int $id, OrderData $data): bool;

    public function delete(int $id): bool;
}
