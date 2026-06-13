<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicsServiceFactory;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Appointment\Models\Appointment;
use App\Models\BaseModel;
use App\Models\User;
use Auth;
// use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Clinic\Models\SystemService;
// use Spatie\Activitylog\LogOptions;


class ClinicsService extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use CustomFieldsTrait;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()->logOnly(['*']);
    // }

    protected $table = 'clinics_services';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['system_service_id', 'name', 'description', 'type', 'service_classification', 'is_video_consultancy', 'charges', 'category_id', 'subcategory_id', 'vendor_id', 'duration_min', 'time_slot', 'discount', 'discount_value', 'discount_type', 'status', 'service_type', 'is_enable_advance_payment', 'advance_payment_amount', 'is_inclusive_tax', 'inclusive_tax', 'inclusive_tax_price'];

    protected $appends = ['file_url'];

    protected $casts = [
        'vendor_id' => 'integer',
        'system_service_id' => 'integer',
        'duration_min' => 'integer',
        'charges' => 'double',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'status' => 'integer',
        'discount' => 'integer',
    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\ClinicsService';

    protected static function newFactory(): ClinicsServiceFactory
    {
        //return ClinicsServiceFactory::new();
    }


    public function category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'category_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(ClinicsCategory::class, 'sub_category_id');
    }
    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }


    public function ClinicServiceMapping()
    {
        return $this->hasMany(ClinicServiceMapping::class, 'service_id')->with('center');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function doctor_service()
    {
        return $this->hasMany(DoctorServiceMapping::class, 'service_id');
    }
    public function appointmentService()
    {
        return $this->hasMany(Appointment::class, 'service_id')->where('status', 'checkout');
    }
    public function scopeSetVendor($query)
    {
        $vendorId = Auth::id();
        return $query->where('vendor_id', $vendorId);
    }
    public function systemservice()
    {
        return $this->belongsTo(SystemService::class, 'system_service_id');
    }

    public function serviceRating()
    {
        return $this->hasMany(DoctorRating::class, 'service_id');
    }

    public function scopeCheckMultivendor($query)
    {
        if (multiVendor() == "0") {
            $query = $query->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });
        } else {
            $query = $query;
        }
    }

    public function scopeSetRole($query, $user)
    {
        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {

            if (multiVendor() == "0") {

                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

                $query = $query->whereIn('vendor_id', $user_ids)->withCount(['doctor_service']);
            } else {

                $query = $query->withCount(['doctor_service']);
            }

            return $query;
        }

        if ($user->hasRole('vendor')) {

            $query = $query->where('vendor_id',  $user_id)->withCount(['doctor_service']);
            return $query;
        }

        if (auth()->user()->hasRole('doctor')) {
            if (multiVendor() == "0") {

                $doctor = Doctor::where('doctor_id', $user_id)->first();

                $vendorId = $doctor->vendor_id;

                $query = $query->where('vendor_id', $vendorId)->withCount(['doctor_service']);
            } else {

                $query = $query->withCount(['doctor_service']);
            }
            //    $query=$query->withCount(['doctor_service']);
            return $query;
        }

        if (auth()->user()->hasRole('receptionist')) {

            $Receptionist = Receptionist::where('receptionist_id', $user_id)->first();

            $vendorId = $Receptionist->vendor_id;

            if (multiVendor() == "0") {

                $query = $query->where('vendor_id', $vendorId)->whereHas('ClinicServiceMapping.center', function ($qry) use ($user_id) {
                    $qry->whereHas('receptionist', function ($qry) use ($user_id) {
                        $qry->where('receptionist_id', $user_id);
                    });
                });
            } else {

                $query = $query->whereHas('ClinicServiceMapping.center', function ($qry) use ($user_id) {
                    $qry->whereHas('receptionist', function ($qry) use ($user_id) {
                        $qry->where('receptionist_id', $user_id);
                    });
                });
            }

            return $query;
        }


        return $query;
    }
}
