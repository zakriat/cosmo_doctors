<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\AppointmentPatientRecordFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class AppointmentPatientRecord extends Model
{
    use HasFactory,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'appointment_patient_records';

    const CUSTOM_FIELD_MODEL = 'Modules\Appointment\Models\Appointment';

    protected $fillable = ['appointment_id','patient_id','encounter_id', 'subjective','objective','assessment','plan'];

    protected function subjective(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '',
            set: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '',
        );
    }

    protected function objective(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '',
            set: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '',
        );
    }

    protected function assessment(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '',
            set: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '',
        );
    }

    protected function plan(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::decrypt($value) : '',
            set: fn (?string $value) => (isset($value) && !empty($value)) ? Crypt::encrypt($value) : '',
        );
    }

    protected static function newFactory(): AppointmentPatientRecordFactory
    {
        //return AppointmentPatientRecordFactory::new();
    }
}
