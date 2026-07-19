<?php

namespace Modules\Clinic\Http\Controllers;
use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Traits\LocationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use App\Models\User;
use Yajra\DataTables\DataTables;
use App\Models\Setting;
use Carbon\Carbon;
use Modules\Clinic\Models\Doctor;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Clinic\Models\DoctorDocument;
use Hash;
use Currency;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\DoctorSession;
use Modules\Clinic\Http\Requests\DoctorRequest;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\DoctorRating;
use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Commission\Models\Commission;
use Illuminate\Database\Query\Expression;
use App\Models\Holiday;
use  App\Models\DoctorHoliday;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\DoctorCategoryMapping;
 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CustomForm\Models\CustomForm;
use Modules\Clinic\Trait\ClinicTrait;

class DoctorController extends Controller
{
    use ClinicTrait, LocationHelper;
    protected string $exportClass = '\App\Exports\DoctorExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Doctor Detail';

        // module name
        $this->module_name = 'doctor';

        // directory path of the module
        $this->module_path = 'clinic::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());
        $filter = [
            'status' => $request->status,
        ];
        $user = User::role('doctor')->SetRole(auth()->user())->with('doctor', 'doctorclinic')->get();
        $clinic = Clinics::SetRole(auth()->user())->with('clinicdoctor','specialty','clinicdoctor','receptionist','clinicservices')->get();
        $vendor =User::where('user_type','vendor')->get();
        $commissions = Commission::where('type', '!=', 'admin_fees')->get();

        $module_title = 'clinic.doctor_list';
        $create_title = 'clinic.doctor_title';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'Name',
                'text' => __('service.lbl_name'),
            ],
            [
                'value' => 'mobile',
                'text' =>  __('clinic.lbl_phone_number'),
            ],
            [
                'value' => 'email',
                'text' => __('appointment.lbl_email'),
            ],
            [
                'value' => 'gender',
                'text' => __('clinic.lbl_gender'),
            ],
            [
                'value' => 'Clinic Center',
                'text' => __('clinic.lbl_clinic_center'),
            ],

            [
                'value' => 'varification_status',
                'text' => __('clinic.lbl_verification_status'),
            ],
            [
                'value' => 'status',
                'text' => __('clinic.lbl_status'),
            ],
        ];
        $export_url = route('backend.doctor.export');

        return view('clinic::backend.doctor.index', compact('filter','vendor','module_action', 'module_title','create_title','columns', 'customefield', 'export_import', 'export_columns','clinic', 'export_url','commissions'));

    }
 

    public function index_list(Request $request)
    {
        $doctors = $this->getDoctorList($request);
        $data = $this->formatDoctorData($doctors);
        
        return response()->json($data);
    }
    /**
     * Common function to fetch service list with filters
     */
    private function getServiceList(Request $request, $filters = [])
    {
        $query = ClinicsService::query()->with('ClinicServiceMapping', 'doctor_service');
        
        // Apply category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        // Apply clinic filter
        if ($request->has('clinic_id') && !empty($request->clinic_id)) {
            $clinicId = $request->clinic_id;
            
            // Convert to array if it's a comma-separated string
            if (is_array($clinicId)) {
                $clinicIdArray = $clinicId;
            } else {
                $clinicIdArray = explode(",", $clinicId);
            }
            
            // Remove empty values and convert to integers
            $clinicIdArray = array_filter(array_map('intval', $clinicIdArray));
            
            // Apply the filter only if we have valid clinic IDs
            if (!empty($clinicIdArray)) {
                $query->whereHas('ClinicServiceMapping', function ($q) use ($clinicIdArray) {
                    $q->whereIn('clinic_id', $clinicIdArray);
                });
            }
        }
        
        // Apply search term filter
        if ($request->has('q') && !empty(trim($request->q))) {
            $term = trim($request->q);
            $query->where('name', 'LIKE', "%$term%")
                  ->orWhere('description', 'LIKE', "%$term%");
        }
        
        // Apply additional filters
        foreach ($filters as $filter => $value) {
            if (!empty($value)) {
                $query->where($filter, $value);
            }
        }
        
        return $query->get();
    }

    /**
     * Common function to format service data for API response
     */
    private function formatServiceData($services, $includeAdditionalFields = false)
    {
        $data = [];
        
        foreach ($services as $service) {
            $serviceData = [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'charges' => $service->charges,
                'category_id' => $service->category_id,
            ];
            
            if ($includeAdditionalFields) {
                $serviceData['time_slot'] = $service->time_slot;
                $serviceData['discount'] = $service->discount;
                $serviceData['discount_type'] = $service->discount_type;
                $serviceData['discount_value'] = $service->discount_value;
                $serviceData['status'] = $service->status;
                $serviceData['file_url'] = $service->file_url;
            }
            
            $data[] = $serviceData;
        }
        
        return $data;
    }

    public function service_list(Request $request)
    {
        $services = $this->getServiceList($request);
        $data = $this->formatServiceData($services);
        
        return response()->json($data);
    }
    /**
     * Common function to fetch employee list with filters
     */
    private function getEmployeeList(Request $request, $filters = [])
    {
        $query = User::role(['doctor'])->with('media');
        
        // Apply search term filter
        if ($request->has('q') && !empty(trim($request->q))) {
            $term = trim($request->q);
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'LIKE', "%$term%")
                  ->orWhere('last_name', 'LIKE', "%$term%")
                  ->orWhere('email', 'LIKE', "%$term%");
            });
        }
        
        // Apply role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->role($request->role);
        }
        
        // Apply calendar resource filter
        if ($request->has('show_in_calender') && $request->show_in_calender) {
            $query->CalenderResource();
        }
        
        // Apply additional filters
        foreach ($filters as $filter => $value) {
            if (!empty($value)) {
                $query->where($filter, $value);
            }
        }
        
        return $query->get();
    }

    /**
     * Common function to format employee data for API response
     */
    private function formatEmployeeData($employees, $includeAdditionalFields = false)
    {
        $data = [];
        
        foreach ($employees as $row) {
            $employeeData = [
                'id' => $row->id,
                'name' => $row->full_name,
                'avatar' => $row->profile_image,
            ];
            
            if ($includeAdditionalFields) {
                $employeeData['email'] = $row->email;
                $employeeData['mobile'] = $row->mobile;
                $employeeData['gender'] = $row->gender;
                $employeeData['status'] = $row->status;
                $employeeData['user_type'] = $row->user_type;
            }
            
            $data[] = $employeeData;
        }
        
        return $data;
    }

    public function employee_list(Request $request)
    {
        $employees = $this->getEmployeeList($request);
        $data = $this->formatEmployeeData($employees);
        
        return response()->json($data);
    }

    public function availableSlot(Request $request)
    {
        $availableSlot = [];

        if ($request->filled(['appointment_date', 'clinic_id', 'doctor_id', 'service_id'])) {

            $timezone = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');

            $time_slot_duration = 10;
            $timeslot = ClinicsService::where('id', $request->service_id)->value('time_slot');
            if ($timeslot) {
                $time_slot_duration = ($timeslot === 'clinic_slot') ?
                    (int) Clinics::where('id', $request->clinic_id)->value('time_slot') :
                    (int) $timeslot;
            }

            $currentDate = Carbon::today($timezone);
            $carbonDate = Carbon::parse($request->appointment_date, $timezone);

            $dayOfWeek = $carbonDate->locale('en')->dayName;
            $availableSlot = [];

            // Check if there is a doctor session for this doctor, clinic, and day
            $doctorSession = DoctorSession::where('clinic_id', $request->clinic_id)
                ->where('doctor_id', $request->doctor_id)
                ->where('day', $dayOfWeek)
                ->first();

            // If no session found, return no available slots immediately
            if (!$doctorSession) {
                $message = __('messages.no_available_slots');
                if ($request->is('api/*')) {
                    return response()->json(['message' => $message, 'data' => [], 'status' => false], 200);
                } else {
                    return response()->json(['availableSlot' => [], 'message' => $message]);
                }
            }

            // If doctor is on holiday for this day, return message immediately
            if ($doctorSession && $doctorSession->is_holiday) {
                $message = __('messages.doctor_on_holiday_select_another_day') ?: 'Doctor is on holiday, please select another day.';
                if ($request->is('api/*')) {
                    return response()->json(['message' => $message, 'data' => [], 'status' => false], 200);
                } else {
                    return response()->json(['availableSlot' => [], 'message' => $message]);
                }
            }

            if ($doctorSession && !$doctorSession->is_holiday) {

                $startTime = Carbon::parse($doctorSession->start_time, $timezone);
                $endTime = Carbon::parse($doctorSession->end_time, $timezone);

                $breaks = $doctorSession->breaks;

                $timeSlots = [];

                $current = $startTime->copy();
                while ($current < $endTime) {

                    $inBreak = false;
                    if (!empty($breaks) && is_array($breaks)) {
                        foreach ($breaks as $break) {
                            // Safely check for 'start_break' and 'end_break' keys
                            $breakStart = isset($break['start_break']) ? $break['start_break'] : null;
                            $breakEnd = isset($break['end_break']) ? $break['end_break'] : null;

                            // If either is missing, skip this break
                            if (empty($breakStart) || empty($breakEnd)) {
                                continue;
                            }

                            try {
                                $breakStartTime = Carbon::parse($breakStart, $timezone);
                                $breakEndTime = Carbon::parse($breakEnd, $timezone);
                            } catch (\Exception $e) {
                                // If parsing fails, skip this break
                                continue;
                            }

                            // If the current slot is within the break, mark as in break
                            if ($current >= $breakStartTime && $current < $breakEndTime) {
                                $inBreak = true;
                                break;
                            }
                        }
                    }

                    if (!$inBreak) {
                        $timeSlots[] = $current->format('H:i');
                    }

                    $current->addMinutes($time_slot_duration);
                }

                $availableSlot = $timeSlots;

                if ($carbonDate == $currentDate) {
                    $todaytimeSlots = [];
                    $currentDateTime = Carbon::now($timezone);
                    foreach ($timeSlots as $slot) {
                        $slotTime = Carbon::parse($slot, $timezone);

                        if ($slotTime->greaterThan(Carbon::parse($currentDateTime, $timezone))) {
                            $todaytimeSlots[] = $slotTime->format('H:i');
                        }
                    }
                    $availableSlot = $todaytimeSlots;
                }

                $clinic_holiday = Holiday::where('clinic_id', $request->clinic_id)
                    ->where('date', $request->appointment_date)
                    ->first();

                if ($clinic_holiday) {
                    $holidayStartTime = Carbon::parse($clinic_holiday->start_time, $timezone);
                    $holidayEndTime = Carbon::parse($clinic_holiday->end_time, $timezone);

                    $availableSlot = array_filter($availableSlot, function ($slot) use ($holidayStartTime, $holidayEndTime, $timezone) {
                        $slotTime = Carbon::parse($slot, $timezone);
                        return !($slotTime->between($holidayStartTime, $holidayEndTime));
                    });

                    $availableSlot = array_values($availableSlot);
                }

                $doctor_holiday = DoctorHoliday::where('doctor_id', $request->doctor_id)
                    ->where('date', $request->appointment_date)
                    ->first();

                if ($doctor_holiday) {
                    $holidayStartTime = Carbon::parse($doctor_holiday->start_time, $timezone);
                    $holidayEndTime = Carbon::parse($doctor_holiday->end_time, $timezone);

                    $availableSlot = array_filter($availableSlot, function ($slot) use ($holidayStartTime, $holidayEndTime, $timezone) {
                        $slotTime = Carbon::parse($slot, $timezone);
                        return !($slotTime->between($holidayStartTime, $holidayEndTime));
                    });

                    $availableSlot = array_values($availableSlot);
                }

                $appointmentData = Appointment::where('appointment_date', $request->appointment_date)
                    ->where('doctor_id', $request->doctor_id)
                    ->where('status', '!=', 'cancelled')
                    ->get();

                $bookedSlots = [];

                foreach ($appointmentData as $appointment) {
                    $startTime = Carbon::parse($appointment->start_date_time)->setTimezone($timezone);
                    $startTime = strtotime($startTime);
                    $duration = $appointment->duration;

                    $endTime = $startTime + ($duration * 60);

                    $startTime = $startTime - ($duration * 60);

                    while ($startTime < $endTime) {
                        $bookedSlots[] = date('H:i', $startTime);
                        $startTime += 300;
                    }
                }
                $availableSlotTime = array_diff($availableSlot, $bookedSlots);
                $availableSlot = array_values($availableSlotTime);
            }
        }

        $message = __('messages.avaibleslot');

        if (empty($availableSlot)) {
            $hasClinicHoliday = Holiday::where('clinic_id', $request->clinic_id)
                ->where('date', $request->appointment_date)
                ->exists();

            $hasDoctorHoliday = DoctorHoliday::where('doctor_id', $request->doctor_id)
                ->where('date', $request->appointment_date)
                ->exists();

            if ($hasClinicHoliday && $hasDoctorHoliday) {
                $message = __('messages.holiday_slot');
            } else if ($hasClinicHoliday) {
                $message = __('messages.clinic_holiday_slot');
            } else if ($hasDoctorHoliday) {
                $message = __('messages.doctor_holiday_slot');
            } else {
                $message = __('messages.no_available_slots'); // Add this to your lang/messages.php
            }
        }

        $data = [
            'availableSlot' => $availableSlot
        ];

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $availableSlot, 'status' => true], 200);
        } else {
            return response()->json($data);
        }
    }

    // public function availableSlot(Request $request)
    // {
    //     $availableSlot = [];
    //     $isHoliday = 0; // default

    //     if ($request->filled(['appointment_date', 'clinic_id', 'doctor_id', 'service_id'])) {

    //         $timezone = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');

    //         $time_slot_duration = 10;
    //         $timeslot = ClinicsService::where('id', $request->service_id)->value('time_slot');
    //         if ($timeslot) {
    //             $time_slot_duration = ($timeslot === 'clinic_slot') ?
    //                 (int) Clinics::where('id', $request->clinic_id)->value('time_slot') :
    //                 (int) $timeslot;
    //         }

    //         $currentDate = Carbon::today($timezone);
    //         $carbonDate = Carbon::parse($request->appointment_date, $timezone);

    //         $dayOfWeek = $carbonDate->locale('en')->dayName;

    //         // Fetch doctor session
    //         $doctorSession = DoctorSession::where('clinic_id', $request->clinic_id)
    //             ->where('doctor_id', $request->doctor_id)
    //             ->where('day', $dayOfWeek)
    //             ->first();

    //         if ($doctorSession) {
    //             $isHoliday = (int) $doctorSession->is_holiday;
    //         }

    //         // If no session found
    //         if (!$doctorSession) {
    //             $message = __('messages.no_available_slots');
    //             return response()->json([
    //                 'message'    => $message,
    //                 'data'       => [],
    //                 'status'     => false,
    //                 'is_holiday' => $isHoliday
    //             ], 200);
    //         }

    //         // If doctor is on holiday
    //         if ($doctorSession->is_holiday) {
    //             $message = __('messages.doctor_on_holiday_select_another_day') ?: 'Doctor is on holiday, please select another day.';
    //             return response()->json([
    //                 'message'    => $message,
    //                 'data'       => [],
    //                 'status'     => false,
    //                 'is_holiday' => $isHoliday
    //             ], 200);
    //         }

    //         // Otherwise generate slots
    //         $startTime = Carbon::parse($doctorSession->start_time, $timezone);
    //         $endTime   = Carbon::parse($doctorSession->end_time, $timezone);
    //         $breaks    = $doctorSession->breaks;

    //         // Prepare break intervals as array of [start, end] Carbon objects
    //         $breakIntervals = [];
    //         if (!empty($breaks) && is_array($breaks)) {
    //             foreach ($breaks as $break) {
    //                 $breakStart = $break['start_break'] ?? null;
    //                 $breakEnd   = $break['end_break'] ?? null;
                    
    //                 if (!empty($breakStart) && !empty($breakEnd)) {
    //                     // Ensure break times are on the same date as the appointment
    //                     $breakStartTime = Carbon::parse($request->appointment_date . ' ' . $breakStart, $timezone);
    //                     $breakEndTime = Carbon::parse($request->appointment_date . ' ' . $breakEnd, $timezone);
    //                     $breakIntervals[] = [
    //                         'start' => $breakStartTime,
    //                         'end'   => $breakEndTime
    //                     ];
    //                 }
    //             }
    //         }

    //         $timeSlots = [];
    //         $current   = $startTime->copy();

    //         while ($current < $endTime) {
    //             $slotStart = $current->copy();
    //             $slotEnd = $current->copy()->addMinutes($time_slot_duration);

    //             // Check if this slot overlaps with any break
    //             $inBreak = false;
    //             foreach ($breakIntervals as $interval) {
    //                 // If slot start is before break end and slot end is after break start, they overlap
    //                 if ($slotStart < $interval['end'] && $slotEnd > $interval['start']) {
    //                     $inBreak = true;
    //                     break;
    //                 }
    //             }

    //             if (!$inBreak) {
    //                 $timeSlots[] = $current->format('H:i');
    //             }

    //             $current->addMinutes($time_slot_duration);
    //         }

    //         $availableSlot = $timeSlots;

    //         // If today → remove past slots
    //         if ($carbonDate->isSameDay($currentDate)) {
    //             $availableSlot = array_filter($availableSlot, function ($slot) use ($timezone, $carbonDate) {
    //                 // Use the appointment date for the slot, not today
    //                 $slotDateTime = Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $slot, $timezone);
    //                 return $slotDateTime->greaterThan(Carbon::now($timezone));
    //             });
    //             $availableSlot = array_values($availableSlot);
    //         }

    //         // Apply clinic holiday filter
    //         $clinic_holiday = Holiday::where('clinic_id', $request->clinic_id)
    //             ->where('date', $request->appointment_date)
    //             ->first();
    //         if ($clinic_holiday) {
    //             $holidayStart = Carbon::parse($request->appointment_date . ' ' . $clinic_holiday->start_time, $timezone);
    //             $holidayEnd   = Carbon::parse($request->appointment_date . ' ' . $clinic_holiday->end_time, $timezone);
    //             $availableSlot = array_filter($availableSlot, function($slot) use ($holidayStart, $holidayEnd, $timezone, $carbonDate) {
    //                 $slotDateTime = Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $slot, $timezone);
    //                 return !$slotDateTime->between($holidayStart, $holidayEnd, false);
    //             });
    //             $availableSlot = array_values($availableSlot);
    //         }

    //         // Apply doctor holiday filter
    //         $doctor_holiday = DoctorHoliday::where('doctor_id', $request->doctor_id)
    //             ->where('date', $request->appointment_date)
    //             ->first();
    //         if ($doctor_holiday) {
    //             $holidayStart = Carbon::parse($request->appointment_date . ' ' . $doctor_holiday->start_time, $timezone);
    //             $holidayEnd   = Carbon::parse($request->appointment_date . ' ' . $doctor_holiday->end_time, $timezone);
    //             $availableSlot = array_filter($availableSlot, function($slot) use ($holidayStart, $holidayEnd, $timezone, $carbonDate) {
    //                 $slotDateTime = Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $slot, $timezone);
    //                 return !$slotDateTime->between($holidayStart, $holidayEnd, false);
    //             });
    //             $availableSlot = array_values($availableSlot);
    //         }

    //         // Remove booked slots
    //         $appointments = Appointment::where('appointment_date', $request->appointment_date)
    //             ->where('doctor_id', $request->doctor_id)
    //             ->where('status', '!=', 'cancelled')
    //             ->get();

    //         $bookedSlots = [];
    //         foreach ($appointments as $appointment) {
    //             $start = Carbon::parse($appointment->start_date_time, $timezone)->timestamp;
    //             $duration = $appointment->duration * 60;
    //             $end = $start + $duration;

    //             while ($start < $end) {
    //                 $bookedSlots[] = date('H:i', $start);
    //                 $start += $time_slot_duration * 60;
    //             }
    //         }
    //         $availableSlot = array_values(array_diff($availableSlot, $bookedSlots));
    //     }

    //     // Default message
    //     $message = __('messages.avaibleslot');

    //     // Overwrite message if no slots
    //     if (empty($availableSlot)) {
    //         $hasClinicHoliday = Holiday::where('clinic_id', $request->clinic_id)
    //             ->where('date', $request->appointment_date)
    //             ->exists();
    //         $hasDoctorHoliday = DoctorHoliday::where('doctor_id', $request->doctor_id)
    //             ->where('date', $request->appointment_date)
    //             ->exists();

    //         if ($hasClinicHoliday && $hasDoctorHoliday) {
    //             $message = __('messages.holiday_slot');
    //         } else if ($hasClinicHoliday) {
    //             $message = __('messages.clinic_holiday_slot');
    //         } else if ($hasDoctorHoliday) {
    //             $message = __('messages.doctor_holiday_slot');
    //         } else {
    //             $message = __('messages.no_available_slots');
    //         }
    //     }

    //     return response()->json([
    //         'message'    => $message,
    //         'data'       => $availableSlot,
    //         'status'     => !empty($availableSlot),
    //         'is_holiday' => $isHoliday
    //     ], 200);
    // }




    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                // Need To Add Role Base
                $employee = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('clinic.doctor_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                User::whereIn('id', $ids)->delete();
                $message = __('clinic.doctor_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('clinic.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {

        $module_name = $this->module_name;
        $userId = auth()->id();
        $query = User::role('doctor')->SetRole(auth()->user())->with('doctor', 'doctorclinic');
        // dd($query->get()->toArray());
        $customform = CustomForm::where('module_type', 'doctor_module')
        ->where('status', 1)
        ->get();

        $filter = $request->filter;

        if (isset($filter)) {

            if (isset($filter['clinic_name'])) {

                $query->whereHas('doctor', function ($query) use ($filter) {
                    $query->whereHas('doctorclinic', function ($query) use ($filter) {
                        $query->whereHas('clinics', function ($query) use ($filter) {
                            $query->where('id', $filter['clinic_name']);
                        });
                    });
                });
            }
            if(isset($filter['doctor_name'])) {
                $fullName = $filter['doctor_name'];

                $query->where(function($query) use ($fullName) {
                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$fullName%"]);
                });
            }
            if(isset($filter['email']) && $filter['email'] !== '') {
                $query->where('email', 'like', '%' . $filter['email'] . '%');
            }
            if(isset($filter['contact']) && $filter['contact'] !== '') {
                $query->where(function($q) use ($filter) {
                    $q->where('mobile', 'like', '%' . $filter['contact'] . '%')
                      ->orWhere('email', 'like', '%' . $filter['contact'] . '%');
                });
            }
            
            if (isset($filter['gender']) && $filter['gender'] !== '') {
                $query->where('gender', $filter['gender']);
            }
            if(isset($filter['vendor_id'])) {

                $query->whereHas('doctor', function ($query) use ($filter) {
                            $query->where('vendor_id', $filter['vendor_id']);
                });
            }
        }

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $query->orderBy('created_at', 'desc');

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) use($customform) {

                $other_settings = Setting::where('name', 'is_provider_push_notification')->first();

                $enable_push_notification = 0;

                if (!empty($other_settings)) {

                    $enable_push_notification = $other_settings->val;
                }
                return view('clinic::backend.doctor.action_column', compact('data', 'enable_push_notification','customform'));
            })
            // ->addColumn('doctor_session', function ($data) {
            //     return " <button type='button' class='btn text-success p-0 fs-5' data-assign-module='" . $data->id . "' data-assign-target='#session-form-offcanvas' data-assign-event='employee_assign' class='fs-6 text-info border-0 bg-transparent text-nowrap' data-bs-toggle='tooltip' title='Session'>  <i class='ph ph-paper-plane-tilt'></i></button>";
            // })
            ->editColumn('doctor_id', function ($data) {
                return view('clinic::backend.doctor.user_id', compact('data'));
            })

            ->editColumn('clinic_id', function ($data) {

                return "<span class='bg-primary-subtle rounded tbl-badge'> <button type='button' data-assign-module='" . $data->id . "' data-assign-target='#clinic-list' data-assign-event='clinic_list' class='btn btn-sm p-0 text-primary' data-bs-toggle='tooltip' title='Clinic List'><b>$data->doctorclinic_count </b> </button></span>";
            })
            ->orderColumn('clinic_id', function ($query, $order) {
                $query->whereHas('doctor', function ($query) use ($order){
                    $query->whereHas('doctorclinic', function ($query) use ($order) {
                        $query->whereHas('clinics', function ($query) use ($order) {
                            $query->orderBy('name', $order);
                        });
                    });
                });
            }, 1)
            ->filterColumn('doctor_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('doctor_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)
            ->editColumn('image', function ($data) {
                return "<img src='" . $data->profile_image . "'class='avatar avatar-50 rounded-pill'>";
            })

            ->editColumn('email_verified_at', function ($data) {

                return view('clinic::backend.doctor.verify_action', compact('data'));
            })
            ->editColumn('user_type', function ($data) {
                return '<span class="badge booking-status bg-primary-subtle p-3">' . str_replace("_", "", ucfirst($data->user_type)) . '</span>';
            })
            ->editColumn('full_name', function ($data) {
                return $data->first_name . ' ' . $data->last_name;
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('full_name', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)
            ->editColumn('gender', function ($data) {
                return ucfirst($data->gender);
            })
            ->filterColumn('gender', function ($query, $keyword) {
                $query->where('gender', 'like', "%$keyword%");
            })


            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.doctor.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                    </div>
                ';
            })

            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action','clinic_id', 'status', 'is_banned', 'email_verified_at', 'check', 'image', 'user_type', 'gender'], $customFieldColumns))
            ->toJson();
    }
    public function update_status(Request $request, User $id)
    {
        $id->update(['status' => $request->status]);
        Doctor::where('doctor_id', $id->id)->update(['status' => $request->status]);
        return response()->json(['status' => true, 'message' => __('clinic.doctor_update')]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clinic::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorRequest $request)
    {
        $data = $request->except('profile_image');
        $data = $request->all();

        if (!empty($data['doctor_email'])) {
            $data['email'] = $data['doctor_email'];
        }

        $data['mobile'] = str_replace(' ', '', $data['mobile']);

        // Ensure $clinicid is always an array for consistent handling
        $clinicid = $request->clinic_id;
        if (is_array($clinicid)) {
            $clinicIdArray = $clinicid;
        } else {
            $clinicIdArray = explode(',', (string)$clinicid);
        }

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $request->vendor_id = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
            // Store current date time for email_verified_at if admin or demo_admin
            $data['email_verified_at'] = now();
        } elseif (auth()->user()->hasRole('receptionist')) {
            $vendor_id = Receptionist::where('receptionist_id', auth()->user()->id)
                ->whereHas('clinics', function ($query) use ($clinicIdArray) {
                    $query->whereIn('clinic_id', $clinicIdArray);
                })
                ->pluck('vendor_id')
                ->first();
            $request->vendor_id = $vendor_id;
            $data['email_verified_at'] = $request->email_verified_at ?? null;
        } else {
            $request->vendor_id = auth()->user()->id;
            $data['email_verified_at'] = $request->email_verified_at ?? null;
        }

        $data['password'] = Hash::make($data['password']);
        $data['user_type'] = 'doctor';

        // Auto-set UK country if not provided
        if (empty($data['country'])) {
            $data['country'] = 229; // UK
        }

        // Handle custom state/city entries with duplicate prevention
        if (!empty($data['state'])) {
            $data['state'] = $this->getOrCreateState($data['state']);
        }
        if (!empty($data['city'])) {
            $data['city'] = $this->getOrCreateCity($data['city']);
        }

        $data = User::create($data);

        $profile = [
            'about_self' => $request->about_self,
            'expert' => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'twitter_link' => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
        ];

        $data->profile()->updateOrCreate([], $profile);

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->has('profile_image') && !empty($request->profile_image)) {
            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }

        $employee_id = $data['id'];
        $roles = ['doctor'];
        $data->syncRoles($roles);

        $doctor_data = [
            'doctor_id' => $data->id,
            'clinic_id' => is_array($request->clinic_id) ? implode(',', $request->clinic_id) : $request->clinic_id,
            'experience' => $request->experience,
            'vendor_id' => $request->vendor_id,
            'signature' => $request->signature,
        ];
        Doctor::create($doctor_data);

        if ($request->has('qualifications') && is_array($request->qualifications)) {
            foreach ($request->qualifications as $qualification) {
                if (
                    !empty($qualification['degree']) &&
                    !empty($qualification['university']) &&
                    !empty($qualification['year'])
                ) {
                    $qualification_data = [
                        'doctor_id' => $data->id,
                        'degree' => $qualification['degree'],
                        'university' => $qualification['university'],
                        'year' => $qualification['year'],
                    ];
                    DoctorDocument::create($qualification_data);
                }
            }
        }

        if ($request->has('clinic_id') && !empty($request->clinic_id)) {
            $days = [
                ['day' => 'monday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'tuesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'thursday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'friday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'sunday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => true, 'breaks' => []],
            ];

            // Use $clinicIdArray for consistent array handling
            foreach ($clinicIdArray as $value) {
                $doctor_clinic = [
                    'doctor_id' => $data->id,
                    'clinic_id' => $value,
                ];

                DoctorClinicMapping::create($doctor_clinic);

                foreach ($days as $key => $val) {
                    $val['clinic_id'] = $value;
                    $val['doctor_id'] = $data->id;
                    DoctorSession::create($val);
                }
            }
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');

        if ($request->has('service_id') && $request->has('clinic_id')) {
            if ($request->service_id !== null && $request->clinic_id !== null) {
                // Handle service_id as array or string
                if (is_array($request->service_id)) {
                    $services = $request->service_id;
                } else {
                    $services = explode(',', (string)$request->service_id);
                }
                $clinices = $clinicIdArray;

                foreach ($clinices as $clinic) {
                    foreach ($services as $value) {
                        $clinic_service = ClinicServiceMapping::where('service_id', $value)->where('clinic_id', $clinic)->first();

                        if ($clinic_service) {
                            $clinicService = ClinicsService::findOrFail($value);

                            if ($clinicService['discount'] == 0) {
                                $clinicService['discount_value'] = 0;
                                $clinicService['discount_type'] = null;
                                $clinicService['service_discount_price'] = $clinicService['charges'];
                            } else {
                                $clinicService['discount_price'] = $clinicService['discount_type'] == 'percentage'
                                    ? $clinicService['charges'] * $clinicService['discount_value'] / 100
                                    : $clinicService['discount_value'];
                                $clinicService['service_discount_price'] = $clinicService['charges'] - $clinicService['discount_price'];
                            }
                            $inclusive_tax_price = $this->inclusiveTaxPrice($clinicService);
                            $inclusive_tax_price = $inclusive_tax_price['inclusive_tax_price'] ?? 0;
                            $charges = $clinicService->charges;
                            $service_data = [
                                'doctor_id' => $data->id,
                                'service_id' => $value,
                                'charges' => $charges,
                                'clinic_id' => $clinic,
                                'inclusive_tax_price' => $inclusive_tax_price
                            ];

                            DoctorServiceMapping::create($service_data);
                        }
                    }
                }
            }
        }

        if ($request->filled('commission_id')) {
            foreach ($request->commission_id as $commissionId) {
                EmployeeCommission::create([
                    'employee_id' => $data->id,
                    'commission_id' => $commissionId,
                ]);
            }
        }

        // NEW: Save doctor-category mappings
        if ($request->has('category_ids') && !empty($request->category_ids)) {
            $categoryIds = is_array($request->category_ids) ? $request->category_ids : explode(',', $request->category_ids);
            $clinices = $clinicIdArray;
            
            foreach ($clinices as $clinic) {
                foreach ($categoryIds as $categoryId) {
                    // Get category to determine pricing
                    $category = ClinicsCategory::find($categoryId);
                    
                    if ($category) {
                        $categoryCharges = $request->input("category_charges.{$categoryId}", $category->price ?? 0);
                        
                        DoctorCategoryMapping::create([
                            'doctor_id' => $data->id,
                            'category_id' => $categoryId,
                            'clinic_id' => $clinic,
                            'charges' => $categoryCharges,
                            'status' => 1
                        ]);
                    }
                }
            }
        }

        $message = __('messages.create_form', ['form' => __('clinic.doctor_title')]);

        return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = User::role('doctor')->findOrFail($id);

        return view('clinic::backend.doctor.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = User::role(['doctor'])
            ->where('id', $id)
            ->with('doctor', 'doctorclinic', 'doctor_service', 'commissionData', 'profile', 'doctor_document')
            ->first();
    
        if (is_null($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    
        // Custom fields
        $custom_field_data = $data->withCustomFields();
        $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
            ->filter(fn ($value) => $value !== null)
            ->toArray();
    
        // Relations
        $data['clinic_id'] = optional($data->doctorclinic)->pluck('clinic_id') ?? [];
        $data['service_id'] = optional($data->doctor_service)->pluck('service_id') ?? [];
        $data['commission_id'] = optional($data->commissionData)->pluck('commission_id') ?? [];
        
        // NEW: Load category assignments
        $data['category_ids'] = DoctorCategoryMapping::where('doctor_id', $data->id)
                                                     ->pluck('category_id')
                                                     ->toArray() ?? [];
    
        // Profile info
        $data['profile_image'] = $data->profile_image ?? null;
        $data['about_self'] = optional($data->profile)->about_self ?? null;
        $data['expert'] = optional($data->profile)->expert ?? null;
        $data['facebook_link'] = optional($data->profile)->facebook_link ?? null;
        $data['instagram_link'] = optional($data->profile)->instagram_link ?? null;
        $data['twitter_link'] = optional($data->profile)->twitter_link ?? null;
        $data['dribbble_link'] = optional($data->profile)->dribbble_link ?? null;
        // $data['gmc_number'] = optional($data->profile)->gmc_number ?? null;

        
    
        // Doctor info
        $data['experience'] = optional($data->doctor)->experience ?? null;
        $data['signature']  = optional($data->doctor)->signature ?? null; // ✅ lowercase
        $data['vendor_id']  = optional($data->doctor)->vendor_id ?? null;
    
        // Documents
        $data['doctor_document'] = optional($data->doctor_document)->map(function ($document) {
            return [
                'degree' => $document->degree,
                'university' => $document->university,
                'year' => $document->year,
            ];
        })->toArray();
    
        // Email
        $data['email'] = $data->email ?? null;
        $data['doctor_email'] = $data->email ?? null;
        $data['gmc_number'] = $data->gmc_number ?? null; // ✅ correct

    // dd($data['doctor_email']);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     // dd($request);
    //     $data = User::role(['doctor'])->findOrFail($id);
    //     $request_data = $request->except(['profile_image', 'password', 'vendor_id']);
    //     $request_data['mobile'] = str_replace(' ', '', $request_data['mobile']);

    //     $data->update($request_data);

    //     $profile = [
    //         'about_self' => $request->about_self,
    //         'expert' => $request->expert,
    //         'facebook_link' => $request->facebook_link,
    //         'instagram_link' => $request->instagram_link,
    //         'twitter_link' => $request->twitter_link,
    //         'dribbble_link' => $request->dribbble_link,
    //     ];

    //     $data->profile()->updateOrCreate([], $profile);

    //     if ($request->custom_fields_data) {

    //         $data->updateCustomFieldData(json_decode($request->custom_fields_data));
    //     }

    //     if ($request->hasFile('profile_image')) {
    //         storeMediaFile($data, $request->file('profile_image'),'profile_image');
    //     }


    //     if ($request->is('api/*')) {
    //         if ($request->profile_image && $request->profile_image == null) {
    //             $data->clearMediaCollection('profile_image');
    //         }
    //     }
    //     else{
    //         if ($request->profile_image == null) {
    //             $data->clearMediaCollection('profile_image');
    //         }
    //     }


    //     DoctorDocument::where('doctor_id', $id)->forceDelete();

    //     // DoctorClinicMapping::where('doctor_id', $id)->forceDelete();

    //     DoctorServiceMapping::where('doctor_id', $id)->forceDelete();

    //     EmployeeCommission::where('employee_id', $id)->forceDelete();

    //     $employee_id = $data->id;
    //     $doctor = Doctor::firstOrNew(['doctor_id' => $data->id]);
    //     $doctor->fill([
    //         'doctor_id' => $data->id,
    //         'experience' => $request->experience,
    //         'signature' => $request->signature,
    //         'vendor_id' => $request->vendor_id,
    //     ]);
    //     $doctor->save();

    //     if ($request->has('qualifications') && $request->qualifications != '[{"degree":"","university":"","year":""}]') {
    //         $qualifications = json_decode($request->qualifications);

    //         // Check if $qualifications is an array
    //         if (is_array($qualifications)) {
    //             foreach ($qualifications as $qualification) {
    //                 // Ensure properties exist
    //                 if (!empty($qualification->degree) && !empty($qualification->university) && !empty($qualification->year)) {
    //                     $qualification_data = [
    //                         'doctor_id' => $data->id,
    //                         'degree' => $qualification->degree,
    //                         'university' => $qualification->university,
    //                         'year' => $qualification->year,
    //                     ];
    //                     DoctorDocument::create($qualification_data);
    //                 }
    //             }
    //         }
    //     }

    //     \Illuminate\Support\Facades\Artisan::call('view:clear');
    //     \Illuminate\Support\Facades\Artisan::call('cache:clear');
    //     \Illuminate\Support\Facades\Artisan::call('route:clear');
    //     \Illuminate\Support\Facades\Artisan::call('config:clear');
    //     \Illuminate\Support\Facades\Artisan::call('config:cache');


    //     if ($request->has('service_id') &&   $request->has('clinic_id') ) {

    //         if ($request->service_id !== null &&  $request->clinic_id !==null) {

    //             $services = explode(',', $request->service_id);
    //             $clinices = explode(",", $request->clinic_id);

    //             foreach( $clinices as $clinic){

    //                 foreach($services as $value) {

    //                     $clinic_service=ClinicServiceMapping::where('service_id',$value)->where('clinic_id',$clinic)->first();

    //                     if($clinic_service){

    //                         $clinicService = ClinicsService::findOrFail($value);

    //                         if($clinicService['discount']==0){

    //                             $clinicService['discount_value']=0;
    //                             $clinicService['discount_type']=null;
    //                             $clinicService['service_discount_price'] = $clinicService['charges'];
    //                         }else{
    //                             $clinicService['discount_price'] = $clinicService['discount_type'] == 'percentage' ? $clinicService['charges'] * $clinicService['discount_value'] / 100 : $clinicService['discount_value'];
    //                             $clinicService['service_discount_price'] = $clinicService['charges'] - $clinicService['discount_price'];
    //                         }
    //                         $inclusive_tax_price = $this->inclusiveTaxPrice($clinicService);
    //                         $inclusive_tax_price = $inclusive_tax_price['inclusive_tax_price'] ?? 0;
    //                         $charges = $clinicService->charges;
    //                         $service_data = [
    //                              'doctor_id' => $data->id,
    //                              'service_id' => $value,
    //                              'charges' => $charges,
    //                              'clinic_id' => $clinic,
    //                              'inclusive_tax_price' => $inclusive_tax_price
    //                         ];

    //                        DoctorServiceMapping::create($service_data);

    //                     }
    //                 }

    //             }

    //         }
    //     }



    //     $existingClinicIds = DoctorClinicMapping::where('doctor_id', $id)->pluck('clinic_id')->toArray();
    //     $newClinicIds = $request->has('clinic_id') && !empty($request->clinic_id) ? explode(",", $request->clinic_id) : [];
    //     $clinicsToRemove = array_diff($existingClinicIds, $newClinicIds);
    //     if (!empty($clinicsToRemove)) {
    //         DoctorSession::where('doctor_id', $id)->whereIn('clinic_id', $clinicsToRemove)->delete();
    //     }
    //     DoctorClinicMapping::where('doctor_id', $id)->whereIn('clinic_id', $clinicsToRemove)->forceDelete();
    //     foreach ($newClinicIds as $clinicId) {
    //         DoctorClinicMapping::updateOrCreate(
    //             ['doctor_id' => $id, 'clinic_id' => $clinicId],
    //             ['doctor_id' => $id, 'clinic_id' => $clinicId]
    //         );
    //     }

    //     if (isset($request->commission_id) && $request->has('commission_id')) {
    //         if ($request->commission_id !== null) {

    //             $commissions = explode(',', $request->commission_id);

    //             foreach ($commissions as $value) {
    //                 $commission_data = [
    //                     'employee_id' => $employee_id,
    //                     'commission_id' => $value,
    //                 ];

    //                 EmployeeCommission::create($commission_data);
    //             }
    //         }
    //     }


    //     $message = __('messages.update_form', ['form' => __('clinic.doctor_title')]);

    //     return response()->json(['message' => $message, 'status' => true], 200);
    // }
    public function update(Request $request, $id)
    {
        // dd('hello');
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gmc_number'  => 'nullable|string|max:255',
            'doctor_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'mobile' => 'required|string',
            'commission_id' => 'required|array',
            'clinic_id'   => 'required|array',
            'service_id'  => 'required|array',
        ], [
            'doctor_email.unique' => 'This email is already taken, please choose a different one.',
            'doctor_email.required' => 'Email is required.',
            'doctor_email.email' => 'Please enter a valid email address.',
            'mobile.required' => 'Contact number is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = User::role(['doctor'])->findOrFail($id);
        $request_data = $request->except(['profile_image', 'password', 'remove_profile_image']);

        // Preserve gmc_number if not sent / empty
        if (!$request->filled('gmc_number')) {
            unset($request_data['gmc_number']);
        }

        $request_data['mobile'] = str_replace(' ', '', $request_data['mobile']);
        
        // Handle custom state/city entries with duplicate prevention
        if (!empty($request_data['state'])) {
            $request_data['state'] = $this->getOrCreateState($request_data['state']);
        }
        if (!empty($request_data['city'])) {
            $request_data['city'] = $this->getOrCreateCity($request_data['city']);
        }
        
        // Handle doctor_email to email mapping
        if (!empty($request_data['doctor_email'])) {
            $request_data['email'] = $request_data['doctor_email'];
        }
        
        // Handle vendor_id assignment based on user role (same logic as store method)
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $request->vendor_id = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
        } elseif (auth()->user()->hasRole('receptionist')) {
            $clinicIdArray = is_array($request->clinic_id) ? $request->clinic_id : explode(',', (string)$request->clinic_id);
            $vendor_id = Receptionist::where('receptionist_id', auth()->user()->id)
                ->whereHas('clinics', function ($query) use ($clinicIdArray) {
                    $query->whereIn('clinic_id', $clinicIdArray);
                })
                ->pluck('vendor_id')
                ->first();
            $request->vendor_id = $vendor_id;
        } else {
            $request->vendor_id = auth()->user()->id;
        }
        
        $data->update($request_data);

        // Profile info
        $profile = [
            'about_self'    => $request->about_self,
            'expert'        => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link'=> $request->instagram_link,
            'twitter_link'  => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
            // 'gmc_number' => $request->gmc_number
        ];
        $data->profile()->updateOrCreate([], $profile);

        // Custom fields safe decode
        if ($request->custom_fields_data) {
            $customFields = is_string($request->custom_fields_data) 
                ? json_decode($request->custom_fields_data, true) 
                : $request->custom_fields_data;
            $data->updateCustomFieldData($customFields);
        }

        // Profile image handling
        if ($request->hasFile('profile_image')) {
            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        } elseif ($request->remove_profile_image == 1) {
            $data->clearMediaCollection('profile_image');
        }

        // Doctor details
        $doctor = Doctor::firstOrNew(['doctor_id' => $data->id]);
        $doctor->fill([
            'doctor_id'  => $data->id,
            'experience' => $request->experience,
            // 'signature'  => $request->signature,
            'signature'  => $request->filled('signature') ? $request->signature : $doctor->signature,
            'vendor_id'  => $request->vendor_id,
        ])->save();

        // if ($request->has('qualifications') && $request->qualifications != '[{"degree":"","university":"","year":""}]') {
        //      $qualifications = $request->qualifications;

        // // if it's a string, decode it
        // if (is_string($qualifications)) {
        //     $qualifications = json_decode($qualifications, true);
        // Qualifications update (only if not empty)
        if (!empty($request->qualifications) && is_array($request->qualifications)) {
            DoctorDocument::where('doctor_id', $id)->delete(); // clear old
            foreach ($request->qualifications as $q) {
                if (!empty($q['degree']) && !empty($q['university']) && !empty($q['year'])) {
                    DoctorDocument::create([
                        'doctor_id'  => $id,
                        'degree'     => $q['degree'],
                        'university' => $q['university'],
                        'year'       => $q['year'],
                    ]);
                }
            }
        }

        // Services mapping (only if both arrays have data)
        if (!empty($request->service_id) && !empty($request->clinic_id)) {
            $services = is_array($request->service_id) ? $request->service_id : explode(',', (string)$request->service_id);
            $clinics  = is_array($request->clinic_id) ? $request->clinic_id : explode(',', (string)$request->clinic_id);

            DoctorServiceMapping::where('doctor_id', $id)->delete();
            foreach ($clinics as $clinic) {
                foreach ($services as $serviceId) {
                    $clinicService = ClinicsService::find($serviceId);
                    if ($clinicService) {
                        $discountPrice = $clinicService->discount_type == 'percentage'
                            ? ($clinicService->charges * $clinicService->discount_value / 100)
                            : $clinicService->discount_value;

                        $service_data = [
                            'doctor_id'              => $id,
                            'service_id'             => $serviceId,
                            'clinic_id'              => $clinic,
                            'charges'                => $clinicService->charges,
                            'inclusive_tax_price'    => $this->inclusiveTaxPrice($clinicService)['inclusive_tax_price'] ?? 0,
                            'service_discount_price' => $clinicService->discount 
                                ? $clinicService->charges - $discountPrice 
                                : $clinicService->charges,
                        ];
                        DoctorServiceMapping::create($service_data);
                    }
                }
            }
        }

        // Clinic mappings (only if not empty)
        if (!empty($request->clinic_id)) {
            $newClinicIds = is_array($request->clinic_id) ? $request->clinic_id : explode(",", (string)$request->clinic_id);

            DoctorClinicMapping::where('doctor_id', $id)
                ->whereNotIn('clinic_id', $newClinicIds)
                ->delete();

            foreach ($newClinicIds as $clinicId) {
                DoctorClinicMapping::updateOrCreate(
                    ['doctor_id' => $id, 'clinic_id' => $clinicId],
                    ['doctor_id' => $id, 'clinic_id' => $clinicId]
                );
            }
        }

        // Employee commissions (only if not empty)
        if (!empty($request->commission_id)) {
            $commissions = is_array($request->commission_id) ? $request->commission_id : explode(',', (string)$request->commission_id);
            EmployeeCommission::where('employee_id', $id)->delete();
            foreach ($commissions as $value) {
                EmployeeCommission::create([
                    'employee_id'   => $id,
                    'commission_id' => $value,
                ]);
            }
        }

        // NEW: Update doctor-category mappings
        if ($request->has('category_ids')) {
            // Delete existing category mappings
            DoctorCategoryMapping::where('doctor_id', $id)->delete();
            
            // Add new category mappings
            if (!empty($request->category_ids)) {
                $categoryIds = is_array($request->category_ids) ? $request->category_ids : explode(',', $request->category_ids);
                $clinicIdArray = is_array($request->clinic_id) ? $request->clinic_id : explode(',', (string)$request->clinic_id);
                
                foreach ($clinicIdArray as $clinic) {
                    foreach ($categoryIds as $categoryId) {
                        $category = ClinicsCategory::find($categoryId);
                        
                        if ($category) {
                            $categoryCharges = $request->input("category_charges.{$categoryId}", $category->price ?? 0);
                            
                            DoctorCategoryMapping::create([
                                'doctor_id' => $id,
                                'category_id' => $categoryId,
                                'clinic_id' => $clinic,
                                'charges' => $categoryCharges,
                                'status' => 1
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.update_form', ['form' => __('clinic.doctor_title')])
        ], 200);
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(\Auth::user()->hasAnyRole(['demo_admin'])){

            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = User::role('doctor')->findOrFail($id);

        $data->profile()->forceDelete();
        $data->doctor()->forceDelete();
        $data->doctor_service()->forceDelete();
        $data->doctorclinic()->forceDelete();
        $data->doctor_document()->forceDelete();

        DoctorSession::where('doctor_id', $id)->delete();

        $appointmentStatus = Appointment::where('doctor_id', $id)
        ->whereNotIn('status', ['checkout', 'check_in'])
        ->update(['status' => 'cancelled']);

        $data->tokens()->delete();

        $data->forceDelete();

        $message = __('messages.delete_form', ['form' => __('clinic.doctor_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    // public function doctorDeatails(Request $request, $id)
    // {
    //     $data = User::with('doctor','profile','media','employeeAppointment','doctor_service','rating')->findOrFail($id);
    //     $doctor_session = DoctorSession::where('doctor_id', $data->id)->where('is_holiday',0)->get();
    //     $data->total_appointment = $data->employeeAppointment->count();
    //     $data->specialization = optional($data->profile)->expert ? optional($data->profile)->expert : '-';
    //     $data->total_sessions = $doctor_session->count();
    //     $data->experience = optional($data->doctor)->experience ? optional($data->doctor)->experience : 0;

    //     // Fetch all commission table data for this doctor
    //     $commissions = optional($data->doctor)->doctorCommission()->with('mainCommission')->get();

    //     $commissionData = [];
    //     if ($commissions && $commissions->count() > 0) {
    //         foreach ($commissions as $commission) {
    //             if ($commission->mainCommission) {
    //                 $value = $commission->mainCommission->commission_value;
    //                 $type = $commission->mainCommission->commission_type;
    //                 $formattedValue = $type === 'percentage' ? $value . ' %' : Currency::format($value);
    //                 $commissionData[] = [
    //                     'commission_id' => $commission->commission_id,
    //                     'commission_value' => $value,
    //                     'commission_type' => $type,
    //                     'formatted_value' => $formattedValue,
    //                     'main_commission' => $commission->mainCommission
    //                 ];
    //             }
    //         }
    //         $data->commission = $commissionData;
    //     } else {
    //         $data->commission = [];
    //     }

    //     $data->doctor_service = $data->doctor_service;
    //     $data->rating = $data->rating;

    //     return response()->json(['data' => $data, 'status' => true]);
    // }

    public function doctorDeatails(Request $request, $id)
    {
        $data = User::with('doctor', 'profile', 'media', 'employeeAppointment', 'doctor_service', 'rating')
            ->findOrFail($id);
 
        $doctor_session = DoctorSession::where('doctor_id', $data->id)
            ->where('is_holiday', 0)
            ->get();

        $data->total_appointment = $data->employeeAppointment->count();
        $data->specialization = optional($data->profile)->expert ?: '-';
        $data->total_sessions = $doctor_session->count();
        $data->experience = optional($data->doctor)->experience ?: 0;

    //     $commission = optional($data->doctor)->doctorCommission()->with('mainCommission')->first();
    // if ($commission && $commission->mainCommission) {
    //     $value = $commission->mainCommission->commission_value;
    //     $type = $commission->mainCommission->commission_type;
    //     if ($type === 'percentage') {
    //         $data->commission = $value . ' %';
    //     } else {
    //         $data->commission = Currency::format($value);
    //     }
    // } else {
    //     $data->commission = '-';
    // }

    //     $data->doctor_service = $data->doctor_service;

    //     $data->rating = $data->rating;
        // 🔶 Fetch all commissions
        $commissions = optional($data->doctor)
            ->doctorCommission()
            ->with('mainCommission')
            ->get();

        // 🔷 Format commission data
        $formattedCommissions = [];

        foreach ($commissions as $commission) {
            $main = $commission->mainCommission;

            if ($main) {
                $value = $main->commission_value;
                $type = $main->commission_type;

                $formattedCommissions[] = [
                    'title' => $main->title,
                    'type' => ucwords(str_replace('_', ' ', $type)),
                    'value' => $type === 'percentage' ? $value . ' %' : Currency::format($value),
                ];
            }
        }
        // 🔹 Add formatted commissions
        $data->commissions = $formattedCommissions;
        // dd($data->commissions);

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'confirm_password' => 'required_with:password|same:password'
        ], [
            'password.regex' => 'Password must contain at least one uppercase / one lowercase / one number and one symbol.',
        ]);

        if ($validator->fails()) {
            // Return errors in a way that the frontend can show them below each input box
            // The errors will be keyed by field name
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor_id = $request->doctor_id;
        $user = User::role(['doctor'])->findOrFail($doctor_id);

        // Check if old_password matches current password
        if (!Hash::check($request->old_password, $user->password)) {
            // Return error for old_password field
            return response()->json([
                'status' => false,
                'errors' => [
                    'old_password' => [__('messages.invalid_old_password')]
                ]
            ], 422);
        }

        // Check if new password is same as old password
        if (Hash::check($request->password, $user->password)) {
            // Return error for password field
            return response()->json([
                'status' => false,
                'errors' => [
                    'password' => [__('messages.old_new_pass_same')]
                ]
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        $message = __('messages.password_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function verify_doctor(Request $request, $id)
    {
        $data = User::role(['doctor'])->findOrFail($id);

        $current_time = Carbon::now();

        $data->update(['email_verified_at' => $current_time]);

        return response()->json(['status' => true, 'message' => __('messages.doctor_verify')]);
    }
    // public function view()
    // {
    //     return view('clinic::backend.doctor.view');
    // }

    public function review(Request $request)
    {
        $module_title = __('clinic.reviews');
        $filter = $request->filter;

        $doctor_id = null;
        if($request->has('doctor_id')){
            $doctor_id = $request->doctor_id;
        }
        return view('clinic::backend.doctor.review', compact('module_title', 'filter', 'doctor_id'));
    }

    public function review_data(Datatables $datatable, Request $request)
    {
// dd('hello');
        $query = DoctorRating::with('user', 'doctor');
        $filter = $request->filter;
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $query;
        } else {
            $query->where('doctor_id', auth()->id());
        }

        if ($request->doctor_id !== null) {
            $doctor_id = $request->doctor_id;
            $query->where('doctor_id', $doctor_id);
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('image', function ($data) {
                if(isset($data->user->profile_image)){
                    return '<img src=' . $data->user->profile_image . " class='avatar avatar-50 rounded-pill'>";
                }
                else{
                    return "<img src='" . default_user_avatar() . "' class='avatar avatar-50 rounded-pill'>";
                }
            })
            ->addColumn('action', function ($data) {
                return view('clinic::backend.doctor.review_action_column', compact('data'));
            })
            ->filterColumn('doctor_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('doctor', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('doctor_id', function ($data) {
                $doctor_id = isset($data->doctor->full_name) ? $data->doctor->full_name : '-';
                if(isset($data->doctor->profile_image)){
                    return '<img src=' . $data->doctor->profile_image . " class='avatar avatar-40 rounded-pill me-2'>".' '.$doctor_id;
                }
                else{
                    return "<img src='" . default_user_avatar() . "' class='avatar avatar-40 rounded-pill me-2'>" . ' ' . $doctor_id;
                }
            })
            ->orderColumn('doctor_id', function ($query, $order) {
                $query->join('users', 'doctor_ratings.doctor_id', '=', 'users.id')
                      ->orderBy('users.first_name', $order);
            }, 1)
            ->editColumn('service_id', function ($data) {
                $service_name = isset($data->clinic_service->name) ? $data->clinic_service->name : '-';
                if(isset($data->clinic_service->file_url)){
                    return '<img src=' . $data->clinic_service->file_url . " class='avatar avatar-40 rounded-pill me-2'>".' '.$service_name;
                }
                else{
                    return "<img src='" . default_file_url() . "' class='avatar avatar-40 rounded-pill me-2'>" . ' ' . $service_name;
                }
            })
            ->filterColumn('service_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('clinic_service', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('service_id', function ($query, $order) {
                $query->join('clinics_services', 'doctor_ratings.service_id', '=', 'clinics_services.id')
                      ->orderBy('clinics_services.name', $order);
            }, 1)
            ->editColumn('review_msg', function ($data) {
                return '<div class="text-desc">'.$data->review_msg.'</div>';
            })
            ->editColumn('rating', function ($data) {
                return $data->rating - floor($data->rating) > 0 ? number_format($data->rating, 1) : $data->rating;
            })

            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->editColumn('user_id', function ($data) {
                $user_id = isset($data->user->full_name) ? $data->user->full_name : '-';
                if(isset($data->user->profile_image)){
                    return '<img src=' . $data->user->profile_image . " class='avatar avatar-40 rounded-pill me-2'>".$user_id;
                }
                else{
                    return "<img src='" . default_user_avatar() . "' class='avatar avatar-40 rounded-pill me-2'>" . ' ' . $user_id;
                }

                // return $user_id;
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = doctor_ratings.user_id LIMIT 1)'), $order);
            }, 1)
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['action', 'image', 'check','service_id', 'doctor_id', 'user_id','review_msg','updated_at']))
            ->toJson();
    }

    public function bulk_action_review(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                DoctorRating::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_review_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function destroy_review($id)
    {

        $module_title = __('clinic.reviews');

        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = DoctorRating::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function user_list(Request $request){

        $data = User::query();


        $data = $data->get();


        return response()->json($data);
    }
    
    public function doctorDetail(Request $request, $id)
    {
        $user = User::with('doctor','appointment', 'profile', 'media', 'employeeAppointment', 'doctor_service', 'rating')
            ->findOrFail($id);

        $doctor_session = DoctorSession::where('doctor_id', $user->id)
            ->where('is_holiday', 0)
            ->get();

        $services = $user->doctor_service->load('clinic');
        $serviceIds = $services->pluck('service_id');
        $serviceNames = ClinicsService::whereIn('id', $serviceIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $services = $services->map(function ($service) use ($serviceNames) {
            $service->clinic_name  = optional($service->clinic)->name ?? '-';
            $service->servicename  = $serviceNames[$service->service_id]->name ?? '-';
            // Keep doctor_service charges, fallback to default if null
            $service->charges      = $service->charges ?? ($serviceNames[$service->service_id]->charges ?? null);
            return $service;
        });

        // Calculate total_appointment: count of appointments with status 'checkout' for each service
        $total_appointment = [];
        foreach ($services as $service) {
            $count = $user->employeeAppointment
                ->where('service_id', $service->service_id)
                ->where('status', 'checkout')
                ->count();
            $total_appointment[] = [
                'service_id' => $service->service_id,
                'service_name' => $service->servicename,
                'count' => $count
            ];
        }
// dd($total_appointment);
        $appointment = $user->employeeAppointment->count();

        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
            'email' => $user->email,
            'mobile' => $user->mobile,
            'gender' => $user->gender,
            'profile_image' => $user->profile_image,
            'about' => $user->about,
            'address' => $user->address,
            // Now total_appointment is an array of service-wise checkout counts
            'total_appointment' => $total_appointment,
            'specialization' => optional($user->profile)->expert ?: '-',
            'total_sessions' => $doctor_session->count(),
            'experience' => optional($user->doctor)->experience ?: 0,
            'services' => $services,
            'ratings' => $user->rating,
            'appointment' => $appointment ?? null,
        ];
// dd($data);
        // Fetch and format commissions
        $commissions = optional($user->doctor)
            ->doctorCommission()
            ->with('mainCommission')
            ->get();

        $formattedCommissions = [];
        foreach ($commissions as $commission) {
            $main = $commission->mainCommission;
            if ($main) {
                $value = $main->commission_value;
                $type = $main->commission_type;
                $formattedCommissions[] = [
                    'title' => $main->title,
                    'type' => ucwords(str_replace('_', ' ', $type)),
                    'value' => $type === 'percentage' ? $value . ' %' : Currency::format($value),
                ];
            }
        }
        $data['commissions'] = $formattedCommissions;

        // Return the doctor details view with the data
        return view('clinic::backend.doctor.doctor-details', compact('data'));
    }



    public function appoitmentdoctorDetail(Request $request, $id)
    {
        $user = User::with('doctor','appointment', 'profile', 'media', 'employeeAppointment', 'doctor_service', 'rating')
            ->findOrFail($id);

        $doctor_session = DoctorSession::where('doctor_id', $user->id)
            ->where('is_holiday', 0)
            ->get();

        $services = $user->doctor_service->load('clinic');
        $serviceIds = $services->pluck('service_id');
        $serviceNames = ClinicsService::whereIn('id', $serviceIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $services = $services->map(function ($service) use ($serviceNames) {
            $service->clinic_name  = optional($service->clinic)->name ?? '-';
            $service->servicename  = $serviceNames[$service->service_id]->name ?? '-';
            // Keep doctor_service charges, fallback to default if null
            $service->charges      = $service->charges ?? ($serviceNames[$service->service_id]->charges ?? null);
            return $service;
        });

        // Calculate total_appointment: count of appointments with status 'checkout' for each service
        $total_appointment = [];
        foreach ($services as $service) {
            $count = $user->employeeAppointment
                ->where('service_id', $service->service_id)
                ->where('status', 'checkout')
                ->count();
            $total_appointment[] = [
                'service_id' => $service->service_id,
                'service_name' => $service->servicename,
                'count' => $count
            ];
        }
// dd($total_appointment);
        $appointment = $user->employeeAppointment->count();

        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
            'email' => $user->email,
            'mobile' => $user->mobile,
            'gender' => $user->gender,
            'profile_image' => $user->profile_image,
            'about' => $user->about,
            'address' => $user->address,
            // Now total_appointment is an array of service-wise checkout counts
            'total_appointment' => $total_appointment,
            'specialization' => optional($user->profile)->expert ?: '-',
            'total_sessions' => $doctor_session->count(),
            'experience' => optional($user->doctor)->experience ?: 0,
            'services' => $services,
            'ratings' => $user->rating,
            'appointment' => $appointment ?? null,
        ];
// dd($data);
        // Fetch and format commissions
        $commissions = optional($user->doctor)
            ->doctorCommission()
            ->with('mainCommission')
            ->get();

        $formattedCommissions = [];
        foreach ($commissions as $commission) {
            $main = $commission->mainCommission;
            if ($main) {
                $value = $main->commission_value;
                $type = $main->commission_type;
                $formattedCommissions[] = [
                    'title' => $main->title,
                    'type' => ucwords(str_replace('_', ' ', $type)),
                    'value' => $type === 'percentage' ? $value . ' %' : Currency::format($value),
                ];
            }
        }
        $data['commissions'] = $formattedCommissions;

        // Return the doctor details view with the data
        return view('clinic::backend.doctor.doctor_detail_page', compact('data'));
    }

       /**
     * Common function to fetch doctor list with filters
     */
    private function getDoctorList(Request $request, $filters = [])
    {
        $query = Doctor::SetRole(auth()->user())->with('user', 'doctorclinic')->where('status', 1);
        
        // Apply clinic filter
        if ($request->has('clinic_id') && $request->clinic_id != '') {
            $clinicId = $request->clinic_id;
            $query = $query->whereHas('doctorclinic', function ($data) use ($clinicId) {
                $data->where('clinic_id', $clinicId);
            });
        }
        
        // Apply search term filter
        if ($request->has('q') && !empty(trim($request->q))) {
            $term = trim($request->q);
            $query = $query->whereHas('user', function ($q) use ($term) {
                $q->where('first_name', 'LIKE', "%$term%")
                  ->orWhere('last_name', 'LIKE', "%$term%")
                  ->orWhere('email', 'LIKE', "%$term%");
            });
        }
        
        // Apply additional filters
        foreach ($filters as $filter => $value) {
            if (!empty($value)) {
                $query = $query->where($filter, $value);
            }
        }
        
        return $query->get();
    }

    /**
     * Common function to format doctor data for API response
     */
    private function formatDoctorData($doctors, $includeAdditionalFields = false)
    {
        $data = [];
        
        foreach ($doctors as $row) {
            $doctorData = [
                'id' => $row->id,
                'doctor_name' => optional($row->user)->full_name,
                'doctor_id' => $row->doctor_id,
                'avatar' => optional($row->user)->profile_image,
            ];
            
            if ($includeAdditionalFields) {
                $doctorData['email'] = optional($row->user)->email;
                $doctorData['mobile'] = optional($row->user)->mobile;
                $doctorData['gender'] = optional($row->user)->gender;
                $doctorData['status'] = $row->status;
            }
            
            $data[] = $doctorData;
        }
        
        return $data;
    }
    
    /**
     * Fetch clinics by vendor_id via AJAX
         * If vendor_id is not provided or empty, returns ALL clinics
     * If vendor_id is provided, returns only that vendor's clinics
     */
    public function getClinicsByVendor(Request $request)
    {
        $vendorId = $request->get('vendor_id');
        
        // Start with base query (respects user role permissions)
        $query = Clinics::SetRole(auth()->user());
        
        // If vendor_id is provided and not empty, filter by vendor
        // Otherwise, return ALL clinics
        if (!empty($vendorId)) {
            $query->where('vendor_id', $vendorId);
        }
        
        $clinics = $query->select('id', 'name')->get();
        
        return response()->json([
            'status' => true,
            'data' => $clinics
        ]);
    }
    
    /**
     * Get clinics for Select2 AJAX dropdown
     */
    public function getClinics(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $query = Clinics::SetRole(auth()->user());
        
        // Search by name
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $total = $query->count();
        $clinics = $query->select('id', 'name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        $results = $clinics->map(function($clinic) {
            return [
                'id' => $clinic->id,
                'text' => $clinic->name
            ];
        });
        
        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
    
    /**
     * Get vendors for Select2 AJAX dropdown
     */
    public function getVendors(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $query = User::where('user_type', 'vendor');
        
        // Search by name
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $total = $query->count();
        $vendors = $query->select('id', 'first_name', 'last_name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        $results = $vendors->map(function($vendor) {
            return [
                'id' => $vendor->id,
                'text' => $vendor->first_name . ' ' . $vendor->last_name
            ];
        });
        
        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Get categories by service ID for doctor assignment
     * 
     * @param int $serviceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesByService($serviceId)
    {
        $categories = ClinicsCategory::where('parent_id', $serviceId)
                                     ->where('status', 1)
                                     ->where('service_classification', 'doctor_required')
                                     ->select('id', 'name', 'price', 'service_classification')
                                     ->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}

