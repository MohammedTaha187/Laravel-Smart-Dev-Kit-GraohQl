<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Database\Factories\Product\ProductFactory;






#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Product extends Model 
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    
    

    
    
}
