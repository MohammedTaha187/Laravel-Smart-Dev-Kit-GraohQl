<?php

namespace Modules\Core\DTOs\Category;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class CategoryData extends Data
{
    public function __construct(
        #[Rule(['exists:categories,id'])]
        public readonly string $parent_id,
        #[Rule(['string', 'max:255'])]
        public readonly string $name,
        #[Rule(['string', 'max:255'])]
        public readonly string $slug,
        #[Rule(['string'])]
        public readonly string $description,
        #[Rule(['integer'])]
        public readonly int $is_active,
        #[Rule(['integer'])]
        public readonly int $sort_order
    ) {}
}
