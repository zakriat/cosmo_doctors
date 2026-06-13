<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\FrontendSetting\Models\FrontendSetting;

class CheckHeaderMenuSettings
{
    public function handle(Request $request, Closure $next)
    {
        // Get header settings
        $headerSettings = FrontendSetting::where('type', 'heder-menu-setting')
            ->where('key', 'heder-menu-setting')
            ->first();

        if (!$headerSettings || !$headerSettings->status) {
            abort(404);
        }

        $settings = json_decode($headerSettings->value, true);
        
        // Map routes to their corresponding settings
        $routeSettingsMap = [
            'categories' => 'categories',
            'services' => 'services',
            'clinics' => 'clinics',
            'doctors' => 'doctors',
            'appointment-list' => 'appointments',
            'incidence' => 'incidence',
        ];

        $currentRoute = $request->route()->getName();
        
        // Check if current route needs to be validated
        foreach ($routeSettingsMap as $route => $setting) {
            if (str_contains($currentRoute, $route) && (!isset($settings[$setting]) || !$settings[$setting])) {
                abort(404);
            }
        }

        return $next($request);
    }
}