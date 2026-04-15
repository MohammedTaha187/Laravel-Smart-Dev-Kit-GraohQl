<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Guarded
{
    /**
     * Create a new attribute instance.
     *
     * @param  array  $columns
     */
    public function __construct(
        public array $columns = ['*']
    ) {}
}
