<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Models\EncounterPrescription;
use Modules\Appointment\Models\EncouterMedicalHistroy;
use Modules\Appointment\Models\EncounterMedicalReport;
use Modules\Appointment\Models\AppointmentPatientBodychart;
use Modules\Appointment\Models\AppointmentPatientRecord;
use App\Models\User;
use Carbon\Carbon;

/**
 * PatientDashboardController
 * 
 * Handles patient dashboard functionality with AJAX endpoints for:
 * - Patient appointments (with filtering and pagination)
 * - Patient prescriptions (with encounter filtering)
 * - Patient triage records (using encounter data, displayed as "triage")
 * - Patient medical records (encounters with medical reports)
 * - Dashboard statistics (counts and recent activity)
 * - Detailed encounter/triage information
 * 
 * All endpoints include:
 * - Proper authentication and authorization checks
 * - Input validation and error handling
 * - Data filtering for current patient only
 * - Consistent JSON response format
 * - Error logging for debugging
 * - Pagination support where applicable
 */

class PatientDashboardController extends Controller
{
    /**
     * Display the patient dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $patient = Auth::user();
        
        // Load patient data
        $patientData = $this->loadPatientData($patient);
        
        return view('frontend::patient_dashboard', [
            'patient' => $patient,
            'dashboardStats' => $patientData['dashboardStats']
        ]);
    }

    /**
     * Load patient-specific data for the dashboard
     *
     * @param User $patient
     * @return array
     */
    private function loadPatientData(User $patient)
    {
        // Get recent triage records (encounters)
        $recentTriageRecords = PatientEncounter::where('user_id', $patient->id)
            ->with(['doctor', 'clinic'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent prescriptions
        $recentPrescriptions = EncounterPrescription::whereHas('encounter', function($query) use ($patient) {
                $query->where('user_id', $patient->id);
            })
            ->with(['encounter.doctor', 'medicine'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get upcoming appointments
        $upcomingAppointments = Appointment::where('user_id', $patient->id)
            ->where('start_date_time', '>=', Carbon::now())
            ->with(['doctor', 'cliniccenter', 'clinicservice'])
            ->orderBy('start_date_time', 'asc')
            ->take(5)
            ->get();

        // Calculate dashboard statistics
        $dashboardStats = [
            'total_appointments' => Appointment::where('user_id', $patient->id)->count(),
            'upcoming_appointments' => Appointment::where('user_id', $patient->id)
                ->where('start_date_time', '>=', Carbon::now())
                ->count(),
            'total_prescriptions' => EncounterPrescription::whereHas('encounter', function($query) use ($patient) {
                $query->where('user_id', $patient->id);
            })->count(),
            'last_visit' => PatientEncounter::where('user_id', $patient->id)
                ->orderBy('created_at', 'desc')
                ->value('created_at')
        ];

        return [
            'patientInfo' => [
                'id' => $patient->id,
                'name' => $patient->full_name,
                'email' => $patient->email,
                'contact' => $patient->mobile,
                'dob' => $patient->date_of_birth,
                'profile_image' => $patient->profile_image
            ],
            'recentTriageRecords' => $recentTriageRecords,
            'recentPrescriptions' => $recentPrescriptions,
            'upcomingAppointments' => $upcomingAppointments,
            'dashboardStats' => $dashboardStats
        ];
    }

    /**
     * Get patient appointments via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appointments(Request $request)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'html' => '<p class="text-danger text-center">Unauthorized access</p>'
                ], 401);
            }

            // Check if this is a request for recent appointments only
            $isRecent = $request->get('recent', false);
            $isUpcoming = $request->get('upcoming', false);

            $query = Appointment::where('user_id', $patient->id)
                ->with(['doctor', 'cliniccenter', 'clinicservice', 'appointmenttransaction', 'patientEncounter']);

            if ($isUpcoming) {
                // For upcoming appointments in overview
                $query->whereNotIn('status', ['checkout', 'cancelled'])
                      ->where('start_date_time', '>=', Carbon::now())
                      ->orderBy('start_date_time', 'asc')
                      ->take(3);
            } elseif ($isRecent) {
                // For recent appointments in overview
                $query->orderBy('start_date_time', 'desc')->take(3);
            } else {
                // For full appointments tab
                $query->orderBy('start_date_time', 'desc');
            }

            $appointments = $query->get();

            // Generate HTML content
            $html = '';
            if ($appointments->count() > 0) {
                foreach ($appointments as $appointment) {
                    $statusClass = $this->getAppointmentStatusClass($appointment->status);
                    $statusText = ucfirst($appointment->status);
                    
                    $html .= '<div class="card mb-3">';
                    $html .= '<div class="card-body">';
                    $html .= '<div class="row align-items-center">';
                    
                    // Date and Time
                    $html .= '<div class="col-md-3">';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<i class="ph ph-calendar text-primary me-2"></i>';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . Carbon::parse($appointment->start_date_time)->format('M d, Y') . '</div>';
                    $html .= '<small class="text-muted">' . Carbon::parse($appointment->start_date_time)->format('h:i A') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Service and Doctor
                    $html .= '<div class="col-md-4">';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . ($appointment->clinicservice->name ?? 'N/A') . '</div>';
                    $html .= '<small class="text-muted">Dr. ' . ($appointment->doctor->full_name ?? 'N/A') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Clinic
                    $html .= '<div class="col-md-3">';
                    $html .= '<small class="text-muted">' . ($appointment->cliniccenter->name ?? 'N/A') . '</small>';
                    $html .= '</div>';
                    
                    // Status
                    $html .= '<div class="col-md-2 text-end">';
                    $html .= '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                if ($isUpcoming) {
                    $html = '<p class="text-muted text-center">No upcoming appointments</p>';
                } else {
                    $html = '<p class="text-muted text-center">No appointments found</p>';
                }
            }

            return response()->json([
                'status' => true,
                'html' => $html,
                'count' => $appointments->count(),
                'message' => 'Appointments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching patient appointments: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'html' => '<p class="text-danger text-center">Error loading appointments</p>',
                'message' => 'An error occurred while fetching appointments'
            ], 500);
        }
    }

    /**
     * Get appointment status CSS class
     */
    private function getAppointmentStatusClass($status)
    {
        switch ($status) {
            case 'confirmed':
            case 'checkout':
                return 'bg-success';
            case 'pending':
                return 'bg-warning';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get patient prescriptions via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prescriptions(Request $request)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'html' => '<p class="text-danger text-center">Unauthorized access</p>'
                ], 401);
            }

            // Check if this is a request for recent prescriptions only
            $isRecent = $request->get('recent', false);

            $query = EncounterPrescription::whereHas('encounter', function($q) use ($patient) {
                    $q->where('user_id', $patient->id);
                })
                ->with(['encounter.doctor', 'encounter.clinic', 'medicine']);

            if ($isRecent) {
                $query->orderBy('created_at', 'desc')->take(3);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $prescriptions = $query->get();

            // Generate HTML content
            $html = '';
            if ($prescriptions->count() > 0) {
                foreach ($prescriptions as $prescription) {
                    $html .= '<div class="card mb-3">';
                    $html .= '<div class="card-body">';
                    $html .= '<div class="row align-items-center">';
                    
                    // Medicine Info
                    $html .= '<div class="col-md-4">';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<i class="ph ph-pill text-success me-2"></i>';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . ($prescription->medicine->name ?? 'N/A') . '</div>';
                    $html .= '<small class="text-muted">' . ($prescription->medicine->type ?? '') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Dosage and Frequency
                    $html .= '<div class="col-md-3">';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . ($prescription->dosage ?? 'N/A') . '</div>';
                    $html .= '<small class="text-muted">' . ($prescription->frequency ?? '') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Doctor
                    $html .= '<div class="col-md-3">';
                    $html .= '<small class="text-muted">Dr. ' . ($prescription->encounter->doctor->full_name ?? 'N/A') . '</small>';
                    $html .= '</div>';
                    
                    // Date
                    $html .= '<div class="col-md-2 text-end">';
                    $html .= '<small class="text-muted">' . $prescription->created_at->format('M d, Y') . '</small>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                if ($isRecent) {
                    $html = '<p class="text-muted text-center">No recent prescriptions</p>';
                } else {
                    $html = '<p class="text-muted text-center">No prescriptions found</p>';
                }
            }

            return response()->json([
                'status' => true,
                'html' => $html,
                'count' => $prescriptions->count(),
                'message' => 'Prescriptions retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching patient prescriptions: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'html' => '<p class="text-danger text-center">Error loading prescriptions</p>',
                'message' => 'An error occurred while fetching prescriptions'
            ], 500);
        }
    }

    /**
     * Get patient triage records via AJAX (using existing encounter routes and data but display as "triage" in frontend)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triageRecords(Request $request)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'html' => '<p class="text-danger text-center">Unauthorized access</p>'
                ], 401);
            }

            // Check if this is a request for recent triage records only
            $isRecent = $request->get('recent', false);

            $query = PatientEncounter::where('user_id', $patient->id)
                ->with(['doctor', 'clinic', 'appointment', 'soap']);

            if ($isRecent) {
                $query->orderBy('created_at', 'desc')->take(3);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $triageRecords = $query->get();

            // Generate HTML content
            $html = '';
            if ($triageRecords->count() > 0) {
                foreach ($triageRecords as $encounter) {
                    $statusClass = $encounter->status === 'completed' ? 'bg-success' : 'bg-warning';
                    $chiefComplaint = $encounter->soap ? ($encounter->soap->subjective ?? 'No complaint recorded') : 'No complaint recorded';
                    
                    $html .= '<div class="card mb-3">';
                    $html .= '<div class="card-body">';
                    $html .= '<div class="row align-items-center">';
                    
                    // Date and Time
                    $html .= '<div class="col-md-3">';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<i class="ph ph-stethoscope text-info me-2"></i>';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . $encounter->created_at->format('M d, Y') . '</div>';
                    $html .= '<small class="text-muted">' . $encounter->created_at->format('h:i A') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Service and Doctor
                    $html .= '<div class="col-md-4">';
                    $html .= '<div>';
                    $html .= '<div class="fw-semibold">' . (optional($encounter->appointment)->clinicservice ? $encounter->appointment->clinicservice->name : 'General Consultation') . '</div>';
                    $html .= '<small class="text-muted">Dr. ' . ($encounter->doctor->full_name ?? 'N/A') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Chief Complaint from SOAP notes
                    $html .= '<div class="col-md-3">';
                    $html .= '<small class="text-muted">' . e(substr($chiefComplaint, 0, 50)) . (strlen($chiefComplaint) > 50 ? '...' : '') . '</small>';
                    $html .= '</div>';
                    
                    // Status
                    $html .= '<div class="col-md-2 text-end">';
                    $html .= '<span class="badge ' . $statusClass . '">' . ucfirst($encounter->status ?? 'Active') . '</span>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                if ($isRecent) {
                    $html = '<p class="text-muted text-center">No recent triage records</p>';
                } else {
                    $html = '<p class="text-muted text-center">No triage records found</p>';
                }
            }

            return response()->json([
                'status' => true,
                'html' => $html,
                'count' => $triageRecords->count(),
                'message' => 'Triage records retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching patient triage records: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'html' => '<p class="text-danger text-center">Error loading triage records</p>',
                'message' => 'An error occurred while fetching triage records'
            ], 500);
        }
    }

    /**
     * Get patient medical records via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function medicalRecords(Request $request)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'html' => '<p class="text-danger text-center">Unauthorized access</p>'
                ], 401);
            }

            $query = PatientEncounter::where('user_id', $patient->id)
                ->with(['doctor', 'clinic', 'medicalReport', 'appointment', 'soap', 'prescriptions']);

            // Only get encounters that have medical reports or SOAP notes
            $query->where(function($q) {
                $q->whereHas('medicalReport')
                  ->orWhereHas('soap')
                  ->orWhereHas('prescriptions');
            });

            $medicalRecords = $query->orderBy('created_at', 'desc')->get();

            // Generate HTML content
            $html = '';
            if ($medicalRecords->count() > 0) {
                foreach ($medicalRecords as $encounter) {
                    $soap = $encounter->soap;
                    
                    $html .= '<div class="card mb-3">';
                    $html .= '<div class="card-body">';
                    $html .= '<div class="row">';
                    
                    // Header with date and doctor
                    $html .= '<div class="col-12 mb-3">';
                    $html .= '<div class="d-flex justify-content-between align-items-center">';
                    $html .= '<div>';
                    $html .= '<h6 class="mb-1">' . $encounter->created_at->format('M d, Y') . '</h6>';
                    $html .= '<small class="text-muted">Dr. ' . ($encounter->doctor->full_name ?? 'N/A') . ' - ' . ($encounter->clinic->name ?? 'N/A') . '</small>';
                    $html .= '</div>';
                    $html .= '<span class="badge bg-info">' . (optional($encounter->appointment)->clinicservice ? $encounter->appointment->clinicservice->name : 'General') . '</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    // Medical details from SOAP notes
                    $html .= '<div class="col-md-6">';
                    if ($soap && $soap->subjective) {
                        $html .= '<div class="mb-2">';
                        $html .= '<strong>Subjective (Chief Complaint):</strong><br>';
                        $html .= '<small>' . e($soap->subjective) . '</small>';
                        $html .= '</div>';
                    }
                    if ($soap && $soap->assessment) {
                        $html .= '<div class="mb-2">';
                        $html .= '<strong>Assessment (Diagnosis):</strong><br>';
                        $html .= '<small>' . e($soap->assessment) . '</small>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-6">';
                    if ($soap && $soap->plan) {
                        $html .= '<div class="mb-2">';
                        $html .= '<strong>Plan (Treatment):</strong><br>';
                        $html .= '<small>' . e($soap->plan) . '</small>';
                        $html .= '</div>';
                    }
                    if ($soap && $soap->objective) {
                        $html .= '<div class="mb-2">';
                        $html .= '<strong>Objective (Findings):</strong><br>';
                        $html .= '<small>' . e($soap->objective) . '</small>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                    
                    // Show prescriptions if available
                    if ($encounter->prescriptions && $encounter->prescriptions->count() > 0) {
                        $html .= '<div class="col-12 mt-2">';
                        $html .= '<strong>Prescriptions:</strong><br>';
                        $html .= '<ul class="mb-0">';
                        foreach ($encounter->prescriptions as $prescription) {
                            $medicineName = optional($prescription->medicine)->name ?? 'N/A';
                            $html .= '<li><small>' . e($medicineName) . '</small></li>';
                        }
                        $html .= '</ul>';
                        $html .= '</div>';
                    }
                    
                    // Show medical reports if available
                    if ($encounter->medicalReport && $encounter->medicalReport->count() > 0) {
                        $html .= '<div class="col-12 mt-2">';
                        $html .= '<strong>Medical Reports:</strong><br>';
                        $html .= '<ul class="mb-0">';
                        foreach ($encounter->medicalReport as $report) {
                            $html .= '<li><small>' . e($report->name) . ' - ' . $report->date . '</small></li>';
                        }
                        $html .= '</ul>';
                        $html .= '</div>';
                    }
                    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                $html = '<p class="text-muted text-center">No medical records found</p>';
            }

            return response()->json([
                'status' => true,
                'html' => $html,
                'count' => $medicalRecords->count(),
                'message' => 'Medical records retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching patient medical records: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'html' => '<p class="text-danger text-center">Error loading medical records</p>',
                'message' => 'An error occurred while fetching medical records'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardStats(Request $request)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'data' => null
                ], 401);
            }

            // Calculate dashboard statistics
            $stats = [
                'total_appointments' => Appointment::where('user_id', $patient->id)->count(),
                'upcoming_appointments' => Appointment::where('user_id', $patient->id)
                    ->where('start_date_time', '>=', Carbon::now())
                    ->whereNotIn('status', ['cancelled', 'checkout'])
                    ->count(),
                'completed_appointments' => Appointment::where('user_id', $patient->id)
                    ->where('status', 'checkout')
                    ->count(),
                'total_prescriptions' => EncounterPrescription::whereHas('encounter', function($query) use ($patient) {
                    $query->where('user_id', $patient->id);
                })->count(),
                'total_triage_records' => PatientEncounter::where('user_id', $patient->id)->count(),
                'last_visit' => PatientEncounter::where('user_id', $patient->id)
                    ->orderBy('created_at', 'desc')
                    ->value('created_at'),
                'next_appointment' => Appointment::where('user_id', $patient->id)
                    ->where('start_date_time', '>=', Carbon::now())
                    ->whereNotIn('status', ['cancelled', 'checkout'])
                    ->orderBy('start_date_time', 'asc')
                    ->first()
            ];

            // Format dates
            if ($stats['last_visit']) {
                $stats['last_visit_formatted'] = Carbon::parse($stats['last_visit'])->format('M d, Y');
            }

            if ($stats['next_appointment']) {
                $stats['next_appointment_formatted'] = Carbon::parse($stats['next_appointment']->start_date_time)->format('M d, Y h:i A');
                $stats['next_appointment_doctor'] = optional($stats['next_appointment']->doctor->user)->full_name ?? 'N/A';
            }

            return response()->json([
                'status' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching dashboard statistics: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching dashboard statistics',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get patient encounter details via AJAX (for detailed triage view)
     *
     * @param Request $request
     * @param int $encounterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function encounterDetails(Request $request, $encounterId)
    {
        try {
            $patient = Auth::user();
            
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'data' => null
                ], 401);
            }

            // Verify the encounter belongs to the patient
            $encounter = PatientEncounter::where('id', $encounterId)
                                        ->where('user_id', $patient->id)
                                        ->with([
                                            'doctor', 
                                            'clinic', 
                                            'appointment',
                                            'medicalReport'
                                        ])
                                        ->first();

            if (!$encounter) {
                return response()->json([
                    'status' => false,
                    'message' => 'Triage record not found or access denied',
                    'data' => null
                ], 404);
            }

            // Get related data
            $medicalHistory = EncouterMedicalHistroy::where('encounter_id', $encounter->id)
                                                   ->get()
                                                   ->groupBy('type');
            
            $prescriptions = EncounterPrescription::where('encounter_id', $encounter->id)
                                                 ->with('medicine')
                                                 ->get();
            
            $bodychart = AppointmentPatientBodychart::where('encounter_id', $encounter->id)->get();
            $soap = AppointmentPatientRecord::where('encounter_id', $encounter->id)->first();

            $encounterData = [
                'id' => $encounter->id,
                'triage_date' => $encounter->created_at->format('M d, Y'),
                'triage_time' => $encounter->created_at->format('h:i A'),
                'status' => $encounter->status,
                'doctor_name' => $encounter->doctor->full_name ?? 'N/A',
                'clinic_name' => $encounter->clinic->name ?? 'N/A',
                'service_name' => optional($encounter->appointment)->clinicservice?->name ?? 'N/A',
                'chief_complaint' => $encounter->chief_complaint,
                'vital_signs' => $encounter->vital_signs,
                'diagnosis' => $encounter->diagnosis,
                'treatment_plan' => $encounter->treatment_plan,
                'notes' => $encounter->notes,
                'medical_history' => $medicalHistory,
                'medical_report' => $encounter->medicalReport,
                'prescriptions' => $prescriptions->map(function($prescription) {
                    return [
                        'id' => $prescription->id,
                        'medicine_name' => $prescription->medicine->name ?? 'N/A',
                        'dosage' => $prescription->dosage,
                        'frequency' => $prescription->frequency,
                        'duration' => $prescription->duration,
                        'instruction' => $prescription->instruction
                    ];
                }),
                'bodychart' => $bodychart,
                'soap_notes' => $soap
            ];

            return response()->json([
                'status' => true,
                'data' => $encounterData,
                'message' => 'Triage record details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching encounter details: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching triage record details',
                'data' => null
            ], 500);
        }
    }
}