<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Database\Factories\Order\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Order extends Model 
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, HasUuids;

    
        public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }


    
    
}
