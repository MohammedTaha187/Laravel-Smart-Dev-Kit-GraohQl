<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Database\Factories\Vendor\VendorFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Vendor extends Model 
{
    /** @use HasFactory<VendorFactory> */
    use HasFactory, HasUuids;

    
        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'vendor_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }


    
    
}
