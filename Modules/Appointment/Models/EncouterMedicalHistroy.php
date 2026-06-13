<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\EncouterMedicalHistroyFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EncouterMedicalHistroy extends BaseModel
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
    protected $table = 'encounter_medical_history';
    
    protected $fillable = ['encounter_id','user_id','type','title','is_from_template'];

    protected $casts = [
        
        'encounter_id' => 'integer',
        'user_id' => 'integer',
        'is_from_template' => 'integer',
       
    ];
    
    protected static function newFactory(): EncouterMedicalHistroyFactory
    {
        //return EncouterMedicalHistroyFactory::new();
    }

    public function encounter()
    {
        return $this->belongsTo(PatientEncounter::class, 'encounter_id', 'id');
    }
}
