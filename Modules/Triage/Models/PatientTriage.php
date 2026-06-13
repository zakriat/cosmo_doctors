<?php

namespace Modules\Triage\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Modules\Appointment\Models\Appointment;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PatientTriage extends BaseModel
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'patient_triages';

    protected $fillable = [
        'patient_id', 'appointment_id', 'nurse_id', 'category_id', 'item_id',
        'urgency_level', 'outcome', 'nurse_notes', 'clinician_escalated_to',
        'handover_summary', 'status', 'onset_bucket', 'trend', 'fever_flag',
        'severity_score', 'function_impacted', 'hydration_concern', 'risk_flags',
        'meds_text', 'allergy_text', 'recent_antibiotics', 'identity_confirmed',
        'red_flag_triggered', 'red_flag_action_taken', 'redirect_service',
    ];

    protected $casts = [
        'patient_id'           => 'integer',
        'appointment_id'       => 'integer',
        'nurse_id'             => 'integer',
        'category_id'          => 'integer',
        'item_id'              => 'integer',
        'clinician_escalated_to' => 'integer',
        'fever_flag'           => 'boolean',
        'function_impacted'    => 'boolean',
        'hydration_concern'    => 'boolean',
        'recent_antibiotics'   => 'boolean',
        'identity_confirmed'   => 'boolean',
        'red_flag_triggered'   => 'boolean',
        'severity_score'       => 'integer',
        'risk_flags'           => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    // Encrypt nurse notes at rest
    protected function nurseNotes(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !empty($value) ? (function () use ($value) {
                try { return Crypt::decrypt($value); } catch (\Exception $e) { return $value; }
            })() : '',
            set: fn($value) => !empty($value) ? Crypt::encrypt($value) : null,
        );
    }

    // Encrypt handover summary at rest
    protected function handoverSummary(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !empty($value) ? (function () use ($value) {
                try { return Crypt::decrypt($value); } catch (\Exception $e) { return $value; }
            })() : '',
            set: fn($value) => !empty($value) ? Crypt::encrypt($value) : null,
        );
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function category()
    {
        return $this->belongsTo(TriageCategory::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(TriageItem::class, 'item_id');
    }

    public function clinicianEscalatedTo()
    {
        return $this->belongsTo(User::class, 'clinician_escalated_to');
    }

    /**
     * Scope to records the logged-in nurse can see (their clinic only).
     */
    public function scopeForNurse($query, $user)
    {
        if ($user->hasRole(['admin', 'demo_admin'])) {
            return $query;
        }

        if ($user->hasRole('nurse')) {
            $nurse = \App\Models\Nurse::where('nurse_id', $user->id)->first();
            if ($nurse) {
                return $query->whereHas('appointment', function ($q) use ($nurse) {
                    $q->where('clinic_id', $nurse->clinic_id);
                })->orWhere('nurse_id', $user->id);
            }
            return $query->where('nurse_id', $user->id);
        }

        if ($user->hasRole('vendor')) {
            return $query->whereHas('appointment.cliniccenter', function ($q) use ($user) {
                $q->where('vendor_id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Auto-suggest urgency based on intake data.
     */
    public function getSuggestedUrgency(): string
    {
        if ($this->red_flag_triggered) {
            return 'E1';
        }

        $highRisk = !empty($this->risk_flags) && !in_array('none', $this->risk_flags ?? []);

        if (
            ($this->severity_score >= 7) ||
            ($this->function_impacted && $this->trend === 'worse') ||
            ($highRisk && $this->trend === 'worse')
        ) {
            return 'U2';
        }

        if ($this->severity_score >= 4 && $this->severity_score <= 6) {
            return 'S3';
        }

        return 'R4';
    }
}
