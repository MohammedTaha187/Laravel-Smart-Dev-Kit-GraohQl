<?php

namespace Modules\Sales\DTOs\Order;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class OrderData extends Data
{
    public function __construct(
        public readonly string $name
    ) {}
}
