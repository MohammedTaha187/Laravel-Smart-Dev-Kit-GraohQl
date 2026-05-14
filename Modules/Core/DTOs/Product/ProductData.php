<?php

namespace Modules\Core\DTOs\Product;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class ProductData extends Data
{
    public function __construct(
        #[Rule(['exists:vendors,id'])]
        public readonly string $vendor_id,
        #[Rule(['exists:categories,id'])]
        public readonly string $category_id,
        #[Rule(['string', 'max:255'])]
        public readonly string $name,
        #[Rule(['string', 'max:255'])]
        public readonly string $slug,
        #[Rule(['string'])]
        public readonly string $description,
        #[Rule(['numeric'])]
        public readonly float $price,
        #[Rule(['numeric'])]
        public readonly float $sale_price,
        #[Rule(['string', 'max:255'])]
        public readonly string $sku,
        #[Rule(['integer'])]
        public readonly int $stock,
        #[Rule([''])]
        public readonly string $status,
        #[Rule(['integer'])]
        public readonly int $is_featured
    ) {}
}
