<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicsCategoryFactory;
use App\Trait\CustomFieldsTrait;
use App\Models\BaseModel;
use Auth;
use App\Models\Traits\HasSlug;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorCategoryMapping;


class ClinicsCategory extends BaseModel
{
    use HasFactory;
    use CustomFieldsTrait;
    use HasSlug;


    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\ClinicsCategory';

    protected $table = 'clinics_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['slug','name','parent_id','description','vendor_id','featured','status','price','service_classification'];

    protected $appends = ['file_url'];

    protected $casts = [
        'client_id' => 'integer',
        'parent_id' => 'integer',
        'status' => 'integer',
        'featured' => 'integer',
        'price' => 'decimal:2',
    ];

    protected static function newFactory(): ClinicsCategoryFactory
    {
        //return ClinicsCategoryFactory::new();
    }


    public function mainCategory()
    {
        return $this->belongsTo(ClinicsCategory::class, 'parent_id','id');
    }

    public function subCategories()
    {
        return $this->hasMany(ClinicsCategory::class, 'parent_id');
    }

    /**
     * Get the service this category belongs to.
     */
    public function service()
    {
        return $this->belongsTo(ClinicsService::class, 'parent_id');
    }

    /**
     * Get all doctor mappings for this category.
     */
    public function doctorMappings()
    {
        return $this->hasMany(DoctorCategoryMapping::class, 'category_id');
    }

    /**
     * Get all doctors assigned to this category (many-to-many).
     */
    public function doctors()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctor_category_mappings',
            'category_id',
            'doctor_id',
            'id',
            'doctor_id'
        )->withPivot('clinic_id', 'charges', 'status')
          ->withTimestamps();
    }

    /**
     * Check if this category requires a doctor.
     */
    public function requiresDoctor()
    {
        return $this->service_classification === 'doctor_required';
    }

    /**
     * Check if this category is optional for doctor.
     */
    public function doctorOptional()
    {
        return $this->service_classification === 'doctor_optional';
    }

    /**
     * Check if this category doesn't require a doctor.
     */
    public function noDoctorRequired()
    {
        return $this->service_classification === 'no_doctor_required';
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


    // public function services()
    // {
    //     return $this->hasMany(Service::class, 'category_id');
    // }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSetVendor($query)
    {
        $vendorId = Auth::id();
        return $query->where('vendor_id', $vendorId);
    }

}
