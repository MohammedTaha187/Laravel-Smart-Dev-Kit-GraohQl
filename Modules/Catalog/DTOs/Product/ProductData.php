<?php

namespace Modules\Catalog\DTOs\Product;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class ProductData extends Data
{
    public function __construct(
        public readonly string $name
    ) {}
}
