<?php

namespace Modules\Appointment\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Clinic\Models\ClinicsService;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Clinic\Models\Clinics;
use Modules\Commission\Models\CommissionEarning;
use Modules\Commission\Trait\CommissionTrait;
use Modules\Tip\Trait\TipTrait;
use Modules\Customer\Models\OtherPatient;
use Modules\Clinic\Models\DoctorRating;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\ClinicsCategory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Appointment extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use CommissionTrait;
    use TipTrait,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    protected $table = 'appointments';

    const CUSTOM_FIELD_MODEL = 'Modules\Appointment\Models\Appointment';

    protected $fillable = ['status', 'start_date_time', 'user_id','otherpatient_id', 'clinic_id', 'doctor_id', 'appointment_extra_info', 'appointment_date', 'appointment_time', 'service_id', 'category_id', 'triage_id', 'linked_appointment_id', 'ordered_by', 'total_amount', 'service_amount', 'service_price', 'duration', 'advance_payment_amount', 'advance_paid_amount', 'is_quick_booking','start_video_link','join_video_link','meet_link','cancellation_charge','cancellation_charge_amount','cancellation_type','reason','type','gf_entry_id','initiated_from_dashboard','test_type','raw_gf_data','synced_at','report_file','report_uploaded_at','report_status','report_notes'];

    protected $appends = ['file_url'];

    protected $casts = [
        'user_id' => 'integer',
        'clinic_id' => 'integer',
        'service_id' => 'integer',
        'category_id' => 'integer',
        'doctor_id' => 'integer',
        'otherpatient_id' => 'integer',
        'is_quick_booking' => 'integer',
        'initiated_from_dashboard' => 'boolean',
        'raw_gf_data' => 'array',
        'synced_at' => 'datetime',
        'report_uploaded_at' => 'datetime',
    ];
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Appointment\database\factories\AppointmentFactory::new();
    }
    public function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && !empty($media) ? $media : Null;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->with('commissionData');
    }

    public function doctorData()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }


    public function otherPatient()
    {
        return $this->belongsTo(OtherPatient::class, 'otherpatient_id', 'id');
    }
    
    public function clinicservice()
    {
        return $this->belongsTo(ClinicsService::class, 'service_id')->with('systemservice', 'serviceRating');
    }

    /**
     * Get the category for this appointment.
     */
    public function category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'category_id');
    }

    public function appointmenttransaction()
    {
        return $this->hasOne(AppointmentTransaction::class, 'appointment_id');
    }

    public function cliniccenter()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id')->with('vendor');
    }

    public function payment()
    {
        return $this->hasOne(AppointmentTransaction::class);
    }

    public function commissionsdata()
    {
        return $this->hasMany(CommissionEarning::class, 'commissionable_id', 'id');
    }

    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class, 'clinic_id', 'clinic_id');
    }

    public function serviceRating()
    {
        return $this->hasOne(DoctorRating::class, 'service_id', 'service_id');
    }

    public function serviceRatingUnique($doctor_id)
    {

        return $this->hasOne(DoctorRating::class, 'service_id', 'service_id')
            ->where('doctor_id', $doctor_id);
    }
    public function triage()
    {
        return $this->belongsTo(\Modules\Triage\Models\PatientTriage::class, 'triage_id');
    }

    public function linkedBloodTests()
    {
        return $this->hasMany(Appointment::class, 'linked_appointment_id')
            ->where('type', 'blood_test');
    }

    public function patientEncounter()
    {
        return $this->hasOne(PatientEncounter::class, 'appointment_id')->with('billingrecord');
    }

    public function scopeCheckMultivendor($query)
    {
        if (multiVendor() == "0") {
            $query = $query->whereHas('cliniccenter', function ($q) {
                $q->whereHas('vendor', function ($que) {
                    $que->whereIn('user_type', ['admin', 'demo_admin']);
                });
            });
        }
    }

    public function scopeSetRole($query, $user)
    {
        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
            if (multiVendor() == "0") {

                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

                $query->with('cliniccenter')->whereHas('cliniccenter', function ($query) use ($user_ids) {
                    $query->whereIn('vendor_id', $user_ids);
                });
            }

            return $query;
        }

        if ($user->hasRole('vendor')) {

            $query->with('cliniccenter')->whereHas('cliniccenter', function ($query) use ($user_id) {

                $query->where('vendor_id', $user_id);

            });
            return $query;
        }

        if (auth()->user()->hasRole('doctor')) {

            if (multiVendor() == 0) {

                $doctor = Doctor::where('doctor_id', $user_id)->first();

                $vendorId = $doctor->vendor_id;

                $query = $query->where('doctor_id', $user_id)->whereHas('doctorData', function ($qry) use ($vendorId) {

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

                $query = $query->where('clinic_id', $clinic_id)->whereHas('cliniccenter', function ($qry) use ($vendorId) {

                    $qry->where('vendor_id', $vendorId);

                });

            } else {

                $query = $query->where('clinic_id', $clinic_id);

            }
            // $query=$query->where('clinic_id',$clinic_id);

            return $query;

        }

        if (auth()->user()->hasRole('user')) {

            $query->where('user_id', $user_id);
            return $query;

        }



        return $query;
    }

    /**
     * Scope to filter blood test appointments
     */
    public function scopeBloodTests($query)
    {
        return $query->where('type', 'blood_test');
    }

    /**
     * Scope to filter regular appointments
     */
    public function scopeRegularAppointments($query)
    {
        return $query->where('type', 'appointment');
    }

    /**
     * Scope to filter dashboard-initiated bookings
     */
    public function scopeFromDashboard($query)
    {
        return $query->where('initiated_from_dashboard', true);
    }

    /**
     * Scope to filter by appointment type
     */
    public function scopeOfType($query, $type)
    {
        if (in_array($type, ['appointment', 'blood_test'])) {
            return $query->where('type', $type);
        }
        return $query;
    }

    public function bodyChart()
    {
        return $this->hasMany(AppointmentPatientBodychart::class, 'appointment_id')->with('patient_encounter');
    }

    public function getCancellationCharges(): float
    {
        // Retrieve service configuration settings
        $cancellationChargeHours = setting('cancellation_charge_hours',0);
        $cancellation_charge = setting('is_cancellation_charge',0);
        $cancelltion_Type = setting('cancellation_type','fixed');
        $cancellation_charge_amount = setting('cancellation_charge');
        $cancellation_charge = isset($cancellation_charge) ? $cancellation_charge : 0;
        $cancellationChargeAmount = 0;
        $datetime = setting('default_time_zone');
        if($cancellation_charge == 1 && auth()->check() && auth()->user()->user_type == 'user') {
            $cancellationChargeHours = isset($cancellationChargeHours) ? (double)$cancellationChargeHours : 0;
            $timezone = new \DateTimeZone($datetime ?? 'UTC');
            $date = \Carbon\Carbon::parse($this->appointment_date)->format('Y-m-d');
            $time = $this->appointment_time;
            $bookingDateTimeString = $date . ' ' . $time;
            $bookingTime = new \DateTime($bookingDateTimeString, $timezone);
            $cancellationRequestTime = new \DateTime('now', $timezone); // Current time when cancellation is requested
            if ($bookingTime > $cancellationRequestTime) {
            $timeDifference = $bookingTime->diff($cancellationRequestTime);
            $totalHours = ($timeDifference->days * 24) + $timeDifference->h + ($timeDifference->i / 60);
            if ($totalHours <= $cancellationChargeHours) {
                $cancellationCharge = isset($cancellation_charge_amount) ? (double)$cancellation_charge_amount : 0;
                        if($cancellationCharge > 0){
                            if($cancelltion_Type == 'percentage'){
                    $cancellationChargeAmount = $this->total_amount * $cancellationCharge / 100;
                            }else{
                    $cancellationChargeAmount = $cancellationCharge;
                }
                }
            }
            }
        }
        return $cancellationChargeAmount;
    }

    public function getRefundAmount()
    {
        $advance_paid_amount = $this->advance_paid_amount ?? 0;
        $total_paid = $this->total_amount ?? 0;
        $payment_status = optional($this->appointmenttransaction)->payment_status;
        $cancellation_charge_amount = $this->cancellation_charge_amount ?? 0;

        if($payment_status == 0 || $advance_paid_amount > 0) { // Unpaid
           
        return $advance_paid_amount - $cancellation_charge_amount; // refund
          
        } else { // Paid
            return $total_paid - $cancellation_charge_amount; // refund
        }
    }


}
