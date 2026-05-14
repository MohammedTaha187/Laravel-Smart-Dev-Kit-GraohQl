<?php

namespace Modules\Core\DTOs\Vendor;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class VendorData extends Data
{
    public function __construct(
        #[Rule(['exists:users,id'])]
        public readonly string $user_id,
        #[Rule(['string', 'max:255'])]
        public readonly string $store_name,
        #[Rule(['string', 'max:255'])]
        public readonly string $store_slug,
        #[Rule(['string'])]
        public readonly string $description,
        #[Rule(['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'])]
        public readonly ?string $logo,
        #[Rule(['string', 'max:255'])]
        public readonly string $banner,
        #[Rule([''])]
        public readonly string $status,
        #[Rule(['numeric'])]
        public readonly float $commission_rate,
        #[Rule(['numeric'])]
        public readonly float $balance,
        #[Rule(['numeric'])]
        public readonly float $total_sales
    ) {}
}
