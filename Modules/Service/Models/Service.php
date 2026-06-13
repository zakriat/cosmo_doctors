<?php

namespace Modules\Service\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;

class Service extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomFieldsTrait;

    protected $table = 'services';

    protected $fillable = ['slug', 'name', 'description', 'duration_min', 'default_price', 'category_id', 'sub_category_id', 'status'];

    protected $appends = ['file_url'];

    protected $casts = [

        'duration_min' => 'integer',
        'default_price' => 'double',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'status' => 'integer',

    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Service\Models\Service';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Service\database\factories\ServiceFactory::new();
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

    public function employee()
    {
        return $this->hasMany(ServiceEmployee::class, 'service_id', 'id');
    }

    public function servicePackage()
    {
        return $this->hasMany(PackageServiceMapping::class, 'service_id', 'id');
    }

    public function gallery()
    {
        return $this->hasMany(ServiceGallery::class, 'id', 'file_url');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function service_providers()
    {
        return $this->hasMany(ServiceProviderServices::class, 'service_id');
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
}
