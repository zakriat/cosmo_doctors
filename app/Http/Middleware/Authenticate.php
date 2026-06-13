<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {

            return route('login-page');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        // DEEP DEBUG: Log authentication middleware execution
        \Log::info('Authenticate Middleware', [
            'url' => $request->fullUrl(),
            'guards' => $guards,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
        ]);
        
        // Perform the usual authentication check
        $this->authenticate($request, $guards);
        
        \Log::info('Authenticate Middleware - After authenticate()', [
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'user_type' => Auth::check() ? Auth::user()->user_type : null,
        ]);
        
        // Check if the user is a vendor and multivendor is off using the helper
        if (Auth::check() && Auth::user()->user_type === 'vendor' && multiVendor() == 0) {
            \Log::warning('Authenticate - Vendor logged out (multivendor disabled)');
            Auth::logout(); // Log out vendor
            return redirect()->route('login-page')->with('error', 'Multivendor is disabled. You have been logged out.');
        }

        \Log::info('Authenticate Middleware - Passing through');
        return $next($request);
    }
}
