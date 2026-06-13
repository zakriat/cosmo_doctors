<?php

namespace App\Trait;

trait CommonQuery
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeServiceProviderBased($query, $service_provider_id = null)
    {
        return $query->where('service_provider_id', $service_provider_id);
    }

    public function scopeMultiServiceProviderBased($query, $service_provider_id = [])
    {
        return $query->whereIn('service_provider_id', $service_provider_id);
    }
}
