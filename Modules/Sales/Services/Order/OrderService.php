<?php

namespace Modules\Sales\Services\Order;

use Modules\Sales\Services\Order\Contracts\OrderServiceInterface;
use Modules\Sales\Repositories\Order\Contracts\OrderRepositoryInterface;
use Modules\Sales\Models\Order;
use Modules\Sales\DTOs\Order\OrderData;

use Illuminate\Database\Eloquent\Collection;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function getById(int $id): ?Order
    {
        return $this->repo->find($id);
    }

    public function create(OrderData $data): Order
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(int $id, OrderData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
