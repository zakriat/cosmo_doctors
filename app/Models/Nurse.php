<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Nurse extends BaseModel
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nurse_id',
        'clinic_id',
        'vendor_id',
        'license_number',
        'specialization',
        'license_expiry',
        'certifications',
        'shift_type',
        'notes',
        'is_head_nurse',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'license_expiry' => 'date',
        'certifications' => 'array',
        'is_head_nurse' => 'boolean',
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * Relationship with User (Nurse)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nurse_id', 'id');
    }

    /**
     * Relationship with Clinic
     */
    public function clinic()
    {
        return $this->belongsTo(\Modules\Clinic\Models\Clinics::class, 'clinic_id');
    }

    /**
     * Relationship with Vendor (Clinic Admin)
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    /**
     * Scope for active nurses
     */
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('status', 1);
        });
    }

    /**
     * Scope for head nurses
     */
    public function scopeHeadNurses($query)
    {
        return $query->where('is_head_nurse', true);
    }

    /**
     * Scope for specific clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Scope for specific shift
     */
    public function scopeForShift($query, $shift)
    {
        return $query->where('shift_type', $shift);
    }

    /**
     * Get nurse's full name
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->full_name : 'Unknown';
    }

    /**
     * Get nurse's display name with credentials
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->full_name;
        if ($this->license_number) {
            $name .= ' (RN: ' . $this->license_number . ')';
        }
        if ($this->is_head_nurse) {
            $name .= ' - Head Nurse';
        }
        return $name;
    }

    /**
     * Check if license is expiring soon (within 30 days)
     */
    public function getIsLicenseExpiringSoonAttribute()
    {
        if (!$this->license_expiry) {
            return false;
        }
        
        return $this->license_expiry->diffInDays(now()) <= 30 && $this->license_expiry->isFuture();
    }

    /**
     * Check if license is expired
     */
    public function getIsLicenseExpiredAttribute()
    {
        if (!$this->license_expiry) {
            return false;
        }
        
        return $this->license_expiry->isPast();
    }

    /**
     * Get formatted certifications
     */
    public function getFormattedCertificationsAttribute()
    {
        if (!$this->certifications || !is_array($this->certifications)) {
            return 'None';
        }
        
        return implode(', ', $this->certifications);
    }

    /**
     * Scope for multi-vendor check
     */
    public function scopeCheckMultivendor($query)
    {
        if (function_exists('multiVendor') && multiVendor() == "0") {
            $query = $query->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });
        }
        
        return $query;
    }
}