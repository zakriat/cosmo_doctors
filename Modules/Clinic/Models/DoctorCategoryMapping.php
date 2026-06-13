<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\Clinics;

class DoctorCategoryMapping extends BaseModel
{
    use SoftDeletes;

    protected $table = 'doctor_category_mappings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'doctor_id',
        'category_id',
        'clinic_id',
        'charges',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'doctor_id' => 'integer',
        'category_id' => 'integer',
        'clinic_id' => 'integer',
        'charges' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the doctor that owns the mapping.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    /**
     * Get the category that owns the mapping.
     */
    public function category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'category_id');
    }

    /**
     * Get the clinic that owns the mapping.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id');
    }

    /**
     * Scope a query to only include active mappings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter by doctor.
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope a query to filter by clinic.
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }
}
