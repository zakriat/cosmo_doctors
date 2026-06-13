<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Modules\ClinicHour\Models\ClinicHour;
use Modules\Employee\Models\ServiceProviderEmployee;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceProviderServices;

class ServiceProvider extends BaseModel
{
    use HasFactory;
    use HasSlug;
    use CustomFieldsTrait;

    const CUSTOM_FIELD_MODEL = 'App\Models\ServiceProvider';

    protected $casts = [
        'contact_number' => 'string',
        'payment_method' => 'array',
        'city' => 'integer',
        'state' => 'integer',
        'country' => 'integer',
    ];

    protected $appends = ['file_url'];

    /**
     * Get all the settings.
     *
     * @return mixed
     */
    public static function getAllservice_providers()
    {
        return Cache::rememberForever('service_providers.all', function () {
            return self::get();
        });
    }

    /**
     * Flush the cache.
     */
    public static function flushCache()
    {
        Cache::forget('service_providers.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });

        static::deleted(function () {
            self::flushCache();
        });
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function serviceProviderEmployee()
    {
        return $this->hasMany(ServiceProviderEmployee::class, 'service_provider_id');
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    public function clinicHours()
    {
        return $this->hasMany(ClinicHour::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_provider_services');
    }

    public function servicesProviderServices()
    {
        return $this->hasMany(ServiceProviderServices::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
