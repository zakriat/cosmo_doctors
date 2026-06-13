<?php

namespace Modules\Frontend\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load the views from the module's Resources/views directory
        $this->loadViewsFrom(module_path('Frontend', 'Resources/views'), 'frontend');
    }

    public function register()
    {
        // You can register other services here if needed
    }
}
