<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\EncounterMedicalReportFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EncounterMedicalReport extends BaseModel
{
    use HasFactory;
    use SoftDeletes,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * The attributes that are mass assignable.
     */

     protected $table = 'encounter_medical_report';
    
     protected $fillable = ['encounter_id','user_id','name','date'];

     protected $appends = ['file_url'];

    
    protected static function newFactory(): EncounterMedicalReportFactory
    {
        //return EncounterMedicalReportFactory::new();
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function encounter()
    {
        return $this->belongsTo(PatientEncounter::class, 'encounter_id', 'id');
    }



}
