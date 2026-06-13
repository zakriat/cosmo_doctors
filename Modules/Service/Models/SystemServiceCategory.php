<?php

namespace Modules\Service\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemServiceCategory extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomFieldsTrait;

    protected $table = 'system_service_category';

    protected $fillable = ['slug', 'name', 'status', 'parent_id'];

    const CUSTOM_FIELD_MODEL = 'Modules\Service\Models\SystemServiceCategory';

    protected $appends = ['file_url'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Category\database\factories\SystemServiceCategoryFactory::new();
    }

    public function mainCategory()
    {
        return $this->belongsTo(SystemServiceCategory::class, 'parent_id','id');
    }
    public function subCategories()
    {
        return $this->hasMany(SystemServiceCategory::class, 'parent_id');
    }




    protected static function boot()
    {
        parent::boot();

        // create a event to happen on creating
        static::creating(function ($table) {
            //
        });

        static::saving(function ($table) {
            //
        });

        static::updating(function ($table) {
            //
        });
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    public static function getCategoryNameById($categoryId)
    {
        $category = self::find($categoryId);

        return $category ? $category->name : null;
    }
}
