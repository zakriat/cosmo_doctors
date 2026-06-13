<?php

namespace Modules\Service\Models;

use Modules\ServiceProvider\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderServices extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'service_provider_id', 'service_price', 'duration_min'];

    protected $casts = [

        'service_id' => 'integer',
        'service_provider_id' => 'integer',
        'service_price' => 'double',
        'duration_min' => 'double',

    ];

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
