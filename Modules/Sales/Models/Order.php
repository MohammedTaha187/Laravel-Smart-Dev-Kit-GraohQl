<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Sales\Database\Factories\Order\OrderFactory;






#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Order extends Model 
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    
    

    
    
}
