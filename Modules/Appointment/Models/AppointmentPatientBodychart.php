<?php

namespace Modules\Appointment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\AppointmentPatientBodychartFactory;
use Modules\Clinic\Database\factories\ClinicFactory;
use App\Trait\CustomFieldsTrait;
use App\Models\BaseModel;
use Modules\Appointment\Models\PatientEncounter;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class AppointmentPatientBodychart extends BaseModel
{

    use HasFactory;
    use CustomFieldsTrait,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'appointment_patient_bodychart';
    protected $fillable = ['name','description','encounter_id','appointment_id','patient_id'];

    protected $appends = ['file_url'];

    public function setNameAttribute($value)
    {
        $this->attributes["name"] = (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '';
    }

    public function getNameAttribute($value)
    {
        return (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '';
    }


    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '',
            set: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '',
        );
    }
    
    protected static function newFactory(): AppointmentPatientBodychartFactory
    {
        //return AppointmentPatientBodychartFactory::new();
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'patient_id');
    }

    public function appoinment()  {
        return $this->belongsTo(PatientEncounter::class, 'appointment_id');
    }

    public function patient_encounter(){
        return $this->belongsTo(PatientEncounter::class, 'encounter_id')->with('doctor', 'user');
    }
}
