<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Modules\Clinic\Models\Doctor;
use App\Models\User;

class DoctorHoliday extends Model
{
    use HasFactory;
    use HasRoles;
    const CUSTOM_FIELD_MODEL = 'App\Models\DoctorHoliday';
    protected $fillable = ['doctor_id', 'date', 'start_time', 'end_time'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
