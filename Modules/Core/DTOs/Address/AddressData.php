<?php

namespace Modules\Core\DTOs\Address;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class AddressData extends Data
{
    public function __construct(
        #[Rule(['exists:users,id'])]
        public readonly string $user_id,
        #[Rule(['string', 'max:255'])]
        public readonly string $address_line_1,
        #[Rule(['string', 'max:255'])]
        public readonly string $address_line_2,
        #[Rule(['string', 'max:255'])]
        public readonly string $city,
        #[Rule(['string', 'max:255'])]
        public readonly string $state,
        #[Rule(['string', 'max:255'])]
        public readonly string $postal_code,
        #[Rule(['string', 'max:255'])]
        public readonly string $country,
        #[Rule(['integer'])]
        public readonly int $is_default
    ) {}
}
