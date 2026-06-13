<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Blog\Models\Blog;
use Modules\Product\Models\Order;
use Modules\Product\Models\OrderGroup;
use Modules\Clinic\Models\ClinicsService;
use Auth;
use Modules\Clinic\Models\ClinicAppointment;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\Receptionist;
use DB;
use Modules\Clinic\Models\Clinics;
use Modules\Commission\Models\CommissionEarning;
use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\Doctor;
use App\Models\Setting;
use Modules\Earning\Models\EmployeeEarning;
use Modules\Clinic\Models\ClinicsCategory;

class BackendController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('employee')) {
            return redirect(RouteServiceProvider::EMPLOYEE_LOGIN_REDIRECT);
        }
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $today = Carbon::today();
        $action = $request->action ?? 'reset';

        $appointment = Appointment::CheckMultivendor();
        $totalappointment = $appointment->count();
        $patientcount = $appointment->with(['user'])->get();
        $childAge = 25;
        $oldAge = 50;

        $childPatientCount = 0;
        $oldPatientCount = 0;
        $adultPatientCount = 0;
        foreach ($patientcount as $appointment) {
            $dob = Carbon::parse(optional($appointment->user)->date_of_birth);
            $age = $dob->diffInYears($today);
            if ($age < $childAge) {
                $childPatientCount++;
            } elseif ($age >= $oldAge) {
                $oldPatientCount++;
            } else {
                $adultPatientCount++;
            }
        }
        $paymenthistory = Appointment::CheckMultivendor()->with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // $paymenthistory = Appointment::CheckMultivendor()->with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])->whereHas('appointmenttransaction', function ($query) {
        //     $query->where('payment_status', 1);
        // })
        //     ->orderByDesc('updated_at')
        //     ->take(5)
        //     ->get();



        $upcomingappointments = Appointment::CheckMultivendor()->with(['user', 'clinicservice', 'doctor', 'cliniccenter'])
            ->where('start_date_time', '>', now())
            ->where('status', 'confirmed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $appointmenttransaction = Appointment::CheckMultivendor()->with(['appointmenttransaction'])
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->take(5)
            ->get();

        $total_amt = Appointment::CheckMultivendor()->with(['appointmenttransaction'])
            ->where('status', 'checkout')
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->get();

        $totalrevenue = $total_amt->sum(function ($item) {
            return $item->appointmenttransaction->total_amount ?? 0;
        });
        
        $cancellation_charge_amount = Appointment::CheckMultivendor()->whereNotNull('cancellation_charge_amount')->get();
        $total_cancellation_charge = $cancellation_charge_amount->sum('cancellation_charge_amount');
        $totalrevenue = $totalrevenue + $total_cancellation_charge;


        $userQuery = User::where('user_type', 'user');
        
        $total_active_user = (clone $userQuery)->where('status', 1)->count();
        $total_inactive_user = (clone $userQuery)->where('status', 0)->count();
        $total_user = $total_active_user + $total_inactive_user;

        $vendorQuery = User::where('user_type', 'vendor')->where('status', 1);

        $totalactivevendor = (clone $vendorQuery)->whereNotNull('email_verified_at')->count();

        $register_vendor = $vendorQuery->orderByDesc('updated_at')->take(4)->get();

        $clinicservice = ClinicsService::CheckMultivendor()->where('status', 1)->get();
        $total_clinicservice = $clinicservice->count();

        $clinicsQuery = Clinics::query();
        if (multiVendor() == "0") {
            $clinicsQuery = $clinicsQuery->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });
        }
        $total_clinics = $clinicsQuery->count();


        $date_range = '';
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';

        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';



        $timeZoneSetting = Setting::where('name', 'default_time_zone')->first();
        $timeZone = $timeZoneSetting ? $timeZoneSetting->val : 'UTC';

        $data = [
            'total_appointments' => $totalappointment ?? 0,
            'total_commission' => 0,
            'total_revenue' => $totalrevenue ?? 0,
            'total_new_customers' => 0,
            'upcomming_appointments' => $upcomingappointments ?? [],
            'top_services' => [],
            'revenue_chart' => [],
            'total_orders' => 0,
            'product_sales' => 0,
            'payment_history' => $paymenthistory ?? [],
            'child_patient_count' => $childPatientCount,
            'old_patient_count' => $oldPatientCount,
            'adult_patient_count' => $adultPatientCount,
            'total_user' => $total_user ?? 0,
            'total_active_user' => $total_active_user ?? 0,
            'total_inactive_user' => $total_inactive_user ?? 0,
            'totalactivevendor' => $totalactivevendor ?? 0,
            'register_vendor' => $register_vendor ?? [],
            'total_clinicservice' => $total_clinicservice ?? 0,
            'total_clinics' => $total_clinics ?? 0,
            'dateformate' => $dateformate,
            'timeformate' => $timeformate,
            'timeZone' => $timeZone,
        ];


        $totalServices = [];

        $data['total_commission'] = [];

        $data['total_commission'] = \Currency::format(0);

        $bookings = [];


        $data['top_services'] = [];

        $chartBookingRevenue = [];

        $data['revenue_chart']['xaxis'] = [];
        $data['revenue_chart']['total_bookings'] = [];
        $data['revenue_chart']['total_price'] = [];

        $orders = Order::where(function ($q) {
            $q->orWhereIn('order_group_id', OrderGroup::pluck('id'));
        });

        $data['total_orders'] = $orders->count();

        $data['product_sales'] = \Currency::format($orders->sum('total_admin_earnings'));
        return view('backend.dashboard.index', compact('data', 'date_range', 'current_user', 'timeZone'));
    }

    public function daterange($daterange)
    {
        if (auth()->user()->hasRole('employee')) {
            return redirect(RouteServiceProvider::EMPLOYEE_LOGIN_REDIRECT);
        }
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $today = Carbon::today();
        $action = request()->action ?? 'reset';
        if ($daterange === null) {
            $startDate = now()->subDays(7)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
            $date_range = $startDate . ' to ' . $endDate;
        } else {
            $decodedDateRange = urldecode($daterange);
            $dateRangeParts = explode(' to ', $decodedDateRange);
            $startDate = $dateRangeParts[0] ?? date('Y-m-d');
            $endDate = $dateRangeParts[1] ?? date('Y-m-d');
            $date_range = $startDate . ' to ' . $endDate;
        }
        $appointment = Appointment::CheckMultivendor();
        $totalappointment = $appointment->where(function ($query) use ($startDate, $endDate) {

            $query->whereDate('start_date_time', '>=', $startDate)
                ->whereDate('start_date_time', '<=', $endDate);
        })->count();
        $patientcount = $appointment->with(['user'])->where(function ($query) use ($startDate, $endDate) {

            $query->whereDate('start_date_time', '>=', $startDate)
                ->whereDate('start_date_time', '<=', $endDate);
        })->get();

        $childAge = 25;
        $oldAge = 50;

        $childPatientCount = 0;
        $oldPatientCount = 0;
        $adultPatientCount = 0;
        foreach ($patientcount as $appointment) {
            $dob = Carbon::parse(optional($appointment->user)->date_of_birth);
            $age = $dob->diffInYears($today);
            if ($age < $childAge) {
                $childPatientCount++;
            } elseif ($age >= $oldAge) {
                $oldPatientCount++;
            } else {
                $adultPatientCount++;
            }
        }
        $paymenthistory = Appointment::CheckMultivendor()->with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])
            ->where(function ($query) use ($startDate, $endDate) {

                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            })
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();



        $upcomingappointments = Appointment::CheckMultivendor()->with(['user', 'clinicservice', 'doctor', 'cliniccenter'])
            ->where(function ($query) use ($startDate, $endDate) {

                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            })
            ->where('start_date_time', '>', now())
            ->where('status', 'confirmed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $appointmenttransaction = Appointment::CheckMultivendor()->with(['appointmenttransaction'])
            ->where(function ($query) use ($startDate, $endDate) {

                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            })
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->get();

        // $cancleappointment=Appointment::CheckMultivendor()->where('status','')




        $totalrevenue = $appointmenttransaction->sum('appointmenttransaction.total_amount');


        $userQuery = User::where('user_type', 'user')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate);
            
        $total_active_user = (clone $userQuery)->where('status', 1)->count();
        $total_inactive_user = (clone $userQuery)->where('status', 0)->count();
        $total_user = $total_active_user + $total_inactive_user;

        $vendorQuery = User::where('user_type', 'vendor')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('status', 1);

        $totalactivevendor = $vendorQuery->count();

        $register_vendor = $vendorQuery->orderByDesc('updated_at')->take(4)->get();

        $clinicservice = ClinicsService::CheckMultivendor()->where('status', 1)->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->get();
        $total_clinicservice = $clinicservice->count();

        $clinicsQuery = Clinics::query()
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate);
        if (multiVendor() == "0") {
            $clinicsQuery = $clinicsQuery->whereHas('vendor', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            });
        }
        $total_clinics = $clinicsQuery->count();
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';

        $timeZoneSetting = Setting::where('name', 'default_time_zone')->first();
        $timeZone = $timeZoneSetting ? $timeZoneSetting->val : 'UTC';

        $data = [
            'total_appointments' => $totalappointment ?? 0,
            'total_commission' => 0,
            'total_revenue' => $totalrevenue ?? 0,
            'total_new_customers' => 0,
            'upcomming_appointments' => $upcomingappointments ?? [],
            'top_services' => [],
            'revenue_chart' => [],
            'total_orders' => 0,
            'product_sales' => 0,
            'payment_history' => $paymenthistory ?? [],
            'child_patient_count' => $childPatientCount,
            'old_patient_count' => $oldPatientCount,
            'adult_patient_count' => $adultPatientCount,
            'total_user' => $total_user ?? 0,
            'total_active_user' => $total_active_user ?? 0,
            'total_inactive_user' => $total_inactive_user ?? 0,
            'totalactivevendor' => $totalactivevendor ?? 0,
            'register_vendor' => $register_vendor ?? [],
            'total_clinicservice' => $total_clinicservice ?? 0,
            'total_clinics' => $total_clinics ?? 0,
            'dateformate' => $dateformate,
            'timeformate' => $timeformate,
        ];



        $totalServices = [];

        $data['total_commission'] = [];

        $data['total_commission'] = \Currency::format(0);

        $bookings = [];


        $data['top_services'] = [];

        $chartBookingRevenue = [];

        $data['revenue_chart']['xaxis'] = [];
        $data['revenue_chart']['total_bookings'] = [];
        $data['revenue_chart']['total_price'] = [];

        $orders = Order::where(function ($q) {
            $q->orWhereIn('order_group_id', OrderGroup::pluck('id'));
        });

        $data['total_orders'] = $orders->count();

        $data['product_sales'] = \Currency::format($orders->sum('total_admin_earnings'));
        return view('backend.dashboard.index', compact('data', 'date_range', 'current_user','timeZone'));
    }

    public function setCurrentServiceProvider($service_provider_id)
    {
        request()->session()->forget('selected_service_provider');

        request()->session()->put('selected_service_provider', $service_provider_id);

        return redirect()->back()->with('success', 'Current Service Provider Has Been Changes')->withInput();
    }

    public function resetServiceProvider()
    {
        request()->session()->forget('selected_service_provider');

        return redirect()->back()->with('success', 'Show All Service Provider Content')->withInput();
    }

    public function setUserSetting(Request $request)
    {
        auth()->user()->update(['user_setting' => $request->settings]);

        return response()->json(['status' => true]);
    }

    public function doctorDashboard(Request $request)
    {
        $user = auth()->user();
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $appointment = Appointment::SetRole(auth()->user());

        $totalappointment = $appointment->where('doctor_id', $user->id)->count();

        $totalpatient = $appointment->distinct()->pluck('user_id')->count();

        $totalearning = EmployeeEarning::where('employee_id', $user->id)->sum('total_amount');

        $total_service = DoctorServiceMapping::where('doctor_id', $user->id)->count();

        $data = [
            'total_appointments' => $totalappointment ?? 0,
            'total_patient' => $totalpatient ?? 0,
            'total_earning' => $totalearning ?? 0,
            'total_service_count' => $total_service ?? 0,

        ];
        return view('backend.dashboard.doctor', compact('data', 'current_user'));
    }
    public function vendorDashboard(Request $request)
    {
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $today = Carbon::today();
        $action = $request->action ?? 'reset';
        $date_range = '';
        $userid = auth()->id();
        $location = Clinics::where('vendor_id', $userid)->where('status', 1)->count();
        $service = ClinicsService::where('vendor_id', $userid)->where('status', 1)->count();
        $appointment = Appointment::query();
        $totalappointment_customer = $appointment->with(['cliniccenter', 'clinicservice'])->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })->count();

        $total_patient = User::role('user')->setRolePatients(auth()->user())->count();

        $paymenthistory = $appointment->with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $childAge = 25;
        $oldAge = 50;

        $childPatientCount = 0;
        $oldPatientCount = 0;
        $adultPatientCount = 0;
        $patient = Appointment::with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })->get();
        foreach ($patient as $appointment) {
            $dob = Carbon::parse(optional($appointment->user)->date_of_birth);
            $age = $dob->diffInYears($today);
            if ($age < $childAge) {
                $childPatientCount++;
            } elseif ($age >= $oldAge) {
                $oldPatientCount++;
            } else {
                $adultPatientCount++;
            }
        }


        $clinics = Clinics::with(['clinicappointment'])
            ->where('vendor_id', $userid)
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->get();

        $toplocation = [];

        foreach ($clinics as $clinic) {
            $locationappointmentCount = optional($clinic->clinicappointment)->count();
            $locationtotalAmount = optional($clinic->clinicappointment)->sum('total_amount');

            $toplocation[] = [
                'clinic_name' => $clinic->name,
                'appointment_count' => $locationappointmentCount,
                'total_amount' => $locationtotalAmount,
                'clinic_image' => $clinic->file_url,
            ];
        }

        $upcomingappointments = $appointment->with(['cliniccenter'])->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })
            ->where('start_date_time', '>', now())
            ->where('status', 'confirmed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $totalrevenue = CommissionEarning::where('employee_id', $userid)->where('commission_status', '!=', 'pending')->sum('commission_amount');

        // $totalrevenue = $appointmenttransaction->sum('appointmenttransaction.total_amount');
        $total_doctor = Doctor::where('vendor_id', $userid)->where('status', 1)->count();
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';
        $data = [
            'total_appointments' => $totalappointment_customer ?? 0,
            'total_commission' => 0,
            'total_revenue' => $totalrevenue ?? 0,
            'total_new_customers' => $total_patient ?? 0,
            'upcomming_appointments' => $upcomingappointments ?? [],
            'top_services' => [],
            'revenue_chart' => [],
            'total_orders' => 0,
            'product_sales' => 0,
            'total_location' => $location ?? 0,
            'total_service' => $service ?? 0,
            'payment_history' => $paymenthistory ?? [],
            'child_patient_count' => $childPatientCount ?? 0,
            'old_patient_count' => $oldPatientCount ?? 0,
            'adult_patient_count' => $adultPatientCount ?? 0,
            'top_location' => $toplocation ?? [],
            'total_doctor' => $total_doctor ?? 0,
            'dateformate' => $dateformate,
            'timeformate' => $timeformate,
        ];

        $totalServices = [];
        $data['total_commission'] = [];

        $data['total_commission'] = \Currency::format(0);

        $bookings = [];

        $data['top_services'] = [];

        $chartBookingRevenue = [];

        $data['revenue_chart']['xaxis'] = [];
        $data['revenue_chart']['total_bookings'] = [];
        $data['revenue_chart']['total_price'] = [];

        $orders = Order::where(function ($q) {
            $q->orWhereIn('order_group_id', OrderGroup::pluck('id'));
        });

        $data['total_orders'] = $orders->count();

        $data['product_sales'] = \Currency::format($orders->sum('total_admin_earnings'));

        return view('backend.dashboard.vendor', compact('data', 'date_range', 'current_user'));
    }
    public function vendorDateRange($daterange)
    {
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $today = Carbon::today();
        $action = request()->action ?? 'reset';
        if ($daterange === null) {
            $startDate = now()->subDays(7)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
            $date_range = $startDate . ' to ' . $endDate;
        } else {
            $decodedDateRange = urldecode($daterange);
            $dateRangeParts = explode(' to ', $decodedDateRange);
            $startDate = $dateRangeParts[0] ?? date('Y-m-d');
            $endDate = $dateRangeParts[1] ?? date('Y-m-d');
            $date_range = $startDate . ' to ' . $endDate;
        }

        $userid = auth()->id();
        $location = Clinics::where('vendor_id', $userid)->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->where('status', 1)->count();
        $service = ClinicsService::where('vendor_id', $userid)->where('status', 1)->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count();
        $appointment = Appointment::query();
        $totalappointment_customer = $appointment->with(['cliniccenter', 'clinicservice'])
            ->where(function ($query) use ($startDate, $endDate) {

                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            })->whereHas('cliniccenter', function ($query) use ($userid) {
                $query->where('vendor_id', $userid);
            })->count();

        $patientcount = $appointment->with(['user', 'cliniccenter'])->where(function ($query) use ($startDate, $endDate) {

            $query->whereDate('start_date_time', '>=', $startDate)
                ->whereDate('start_date_time', '<=', $endDate);
        })->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })
            ->orderByDesc('updated_at')
            ->get();

        $childAge = 25;
        $oldAge = 50;

        $childPatientCount = 0;
        $oldPatientCount = 0;
        $adultPatientCount = 0;
        foreach ($patientcount as $appointment) {
            $dob = Carbon::parse(optional($appointment->user)->date_of_birth);
            $age = $dob->diffInYears($today);
            if ($age < $childAge) {
                $childPatientCount++;
            } elseif ($age >= $oldAge) {
                $oldPatientCount++;
            } else {
                $adultPatientCount++;
            }
        }

        $paymenthistory = Appointment::with(['user', 'clinicservice', 'doctor', 'cliniccenter', 'appointmenttransaction'])
            ->where(function ($query) use ($startDate, $endDate) {

                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            })
            ->whereHas('cliniccenter', function ($query) use ($userid) {
                $query->where('vendor_id', $userid);
            })
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $clinics = Clinics::with([
            'clinicappointment' => function ($query) use ($startDate, $endDate) {
                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            }
        ])
            ->where('vendor_id', $userid)
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->get();

        $toplocation = [];

        foreach ($clinics as $clinic) {
            $locationappointmentCount = optional($clinic->clinicappointment)->count();
            $locationtotalAmount = optional($clinic->clinicappointment)->sum('total_amount');

            $toplocation[] = [
                'clinic_name' => $clinic->name,
                'appointment_count' => $locationappointmentCount,
                'total_amount' => $locationtotalAmount,
                'clinic_image' => $clinic->file_url,
            ];
        }
        $upcomingappointments = Appointment::with(['cliniccenter'])->whereHas('cliniccenter', function ($query) use ($userid) {
            $query->where('vendor_id', $userid);
        })
            ->whereDate('start_date_time', '>=', $startDate)
            ->whereDate('start_date_time', '<=', $endDate)
            ->where('start_date_time', '>', now())
            ->where('status', 'confirmed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $totalrevenue = CommissionEarning::where('employee_id', $userid)->where('commission_status', '!=', 'pending')
            ->WhereHas('getAppointment', function ($appointment) use ($startDate, $endDate) {
                $appointment->whereBetween('start_date_time', [$startDate, $endDate]);
            })
            ->sum('commission_amount');

        $total_doctor = Doctor::where('vendor_id', $userid)->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->where('status', 1)->count();
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';
        $data = [
            'total_appointments' => $totalappointment_customer ?? 0,
            'total_commission' => 0,
            'total_revenue' => $totalrevenue ?? 0,
            'total_new_customers' => $totalappointment_customer ?? 0,
            'upcomming_appointments' => $upcomingappointments ?? [],
            'top_services' => [],
            'revenue_chart' => [],
            'total_orders' => 0,
            'product_sales' => 0,
            'total_location' => $location ?? 0,
            'total_service' => $service ?? 0,
            'payment_history' => $paymenthistory ?? [],
            'child_patient_count' => $childPatientCount ?? 0,
            'old_patient_count' => $oldPatientCount ?? 0,
            'adult_patient_count' => $adultPatientCount ?? 0,
            'top_location' => $toplocation ?? [],
            'total_doctor' => $total_doctor ?? 0,
            'dateformate' => $dateformate,
            'timeformate' => $timeformate,
        ];

        $totalServices = [];
        $data['total_commission'] = [];

        $data['total_commission'] = \Currency::format(0);

        $bookings = [];

        $data['top_services'] = [];

        $chartBookingRevenue = [];

        $data['revenue_chart']['xaxis'] = [];
        $data['revenue_chart']['total_bookings'] = [];
        $data['revenue_chart']['total_price'] = [];

        $orders = Order::where(function ($q) {
            $q->orWhereIn('order_group_id', OrderGroup::pluck('id'));
        });

        $data['total_orders'] = $orders->count();

        $data['product_sales'] = \Currency::format($orders->sum('total_admin_earnings'));

        return view('backend.dashboard.vendor', compact('data', 'date_range', 'current_user'));
    }
    public function receptionistDashboard(Request $request)
    {
        $current_user = setNamePrefix(User::find(auth()->user()->id));
        $user = auth()->user();

        $receptionist = Receptionist::CheckMultivendor()->where('receptionist_id', $user->id)->pluck('clinic_id');

        $totalappointment = 0;
        $totalpatient = 0;
        $totalearning = 0;
        $totalassigndoctor = 0;
        $upcoming_appointment = [];

        if ($receptionist->isNotEmpty()) {
            $appointment = Appointment::SetRole(auth()->user());
            $totalappointment = $appointment->where('clinic_id', $receptionist)->count();
            $totalpatient = User::with('appointment')->whereHas('appointment.cliniccenter', function ($qry) use ($receptionist) {
                $qry->where('clinic_id', $receptionist);
            })->count();
            $totalearning = $appointment->with('appointmenttransaction')->whereHas('appointmenttransaction', function ($q) {
                $q->where('payment_status', '!=', '0');
            })
                ->where('clinic_id', $receptionist)
                ->selectRaw('SUM(total_amount) as total_amount_sum')
                ->first();

            $totalassigndoctor = DoctorClinicMapping::Where('clinic_id', $receptionist)->count();
        }

        $data = [
            'total_appointments' => $totalappointment ?? 0,
            'total_patient' => $totalpatient ?? 0,
            'total_earning' => $totalearning->total_amount_sum ?? 0,
            'total_assign_doctor' => $totalassigndoctor ?? 0,
        ];

        return view('backend.dashboard.receptionist', compact('data', 'current_user'));
    }

    public function nurseDashboard(Request $request)
    {
        $user = auth()->user();
        $current_user = $user; // Keep the user object
        $display_name = setNamePrefix($user); // Get the formatted name separately

        // Get nurse's assigned clinic (similar to receptionist)
        $nurse = \App\Models\Nurse::where('nurse_id', $user->id)->first();
        $clinic_id = $nurse ? $nurse->clinic_id : null;

        $totalappointment = 0;
        $totalpatient = 0;
        $totalearning = 0;
        $totalassigndoctor = 0;

        if ($clinic_id) {
            $appointment = Appointment::SetRole(auth()->user());
            $totalappointment = $appointment->where('clinic_id', $clinic_id)->count();
            $totalpatient = User::with('appointment')->whereHas('appointment.cliniccenter', function ($qry) use ($clinic_id) {
                $qry->where('clinic_id', $clinic_id);
            })->count();
            $totalearning = $appointment->with('appointmenttransaction')->whereHas('appointmenttransaction', function ($q) {
                $q->where('payment_status', '!=', '0');
            })
                ->where('clinic_id', $clinic_id)
                ->selectRaw('SUM(total_amount) as total_amount_sum')
                ->first();

            $totalassigndoctor = DoctorClinicMapping::Where('clinic_id', $clinic_id)->count();
        }

        $data = [
            'total_appointments' => $totalappointment ?? 0,
            'total_patient' => $totalpatient ?? 0,
            'total_earning' => $totalearning->total_amount_sum ?? 0,
            'total_assign_doctor' => $totalassigndoctor ?? 0,
            'clinic_name' => $nurse && $nurse->clinic ? $nurse->clinic->name : 'No Clinic Assigned',
            'nurse_specialization' => $nurse ? $nurse->specialization : 'General Nursing',
            'display_name' => $display_name,
        ];

        return view('backend.dashboard.nurse', compact('data', 'current_user'));
    }

    public function getRevenuechartData(Request $request, $type)
    {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $user = auth()->user();
        $userid = $user->id;

        $date_range_string = $startDate . ' to ' . $endDate;

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        if ($type == 'Year') {

            $monthlyTotals = Appointment::CheckMultivendor()
                ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id') // Join with transaction table
                ->leftJoin('patient_encounters', 'appointments.id', '=', 'patient_encounters.appointment_id') // Join with encounters
                ->leftJoin('billing_record', 'patient_encounters.id', '=', 'billing_record.encounter_id') // Join with billing records
                ->selectRaw('YEAR(appointments.start_date_time) as year')
                ->selectRaw('MONTH(appointments.start_date_time) as month')
                ->selectRaw('SUM(COALESCE(NULLIF(billing_record.final_total_amount, 0), appointment_transactions.total_amount)) as total_amount') // Use billing amount if exists, else transaction amount
                ->where('appointments.status', 'checkout')
                ->where(function($query) {
                    $query->where('appointment_transactions.payment_status', 1)
                          ->orWhere('billing_record.payment_status', 1);
                })
                ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                ->get();


            if (auth()->user()->hasRole('vendor')) {
                $monthlyTotals = CommissionEarning::where('employee_id', $userid)->where('commission_status', '!=', 'pending')
                    ->join('appointments', 'commission_earnings.commissionable_id', '=', 'appointments.id')
                    ->selectRaw('YEAR(appointments.start_date_time) as year, MONTH(appointments.start_date_time) as month, SUM(commission_earnings.commission_amount) as total_amount')
                    ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                    ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                    ->get();
            }
            $chartData = [];

            for ($month = 1; $month <= 12; $month++) {
                $found = false;
                foreach ($monthlyTotals as $total) {
                    if ((int) $total->month === $month) {
                        $total_amount = (float) $total->total_amount;
                        $chartData[] = round($total_amount, 2);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            }
            ;

            $category = [
                __('dashboard.Jan'),
                __('dashboard.Feb'),
                __('dashboard.Mar'),
                __('dashboard.Apr'),
                __('dashboard.May'),
                __('dashboard.Jun'),
                __('dashboard.Jul'),
                __('dashboard.Aug'),
                __('dashboard.Sep'),
                __('dashboard.Oct'),
                __('dashboard.Nov'),
                __('dashboard.Dec')
            ];
        } else if ($type == 'Month') {

            $firstWeek = Carbon::now()->startOfMonth()->week;

            $monthlyWeekTotals = Appointment::CheckMultivendor()
                ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id') // Join with transaction table
                ->leftJoin('patient_encounters', 'appointments.id', '=', 'patient_encounters.appointment_id') // Join with encounters
                ->leftJoin('billing_record', 'patient_encounters.id', '=', 'billing_record.encounter_id') // Join with billing records
                ->selectRaw('YEAR(appointments.start_date_time) as year, MONTH(appointments.start_date_time) as month, WEEK(appointments.start_date_time) as week, COALESCE(SUM(COALESCE(NULLIF(billing_record.final_total_amount, 0), appointment_transactions.total_amount)), 0) as total_amount') // Use billing amount if exists
                ->where('appointments.status', 'checkout')
                ->where(function($query) {
                    $query->where('appointment_transactions.payment_status', 1)
                          ->orWhere('billing_record.payment_status', 1);
                })
                ->whereYear('appointments.start_date_time', $currentYear)
                ->whereMonth('appointments.start_date_time', $currentMonth)
                ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time), WEEK(appointments.start_date_time)')
                ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time), WEEK(appointments.start_date_time)')
                ->get();

            if (auth()->user()->hasRole('vendor')) {

                $monthlyWeekTotals = CommissionEarning::where('employee_id', $userid)
                    ->where('commission_status', '!=', 'pending')
                    ->join('appointments', 'commission_earnings.commissionable_id', '=', 'appointments.id') // assuming commissionable_id relates to appointments
                    ->selectRaw('YEAR(appointments.start_date_time) as year, MONTH(appointments.start_date_time) as month, WEEK(appointments.start_date_time) as week, COALESCE(SUM(commission_earnings.commission_amount), 0) as total_amount')
                    ->whereYear('appointments.start_date_time', $currentYear)
                    ->whereMonth('appointments.start_date_time', $currentMonth)
                    ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time), WEEK(appointments.start_date_time)')
                    ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time), WEEK(appointments.start_date_time)')
                    ->get();
            }

            $chartData = [];


            for ($i = $firstWeek; $i <= $firstWeek + 4; $i++) {
                $found = false;

                foreach ($monthlyWeekTotals as $total) {

                    if ((int) $total->month === $currentMonth && (int) $total->week === $i) {
                        $total_amount = (float) $total->total_amount;
                        $chartData[] = round($total_amount, 2);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            }

            $category = ["Week 1", "Week 2", "Week 3", "Week 4", 'Week 5'];
        } else if ($type == 'Week') {

            $currentWeekStartDate = Carbon::now()->startOfWeek();
            $lastDayOfWeek = Carbon::now()->endOfWeek();

            $weeklyDayTotals = Appointment::CheckMultivendor()
                ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id') // Join with transaction table
                ->leftJoin('patient_encounters', 'appointments.id', '=', 'patient_encounters.appointment_id') // Join with encounters
                ->leftJoin('billing_record', 'patient_encounters.id', '=', 'billing_record.encounter_id') // Join with billing records
                ->selectRaw('DAY(appointments.start_date_time) as day, COALESCE(SUM(COALESCE(NULLIF(billing_record.final_total_amount, 0), appointment_transactions.total_amount)), 0) as total_amount') // Use billing amount if exists
                ->where('appointments.status', 'checkout')
                ->where(function($query) {
                    $query->where('appointment_transactions.payment_status', 1)
                          ->orWhere('billing_record.payment_status', 1);
                })
                ->whereYear('appointments.start_date_time', $currentYear)
                ->whereMonth('appointments.start_date_time', $currentMonth)
                ->whereBetween('appointments.start_date_time', [$currentWeekStartDate, $currentWeekStartDate->copy()->addDays(6)])
                ->groupBy(DB::raw('DAY(appointments.start_date_time)'))
                ->orderBy(DB::raw('DAY(appointments.start_date_time)'))
                ->get();

            if (auth()->user()->hasRole('vendor')) {

                $weeklyDayTotals = CommissionEarning::where('employee_id', $userid)
                    ->where('commission_status', '!=', 'pending')
                    ->join('appointments', 'commission_earnings.commissionable_id', '=', 'appointments.id')
                    ->selectRaw('DAY(appointments.start_date_time) as day, COALESCE(SUM(commission_earnings.commission_amount), 0) as total_amount')
                    ->where('appointments.status', 'checkout')
                    ->whereYear('appointments.start_date_time', $currentYear)
                    ->whereMonth('appointments.start_date_time', $currentMonth)
                    ->whereBetween('appointments.start_date_time', [$currentWeekStartDate, $currentWeekStartDate->copy()->addDays(6)])
                    ->groupBy(DB::raw('DAY(appointments.start_date_time)'))
                    ->orderBy(DB::raw('DAY(appointments.start_date_time)'))
                    ->get();

            }

            $chartData = [];

            for ($day = $currentWeekStartDate; $day <= $lastDayOfWeek; $day->addDay()) {
                $found = false;

                foreach ($weeklyDayTotals as $total) {
                    if ((int) $total->day === $day->day) {
                        $total_amount = (float) $total->total_amount;
                        $chartData[] = round($total_amount, 2);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $chartData[] = 0;
                }
            }
            ;

            $category = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        } else {
            // For custom date ranges, determine the best grouping based on range length
            $startDateObj = \Carbon\Carbon::parse($startDate);
            $endDateObj = \Carbon\Carbon::parse($endDate);
            $daysDifference = $startDateObj->diffInDays($endDateObj);
            
            if ($daysDifference <= 7) {
                // For ranges <= 7 days, show daily data
                $monthlyTotals = Appointment::CheckMultivendor()
                    ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id') // Join with transaction table
                    ->leftJoin('patient_encounters', 'appointments.id', '=', 'patient_encounters.appointment_id') // Join with encounters
                    ->leftJoin('billing_record', 'patient_encounters.id', '=', 'billing_record.encounter_id') // Join with billing records
                    ->selectRaw('DATE(appointments.start_date_time) as date')
                    ->selectRaw('SUM(COALESCE(NULLIF(billing_record.final_total_amount, 0), appointment_transactions.total_amount)) as total_amount') // Use billing amount if exists
                    ->where('appointments.status', 'checkout')
                    ->where(function($query) {
                        $query->where('appointment_transactions.payment_status', 1)
                              ->orWhere('billing_record.payment_status', 1);
                    })
                    ->whereBetween('appointments.start_date_time', [$startDate, $endDate])
                    ->groupBy(DB::raw('DATE(appointments.start_date_time)'))
                    ->orderBy(DB::raw('DATE(appointments.start_date_time)'))
                    ->get();
            } else {
                // For longer ranges, show monthly data
                $monthlyTotals = Appointment::CheckMultivendor()
                    ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id') // Join with transaction table
                    ->leftJoin('patient_encounters', 'appointments.id', '=', 'patient_encounters.appointment_id') // Join with encounters
                    ->leftJoin('billing_record', 'patient_encounters.id', '=', 'billing_record.encounter_id') // Join with billing records
                    ->selectRaw('YEAR(appointments.start_date_time) as year')
                    ->selectRaw('MONTH(appointments.start_date_time) as month')
                    ->selectRaw('SUM(COALESCE(NULLIF(billing_record.final_total_amount, 0), appointment_transactions.total_amount)) as total_amount') // Use billing amount if exists
                    ->where('appointments.status', 'checkout')
                    ->where(function($query) {
                        $query->where('appointment_transactions.payment_status', 1)
                              ->orWhere('billing_record.payment_status', 1);
                    })
                    ->whereBetween('appointments.start_date_time', [$startDate, $endDate])
                    ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                    ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                    ->get();
            }

            if (auth()->user()->hasRole('vendor')) {
                if ($daysDifference <= 7) {
                    // For ranges <= 7 days, show daily data
                    $monthlyTotals = CommissionEarning::where('employee_id', $userid)
                        ->where('commission_status', '!=', 'pending')
                        ->join('appointments', 'commission_earnings.commissionable_id', '=', 'appointments.id')
                        ->selectRaw('DATE(appointments.start_date_time) as date, SUM(commission_earnings.commission_amount) as total_amount')
                        ->whereBetween('appointments.start_date_time', [$startDate, $endDate])
                        ->groupBy(DB::raw('DATE(appointments.start_date_time)'))
                        ->orderBy(DB::raw('DATE(appointments.start_date_time)'))
                        ->get();
                } else {
                    // For longer ranges, show monthly data
                    $monthlyTotals = CommissionEarning::where('employee_id', $userid)
                        ->where('commission_status', '!=', 'pending')
                        ->join('appointments', 'commission_earnings.commissionable_id', '=', 'appointments.id')
                        ->selectRaw('YEAR(appointments.start_date_time) as year, MONTH(appointments.start_date_time) as month, SUM(commission_earnings.commission_amount) as total_amount')
                        ->whereBetween('appointments.start_date_time', [$startDate, $endDate])
                        ->groupByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                        ->orderByRaw('YEAR(appointments.start_date_time), MONTH(appointments.start_date_time)')
                        ->get();
                }
            }

            $chartData = [];
            $category = [];

            if ($daysDifference <= 7) {
                // Handle daily data
                $currentDate = $startDateObj->copy();
                while ($currentDate <= $endDateObj) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $category[] = $currentDate->format('M d');
                    
                    $totalAmount = $monthlyTotals->where('date', $dateStr)->sum('total_amount');
                    $total_amount = (float) $totalAmount;
                    $chartData[] = round($total_amount, 2);
                    
                    $currentDate->addDay();
                }
            } else {
                // Handle monthly data
                if ($startDateObj->year == $endDateObj->year) {
                    // Same year - show months from start to end
                    $monthStart = $startDateObj->month;
                    $monthEnd = $endDateObj->month;
                    
                    for ($month = $monthStart; $month <= $monthEnd; $month++) {
                        $category[] = date("M", mktime(0, 0, 0, $month, 1));
                        
                        $totalAmount = $monthlyTotals->where('month', $month)->sum('total_amount');
                        $total_amount = (float) $totalAmount;
                        $chartData[] = round($total_amount, 2);
                    }
                } else {
                    // Different years - show all months in range
                    for ($year = $startDateObj->year; $year <= $endDateObj->year; $year++) {
                        $monthStart = ($year == $startDateObj->year) ? $startDateObj->month : 1;
                        $monthEnd = ($year == $endDateObj->year) ? $endDateObj->month : 12;

                        for ($month = $monthStart; $month <= $monthEnd; $month++) {
                            $category[] = date("M Y", mktime(0, 0, 0, $month, 1, $year));

                            $totalAmount = $monthlyTotals->where('month', $month)->where('year', $year)->sum('total_amount');
                            $total_amount = (float) $totalAmount;
                            $chartData[] = round($total_amount, 2);
                        }
                    }
                }
            }
        }

        $data = [

            'chartData' => $chartData,
            'category' => $category

        ];

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function getAppointments(Request $request)
    {
        $user = auth()->user();

        $data = Appointment::with('user')->where('doctor_id', $user->id)->get();

        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';

        $data->transform(function ($appointment) use ($timezone) {
            $service = ClinicsService::with('systemservice')->find($appointment->service_id);
            $appointment->service_name = $service ? $service->name ?? null : null;

            $appointment->start_date_time = Carbon::parse($appointment->start_date_time)->setTimezone($timezone)->format('Y-m-d H:i:s');

            return $appointment;
        });

        return response()->json(['data' => $data, 'status' => true]);
    }


    public function getClinicAppointments(Request $request)
    {
        $user = auth()->user();


        $clinic_id = Receptionist::where('receptionist_id', $user->id)->pluck('clinic_id');

        $data = Appointment::with('user')->where('clinic_id', $clinic_id)->get();

        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';

        $data->transform(function ($appointment) use ($timezone) {
            $service = ClinicsService::with('systemservice')->find($appointment->service_id);
            $appointment->service_name = $service ? $service->name ?? null : null;

            $appointment->start_date_time = Carbon::parse($appointment->start_date_time)->setTimezone($timezone)->format('Y-m-d H:i:s');

            return $appointment;
        });
        return response()->json(['data' => $data, 'status' => true]);
    }


    public function getAjaxList(Request $request)
    {
        $items = [];
        $value = $request->q;

        $auth_user = auth()->user();

        switch ($request->type) {
            case 'service':
                $items = ClinicsService::CheckMultivendor()->select('id', 'name as text')->where('status', 1)->get();
                break;
            case 'category':
                $items = ClinicsCategory::select('id', 'name as text')->where('status', 1)->where('featured', 1)->get();
                break;
            case 'clinic':
                $items = Clinics::CheckMultivendor()->select('id', 'name as text')->where('status', 1)->get();
                break;
            case 'doctor':
                $items = Doctor::CheckMultivendor()->with('user:id,first_name,last_name')
                    ->whereHas('user', function ($query) {
                        $query->where('status', 1);
                    })
                    ->get()
                    ->map(function ($doctor) {
                        return [
                            'id' => $doctor->id,
                            'text' => optional($doctor->user)->first_name . ' ' . optional($doctor->user)->last_name,
                        ];
                    });
                break;
            case 'blog':

                $items = Blog::select('id', 'title as text')->where('status', 1)->get();
                break;

            default:
                $items = collect();
                break;
        }

        return response()->json(['status' => true, 'results' => $items]);
    }

}
