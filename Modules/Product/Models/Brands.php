<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use App\Trait\CustomFieldsTrait;

class Brands extends BaseModel
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    // use CustomFieldsTrait;

    protected $table = 'brands';

    const CUSTOM_FIELD_MODEL = 'Modules\Product\Models\Brands';

    protected $appends = ['file_url'];

    protected $fillable = ['name', 'slug', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\BrandsFactory::new();
    }
}
