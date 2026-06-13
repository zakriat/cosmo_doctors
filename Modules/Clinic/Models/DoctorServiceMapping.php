<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Trait\CustomFieldsTrait;
use Modules\Clinic\Database\factories\DoctorServiceMappingFactory;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\Clinics;
use App\Models\BaseModel;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\ClinicServiceMapping;


class DoctorServiceMapping extends BaseModel
{
    use HasFactory;
    use CustomFieldsTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['doctor_id','clinic_id','service_id','charges','status','inclusive_tax_price'];
    protected $casts = [
        'doctor_id' => 'integer',
        'service_id' => 'integer',
        'charges' => 'double',
        'clinic_id'=>'integer',
        'inclusive_tax_price'=>'double',
    ];
    
    protected static function newFactory(): DoctorServiceMappingFactory
    {
        //return DoctorServiceMappingFactory::new();
    }
    public function setChargesAttribute($value)
    {
        $this->attributes['charges'] = round($value, 2);
    }

    public function doctors()
    {
        return $this->belongsTo(Doctor::class,'doctor_id','doctor_id')->with('user');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinics::class,'clinic_id','id');

    }
    public function clinicservice()
    {
        return $this->belongsTo(ClinicsService::class,'service_id')->with('ClinicServiceMapping','appointmentService','systemservice');
    }

    
    public function clinicserviceMapping(){

        return $this->hasmany(ClinicServiceMapping::class,'service_id','service_id');
        
    }
}
