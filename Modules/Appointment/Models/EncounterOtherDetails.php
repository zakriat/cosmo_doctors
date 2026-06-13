<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\EncounterOtherDetailsFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EncounterOtherDetails extends BaseModel
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
    protected $table = 'encounter_other_details';
    
    protected $fillable = ['encounter_id','user_id','other_details'];
    
    protected static function newFactory(): EncounterOtherDetailsFactory
    {
        //return EncounterOtherDetailsFactory::new();
    }

    public function encounter()
    {
        return $this->belongsTo(PatientEncounter::class, 'encounter_id', 'id');
    }
}
