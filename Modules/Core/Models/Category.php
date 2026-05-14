<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Database\Factories\Category\CategoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Category extends Model 
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasUuids;

    
        public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }


    
    
}
