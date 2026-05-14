<?php

namespace Modules\Core\DTOs\Tag;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class TagData extends Data
{
    public function __construct(
        #[Rule(['string', 'max:255'])]
        public readonly string $name,
        #[Rule(['string', 'max:255'])]
        public readonly string $slug
    ) {}
}
