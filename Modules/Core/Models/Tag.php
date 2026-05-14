<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Database\Factories\Tag\TagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Tag extends Model 
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasUuids;

    
        public function productTags(): HasMany
    {
        return $this->hasMany(ProductTag::class, 'tag_id', 'id');
    }


    
    
}
