<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasPayments;
use App\Traits\HasProfessionalLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use App\Traits\HandlesAttributeConfig;

#[Guarded(['id', 'created_at', 'updated_at', 'deleted_at'])]
class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasProfessionalLogs, HasPayments, BelongsToTenant, HandlesAttributeConfig;


}
