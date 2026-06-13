<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\PatientEncounterFactory;
use Modules\Clinic\Models\Clinics;
use App\Models\User;
use Modules\Appointment\Models\EncouterMedicalHistroy;
use Modules\Appointment\Models\EncounterPrescription;
use Modules\Appointment\Models\EncounterOtherDetails;
use Modules\Appointment\Models\EncounterMedicalReport;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\AppointmentPatientRecord;
use Modules\Appointment\Models\AppointmentPatientBodychart;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\Doctor;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PatientEncounter extends BaseModel
{
    use HasFactory;
    use SoftDeletes, LogsActivity;


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'patient_encounters';

    protected $fillable = ['encounter_date', 'user_id', 'clinic_id', 'doctor_id', 'vendor_id', 'appointment_id', 'encounter_template_id', 'description', 'status'];

    protected $casts = [
        'user_id' => 'integer',
        'clinic_id' => 'integer',
        'doctor_id' => 'integer',
        'appointment_id' => 'integer',
        'encounter_template_id' => 'integer',
        'status' => 'integer',
    ];

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !empty($value)
                ? (function () use ($value) {
                    try {
                        return Crypt::decrypt($value);
                    } catch (\Exception $e) {
                        return $value;
                    }
                })()
                : '',
            set: fn($value) => !empty($value) ? Crypt::encrypt($value) : null,
        );
    }

    protected static function newFactory(): PatientEncounterFactory
    {
        //return PatientEncounterFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function medicalHistroy()
    {
        return $this->hasmany(EncouterMedicalHistroy::class, 'encounter_id', 'id');
    }

    public function prescriptions()
    {
        return $this->hasmany(EncounterPrescription::class, 'encounter_id', 'id')->with('medicine');
    }
    public function EncounterOtherDetails()
    {

        return $this->hasone(EncounterOtherDetails::class, 'encounter_id', 'id');
    }

    public function appointmentdetail()
    {

        return $this->hasone(Appointment::class, 'id', 'appointment_id')->with('appointmenttransaction');
    }

    public function billingrecord()
    {

        return $this->hasone(BillingRecord::class, 'encounter_id', 'id')->with('billingItem.clinicservice');
    }


    public function medicalReport()
    {

        return $this->hasMany(EncounterMedicalReport::class, 'encounter_id', 'id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id')->with('clinicservice', 'appointmenttransaction');
    }


    public function scopeSetRole($query, $user)
    {
        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
            if (multiVendor() == "0") {

                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

                $query->with('clinic')->whereHas('clinic', function ($query) use ($user_ids) {
                    $query->whereIn('vendor_id', $user_ids);
                });
            }

            return $query;
        }

        if ($user->hasRole('vendor')) {

            $query->with('clinic')->whereHas('clinic', function ($query) use ($user_id) {

                $query->where('vendor_id', $user_id);
            });
            return $query;
        }

        if (auth()->user()->hasRole('doctor')) {

            if (multiVendor() == 0) {

                $doctor = Doctor::where('doctor_id', $user_id)->first();

                $vendorId = $doctor->vendor_id;

                $query = $query->where('doctor_id', $user_id)->whereHas('clinic', function ($qry) use ($vendorId) {

                    $qry->where('vendor_id', $vendorId);
                });
            } else {

                $query = $query->where('doctor_id', $user_id);
            }

            return $query;
        }

        if (auth()->user()->hasRole('receptionist')) {

            $Receptionist = Receptionist::where('receptionist_id', $user_id)->first();

            $vendorId = $Receptionist->vendor_id;
            $clinic_id = $Receptionist->clinic_id;

            if (multiVendor() == "0") {

                $query = $query->where('clinic_id', $clinic_id)->whereHas('clinic', function ($qry) use ($vendorId) {

                    $qry->where('vendor_id', $vendorId);
                });
            } else {

                $query = $query->where('clinic_id', $clinic_id);
            }

            return $query;
        }

        if (auth()->user()->hasRole('user')) {
            $query = $query->where('user_id', $user_id);
            return $query;
        }

        return $query;
    }

    public function soap()
    {

        return $this->hasone(AppointmentPatientRecord::class, 'encounter_id', 'id');
    }

    public function bodyChart()
    {
        return $this->hasMany(AppointmentPatientBodychart::class, 'encounter_id');
    }
}
