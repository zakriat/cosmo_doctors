<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * PatientAuthentication Middleware
 * 
 * Ensures only authenticated patients can access patient dashboard routes.
 * Implements session timeout handling and audit logging for patient access.
 */
class PatientAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // DEEP DEBUG: Log everything about the request and session
        Log::info('=== PatientAuth Middleware START ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'session_id' => $request->session()->getId(),
            'has_session_token' => $request->session()->has('_token'),
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'session_driver' => config('session.driver'),
        ]);
        
        if (Auth::check()) {
            Log::info('PatientAuth - User IS authenticated', [
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->user_type,
                'roles' => Auth::user()->getRoleNames()->toArray(),
            ]);
        }
        
        // Check if user is authenticated
        if (!Auth::check()) {
            $this->logPatientAccess($request, null, 'unauthenticated_access_attempt');
            
            Log::warning('PatientAuth - User NOT authenticated, redirecting to login', [
                'session_id' => $request->session()->getId(),
                'session_data' => $request->session()->all(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required',
                    'redirect' => route('login-page')
                ], 401);
            }
            
            return redirect()->route('login-page')
                           ->with('error', 'Please log in to access your dashboard.');
        }

        $user = Auth::user();

        // Check if user is a patient (user_type should be 'user' for patients)
        if ($user->user_type !== 'user') {
            $this->logPatientAccess($request, $user->id, 'non_patient_access_attempt', [
                'user_type' => $user->user_type,
                'user_roles' => $user->getRoleNames()->toArray()
            ]);
            
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied. Patient authentication required.',
                    'redirect' => route('login-page')
                ], 403);
            }
            
            return redirect()->route('login-page')
                           ->with('error', 'Access denied. This area is for patients only.');
        }

        // Check session timeout (default Laravel session lifetime is used)
        $sessionLifetime = config('session.lifetime', 120); // minutes
        $lastActivity = session('last_activity');
        
        if ($lastActivity && Carbon::now()->diffInMinutes(Carbon::parse($lastActivity)) > $sessionLifetime) {
            $this->logPatientAccess($request, $user->id, 'session_timeout');
            
            Auth::logout();
            session()->flush();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'redirect' => route('login-page')
                ], 401);
            }
            
            return redirect()->route('login-page')
                           ->with('error', 'Your session has expired. Please log in again.');
        }

        // Update last activity timestamp
        session(['last_activity' => Carbon::now()->toDateTimeString()]);

        // Log successful patient dashboard access
        $this->logPatientAccess($request, $user->id, 'dashboard_access_granted');

        Log::info('=== PatientAuth Middleware END - Access Granted ===', [
            'user_id' => $user->id,
            'url' => $request->fullUrl(),
        ]);

        return $next($request);
    }

    /**
     * Log patient dashboard access for audit purposes
     *
     * @param Request $request
     * @param int|null $userId
     * @param string $action
     * @param array $additionalData
     * @return void
     */
    private function logPatientAccess(Request $request, $userId = null, $action = 'access', $additionalData = [])
    {
        $logData = [
            'action' => $action,
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => Carbon::now()->toDateTimeString(),
            'session_id' => session()->getId(),
        ];

        // Add any additional data
        if (!empty($additionalData)) {
            $logData['additional_data'] = $additionalData;
        }

        // Log to Laravel log with patient_dashboard channel
        Log::channel('single')->info('Patient Dashboard Access', $logData);

        // Also log to activity log if available (for audit trail)
        if (class_exists('\Spatie\Activitylog\Models\Activity')) {
            try {
                activity('patient_dashboard')
                    ->causedBy($userId ? \App\Models\User::find($userId) : null)
                    ->withProperties($logData)
                    ->log($action);
            } catch (\Exception $e) {
                // Silently fail if activity log is not properly configured
                Log::warning('Failed to log to activity log: ' . $e->getMessage());
            }
        }
    }
}