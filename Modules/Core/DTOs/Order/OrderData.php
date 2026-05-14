<?php

namespace Modules\Core\DTOs\Order;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class OrderData extends Data
{
    public function __construct(
        #[Rule(['exists:users,id'])]
        public readonly string $user_id,
        #[Rule(['exists:addresses,id'])]
        public readonly string $address_id,
        #[Rule([''])]
        public readonly string $status,
        #[Rule([''])]
        public readonly string $payment_status,
        #[Rule(['string', 'max:255'])]
        public readonly string $payment_method,
        #[Rule(['numeric'])]
        public readonly float $subtotal,
        #[Rule(['numeric'])]
        public readonly float $discount,
        #[Rule(['numeric'])]
        public readonly float $shipping_cost,
        #[Rule(['numeric'])]
        public readonly float $tax,
        #[Rule(['numeric'])]
        public readonly float $total,
        #[Rule(['string'])]
        public readonly string $notes
    ) {}
}
