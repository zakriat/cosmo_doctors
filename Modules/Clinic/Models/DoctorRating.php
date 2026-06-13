<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\DoctorRatingFactory;
use App\Models\User;

class DoctorRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'doctor_ratings';

    protected $fillable = [
        'doctor_id', 'title', 'review_msg', 'rating', 'user_id', 'service_id'
    ];
    protected $casts = [
        'doctor_id' => 'integer',
        'rating' => 'double',
        'user_id'=> 'integer',
    ];
    
    protected static function newFactory(): DoctorRatingFactory
    {
        //return DoctorRatingFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinic_service()
    {
        return $this->belongsTo(ClinicsService::class, 'service_id')->with('systemservice');
    }
}
