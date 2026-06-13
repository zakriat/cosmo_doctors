<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Customer\Database\Factories\OtherPatientFactory;
use App\Models\User;
use Modules\Appointment\Models\Appointment;
class OtherPatient extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'other_patients';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'relation',
        'contactNumber',
        'subtitle',
    ];

    protected $appends = ['full_name', 'profile_image'];

    public function getFullNameAttribute() // notice that the attribute name is in CamelCase.
    {
        return $this->first_name.' '.$this->last_name;
    }

    protected function getProfileImageAttribute()
    {
        $media = $this->getFirstMediaUrl('profile_image');

        return isset($media) && ! empty($media) ? $media : asset(config('app.avatar_base_path').'avatar.webp');
    }
    /**
     * Get a new factory instance for the model.
     *
     * @return \Modules\Customer\Database\Factories\OtherPatientFactory
     */
    protected static function newFactory()
    {
        return OtherPatientFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function appointments()
    {
        return $this->hasMany(\Modules\Appointment\Models\Appointment::class, 'otherpatient_id', 'id');
    }
}
