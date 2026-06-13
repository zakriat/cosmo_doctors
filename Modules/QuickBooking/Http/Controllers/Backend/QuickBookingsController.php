<?php

namespace Modules\QuickBooking\Http\Controllers\Backend;

use App\Events\Backend\UserCreated;
use App\Http\Controllers\Controller;
use App\Models\Address;
// Traits
use Modules\ServiceProvider\Models\ServiceProvider;
;
// Listing Models
use App\Models\User;
use App\Notifications\UserAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Booking\Models\Booking;
// Events
// use Modules\Service\Transformers\ServiceResource;
use Modules\Tax\Models\Tax;
use Modules\Clinic\Models\SystemService;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Transformers\ServiceResource;
use Modules\Clinic\Transformers\ClinicsResource;
use Modules\Clinic\Transformers\DoctorResource;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorSession;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Commission\Models\CommissionEarning;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Transformers\AppointmentDetailResource;
use App\Models\Holiday;
use App\Models\DoctorHoliday;


class QuickBookingsController extends Controller
{
    use AppointmentTrait;


    public function index(Request $request)
    {

        $id = $request->id;

        return view('quickbooking::backend.quickbookings.index', compact('id'));
    }

    // API Methods for listing api
    public function service_provider_list()
    {
        $list = ServiceProvider::active()->with('address')->select('id', 'name', 'contact_number', 'contact_email')->get();

        return $this->sendResponse($list, __('booking.booking_service_provider'));
    }

    public function slot_time_list(Request $request)
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

            $dayOfWeek = $carbonDate->format('l'); 
            $availableSlot = [];

            $doctorSession = DoctorSession::where('clinic_id', $request->clinic_id)->where('doctor_id', $request->doctor_id)->where('day', $dayOfWeek)->first();

            if ($doctorSession && !$doctorSession->is_holiday) {

                $startTime = Carbon::parse($doctorSession->start_time, $timezone);
                $endTime = Carbon::parse($doctorSession->end_time, $timezone);

                $breaks = $doctorSession->breaks;

                $timeSlots = [];

                $current = $startTime->copy();
                while ($current < $endTime) {

                    $inBreak = false;
                    foreach ($breaks as $break) {
                        $breakStartTime = Carbon::parse($break['start_break'], $timezone);
                        $breakEndTime = Carbon::parse($break['end_break'], $timezone);
                        if ($current >= $breakStartTime && $current < $breakEndTime) {
                            $inBreak = true;
                            break;
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
                    $holidayStartTime = Carbon::parse($doctorSession->start_time, $timezone);
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


                $appointmentData = Appointment::where('appointment_date', $request->appointment_date)->where('doctor_id', $request->doctor_id)->where('status', '!=', 'cancelled')->get();


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

        $message = 'messages.avaibleslot';

        $data = [
            'availableSlot' => $availableSlot
        ];


        if ($request->is('api/*')) {

            return response()->json(['message' => $message, 'data' => $availableSlot, 'status' => true], 200);

        } else {

            return response()->json($data);
        }


    }
    public function system_service_list(Request $request)
    {

        $user_id = $request->user_id;

        $user = User::where('id', $user_id)->first();

        $data = SystemService::query();

        if ($user) {

            if ($user->user_type == 'vendor') {

                $system_service_arr = ClinicsService::where('vendor_id', $user_id)->pluck('system_service_id')->toArray();

                $data = $data->whereIn('id', $system_service_arr);

            }
        }

        $data = $data->where('status', 1)->get();

        return $this->sendResponse($data, __('booking.booking_sevice'));

    }

    public function services_list(Request $request)
    {
        $user_id = $request->user_id;
        $system_service_id = $request->system_service_id;

        $data = ClinicsService::query();
// dd($data);
        if (multiVendor() == 1) {

            $data = $data->where('system_service_id', $system_service_id);

            if (isset($user_id) && $user_id !== null && $user_id !== 'undefined' && $user_id > 0) {

                $data = $data->where('vendor_id', $user_id);
            }

        } else {

            $data = $data->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });

        }

        $data = $data->where('status', 1)->where('is_enable_advance_payment',0)->get();

        $data = ServiceResource::collection($data);
// dd($data);
        return $this->sendResponse($data, __('booking.booking_sevice'));

    }

    public function clinic_list(Request $request)
    {

        $user_id = $request->user_id;
        $service_id = $request->service_id;

        $data = Clinics::whereHas('clinicservices', function ($query) use ($service_id) {
            $query->where('service_id', $service_id);
        });

        if (multiVendor() == 1) {

            if (isset($user_id) && $user_id !== null && $user_id !== 'undefined' && $user_id > 0) {
                $data = $data->where('vendor_id', $user_id);
            }

        } else {

            $data = $data->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });

        }

        $data = $data->where('status', 1)->get();

        $data = ClinicsResource::collection($data);

        return $this->sendResponse($data, __('booking.clinic_list'));

    }

    public function doctor_list(Request $request)
    {

        $user_id = $request->user_id;
        $service_id = $request->service_id;
        $clinic_id = $request->clinic_id;

        $data = Doctor::with(['user', 'doctorService'])
            ->whereHas('doctorService', function ($query) use ($service_id, $clinic_id) {
                $query->where('service_id', $service_id)
                    ->where('clinic_id', $clinic_id);
            });

        if (multiVendor() == 1) {

            if (isset($user_id) && $user_id !== null && $user_id !== 'undefined' && $user_id > 0) {

                $data = $data->where('vendor_id', $user_id);
            }

        } else {

            $data = $data->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });

        }

        $data = $data->where('status', 1)->get();

        $data = DoctorResource::collection($data);

        return $this->sendResponse($data, __('booking.doctor_list'));


    }

    public function verify_customer(Request $request)
    {

        $data = User::Where('email', $request->email)->first();

        return response()->json(['data' => $data, 'status' => true]);

    }


    // Create Method for Booking API
    public function create_booking(Request $request)
    {

        $userRequest = $request->user;
        // dd($userRequest);
        $user = User::where('email', $userRequest['email'])->first();

        if (!isset($user)) {
            $userRequest['password'] = Hash::make('12345678');
            $userRequest['user_type'] = 'user';
            $user = User::create($userRequest);

            $roles = ['user'];
            $user->syncRoles($roles);

            \Artisan::call('cache:clear');
 
            event(new UserCreated($user));

            $data = [
                'password' => '12345678',
            ];

            try {
                $user->notify(new UserAccountCreated($data));
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $data = $request->booking;

        $firstServiceId = $data['services'][0]['service_id'];

        $data['service_id'] = $firstServiceId;


        $data['start_date_time'] = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['start_time'], setting('default_time_zone'))->setTimezone('UTC');
        $data['appointment_date'] = $data['date'];
        $data['appointment_time'] = $data['start_time'];
        $data['user_id'] = $user->id;
        $data['created_by'] = $user->id;
        $serviceData = $this->getServiceAmount($data['service_id'], $data['doctor_id'], $data['clinic_id']);

        $data['service_price'] = $serviceData['service_price'];
        $data['service_amount'] = $serviceData['service_amount'];
        $data['total_amount'] = $serviceData['total_amount'];
        $data['duration'] = $serviceData['duration'];
        $data['status'] = 'pending';
        // $data['is_quick_booking'] = 1;
        $data = Appointment::create($data);
        // $is_telemet = SystemService::where('id', $data['service_id'])->pluck('is_video_consultancy')->first();
        // if ($is_telemet == 1) {
        //     $setting = Setting::where('name', 'google_meet_method')->orwhere('name', 'is_zoom')->first();
        //     if ($data && $setting) {
        //         if ($setting->name == 'google_meet_method' && $setting->val == 1) {
        //             $meetLink = $this->generateMeetLink($request, $data['start_date_time'], $data['duration'], $data);
        //         } else {
        //             $zoom_url = getzoomVideoUrl($data);
        //             if (!empty($zoom_url) && isset($zoom_url['start_url']) && isset($zoom_url['join_url'])) {
        //                 $startUrl = $zoom_url['start_url'];
        //                 $joinUrl = $zoom_url['join_url'];

        //                 $data->start_video_link = $startUrl;
        //                 $data->join_video_link = $joinUrl;
        //                 $data->save();
        //             }
        //         }
        //     }
        // }

        $service_data = ClinicsService::where('id', $data['service_id'])->with('systemservice')->first();

        $clinic_data = Clinics::where('id', $data['clinic_id'])->first();

        $data['service_name'] = $service_data->systemservice->name ?? '--';
        $data['clinic_name'] = $clinic_data->name ?? '--';
        $notification_data = [
            'id' => $data->id,
            'description' => $data->description,
            'appointment_duration' => $data->duration,
            'user_id' => $data->user_id,
            'user_name' => optional($data->user)->first_name ?? default_user_name(),
            'doctor_id' => $data->doctor_id,
            'doctor_name' => optional($data->doctor)->first_name,
            'appointment_date' => $data->start_date_time->format('d/m/Y'),
            'appointment_time' => $data->start_date_time->format('h:i A'),
            'appointment_services_names' => ClinicsService::with('systemservice')->find($data->service_id)->systemservice->name ?? '--',
            'appointment_services_image' => optional($data->clinicservice)->file_url,
            'appointment_date_and_time' => $data->start_date_time->format('Y-m-d H:i'),
            'latitude' => null,
            'longitude' => null,
            'clinic_name' => $data['clinic_name'],
            'clinic_id' => $clinic_data->id
        ];
        $this->sendNotificationOnBookingUpdate('new_appointment', $notification_data);

        $appointment = Appointment::findOrFail($data['id']);
        $serviceDetails = ClinicsService::where('id', $appointment->service_id)->with('vendor')->first();
        $vendor = $serviceDetails->vendor ?? null;
        //   $serviceData = $this->getServiceAmount($appointment->service_id, $appointment->doctor_id, $appointment->clinic_id);
        $tax = $data['tax_percentage'] ?? Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('tax_type', 'exclusive')->where('status', 1)->get();

        // Get inclusive tax data from service
        $serviceDetails = ClinicsService::where('id', $appointment->service_id)->first();
        $inclusive_tax_data = null;
        $inclusive_tax_price = 0;
        
        if ($serviceDetails && $serviceDetails->is_inclusive_tax == 1) {
            $inclusive_tax_data = $serviceDetails->inclusive_tax;
            $inclusive_tax_price = $serviceData['total_inclusive_tax'] ?? 0;
        }

        $transactionData = [
            'appointment_id' => $appointment->id,
            'transaction_type' => 'cash',
            'total_amount' => $serviceData['total_amount'],
            'payment_status' => 0,
            'discount_value' => $serviceData['discount_value'] ?? 0,
            'discount_type' => $serviceData['discount_type'] ?? null,
            'discount_amount' => $serviceData['discount_amount'] ?? 0,
            'external_transaction_id' => null,
            'tax_percentage' => json_encode($tax),
            'inclusive_tax' => $inclusive_tax_data,
            'inclusive_tax_price' => $inclusive_tax_price,
        ];

        $payment = AppointmentTransaction::updateOrCreate(
            ['appointment_id' => $appointment->id],
            $transactionData
        );

        if ($appointment->doctor_id && $earning_data = $this->commissionData($payment)) {

            $earning_data['commission_data']['user_type'] = 'doctor';
            $commissionEarning = new CommissionEarning($earning_data['commission_data']);
            $appointment->commission()->save($commissionEarning);


            if (multiVendor() != 1) {

                $admin_earning_data = [
                    'user_type' => $vendor->user_type ?? 'admin',
                    'employee_id' => $vendor->id ?? User::where('user_type', 'admin')->value('id'),
                    'commissions' => null,
                    'commission_status' => $payment->payment_status == 1 ? 'unpaid' : 'pending',
                    'commission_amount' => $serviceData['total_amount'] - $earning_data['commission_data']['commission_amount'],
                ];

                $admincommissionEarning = new CommissionEarning($admin_earning_data);
                $appointment->commission()->save($admincommissionEarning);
            } elseif (multiVendor() == 1) {


                if ($vendor && $vendor->user_type == 'vendor') {
                    $admin_earning = $this->AdminEarningData($payment);

                    $admin_earning['user_type'] = 'admin';

                    $admincommissionEarning = new CommissionEarning($admin_earning);
                    $appointment->commission()->save($admincommissionEarning);

                    $vendor_earning_data = [
                        'user_type' => $vendor->user_type,
                        'employee_id' => $vendor->id,
                        'commissions' => null,
                        'commission_status' => $payment->payment_status == 1 ? 'unpaid' : 'pending',
                        'commission_amount' => $serviceData['total_amount'] - $admin_earning['commission_amount'] - $earning_data['commission_data']['commission_amount'],
                    ];

                    $vendorcommissionEarning = new CommissionEarning($vendor_earning_data);
                    $appointment->commission()->save($vendorcommissionEarning);
                } else {

                    $admin_earning_data = [
                        'user_type' => 'admin',
                        'employee_id' => User::where('user_type', 'admin')->value('id'),
                        'commissions' => null,
                        'commission_status' => $payment->payment_status == 1 ? 'unpaid' : 'pending',
                        'commission_amount' => $serviceData['total_amount'] - $earning_data['commission_data']['commission_amount'],
                    ];

                    $admincommissionEarning = new CommissionEarning($admin_earning_data);
                    $appointment->commission()->save($admincommissionEarning);
                }
            }
        }

        $data = new AppointmentDetailResource($data);

        return $this->sendResponse($data, __('booking.booking_create'));
    }

    public function requestData($request)
    {
        return [
            'service_provider_id' => $request->service_provider_id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'employee_id' => $request->employee_id,
            'start_date_time' => $request->start_date_time,
        ];
    }
}


