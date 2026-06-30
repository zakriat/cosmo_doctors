<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsCategory;
use Yajra\DataTables\DataTables;
use Modules\Tax\Models\Tax;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorSession;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use App\Models\Holiday;
use App\Models\User;
use App\Models\DoctorHoliday;
use Illuminate\Support\Facades\Route;
class ServiceController extends Controller
{
    public function servicesList(Request $request)
    {
        $doctor_id = $request->query('doctor_id');
        $clinic_id = $request->query('clinic_id');
        $category_id = $request->query('category_id');

        $clinics = Clinics::checkMultivendor()->with('clinicdoctor', 'specialty', 'clinicdoctor', 'receptionist')->where('status', 1)->get();
        $categories = ClinicsCategory::whereNull('parent_id')->where('status', 1)->get();
        $service = ClinicsService::with('sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice')
            ->where('status', 1)
            ->get();
        
        // Calculate total prices including inclusive tax
        $prices = $service->map(function($item) {
            return $item->charges + ($item->inclusive_tax_price ?? 0);
        });
        
        $minPrice = 0;
        $maxPrice = $prices->max();

        $interval = 50;
        $priceRanges = [];
        if ($maxPrice <= $interval) {
            $priceRanges[] = [$minPrice, $maxPrice];
        } else {
            for ($i = $minPrice; $i <= $maxPrice; $i += $interval) {
                $priceRanges[] = [$i, min($i + $interval, $maxPrice)];
            }
        }

        return view('frontend::services', compact('clinics', 'categories', 'doctor_id', 'clinic_id', 'priceRanges', 'category_id'));

    }

    public function index_data(Request $request)
    {

        $service_list = ClinicsService::CheckMultivendor()->with('category');

        $search = $request->input('search');
        if ($search) {
            $service_list = $service_list->where('name', 'like', '%' . $search . '%');
        }
        $doctor_id = $request->query('doctor_id');
        if ($doctor_id) {
            $service_list = $service_list->whereHas('doctor_service', function ($query) use ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            });
        }

        $category_id = $request->query('category_id');
        if ($category_id && $request->has('filter.category_id') && $request->input('filter.category_id')) {
            $service_list = $service_list->where('category_id', $category_id);
        }

        $clinic_id = $request->query('clinic_id');
        if ($clinic_id) {
            $service_list = ClinicsService::with('category', 'ClinicServiceMapping')
                ->where('type', 'in_clinic')
                ->whereHas('ClinicServiceMapping', function ($query) use ($clinic_id) {
                    $query->where('clinic_id', $clinic_id);
                });
        }
        if ($request->has('filter.clinic_id') && $request->input('filter.clinic_id') ) {
            $clinicId = $request->input('filter.clinic_id');
            $service_list = $service_list
                ->where('type', 'in_clinic')
                ->whereHas('ClinicServiceMapping', function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                });
        }



        if ($request->has('filter.price') && $request->input('filter.price')) {
            $priceRange = $request->input('filter.price');
            [$minPrice, $maxPrice] = explode('-', $priceRange);

            if($minPrice == $maxPrice) {
                $service_list = $service_list->where(function($query) use ($minPrice) {
                    $query->whereRaw('(charges + COALESCE(inclusive_tax_price, 0)) > ?', [(float)$minPrice]);
                });
            } else {
                
                $service_list = $service_list->where(function($query) use ($minPrice, $maxPrice) {
                    $query->whereRaw('(charges + COALESCE(inclusive_tax_price, 0)) BETWEEN ? AND ?', [
                        (float)$minPrice,
                        (float)$maxPrice
                    ]);
                });
            }
        }

        if ($request->has('filter.category_id') && $request->input('filter.category_id')) {
            $service_list = $service_list->where('category_id', $request->input('filter.category_id'));
        }
        $service_list = $service_list->where('status', 1);

        $services = $service_list->orderBy('updated_at', 'desc');

        return DataTables::of($services)
            ->addColumn('card', function ($service) {
                $inclusive_tax = $service->charges;
                if($service->is_inclusive_tax == 1 && $service->inclusive_tax_price > 0) {
                    $inclusive_tax = $service->charges + $service->inclusive_tax_price;
                }
                $discount_amount =0;
                if ($service->discount) {
                    $discount_amount = ($service->discount_type == 'percentage')
                        ? $inclusive_tax * $service->discount_value / 100
                        : $service->discount_value;

                }

                // Calculate tax on the discounted price, not on original price
                // $discounted_price = $service->charges - $discount_amount;
                // if ($service->charges > 0 && $service->inclusive_tax_price > 0) {
                //     $tax_rate = $service->inclusive_tax_price / $service->charges;
                //     $tax_on_discounted = $discounted_price * $tax_rate;
                //     $service->payable_amount = $discounted_price + $tax_on_discounted;
                // } else {
                //     $service->payable_amount = $discounted_price;
                // }
                $service->payable_amount = $inclusive_tax - $discount_amount;
                return view('frontend::components.card.service_card', compact('service'))->render();
            })
            ->rawColumns(['card'])
            ->make(true);

    }

    public function serviceDetails($id)
    {
        $service = ClinicsService::CheckMultivendor()->where('id', $id)->with('category', 'sub_category', 'ClinicServiceMapping', 'doctor_service', 'systemservice')->first();
        $amount = $service->charges;
        if($service->is_inclusive_tax == 1) {
            $amount = $service->charges + $service->inclusive_tax_price;
        } 

        $discount_amount =0;
        if ($service->discount == 1) {
            $discount_amount = ($service->discount_type == 'percentage')
                ? $amount * $service->discount_value / 100
                : $service->discount_value;

        }

        // Calculate tax on the discounted price, not on original price
        $discounted_price = $amount - $discount_amount;
        $service->payable_amount = $discounted_price;
        
// dd($service);
        return view('frontend::service_detail', compact('service'));
    }

    public function booking($id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login-page', ['redirect_to' => $request->fullUrl()]));
        }
        
        // ✅ Clear any old pending audio IDs from previous booking sessions
        // This prevents stale audio from being linked to new appointments
        if (session()->has('pending_audio_ids')) {
            \Log::info('Clearing old pending audio IDs from session', [
                'old_ids' => session()->get('pending_audio_ids'),
                'user_id' => auth()->id()
            ]);
            session()->forget('pending_audio_ids');
        }
        
        $previousUrl = url()->previous();
        $selectedService = ClinicsService::CheckMultivendor()->findOrFail($id);
        $serviceId = $selectedService->id;
        
        // Initialize variables
        $clinicId = null;
        $doctorId = null;
        $categoryId = null;
        $selectedClinic = null;
        $selectedDoctor = null;
        $selectedCategory = null;
        $currentStep = 0;
        $hasCategories = false;
        
        // Check for category selection
        if ($request->has('category_id')) {
            $categoryId = $request->query('category_id');
            $selectedCategory = ClinicsCategory::findOrFail($categoryId);
            $currentStep = 1; // Move to clinic selection after category
        }
        
        // Check for clinic selection
        if ($request->has('clinic_id')) {
            $clinicId = $request->query('clinic_id');
            $selectedClinic = Clinics::CheckMultivendor()->findOrFail($clinicId);
            $currentStep = $categoryId ? 2 : 1; // Adjust step based on category selection
        } else if (preg_match('/clinic-details\/(\d+)/', $previousUrl, $matches)) {
            $clinicId = $matches[1];
            $selectedClinic = Clinics::CheckMultivendor()->findOrFail($clinicId);
            $currentStep = $categoryId ? 2 : 1;
        }
        
        // Check for doctor selection
        if (preg_match('/doctor-details\/(\d+)/', $previousUrl, $matches)) {
            $doctorId = $matches[1];
            $selectedDoctor = Doctor::CheckMultivendor()->with('user')->findOrFail($doctorId);
            $currentStep = $categoryId ? 3 : 2;
        }
        
        // Determine if service has categories (enhanced flow)
        $hasCategories = ClinicsCategory::where('parent_id', $serviceId)->exists();
        
        // Debug: Log the hasCategories value
        \Log::info('Booking Debug', [
            'serviceId' => $serviceId,
            'hasCategories' => $hasCategories,
            'categoriesCount' => ClinicsCategory::where('parent_id', $serviceId)->count()
        ]);
        
        // Build tabs based on service structure
        if ($hasCategories) {
            // Enhanced flow: Service → Category → Clinic → Doctor (conditional) → DateTime/Payment
            $tabs = [
                ['index' => 0, 'label' => __('frontend.select_category'), 'value' => 'Select Category'],
                ['index' => 1, 'label' => __('frontend.choose_clinics'), 'value' => 'Choose Clinics'],
                ['index' => 2, 'label' => __('frontend.choose_doctors'), 'value' => 'Choose Doctors'],
                ['index' => 3, 'label' => __('frontend.choose_date_time_payment'), 'value' => 'Choose Date, Time, Payment'],
            ];
            
            // Adjust doctor step visibility based on category requirements
            if ($selectedCategory && $selectedCategory->service_classification === 'no_doctor_required') {
                // Skip doctor step for services that don't require doctors
                $tabs = [
                    ['index' => 0, 'label' => __('frontend.select_category'), 'value' => 'Select Category'],
                    ['index' => 1, 'label' => __('frontend.choose_clinics'), 'value' => 'Choose Clinics'],
                    ['index' => 2, 'label' => __('frontend.choose_date_time_payment'), 'value' => 'Choose Date, Time, Payment'],
                ];
            }
        } else {
            // Original flow: Service → Clinic → Doctor → DateTime/Payment
            $tabs = [
                ['index' => 0, 'label' => __('frontend.choose_clinics'), 'value' => 'Choose Clinics'],
                ['index' => 1, 'label' => __('frontend.choose_doctors'), 'value' => 'Choose Doctors'],
                ['index' => 2, 'label' => __('frontend.choose_date_time_payment'), 'value' => 'Choose Date, Time, Payment'],
            ];
        }
        
        // Get current step from session if not determined by URL
        if (!$categoryId && !$clinicId && !$doctorId) {
            $currentStep = session('currentStep', 0);
        }
        
        // Check if only one clinic exists for auto-select
        $clinics = Clinics::where('status', 1)->get();
        $autoSelectClinic = $clinics->count() === 1;
        $selectedClinicId = $autoSelectClinic ? $clinics->first()->id : null;
        $selectedClinicName = $autoSelectClinic ? $clinics->first()->name : null;
        
        $selectedService = ClinicsService::CheckMultivendor()->findOrFail($id);
        $serviceId = $selectedService->id;
        $paymentMethods = [];

        // List of available payment methods
        $paymentMethodsList = [
            'cash' => 'cash_payment_method',  // Always available
            'Wallet' => 'wallet_payment_method', // Always available
            'Stripe' => 'str_payment_method',
            'Paystack' => 'paystack_payment_method',
            'PayPal' => 'paypal_payment_method',
            'Flutterwave' => 'flutterwave_payment_method',
            'Airtel' => 'airtel_payment_method',
            'PhonePay' => 'phonepay_payment_method',
            'Midtrans' => 'midtrans_payment_method',
            'Cinet' => 'cinet_payment_method',
            'Sadad' => 'sadad_payment_method',
            'Razor Pay' => 'razor_payment_method',
        ];

        $enabledPaymentMethods = [];

        // If service type is not 'online', allow 'cash' and 'Wallet' by default.
        // If type is online, exclude 'cash'.
        if ($selectedService->type != 'online') {
            $enabledPaymentMethods = ['cash', 'Wallet'];
        } else {
            $enabledPaymentMethods = ['Wallet'];
        }

        if ($selectedService->is_enable_advance_payment == 1) {
            $enabledPaymentMethods = array_filter($enabledPaymentMethods, function($method) {
                return $method !== 'cash';
            });
        }
        // Iterate through all payment methods and check if they are enabled
        foreach ($paymentMethodsList as $displayName => $settingKey) {
            if (setting($settingKey, 0) == 1) { // Assuming 1 means enabled
                $enabledPaymentMethods[] = $displayName; // Add enabled methods to the list
            }

        }
        
        return view('frontend::booking', compact(
            'tabs', 
            'currentStep', 
            'selectedService', 
            'serviceId', 
            'selectedClinic', 
            'clinicId', 
            'selectedDoctor', 
            'doctorId', 
            'selectedCategory',
            'categoryId',
            'hasCategories',
            'autoSelectClinic',
            'selectedClinicId',
            'selectedClinicName',
            'previousUrl', 
            'enabledPaymentMethods'
        ));
    }

    /**
     * Transcribe audio file to text using Groq API
     */
    public function transcribeAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,ogg,m4a,webm,flac,mpeg,mpga|max:25600' // 25MB max
        ]);

        try {
            $startTime = microtime(true);
            
            // Store audio temporarily
            // $audioFile = $request->file('audio');
            // $audioPath = $audioFile->store(config('groq.temp_audio_path'));
            // $fullPath = storage_path('app/' . $audioPath);

            $tempDisk = 'local';
    
            $audioFile = $request->file('audio');
            $audioPath = $audioFile->store(config('groq.temp_audio_path'), $tempDisk);
            $fullPath = \Storage::disk($tempDisk)->path($audioPath);
            
            \Log::info('Groq audio transcription started', [
                'original_name' => $audioFile->getClientOriginalName(),
                'mime_type' => $audioFile->getMimeType(),
                'size' => $audioFile->getSize(),
            ]);
            
            // Initialize Groq service
            $groqService = new \App\Services\GroqSpeechService();
            
            // Transcribe with medical context
            $result = $groqService->transcribeAudio($fullPath, [
                'response_format' => 'verbose_json',
                'language' => 'en'
            ]);
            
            $transcription = $result['text'];
            $processingTime = $result['processing_time_ms'];
            
            // Store permanent audio file
            $permanentPath = $audioFile->store(config('groq.audio_storage_path') . '/' . auth()->id());
            
            // Extract medical entities
            $medicalEntities = $groqService->extractMedicalEntities($transcription);
            
            // Format for medical record
            $formattedText = $groqService->formatForMedicalRecord($result);
            
            // Get quality metrics
            $qualityMetrics = $groqService->getQualityMetrics($result);
            
            // Save transcription to database
            $audioTranscription = \App\Models\AudioTranscription::create([
                'user_id' => auth()->id(),
                'audio_file_path' => $permanentPath,
                'transcription_text' => $transcription,
                'original_text' => $transcription,
                'final_text' => $formattedText,
                'medical_categories' => $medicalEntities,
                'confidence_scores' => [
                    'groq' => $qualityMetrics['quality_score'] ?? 0.9,
                    'avg_logprob' => $qualityMetrics['avg_confidence'] ?? 0
                ],
                'duration_seconds' => $result['duration'] ?? 0,
                'model_used' => $result['model_used'],
                'processing_time_ms' => (int) $processingTime,
                'status' => 'completed',
            ]);
            
            // ✅ Store audio ID in session for later linking to appointment
            session()->push('pending_audio_ids', $audioTranscription->id);
            
            // Clean up temp file
            // \Storage::delete($audioPath);
            \Storage::disk($tempDisk)->delete($audioPath);
            
            \Log::info('Groq transcription completed', [
                'transcription_id' => $audioTranscription->id,
                'text_length' => strlen($transcription),
                'processing_time_ms' => $processingTime,
                'medical_entities' => count($medicalEntities),
                'quality_score' => $qualityMetrics['quality_score'] ?? 0,
                'stored_in_session' => true
            ]);
            
            return response()->json([
                'success' => true,
                'transcription' => $formattedText,
                'original_text' => $transcription,
                'medical_text' => $formattedText, // For JavaScript compatibility
                'combined_text' => $formattedText, // For JavaScript compatibility
                'transcription_id' => $audioTranscription->id,
                'audio_id' => $audioTranscription->id,
                'audio_file' => basename($permanentPath),
                'duration' => $result['duration'] ?? 0,
                'processing_time' => round($processingTime / 1000, 2),
                'model_used' => $result['model_used'],
                'medical_entities' => $medicalEntities,
                'categories' => $medicalEntities, // For JavaScript compatibility
                'quality_metrics' => $qualityMetrics,
                'category_colors' => config('groq.category_colors', []),
                'gemini_fallback_used' => false, // Groq is primary now
                'processing_times' => [
                    'groq_transcription' => $processingTime,
                    'total' => $processingTime
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Groq transcription failed: ' . $e->getMessage(), [
                'file_info' => isset($audioFile) ? [
                    'name' => $audioFile->getClientOriginalName(),
                    'mime' => $audioFile->getMimeType(),
                    'size' => $audioFile->getSize()
                ] : null,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            // Clean up temp file
            if (isset($audioPath)) {
                // \Storage::delete($audioPath);
                \Storage::disk($tempDisk)->delete($audioPath);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Transcription failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Enhanced transcribe audio with Groq (same as regular, kept for backward compatibility)
     */
    public function transcribeAudioEnhanced(Request $request)
    {
        // Groq already provides enhanced medical transcription
        // This method is kept for backward compatibility
        return $this->transcribeAudio($request);
    }

    /**
     * Get categories for a specific service (API endpoint)
     */
    public function getServiceCategories($serviceId)
    {
        try {
            $service = ClinicsService::CheckMultivendor()->findOrFail($serviceId);
            
            // Get categories where parent_id matches the service ID
            $categories = ClinicsCategory::where('parent_id', $serviceId)
                ->where('status', 1)
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'price' => $category->price ?? 0,
                        'requires_doctor' => $category->service_classification !== 'no_doctor_required',
                        'service_classification' => $category->service_classification ?? 'doctor_required'
                    ];
                });
            
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'has_categories' => $categories->count() > 0
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctors for a specific category (API endpoint)
     */
    public function getCategoryDoctors($categoryId)
    {
        try {
            $category = ClinicsCategory::findOrFail($categoryId);
            
            // Get doctors assigned to this category through service mappings
            $doctors = Doctor::CheckMultivendor()
                ->with('user')
                ->whereHas('doctor_service', function($query) use ($categoryId) {
                    $query->whereHas('service', function($serviceQuery) use ($categoryId) {
                        $serviceQuery->where('category_id', $categoryId);
                    });
                })
                ->where('status', 1)
                ->get()
                ->map(function($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->user->first_name . ' ' . $doctor->user->last_name,
                        'specialization' => $doctor->specialization ?? '',
                        'experience' => $doctor->experience ?? '',
                        'rating' => $doctor->rating ?? 0,
                        'avatar' => $doctor->user->avatar ?? null
                    ];
                });
            
            return response()->json([
                'success' => true,
                'doctors' => $doctors,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'requires_doctor' => $category->service_classification !== 'no_doctor_required'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctors by category for booking flow
     * 
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctorsByCategory($categoryId)
    {
        try {
            $category = ClinicsCategory::findOrFail($categoryId);
            
            // Get the single clinic (auto-selected)
            $clinic = Clinics::first();
            
            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clinic found'
                ], 404);
            }
            
            // Get doctors assigned to this category
            $doctors = Doctor::whereHas('categoryMappings', function($q) use ($categoryId, $clinic) {
                $q->where('category_id', $categoryId)
                  ->where('clinic_id', $clinic->id)
                  ->where('status', 1);
            })
            ->with([
                'user:id,first_name,last_name,email,mobile,avatar',
                'categoryMappings' => function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            ])
            ->where('status', 1)
            ->get();
            
            // Format doctor data
            $formattedDoctors = $doctors->map(function($doctor) use ($categoryId) {
                $categoryMapping = $doctor->categoryMappings->first();
                
                return [
                    'id' => $doctor->id,
                    'doctor_id' => $doctor->doctor_id,
                    'user' => $doctor->user,
                    'experience' => $doctor->experience,
                    'signature' => $doctor->signature,
                    'category_charges' => $categoryMapping ? $categoryMapping->charges : 0,
                    'avatar' => $doctor->user->avatar ?? null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'doctors' => $formattedDoctors,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'price' => $category->price,
                    'service_classification' => $category->service_classification
                ],
                'clinic' => [
                    'id' => $clinic->id,
                    'name' => $clinic->name
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting doctors by category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading doctors for this category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if only one clinic exists (for auto-select)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSingleClinic()
    {
        try {
            $clinics = Clinics::where('status', 1)->get();
            $isSingleClinic = $clinics->count() === 1;
            
            return response()->json([
                'success' => true,
                'is_single_clinic' => $isSingleClinic,
                'clinic' => $isSingleClinic ? [
                    'id' => $clinics->first()->id,
                    'name' => $clinics->first()->name
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking clinics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
