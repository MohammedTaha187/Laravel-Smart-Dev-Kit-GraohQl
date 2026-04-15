<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use ReflectionClass;

trait HandlesAttributeConfig
{
    /**
     * Boot the trait and process attributes.
     */
    public static function bootHandlesAttributeConfig()
    {
        static::creating(function ($model) {
            $model->syncGuardedFromAttribute();
        });

        static::updating(function ($model) {
            $model->syncGuardedFromAttribute();
        });
    }

    /**
     * Initialize the model and process attributes during instantiation.
     */
    public function initializeHandlesAttributeConfig()
    {
        $this->syncGuardedFromAttribute();
    }

    /**
     * Sync the guarded property from the #[Guarded] attribute using reflection.
     */
    protected function syncGuardedFromAttribute()
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(Guarded::class);

        if (!empty($attributes)) {
            $instance = $attributes[0]->newInstance();
            $this->guarded = $instance->columns;
        }
    }
}
