<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */


    public const VENDOR_LOGIN_REDIRECT = '/app/vendor-dashboard';
    public const DOCTOR_LOGIN_REDIRECT = '/app/doctor-dashboard';
    public const RECEPTIONIST_LOGIN_REDIRECT = '/app/receptionist-dashboard';
    public const NURSE_LOGIN_REDIRECT = '/app/nurse-dashboard';

    public const USER_LOGIN_REDIRECT='/patient-dashboard';
    // public const USER_LOGIN_REDIRECT = '/app/appointments';
    public const HOME = '/app';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function map()
    {
        $this->mapWebRoutes();
        $this->mapAccountsRoutes(); // Add this line
    }

    protected function mapAccountsRoutes()
    {
        Route::middleware('web')
            ->namespace('Modules\Subscriptions\Http\Controllers')
            ->group(base_path('app/Modules/Subscriptions/routes/web.php'));
    }
}
