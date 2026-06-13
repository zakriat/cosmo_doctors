<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Illuminate\Support\Facades\Log;

class checkUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
       // DEEP DEBUG: Log middleware execution
       Log::info('checkUser Middleware', [
           'url' => $request->fullUrl(),
           'auth_check' => Auth::check(),
           'auth_id' => Auth::id(),
           'session_id' => $request->session()->getId(),
       ]);
       
       // TEMPORARY: Bypass check for testing OTP removal
       if(Auth::check()) {
            Log::info('checkUser - User authenticated, passing through');
            return $next($request);
        }

        Log::warning('checkUser - User NOT authenticated, redirecting to login');
        return redirect()->route('login-page');
    }
}
