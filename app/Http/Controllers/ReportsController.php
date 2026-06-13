<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\ReportTrait;
use DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Earning\Models\EmployeeEarning;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Modules\Product\Models\Order;
use Modules\Product\Models\OrderGroup;
use Currency;
use Modules\Clinic\Models\Clinics;
use Modules\Commission\Models\CommissionEarning;
use Modules\Constant\Models\Constant;
use App\Models\Setting;

class ReportsController extends Controller
{
    use ReportTrait;
    public function __construct()
    {
        // Page Title

        $this->module_title = 'report.report';

        // module name
        $this->module_name = 'reports';

        // module icon
        $this->module_icon = 'fa-solid fa-chart-line';

        view()->share([
            'module_icon' => $this->module_icon,
        ]);
    }

    public function commission_revenue(Request $request)
    {
        $query = Appointment::SetRole(auth()->user())->with('cliniccenter', 'commissionsdata', 'appointmenttransaction')
            ->where('status', 'checkout')
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->get();


        $totalAppointments = $query->count();

        $totalDoctorCommission = $query->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'doctor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();




        $totalAdminCommission = $query->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'admin')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();




        $totalClinicRevenue = $query->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'vendor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();




        $module_title = $this->module_title;

        return view('backend.reports.commission-revenue', compact('module_title', 'totalAppointments', 'totalDoctorCommission', 'totalAdminCommission', 'totalClinicRevenue'));
    }

    public function commission_revenue_index_data(Datatables $datatable, Request $request)
    {
        $module_title = __('report.title_commission_revenue');

        $query = Appointment::with('user', 'doctor', 'clinicservice', 'cliniccenter', 'commissionsdata', 'appointmenttransaction')
            ->where('status', 'checkout')
            ->whereHas('cliniccenter', function ($query) {
                $query->where('vendor_id', auth()->id());
            })
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            });

        $filter = $request->filter;

        if (isset($filter['appointment_date'])) {
            try {
                $startDate = explode(' to ', $filter['appointment_date'])[0];
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate = explode(' to ', $filter['appointment_date'])[1];
                $endDate = date('Y-m-d', strtotime($endDate));
                $query = $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        return $datatable->eloquent($query)
            ->addIndexColumn()
            ->addColumn('user_name', function ($appointment) {
                return $appointment->user->first_name . ' ' . $appointment->user->last_name;
            })
            ->addColumn('employee_name', function ($appointment) {
                return $appointment->doctor->first_name . ' ' . $appointment->doctor->last_name;
            })
            ->addColumn('clinic_name', function ($appointment) {
                return $appointment->cliniccenter->name;
            })
            ->addColumn('service_name', function ($appointment) {
                return $appointment->clinicservice->systemservice->name;
            })
            ->addColumn('price', function ($appointment) {
                return Currency::format($appointment->service_amount);
            })
            ->addColumn('start_date_time', function ($appointment) {
                $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';

                $dateSetting = Setting::where('name', 'date_formate')->first();
                $dateformate = $dateSetting ? $dateSetting->val : 'Y-m-d';

                $timeSetting = Setting::where('name', 'time_formate')->first();
                $timeformate = $timeSetting ? $timeSetting->val : 'h:i A';

                $combinedFormat = $dateformate . ' ' . $timeformate;
                $date = Carbon::parse($appointment->start_date_time)->timezone($timezone)->format($combinedFormat);
                // return customDate($appointment->start_date_time);
                return $date;
            })
            ->addColumn('doctor_pay', function ($appointment) {

                $commission = $appointment->commissionsdata->firstWhere('user_type', 'doctor');

                if ($commission) {
                    return Currency::format($commission->commission_amount);
                }
                return Currency::format(0);
            })
            ->addColumn('admin_pay', function ($appointment) {

                $commission = $appointment->commissionsdata->firstWhere('user_type', 'admin');

                if ($commission) {
                    return Currency::format($commission->commission_amount);
                }
                return Currency::format(0);
            })
            ->addColumn('clinic_pay', function ($appointment) {

                $commission = $appointment->commissionsdata->firstWhere('user_type', 'vendor');

                if ($commission) {
                    return Currency::format($commission->commission_amount);
                }
                return Currency::format(0);
            })
            ->editColumn('action', function ($data) use ($module_title) {
                return '';
            })
            // Add other columns as needed
            ->rawColumns(['action'])
            ->toJson();
    }

    public function appointment_overview(Request $request)
    {
        // $module_title = $this->module_title;

        $module_title = __('report.title_appointment_overview');

        return view('backend.reports.appointment-overview', compact('module_title'));
    }

    // se how i take oin this

    public function appointment_overview_index_data(Datatables $datatable, Request $request)
    {
        $module_title = __('report.title_commission_revenue');
        $query = Appointment::SetRole(auth()->user())
            ->with('user', 'doctor', 'clinicservice', 'cliniccenter', 'appointmenttransaction', 'patientEncounter');

        $query = $query->where('status', 'checkout');

        $filter = $request->filter;

        // Date range filter
        if (isset($filter['appointment_date'])) {
            try {
                $dateParts = explode(' to ', $filter['appointment_date']);
                $startDate = isset($dateParts[0]) ? date('Y-m-d', strtotime($dateParts[0])) : null;
                $endDate = isset($dateParts[1]) ? date('Y-m-d', strtotime($dateParts[1])) : null;
                if ($startDate && $endDate) {
                    $query = $query->whereDate('start_date_time', '>=', $startDate)
                        ->whereDate('start_date_time', '<=', $endDate);
                }
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        return $datatable->eloquent($query)
            ->editColumn('user_id', function ($appointment) {
                return optional($appointment->user)->first_name . ' ' . optional($appointment->user)->last_name;
            })
            ->editColumn('doctor_id', function ($appointment) {
                return optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name;
            })
            ->editColumn('clinic_id', function ($appointment) {
                return optional($appointment->cliniccenter)->name;
            })
            ->editColumn('service_id', function ($appointment) {
                return optional($appointment->clinicservice)->name;
            })
            ->editColumn('service_amount', function ($appointment) {
                // Take into account patient encounter billing items (like in appointment_detail)
                $serviceTotal = 0;

                if (
                    $appointment->relationLoaded('patientEncounter') &&
                    $appointment->patientEncounter &&
                    optional(optional($appointment->patientEncounter)->billingrecord)->billingItem != null
                ) {
                    // Sum each billing item (taking quantity and discount per item)
                    foreach ($appointment->patientEncounter->billingrecord->billingItem as $billingItem) {
                        $quantity = $billingItem->quantity ?? 1;
                        $service_price = $billingItem->service_amount;
                        $inclusive_tax = $billingItem->inclusive_tax_amount ?? 0;
                        $price_per_unit = $service_price + $inclusive_tax;
                        $item_total = $price_per_unit * $quantity;

                        $discount = 0;
                        if ($billingItem->discount_value > 0) {
                            if ($billingItem->discount_type === 'percentage') {
                                $discount = $item_total * ($billingItem->discount_value / 100);
                            } else {
                                $discount = $billingItem->discount_value;
                            }
                        }

                        $final_total = $item_total - $discount;
                        $serviceTotal += $final_total;
                    }
                    return Currency::format($serviceTotal);
                } else {
                    // Fallback: use appointment's service_amount and check for appointment-level discount and inclusive_tax
                    $service_amount = $appointment->service_amount ?? 0;
                    $inclusive_tax = optional($appointment->clinicservice)->inclusive_tax_price ?? 0;
                    $serviceTotalWithTax = $service_amount + $inclusive_tax;

                    // Appointment transaction discount
                    $discount_percent = optional($appointment->appointmenttransaction)->discount_value;
                    $discount_type = optional($appointment->appointmenttransaction)->discount_type ?? 'percentage';

                    $discount_amount = 0;
                    if ($discount_percent > 0) {
                        if ($discount_type === 'percentage') {
                            $discount_amount = $serviceTotalWithTax * ($discount_percent / 100);
                        } else {
                            $discount_amount = $discount_percent;
                        }
                    }

                    $serviceAfterDiscount = $serviceTotalWithTax - $discount_amount;
                    return Currency::format($serviceAfterDiscount);
                }
            })
            ->editColumn('total_amount', function ($appointment) {
                return Currency::format($appointment->total_amount);
            })
            ->editColumn('start_date_time', function ($appointment) {
                $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
                $dateSetting = Setting::where('name', 'date_formate')->first();
                $dateformate = $dateSetting ? $dateSetting->val : 'Y-m-d';

                $timeSetting = Setting::where('name', 'time_formate')->first();
                $timeformate = $timeSetting ? $timeSetting->val : 'h:i A';

                $combinedFormat = $dateformate . ' ' . $timeformate;
                // $date = Carbon::parse($appointment->start_date_time)->timezone($timezone)->format($combinedFormat);
                $date = Carbon::parse($appointment->start_date_time)->format($combinedFormat);
                return $date;
            })
            ->editColumn('status', function ($appointment) {
                $appointment_status = Constant::getAllConstant()->where('name', $appointment->status)->first();
                $status_value = $appointment_status ? $appointment_status->value : $appointment->status;

                if (strtolower($status_value) === 'checkout') {
                    $status_value = 'complete';
                }

                return ucfirst($status_value);
            })
            ->editColumn('payment_status', function ($appointment) {
                return optional($appointment->appointmenttransaction)->payment_status == 1 ? 'Paid' : 'Pending';
            })
            // Add other columns as needed
            ->rawColumns([
                'action',
                'user_id',
                'doctor_id',
                'service_id',
                'service_amount',
                'total_amount',
                'start_date_time',
                'status',
                'payment_status'
            ])
            ->toJson();
    }


    public function clinic_overview(Request $request)
    {
        $query = Appointment::SetRole(auth()->user())->with('commissionsdata', 'cliniccenter', 'appointmenttransaction')->where('status', 'checkout');

        $query->whereHas('appointmenttransaction', function ($query) {
            $query->where('payment_status', 1);
        });

        $appointments = $query->get();

        $totalAppointments = $appointments->count();

        $totalDoctorCommission = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'doctor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();

        $totalAdminCommission = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'admin')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();

        $totalClinicRevenue = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'vendor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();
        $clinics = Clinics::CheckMultivendor()->where('status', 1)->get();
        $total_clinics = $clinics->count();
        // $module_title = $this->module_title;

        $module_title = __('report.title_clinic_overview');

        return view('backend.reports.clinic-overview', compact('module_title', 'total_clinics', 'totalAppointments', 'totalDoctorCommission', 'totalAdminCommission', 'totalClinicRevenue'));
    }

    public function clinic_overview_date_range(Request $request)
    {
        $dateRange = $request->date_range;

        if ($request->has('date_range') && $dateRange !== null) {
            $dateRangeParts = explode(' to ', $dateRange);
            $startDate = isset($dateRangeParts[0]) ? date('Y-m-d', strtotime($dateRangeParts[0])) : date('Y-m-d');
            $endDate = isset($dateRangeParts[1]) ? date('Y-m-d', strtotime($dateRangeParts[1])) : date('Y-m-d');
        }

        $query = Appointment::with('commissionsdata', 'cliniccenter', 'appointmenttransaction')
            ->where('status', 'checkout')
            ->whereHas('appointmenttransaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            });




        if (auth()->user()->hasrole('vendor')) {

            $query->whereHas('cliniccenter', function ($query) {
                $query->where('vendor_id', auth()->id());
            });
        } else if (auth()->user()->hasrole('doctor')) {
            $query->where('doctor_id', auth()->id());
        } else if (auth()->user()->hasRole('receptionist')) {
            $query->whereHas('cliniccenter.receptionist', function ($query) {
                $query->where('receptionist_id', auth()->id());
            });
        }



        $appointments = $query->get();

        $totalAppointments = $appointments->count();

        $totalDoctorCommission = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'doctor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();

        $totalAdminCommission = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'admin')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();

        $totalClinicRevenue = $appointments->flatMap(function ($appointment) {
            return $appointment->commissionsdata
                ->where('user_type', 'vendor')
                ->where('commission_status', '!=', 'pending')
                ->pluck('commission_amount');
        })->sum();

        $module_title = $this->module_title;

        $data = [
            'total_appointments' => $totalAppointments,
            'total_doctor_commission' => $totalDoctorCommission,
            'total_admin_commission' => $totalAdminCommission,
            'total_clinic_revenue' => $totalClinicRevenue,

        ];

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function clinic_overview_index_data(Datatables $datatable, Request $request)
    {
        $module_title = __('report.title_commission_revenue');
        $query = Clinics::SetRole(auth()->user())->with([
            'vendor',
            'clinicdoctor',
            'receptionist',
            'clinicappointment' => function ($query) {
                $query->where('status', 'checkout');
            }
        ])
            ->where('clinic.status', 1);


        // if (auth()->user()->hasrole('vendor')) {

        //     $query->where('vendor_id', auth()->id());
        // } else if (auth()->user()->hasrole('doctor')) {
        //     $query->whereHas('clinicdoctor.doctor', function ($query) {
        //         $query->where('doctor_id', auth()->id());
        //     });
        // } else if (auth()->user()->hasRole('receptionist')) {
        //     $query->whereHas('receptionist', function ($query) {
        //         $query->where('receptionist_id', auth()->id());
        //     });
        // }

        $filter = $request->filter;

        if (isset($filter['appointment_date'])) {
            try {
                $startDate = explode(' to ', $filter['appointment_date'])[0];
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate = explode(' to ', $filter['appointment_date'])[1];
                $endDate = date('Y-m-d', strtotime($endDate));
                $query = $query->whereHas('clinicappointment', function ($query) use ($startDate, $endDate) {
                    $query->whereDate('start_date_time', '>=', $startDate)
                        ->whereDate('start_date_time', '<=', $endDate);
                });
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }
        return $datatable->eloquent($query)
            ->addIndexColumn()
            ->editColumn('clinic_admin', function ($data) {
                return optional($data->vendor)->first_name . '' . optional($data->vendor)->last_name; // Assuming 'name' is the field for vendor name
            })

            ->orderColumn('clinic_admin', function ($query, $order) {
                $query->join('users as vendor', 'clinic.vendor_id', '=', 'vendor.id')
                    ->orderBy('vendor.first_name', $order)
                    ->orderBy('vendor.last_name', $order);
            })


            ->editColumn('no_booking', function ($data) {

                $appointment_count = $data->clinicappointment()
                    ->where('status', 'checkout')
                    ->whereHas('appointmenttransaction', function ($query) {
                        $query->where('payment_status', 1);
                    })
                    ->count();

                return $appointment_count;
            })

            ->orderColumn('no_booking', function ($query, $order) {
                $query->orderBy(function ($query) {
                    $query->select(DB::raw('COUNT(*)'))
                        ->from('appointments')
                        ->join('appointment_transactions', 'appointments.id', '=', 'appointment_transactions.appointment_id')
                        ->whereColumn('appointments.clinic_id', 'clinic.id')
                        ->where('appointments.status', 'checkout')
                        ->where('appointment_transactions.payment_status', 1);
                }, $order);
            })

            ->editColumn('total_income', function ($data) {

                $appointment_amount = $data->clinicappointment()
                    ->where('status', 'checkout')
                    ->whereHas('appointmenttransaction', function ($query) {
                        $query->where('payment_status', 1);
                    })->sum('total_amount');

              
                \Log::info('Total appointment amount calculated: ' . $appointment_amount);

                return Currency::format($appointment_amount);
            })

            ->orderColumn('total_income', function ($query, $order) {
                $query->orderBy(DB::raw('(SELECT SUM(total_amount) FROM appointments WHERE clinic_id = clinic.id AND status = "completed" AND EXISTS (SELECT * FROM appointment_transactions WHERE appointment_transactions.appointment_id = appointments.id AND payment_status = 1))'), $order);
            })


            ->editColumn('doctor_pay', function ($data) {

                $data['commission_type'] = 'doctor';
                $doctorpay = $this->getCommission($data);
                return Currency::format($doctorpay);
            })

            ->orderColumn('doctor_pay', function ($query, $order) {
                $query->orderBy(DB::raw('(SELECT SUM(commission_amount) FROM commission_earnings
                                          WHERE commissionable_id IN (
                                              SELECT id FROM clinic_appointments
                                              WHERE clinic_id = clinic.id
                                          )
                                          AND user_type = "doctor"
                                          AND commission_status != "pending")'), $order);
            })

            ->editColumn('clinic_pay', function ($data) {
                $data['commission_type'] = 'vendor';
                $clinicpay = $this->getCommission($data);
                return Currency::format($clinicpay);
            })

            ->orderColumn('clinic_pay', function ($query, $order) {
                $query->orderBy(DB::raw('(SELECT SUM(commission_amount) FROM commission_earnings
                                          WHERE commissionable_id IN (
                                              SELECT id FROM clinic_appointments
                                              WHERE clinic_id = clinic.id
                                          )
                                          AND user_type = "vendor"
                                          AND commission_status != "pending")'), $order);
            })

            ->editColumn('admin_pay', function ($data) {
                $data['commission_type'] = 'admin';
                $adminpay = $this->getCommission($data);
                return Currency::format($adminpay);
            })

            ->orderColumn('admin_pay', function ($query, $order) {
                $query->orderBy(DB::raw('(SELECT SUM(commission_amount) FROM commission_earnings
                                          WHERE commissionable_id IN (
                                              SELECT id FROM clinic_appointments
                                              WHERE clinic_id = clinic.id
                                          )
                                          AND user_type = "admin"
                                          AND commission_status != "pending")'), $order);
            })
            ->editColumn('action', function ($data) use ($module_title) {
                return view('backend.reports.commission-view', compact('module_title', 'data'));
            })
            // Add other columns as needed
            ->rawColumns(['action', 'clinic_admin', 'clinic_name', 'no_booking', 'total_income', 'doctor_pay', 'clinic_pay', 'admin_pay'])
            ->toJson();
    }


    public function doctor_payout_report(Request $request)
    {
        $module_title = __('report.doctor_payout');

        return view('backend.reports.doctor-payout-report', compact('module_title'));
    }

    public function doctor_payout_report_index_data(Datatables $datatable, Request $request)
    {
        $query = EmployeeEarning::doctorRole(auth()->user())->with('employee')->where('user_type', 'doctor');

        $filter = $request->filter;

        if (isset($filter['appointment_date'])) {
            $appointmentDates = explode(' to ', $filter['appointment_date']);

            if (count($appointmentDates) >= 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($appointmentDates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($appointmentDates[1]));

                $query->where('payment_date', '>=', $startDate)
                    ->where('payment_date', '<=', $endDate);
            }
        }

        if (isset($filter['employee_id'])) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('employee_id', $filter['employee_id']);
            });
        }

        return $datatable->eloquent($query)
            ->editColumn('payment_date', function ($data) {
                return formatDate($data->payment_date);
            })
            ->editColumn('first_name', function ($data) {
                return '
                        <div class="d-flex gap-3 align-items-center">
                            <img src="' . (optional($data->employee)->profile_image ?? default_user_avatar()) . '" alt="avatar" class="avatar avatar-40 rounded-pill">
                            <div class="text-start">
                                <h6 class="m-0">' . (optional($data->employee)->full_name ?? default_user_name()) . '</h6>
                                <span>' . (optional($data->employee)->email ?? '--') . '</span>
                            </div>
                        </div>
                    ';
                // return $data->employee->full_name;
            })
            ->orderColumn('first_name', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = bookings.employee_id LIMIT 1)'), $order);
            }, 1)
            ->editColumn('commission_amount', function ($data) {
                return Currency::format($data->commission_amount ?? 0);
            })
            ->editColumn('tip_amount', function ($data) {
                return Currency::format($data->tip_amount ?? 0);
            })
            ->editColumn('total_pay', function ($data) {
                return Currency::format($data->total_amount ?? 0);
            })
            ->addIndexColumn()
            ->rawColumns(['first_name'])
            ->toJson();
    }

    public function vendor_payout_report(Request $request)
    {
        $module_title = __('report.vendor_payout');

        return view('backend.reports.vendor-payout-report', compact('module_title'));
    }

    public function vendor_payout_report_index_data(Datatables $datatable, Request $request)
    {

        $query = EmployeeEarning::with('employee')->where('user_type', 'vendor');

        $filter = $request->filter;

        if (isset($filter['appointment_date'])) {
            $appointmentDates = explode(' to ', $filter['appointment_date']);

            if (count($appointmentDates) >= 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($appointmentDates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($appointmentDates[1]));

                $query->where('payment_date', '>=', $startDate)
                    ->where('payment_date', '<=', $endDate);
            }
        }

        if (isset($filter['employee_id'])) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('employee_id', $filter['employee_id']);
            });
        }

        return $datatable->eloquent($query)
            ->editColumn('payment_date', function ($data) {
                return formatDate($data->payment_date);

            })
            ->editColumn('first_name', function ($data) {
                return $data->employee->full_name;
            })
            ->orderColumn('first_name', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = bookings.employee_id LIMIT 1)'), $order);
            }, 1)
            ->editColumn('commission_amount', function ($data) {
                return Currency::format($data->commission_amount ?? 0);
            })
            ->editColumn('tip_amount', function ($data) {
                return Currency::format($data->tip_amount ?? 0);
            })
            ->editColumn('total_pay', function ($data) {
                return Currency::format($data->total_amount ?? 0);
            })
            ->addIndexColumn()
            ->rawColumns([])
            ->toJson();
    }
}
