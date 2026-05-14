<?php

namespace Modules\Core\Services\Order\Contracts;

use Modules\Core\Models\Order;
use Modules\Core\DTOs\Order\OrderData;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    public function getAll(): Collection;

    public function getById(string $id): ?Order;

    public function create(OrderData $data): Order;

    public function update(string $id, OrderData $data): bool;

    public function delete(string $id): bool;
}
