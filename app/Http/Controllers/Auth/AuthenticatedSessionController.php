<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Trait\AuthTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class AuthenticatedSessionController extends Controller
{
    use AuthTrait;

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $response = $this->loginTrait($request);

      
    
        if (isset($response['error'])) {
        
            return back()->withErrors([
                'email' => $response['error']
            ])->onlyInput('email');
        } elseif ($response['status'] == 406) {

            return back()->withErrors([
                'email' => $response['message']
            ])->onlyInput('email');

        } else {

          $request->session()->regenerate();
    
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('config:cache');
            Artisan::call('route:clear');
    
            // Check if the authenticated user is a patient and redirect accordingly
            $user = Auth::user();
            if ($user && $user->user_type === 'user') {
                return redirect('/patient-dashboard');
            }
            
            return redirect('/home');
        }
    }
    
    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/home');
    }
}
