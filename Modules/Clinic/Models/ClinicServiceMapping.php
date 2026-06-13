<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicServiceMappingFactory;
use App\Models\BaseModel;
use App\Models\User;

class ClinicServiceMapping extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'clinic_service_mapping';

    protected $fillable = ['service_id', 'clinic_id'];
    
    protected static function newFactory(): ClinicServiceMappingFactory
    {
        //return ClinicServiceMappingFactory::new();
    }

    public function service(){
        return $this->belongsTo(ClinicsService::class, 'service_id');
    }

    public function center(){
        return $this->belongsTo(Clinics::class, 'clinic_id')->with('receptionist');
    }
    
}
