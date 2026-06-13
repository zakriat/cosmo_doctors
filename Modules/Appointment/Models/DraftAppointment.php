<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsCategory;
use Carbon\Carbon;

class DraftAppointment extends Model
{
    use HasFactory;

    protected $table = 'draft_appointments';

    protected $fillable = [
        'user_id',
        'service_id',
        'category_id',
        'clinic_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'current_step',
        'booking_data',
        'expires_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'service_id' => 'integer',
        'category_id' => 'integer',
        'clinic_id' => 'integer',
        'doctor_id' => 'integer',
        'current_step' => 'integer',
        'booking_data' => 'array',
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Boot method to set expires_at automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($draft) {
            if (!$draft->expires_at) {
                $draft->expires_at = Carbon::now()->addDays(7);
            }
        });
    }

    /**
     * Get the user that owns the draft
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the service for this draft
     */
    public function service()
    {
        return $this->belongsTo(ClinicsService::class, 'service_id');
    }

    /**
     * Get the category for this draft
     */
    public function category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'category_id');
    }

    /**
     * Get the clinic for this draft
     */
    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id');
    }

    /**
     * Get the doctor for this draft
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Scope to get only active (non-expired) drafts
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope to get expired drafts
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Scope to get drafts for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        $stepPercentages = [
            0 => 25,  // Category selected
            1 => 50,  // Clinic selected
            2 => 75,  // Doctor selected
            3 => 90,  // Date/Time selected (but not completed)
        ];

        return $stepPercentages[$this->current_step] ?? 0;
    }

    /**
     * Get step name
     */
    public function getStepNameAttribute()
    {
        $stepNames = [
            0 => 'Category Selection',
            1 => 'Clinic Selection',
            2 => 'Doctor Selection',
            3 => 'Date & Time Selection',
        ];

        return $stepNames[$this->current_step] ?? 'Unknown';
    }

    /**
     * Check if draft is expired
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        // return Carbon::now()->diffInDays($this->expires_at, false);
        return (int) Carbon::now()->diffInDays($this->expires_at, false);

    }
}
