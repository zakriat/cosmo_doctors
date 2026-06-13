<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicSessionFactory;
use App\Models\BaseModel;

class ClinicSession extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'clinic_session';
    protected $fillable = ['clinic_id', 'day', 'start_time','end_time','is_holiday','breaks'];
    protected $casts = [
        'breaks' => 'array',
    ];

    
    protected static function newFactory(): ClinicSessionFactory
    {
        //return ClinicSessionFactory::new();
    }
    public function clinic(){
        return $this->belongsTo(Clinics::class, 'clinic_id');
    }
}
