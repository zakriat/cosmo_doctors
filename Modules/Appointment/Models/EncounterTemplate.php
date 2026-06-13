<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\EncounterTemplateFactory;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Appointment\Models\TemplateMedicalHistory;
use Modules\Appointment\Models\TemplatePrescription;
use Modules\Appointment\Models\TemplateOtherDetails;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class EncounterTemplate extends BaseModel
{
    use HasFactory;
    use CustomFieldsTrait;
    use SoftDeletes,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    const CUSTOM_FIELD_MODEL = 'Modules\Appointment\Models\EncounterTemplate';
    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'encounter_templates';
    
    protected $fillable = ['name','status'];

    protected $casts = [
        'status' => 'integer',
    ];
    
    protected static function newFactory(): EncounterTemplateFactory
    {
        //return EncounterTemplateFactory::new();
    }


    public function templateMedicalHistroy()
    {
        return $this->hasmany(TemplateMedicalHistory::class, 'template_id','id');
    }

    public function templatePrescriptions()
    {
        return $this->hasmany(TemplatePrescription::class, 'template_id','id');
    }

    public function TemplateOtherDetails()
    {
        return $this->hasOne(TemplateOtherDetails::class, 'template_id','id');
    }


}
