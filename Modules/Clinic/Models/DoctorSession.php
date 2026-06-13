<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\DoctorSessionFactory;
use App\Models\BaseModel;
use Modules\Clinic\Models\Clinics;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class DoctorSession extends BaseModel
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'doctor_session';
    
    protected $fillable = ['doctor_id', 'clinic_id', 'day', 'start_time','end_time','is_holiday','breaks'];

    protected $casts = [
        'breaks' => 'array',
    ];
    
    protected static function newFactory(): DoctorSessionFactory
    {
        //return DoctorSessionFactory::new();
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id');
    }
}


