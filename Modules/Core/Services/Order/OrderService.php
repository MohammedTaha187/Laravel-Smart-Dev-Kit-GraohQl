<?php

namespace Modules\Core\Services\Order;

use Modules\Core\Services\Order\Contracts\OrderServiceInterface;
use Modules\Core\Repositories\Order\Contracts\OrderRepositoryInterface;
use Modules\Core\Models\Order;
use Modules\Core\DTOs\Order\OrderData;

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

    public function getById(string $id): ?Order
    {
        return $this->repo->find($id);
    }

    public function create(OrderData $data): Order
    {
        $payload = $data->toArray();
        

        return $this->repo->create($payload);
    }

    public function update(string $id, OrderData $data): bool
    {
        $payload = $data->toArray();
        

        return $this->repo->update($id, $payload);
    }

    public function delete(string $id): bool
    {
        return $this->repo->delete($id);
    }
}
