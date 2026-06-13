<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicAppointmentFactory;
use App\Models\BaseModel;

class ClinicAppointment extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): ClinicAppointmentFactory
    {
        //return ClinicAppointmentFactory::new();
    }
}
