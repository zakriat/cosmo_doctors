<?php

namespace Modules\Appointment\Models;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\EncounterPrescriptionFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Models\Medicine;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class EncounterPrescription extends Model
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
    protected $table = 'encounter_prescription';
    
    protected $fillable = ['encounter_id','user_id','medicine_id','name','frequency','duration','instruction'];
 
    protected static function newFactory(): EncounterPrescriptionFactory
    {
        //return EncounterPrescriptionFactory::new();
    }

    public function encounter()
    {
        return $this->belongsTo(PatientEncounter::class)->with('clinic','doctor');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }


    

    
}
