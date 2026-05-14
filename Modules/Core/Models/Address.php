<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Database\Factories\Address\AddressFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Address extends Model 
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory, HasUuids;

    
        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'address_id', 'id');
    }


    
    
}
