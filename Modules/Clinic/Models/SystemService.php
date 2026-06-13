<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\SystemServiceFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Trait\CustomFieldsTrait;
use App\Models\BaseModel;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\ClinicsService;

class SystemService extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use CustomFieldsTrait;
    protected $table = 'system_service';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name','category_id','subcategory_id','description','type','is_video_consultancy','featured','status'];

    protected $appends = ['file_url'];

    protected $casts = [
        
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'status' => 'integer',
        'featured'=>'integer',
        'is_video_consultancy' => 'integer',
    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\SystemService';

    
    protected static function newFactory(): SystemServiceFactory
    {
        //return SystemServiceFactory::new();
    }
    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }
    public function category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'category_id');
    }
    public function sub_category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'subcategory_id');
    }
    public function clinicservice()
    {
        return $this->hasMany(ClinicsService::class, 'system_service_id');
    }
}
