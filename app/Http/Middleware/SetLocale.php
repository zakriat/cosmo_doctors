<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (! session()->has('locale')) {
            session()->put('locale', setting('default_language','en'));
        }

        app()->setLocale(session()->get('locale'));

        return $next($request);
    }
}
