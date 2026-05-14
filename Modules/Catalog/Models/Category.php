<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Database\Factories\Category\CategoryFactory;






#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Category extends Model 
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    
    

    
    
}
