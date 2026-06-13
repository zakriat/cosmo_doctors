<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class checkAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

      

        if ($user && $user->hasRole('admin') || $user && $user->hasRole('demo_admin')) {
            return $next($request);
        }

    

        if ($user->hasRole('vendor')) {
            return redirect(RouteServiceProvider::VENDOR_LOGIN_REDIRECT);
        } else if ($user->hasRole('doctor')) {
            return redirect(RouteServiceProvider::DOCTOR_LOGIN_REDIRECT);
        } else if ($user->hasRole('receptionist')) {
            return redirect(RouteServiceProvider::RECEPTIONIST_LOGIN_REDIRECT);
        }else if ($user->hasRole('user')) {
            return redirect(RouteServiceProvider::USER_LOGIN_REDIRECT);

        }

        return redirect(RouteServiceProvider::HOME);
    }
}
