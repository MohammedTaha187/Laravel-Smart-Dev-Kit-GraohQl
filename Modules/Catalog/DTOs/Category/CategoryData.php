<?php

namespace Modules\Catalog\DTOs\Category;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class CategoryData extends Data
{
    public function __construct(
        public readonly string $name
    ) {}
}
