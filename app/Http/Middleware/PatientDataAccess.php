<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * PatientDataAccess Middleware
 * 
 * Ensures all dashboard data requests validate patient identity and implement
 * proper authorization checks for patient-specific data access.
 */
class PatientDataAccess
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
        $user = Auth::user();

        // Ensure user is authenticated and is a patient
        if (!$user || $user->user_type !== 'user') {
            return $this->unauthorizedResponse($request, 'Invalid patient authentication');
        }

        // Validate and sanitize input parameters
        $this->validateAndSanitizeInput($request);

        // Check for patient ID parameter in route and ensure it matches authenticated user
        $routePatientId = $request->route('patient_id') ?? $request->route('user_id');
        if ($routePatientId && (int)$routePatientId !== $user->id) {
            $this->logUnauthorizedAccess($request, $user->id, 'patient_id_mismatch', [
                'requested_patient_id' => $routePatientId,
                'authenticated_user_id' => $user->id
            ]);
            
            return $this->unauthorizedResponse($request, 'Access denied to requested patient data');
        }

        // Validate encounter_id parameter if present (ensure it belongs to the patient)
        $encounterId = $request->input('encounter_id') ?? $request->route('encounter_id');
        if ($encounterId) {
            if (!$this->validateEncounterAccess($user->id, $encounterId)) {
                $this->logUnauthorizedAccess($request, $user->id, 'invalid_encounter_access', [
                    'encounter_id' => $encounterId
                ]);
                
                return $this->unauthorizedResponse($request, 'Access denied to requested encounter data');
            }
        }

        // Validate appointment_id parameter if present (ensure it belongs to the patient)
        $appointmentId = $request->input('appointment_id') ?? $request->route('appointment_id');
        if ($appointmentId) {
            if (!$this->validateAppointmentAccess($user->id, $appointmentId)) {
                $this->logUnauthorizedAccess($request, $user->id, 'invalid_appointment_access', [
                    'appointment_id' => $appointmentId
                ]);
                
                return $this->unauthorizedResponse($request, 'Access denied to requested appointment data');
            }
        }

        // Log successful data access validation
        $this->logDataAccess($request, $user->id, 'data_access_validated');

        return $next($request);
    }

    /**
     * Validate and sanitize input parameters
     *
     * @param Request $request
     * @return void
     */
    private function validateAndSanitizeInput(Request $request)
    {
        // Define validation rules for common parameters
        $rules = [
            'page' => 'sometimes|integer|min:1|max:1000',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'status' => 'sometimes|string|max:50|regex:/^[a-zA-Z_]+$/',
            'search' => 'sometimes|string|max:255',
            'encounter_id' => 'sometimes|integer|min:1',
            'appointment_id' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ];

        // Validate input
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $this->logValidationFailure($request, Auth::id(), $validator->errors()->toArray());
            
            if ($request->expectsJson()) {
                abort(422, 'Invalid input parameters');
            } else {
                abort(400, 'Invalid request parameters');
            }
        }

        // Sanitize search input if present
        if ($request->has('search')) {
            $search = strip_tags($request->input('search'));
            $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
            $request->merge(['search' => $search]);
        }
    }

    /**
     * Validate that an encounter belongs to the specified patient
     *
     * @param int $patientId
     * @param int $encounterId
     * @return bool
     */
    private function validateEncounterAccess($patientId, $encounterId)
    {
        try {
            $encounter = \Modules\Appointment\Models\PatientEncounter::where('id', $encounterId)
                                                                    ->where('user_id', $patientId)
                                                                    ->first();
            return $encounter !== null;
        } catch (\Exception $e) {
            Log::error('Error validating encounter access: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate that an appointment belongs to the specified patient
     *
     * @param int $patientId
     * @param int $appointmentId
     * @return bool
     */
    private function validateAppointmentAccess($patientId, $appointmentId)
    {
        try {
            $appointment = \Modules\Appointment\Models\Appointment::where('id', $appointmentId)
                                                                 ->where('user_id', $patientId)
                                                                 ->first();
            return $appointment !== null;
        } catch (\Exception $e) {
            Log::error('Error validating appointment access: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Return unauthorized response
     *
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function unauthorizedResponse(Request $request, $message = 'Unauthorized access')
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => $message,
                'data' => null
            ], 403);
        }
        
        return redirect()->route('patient.dashboard')
                       ->with('error', $message);
    }

    /**
     * Log unauthorized access attempt
     *
     * @param Request $request
     * @param int $userId
     * @param string $reason
     * @param array $additionalData
     * @return void
     */
    private function logUnauthorizedAccess(Request $request, $userId, $reason, $additionalData = [])
    {
        $logData = [
            'reason' => $reason,
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'parameters' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ];

        if (!empty($additionalData)) {
            $logData['additional_data'] = $additionalData;
        }

        Log::warning('Unauthorized Patient Data Access Attempt', $logData);

        // Log to activity log if available
        if (class_exists('\Spatie\Activitylog\Models\Activity')) {
            try {
                activity('patient_data_access')
                    ->causedBy(\App\Models\User::find($userId))
                    ->withProperties($logData)
                    ->log('unauthorized_access_attempt');
            } catch (\Exception $e) {
                Log::warning('Failed to log unauthorized access to activity log: ' . $e->getMessage());
            }
        }
    }

    /**
     * Log successful data access validation
     *
     * @param Request $request
     * @param int $userId
     * @param string $action
     * @return void
     */
    private function logDataAccess(Request $request, $userId, $action)
    {
        $logData = [
            'action' => $action,
            'user_id' => $userId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::info('Patient Data Access', $logData);
    }

    /**
     * Log validation failure
     *
     * @param Request $request
     * @param int $userId
     * @param array $errors
     * @return void
     */
    private function logValidationFailure(Request $request, $userId, $errors)
    {
        $logData = [
            'user_id' => $userId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'validation_errors' => $errors,
            'input_data' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::warning('Patient Data Access Validation Failure', $logData);
    }
}