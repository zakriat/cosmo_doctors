<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Models\Appointment;
use Carbon\Carbon;
use Modules\Clinic\Models\ClinicsService;
use Modules\Commission\Models\CommissionEarning;
use Modules\Appointment\Trait\AppointmentTrait;
use App\Models\Setting;
use Modules\Appointment\Models\BillingItem;
use Modules\Appointment\Trait\BillingRecordTrait;
use Modules\Appointment\Transformers\BillingItemResource;
use Modules\Clinic\Http\Controllers\ClinicsServiceController;
use App\Models\User;
use Modules\Tip\Models\TipEarning;
use Modules\Clinic\Models\Clinics;
class BillingRecordController extends Controller
{
    use AppointmentTrait;
    use BillingRecordTrait;
    protected string $exportClass = '\App\Exports\BillingExport';
    public function __construct()
    {
        // Page Title
        $this->module_title = 'appointment.billing_record';
        // module name
        $this->module_name = 'billing-record';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'payment_status' => $request->payment_status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new BillingRecord());
        $customefield = CustomField::exportCustomFields(new BillingRecord());
        $service = ClinicsService::SetRole(auth()->user())->with('sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice')->where('status', 1)->get();

        $export_import = true;
        $export_columns = [
            [
                'value' => 'encounter_id',
                'text' => __('appointment.lbl_encounter_id'),
            ],
            [
                'value' => 'user_id',
                'text' => __('appointment.lbl_patient_name'),
            ],
            [
                'value' => 'clinic_id',
                'text' => __('appointment.lbl_clinic'),
            ],
            [
                'value' => 'doctor_id',
                'text' => __('appointment.lbl_doctor'),
            ],
            [
                'value' => 'service_id',
                'text' => __('appointment.lbl_service'),
            ],
            [
                'value' => 'total_amount',
                'text' => __('appointment.lbl_total_amount'),
            ],
            [
                'value' => 'date',
                'text' => __('appointment.lbl_date'),
            ],
            [
                'value' => 'payment_status',
                'text' => __('appointment.lbl_payment_status'),
            ],

        ];
        $export_url = route('backend.billing-record.export');

        return view('appointment::backend.billing_record.index_datatable', compact('service', 'module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $clinic = BillingRecord::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('clinic.clinic_status');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                BillingRecord::whereIn('id', $ids)->delete();
                $message = __('clinic.clinic_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
    public function index_data(Datatables $datatable, Request $request)
    {
        $query = BillingRecord::SetRole(auth()->user());

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('payment_status', $filter['column_status']);
            }
        }


        if (isset($filter)) {
            if (isset($filter['doctor_name']) && $filter['doctor_name'] !== '') {
                $query->where('doctor_id', $filter['doctor_name']);
            }
            if (isset($filter['patient_name']) && $filter['patient_name'] !== '') {
                $query->where("user_id", $filter['patient_name']);
            }
            if (isset($filter['clinic_name']) && $filter['clinic_name'] !== '') {
                $query->where("clinic_id", $filter['clinic_name']);
            }
            if (isset($filter['service_name']) && $filter['service_name'] !== '') {
                $query->where('service_id', $filter['service_name']);
            }
        
            
            if (!empty($filter['date_start']) && !empty($filter['date_end'])) {
                $query->whereBetween('date', [$filter['date_start'], $filter['date_end']]);
            } elseif (!empty($filter['date_start'])) {
                $query->whereDate('date', '>=', $filter['date_start']);
            } elseif (!empty($filter['date_end'])) {
                $query->whereDate('date', '<=', $filter['date_end']);
            }
        }
        


        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('appointment::backend.billing_record.action_column', compact('data'));
            })

            ->editColumn('clinic_id', function ($data) {
                return view('appointment::backend.patient_encounter.clinic_id', compact('data'));
            })

            ->editColumn('user_id', function ($data) {
                return view('appointment::backend.clinic_appointment.user_id', compact('data'));
            })

            ->editColumn('date', function ($data) {
                return $data->date ? DateFormate($data->date) : '--';
            })

            ->editColumn('doctor_id', function ($data) {
                return view('appointment::backend.clinic_appointment.doctor_id', compact('data'));
            })

            ->editColumn('payment_status', function ($data) {

                return view('appointment::backend.billing_record.verify_action', compact('data'));
            })


            ->editColumn('service_id', function ($data) {
                if ($data->clinicservice) {
                    return optional($data->clinicservice)->name;
                } else {
                    return '-';
                }
            })

            ->editColumn('total_amount', function ($data) {

                if($data->final_total_amount ){

                    return '<span>' . \Currency::format($data->final_total_amount) . '</span>';

                }else{

                    return '<span>' . \Currency::format($data->total_amount) . '</span>';
                }



            })


            ->filterColumn('doctor_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('doctor', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })


            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->filterColumn('clinic_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('clinic', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->filterColumn('service_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('clinicservice', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
                }
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
        $customFieldColumns = CustomField::customFieldData($datatable, BillingRecord::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'payment_status', 'check', 'total_amount'], $customFieldColumns))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('appointment::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('appointment::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = BillingRecord::where('id', $id)->first();

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function saveBillingDetails(Request $request)
    {

        $data = $request->all();
 
        $encounter_details = PatientEncounter::where('id', $data['encounter_id'])->with('appointment', 'billingrecord')->first();

        $service_price_data = $data['service_details']['service_price_data'] ?? null;

        $tax_data = isset($data['service_details']['tax_data']) ? json_encode($data['service_details']['tax_data'], true) : null;

        $date = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : (isset($encounter_details['encounter_date']) ? date('Y-m-d', strtotime($encounter_details['encounter_date'])) : null);

        $service_id = $data['service_details']['id'] ?? null;


        if ($request->is('api/*')) {
            $service_id = $request->input('service_id');

            if ($request->service_id == null) {
                $billingData = optional($encounter_details->billingrecord)->billingItem ?? collect();
                $service_id = optional($billingData->first())->item_id;
            }

            $data['service_details'] = ClinicsService::where('id', $service_id)->first();

            $newRequest = new Request([
                'service_id' => $service_id,
                'encounter_id' => $request->input('encounter_id')
            ]);

            $data['final_discount'] = $data['final_discount_enabled'] ?? 0;
// dd($data['final_discount']);
            $serviceController = new ClinicsServiceController();
            $serviceDetailsResponse = $serviceController->ServiceDetails($newRequest);

            $serviceDetailsData = $serviceDetailsResponse->getData();

            $serviceDetails = $serviceDetailsData->data ?? [];
// dd($serviceDetails->id);
            $service_id = $serviceDetails->id;
            $service_price_data = (array) $serviceDetails->service_price_data;

            $taxData = json_encode($serviceDetails->tax_data);

            $billingData = optional($encounter_details->billingrecord)->billingItem ?? collect();
            $total_amount = $billingData->sum('total_amount');

            if ($data['final_discount'] == 1) {
                $discount = 0;
                if ($request->final_discount_type == 'fixed') {
                    $discount = $request->final_discount_value;
                } else {
                    $discount = $total_amount * $request->final_discount_value / 100;
                }
                $total_amount = $total_amount - $discount;
            }

            $tax_data = $this->calculateTaxAmounts( null, $total_amount);



            $data['final_tax_amount'] = array_sum(array_column($tax_data, 'amount'));
            $data['final_total_amount'] = $total_amount + $data['final_tax_amount'] ;
        }


        $biling_details = [
            'encounter_id' => $data['encounter_id'],
            'user_id' => $data['user_id'],
            'clinic_id' => $encounter_details['clinic_id'],
            'doctor_id' => $encounter_details['doctor_id'],
            'service_id' => $service_id ?? null,
            'total_amount' => $service_price_data['total_amount'] ?? 0,
            'service_amount' => $service_price_data['service_price'] ?? 0,
            'discount_amount' => $service_price_data['discount_amount'] ?? 0,
            'discount_type' => $service_price_data['discount_type'] ?? null,
            'discount_value' => $service_price_data['discount_value'] ?? null,
            'tax_data' => $tax_data,
            'date' => $date,
            'payment_status' => $data['payment_status'],
            'final_discount' => $data['final_discount'] ?? 0,
            'final_discount_value' => $data['final_discount_value'] ?? null,
            'final_discount_type' => $data['final_discount_type'] ?? null,
            'final_tax_amount' => $data['final_tax_amount'] ?? 0,
            'final_total_amount' => $data['final_total_amount'] ?? 0,
        ];

        $billing_data = BillingRecord::updateOrCreate(
            ['encounter_id' => $data['encounter_id']],
            $biling_details
        );
        $billing_record = $billing_data->where('id', $billing_data->id)->with('clinicservice', 'patientencounter')->first();
        if ($billing_record && !empty($billing_record['service_id'])) {
            $billing_item = $this->generateBillingItem($billing_record);
        }


        if ($encounter_details['appointment_id'] !== null && $data['payment_status'] == 1) {
            $finalTotalAmount = $data['final_total_amount'] ?? 0;
            $paymentStatus = $data['payment_status'];


            // Update the appointment transaction
            AppointmentTransaction::where('appointment_id', $encounter_details['appointment_id'])
                ->update([
                    'total_amount' => $finalTotalAmount,
                    'payment_status' => $paymentStatus,
                ]);

            if ($encounter_details['doctor_id'] && $earning_data = $this->commissionData($encounter_details)) {
                $appointment = Appointment::findOrFail($encounter_details['appointment_id']);

                // Save doctor commission
                $earning_data['commission_data']['user_type'] = 'doctor';
                $earning_data['commission_data']['commission_status'] = $paymentStatus == 1 ? 'unpaid' : 'pending';
                $commissionEarning = new CommissionEarning($earning_data['commission_data']);
                $appointment->commission()->save($commissionEarning);
                
                // $vendor_id = $data['service_details']['vendor_id'] ?? null;
                // Get vendor_id from the service through billing record relationship
                $vendor_id = null;
                // if ($billingData && $billingData->service_id) {
                //     $service = ClinicsService::find($billingData->service_id);
                //     $vendor_id = $service ? $service->vendor_id : null;
                // }
                if ($billingData && $billingData->count() > 0) {
                    foreach ($billingData as $item) {
                        $serviceId = $item->service_id ?? $item->item_id ?? null;

                        if ($item->service_id) {
                            $service = ClinicsService::find($item->service_id);
                            if ($service && $service->vendor_id) {
                                $vendor_id = $service->vendor_id;
                                break; // Exit loop once vendor_id is found
                            }
                        }
                    }
                }
                
                // Fallback: If vendor_id not found in billing data, try from service_details
                if (!$vendor_id) {
                    $vendor_id = $data['service_details']['vendor_id'] ?? null;
                }
                
                \Log::info('vendor_id', [$vendor_id]);
            // }

                $vendor = User::find($vendor_id);

                // Determine admin and vendor commission logic
                if (multiVendor() != 1) {
                    // Admin commission when not multi-vendor
                    $adminEarningData = [
                        'user_type' => $vendor->user_type ?? 'admin',
                        'employee_id' => $vendor->id ?? User::where('user_type', 'admin')->value('id'),
                        'commissions' => null,
                        'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                        'commission_amount' => $finalTotalAmount - $earning_data['commission_data']['commission_amount'],
                    ];
                    $adminCommissionEarning = new CommissionEarning($adminEarningData);

                    $appointment->commission()->save($adminCommissionEarning);
                } else {
                    // Logic for multi-vendor scenario
                    if ($vendor && $vendor->user_type == 'vendor') {
                        // Admin earning for vendor
                        $adminEarning = $this->AdminEarningData($encounter_details);
                        $adminEarning['user_type'] = 'admin';
                        $adminEarning['commission_status'] = $paymentStatus == 1 ? 'unpaid' : 'pending';

                        $adminCommissionEarning = new CommissionEarning($adminEarning);

                        $appointment->commission()->save($adminCommissionEarning);

                        // Vendor earning
                        $vendorEarningData = [
                            'user_type' => $vendor->user_type,
                            'employee_id' => $vendor->id,
                            'commissions' => null,
                            'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                            'commission_amount' => $finalTotalAmount - $adminEarning['commission_amount'] - $earning_data['commission_data']['commission_amount'],
                        ];
                        $vendorCommissionEarning = new CommissionEarning($vendorEarningData);
                        $appointment->commission()->save($vendorCommissionEarning);
                    } else {
                        // Fallback to admin earning if vendor is not found
                        $adminEarningData = [
                            'user_type' => 'admin',
                            'employee_id' => User::where('user_type', 'admin')->value('id'),
                            'commissions' => null,
                            'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                            'commission_amount' => $finalTotalAmount - $earning_data['commission_data']['commission_amount'],
                        ];


                        $adminCommissionEarning = new CommissionEarning($adminEarningData);
                        $appointment->commission()->save($adminCommissionEarning);
                    }
                }
            }
        }


        if ($request->has('encounter_status') && $request->encounter_status == 0 && $data['payment_status'] == 1) {

            PatientEncounter::where('id', $data['encounter_id'])->update(['status' => $request->encounter_status]);

            if ($encounter_details['appointment_id'] != null && $data['payment_status'] == 1) {

                $appointment = Appointment::where('id', $encounter_details['appointment_id'])->first();
                $clinic_data = Clinics::where('id', $appointment->clinic_id)->first();

                $data['service_name'] = $service_data->systemservice->name ?? '--';
                $data['clinic_name'] = $clinic_data->name ?? '--';
                if ($appointment && $appointment->status == 'check_in') {
                    $finalTotalAmount = $data['final_total_amount'] ?? 0;
                    $appointment->update([
                        'total_amount' => $finalTotalAmount,
                        'status' => 'checkout',
                    ]);
                    $startDate = Carbon::parse($appointment['start_date_time']);
                    $notification_data = [
                        'id' => $appointment->id,
                        'description' => $appointment->description,
                        'appointment_duration' => $appointment->duration,
                        'user_id' => $appointment->user_id,
                        'user_name' => optional($appointment->user)->first_name ?? default_user_name(),
                        'doctor_id' => $appointment->doctor_id,
                        'doctor_name' => optional($appointment->doctor)->first_name,
                        'appointment_date' => $startDate->format('d/m/Y'),
                        'appointment_time' => $startDate->format('h:i A'),
                        'appointment_services_names' => ClinicsService::with('systemservice')->find($appointment->service_id)->systemservice->name ?? '--',
                        'appointment_services_image' => optional($appointment->clinicservice)->file_url,
                        'appointment_date_and_time' => $startDate->format('Y-m-d H:i'),
                        'clinic_name'=> optional($appointment->cliniccenter)->name,
                        'clinic_id'=> optional($appointment->cliniccenter)->id,
                        'latitude' => null,
                        'longitude' => null,
                        'clinic_name' => $clinic_data->name,
                        'clinic_id' => $clinic_data->id
                    ];
                    $this->sendNotificationOnBookingUpdate('checkout_appointment', $notification_data);
                }
            }
        }

        $message = __('clinic.save_biiling_form');

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }
    public function billing_detail(Request $request)
    {
        $id = $request->id;
        $module_action = 'Billing Detail';
        $appointments = BillingRecord::with('user', 'doctor', 'clinicservice', 'clinic', 'billingItem', 'patientencounter')
            ->where('id', $id)
            ->first();
        $billing = $appointments;
        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';
        $combinedFormat = $dateformate . ' ' . $timeformate;
        return view('appointment::backend.billing_record.billing_detail', compact('module_action', 'billing', 'dateformate', 'timeformate', 'timezone', 'combinedFormat'));
    }

    public function EditBillingDetails(Request $request)
    {

        $encounter_id = $request->encounter_id;

        $data = [];

        $encounter_details = PatientEncounter::where('id', $encounter_id)->with('appointmentdetail', 'billingrecord')->first();

        if ($encounter_details->appointmentdetail) {

            $data['service_id'] = optional($encounter_details->appointmentdetail)->service_id ?? null;
            $data['payment_status'] = optional($encounter_details->appointmentdetail)->appointmenttransaction->payment_status ?? 0;
        } else {

            $data['service_id'] = optional($encounter_details->billingrecord)->service_id ?? null;
            $data['payment_status'] = optional($encounter_details->billingrecord)->payment_status ?? 0;
        }
        $data['billing_id'] = optional($encounter_details->billingrecord)->id ?? null;
        $data['final_discount'] = optional($encounter_details->billingrecord)->final_discount ?? 0;
        $data['final_discount_type'] = optional($encounter_details->billingrecord)->final_discount_type ?? 0;
        $data['final_discount_value'] = optional($encounter_details->billingrecord)->final_discount_value ?? 0;

        $data['appointment'] = $encounter_details->appointmentdetail;

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function encounter_billing_detail(Request $request)
    {

        $encouter_id = $request->id;

        $module_action = 'Billing Detail';
        $appointments = BillingRecord::with('user', 'doctor', 'clinicservice', 'clinic', 'billingItem')
            ->where('encounter_id', $encouter_id)
            ->first();
        $billing = $appointments;
        $setting = Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';
        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
        $combinedFormat = $dateformate . ' ' . $timeformate;
        return view('appointment::backend.billing_record.billing_detail', compact('module_action', 'billing', 'dateformate', 'timeformate', 'timezone', 'combinedFormat'));
    }
    public function saveBillingItems(Request $request)
    {
        $data = $request->all();
        $quantity = $data['quantity'] ?? 1;
            $serviceAmount = $data['total_amount']; // total before tax and discount
            $unitPrice = $data['service_amount'];

            // $discountAmount = 0;
            // if (isset($data['discount_value'], $data['discount_type']) &&
            //     $data['discount_value'] != null && $data['discount_type'] != null) {

            //     if ($data['discount_type'] == 'fixed') {
            //         $discountAmount = $data['discount_value'];
            //     } elseif ($data['discount_type'] == 'percentage') {
            //         $discountAmount = ($unitPrice * $data['discount_value']) / 100;
            //     }

            // }

            // $discountedUnitPrice = $unitPrice - $discountAmount;
            // Decode inclusive tax array
            $totalInclusiveTax = 0;
            if($data['inclusive_tax'] != null){
                $inclusiveTaxes = json_decode($data['inclusive_tax'], true);

                foreach ($inclusiveTaxes as $tax) {
                    if ($tax['type'] == 'fixed') {
                        $totalInclusiveTax += $tax['value'];
                    } elseif ($tax['type'] == 'percent') {
                        $totalInclusiveTax += ($unitPrice * $tax['value']) / 100;
                    }

                }
            }

            // $finalTotal = ($discountedUnitPrice + $totalInclusiveTax) * $quantity;
            $discountAmount = 0;
            if (isset($data['discount_value'], $data['discount_type']) &&
                $data['discount_value'] != null && $data['discount_type'] != null) {

                if ($data['discount_type'] == 'fixed') {
                    $discountAmount = $data['discount_value'];
                } elseif ($data['discount_type'] == 'percentage') {
                    $discountAmount = (($unitPrice + $totalInclusiveTax) * $data['discount_value']) / 100;
                }

            }
            
            $discountedUnitPrice = ($unitPrice + $totalInclusiveTax) - $discountAmount;
            
            // Decode inclusive tax array
            
            
            $finalTotal = $discountedUnitPrice * $quantity;
            $data['inclusive_tax_amount'] = $totalInclusiveTax;
            $data['total_amount'] = $finalTotal;

        $item = ClinicsService::where('id', $data['item_id'])->first();

        $html = '';

        $data['item_name'] = $item ? $item->name : '';

        $billing_item = BillingItem::updateOrCreate(
            [
                'billing_id' => $data['billing_id'],
                'item_id' => $data['item_id'],
            ],
            $data
        );

        $message = __('clinic.save_billing_item');

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {

            if ($data['type'] === 'encounter_details') {

                $service_details = [];
                $html = '';

                $data = BillingRecord::where('id', $data['billing_id'])->with('billingItem')->first();
                $encounter = PatientEncounter::where('id', $data['encounter_id'])->first();

                if (!empty($data)) {
                    $html = view('appointment::backend.patient_encounter.component.service_list', [
                        'data' => $data,
                        'status' => $encounter['status']
                    ])->render();
                }

                $service_details['service_total'] = 0; // Default value
                $service_details['total_tax'] = 0;
                $service_details['total_amount'] = 0;


               $service_details['final_discount'] =  0 ;
               $service_details['final_discount_value'] =  0 ;
               $service_details['final_discount_type'] =  null ;
               $service_details['final_discount_amount']=0;



                    if (!empty($data->billingItem) && is_array($data->billingItem->toArray())) {

                        $service_details['service_total'] = array_sum(array_column($data->billingItem->toArray(), 'total_amount'));

                        if ($data['final_discount'] == 1 && $data['final_discount_value'] > 0) {


                              $service_details['final_discount'] =  $data['final_discount'] ;
                              $service_details['final_discount_value'] =  $data['final_discount_value'] ;
                              $service_details['final_discount_type'] =  $data['final_discount_type'] ;


                            if ($data['final_discount_type'] == 'fixed') {

                                 $service_details['final_discount_amount'] = $data['final_discount_value'];

                            } else {

                                $service_details['final_discount_amount'] = ($data['final_discount_value'] * $service_details['service_total']) / 100;


                            }
                        }


                    $taxDetails = getBookingTaxamount($service_details['service_total'] - $service_details['final_discount_amount'], null);
                    $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;

                    $service_details['total_amount'] = ($service_details['service_total'] - $service_details['final_discount_amount']) + $service_details['total_tax'];

                    $service_details['service_total'] = $service_details['service_total'] ;

                }


                return response()->json([
                    'html' => $html,
                    'service_details' => $service_details,
                ]);
            } else {

                return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);

            }

        }
    }

    public function billing_item_list(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $query = BillingItem::with('clinicservice');
        if ($request->has('filter')) {
            $filters = $request->input('filter');
            if (isset($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }
        }
        $billingItems = $query->orderBy('updated_at', 'desc')->paginate($perPage);
        $billingitemCollection = BillingItemResource::collection($billingItems);

        return response()->json([
            'status' => true,
            'data' => $billingitemCollection,
            'message' => __('appointment.lbl_billing_item_list'),
        ], 200);
    }

    public function billing_item_detail(Request $request)
    {
        $billingid = $request->billing_id;
        $data = [];
        if ($billingid != null) {
            $billingItems = BillingItem::with('clinicservice')
                ->where('billing_id', $billingid)
                ->get();

            foreach ($billingItems as $billingItem) {
                $name = optional($billingItem->clinicservice)->name;
                // $servicePricedata = [
                //     'service_price' => $billingItem->service_amount,
                //     'total_amount' => $billingItem->total_amount,
                // ];
                $data[] = [
                    'id' => $billingItem->id,
                    'billing_id' => $billingItem->billing_id,
                    'name' => $name,
                    'item_id' => $billingItem->item_id,
                    'service_price' => $billingItem->service_amount,
                    'total_amount' => $billingItem->total_amount,
                    'discount_value' => $billingItem->discount_value,
                    'discount_type' => $billingItem->discount_type,
                    'quantity' => $billingItem->quantity,
                ];
            }
        }

        return response()->json(['data' => $data, 'status' => true]);
    }
    public function editBillingItem(Request $request, $id)
    {

        $billing_item = BillingItem::where('id', $id)->first();


        return response()->json(['data' => $billing_item, 'status' => true]);
    }
    public function deleteBillingItem(Request $request, $id)
    {
        $billing_item = BillingItem::where('id', $id)->first();

        $billing_id = $billing_item->billing_id;

        $billing_item->forceDelete();

        if ($request->is('api/*')) {

            $message = __('appointment.billing_item_delete');

            return response()->json(['message' => $message, 'status' => true], 200);
        } else {

            // $billing_item = BillingItem::where('billing_id', $billing_item->billing_id)->get();

            $service_details = [];
            $html = '';

            $data = BillingRecord::where('id', $billing_id)->with('billingItem')->first();
            $encounter = PatientEncounter::where('id', $data['encounter_id'])->first();

            if (!empty($data)) {
                $html = view('appointment::backend.patient_encounter.component.service_list', [
                    'data' => $data,
                    'status' => $encounter['status']
                ])->render();
            }

            $service_details['service_total'] = 0; // Default value
            $service_details['total_tax'] = 0;
            $service_details['total_amount'] = 0;
            $service_details['final_discount'] =  0 ;
            $service_details['final_discount_value'] =  0 ;
            $service_details['final_discount_type'] =  null ;
            $service_details['final_discount_amount']=0;


            if (!empty($data->billingItem) && is_array($data->billingItem->toArray())) {
                $service_details['service_total'] = array_sum(array_column($data->billingItem->toArray(), 'total_amount'));

                if ($data['final_discount'] == 1 && $data['final_discount_value'] > 0) {


                    $service_details['final_discount'] =  $data['final_discount'] ;
                    $service_details['final_discount_value'] =  $data['final_discount_value'] ;
                    $service_details['final_discount_type'] =  $data['final_discount_type'] ;

                  if ($data['final_discount_type'] == 'fixed') {

                      $service_details['final_discount_amount'] = $data['final_discount_value'];

                  } else {

                      $service_details['final_discount_amount'] = ($data['final_discount_value'] * $service_details['service_total']) / 100;


                  }
              }

              $taxDetails = getBookingTaxamount($service_details['service_total'] - $service_details['final_discount_amount'], null);
              $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;


                $service_details['total_amount'] = ($service_details['service_total'] - $service_details['final_discount_amount']) + $service_details['total_tax'];
            }

            return response()->json([
                'html' => $html,
                'service_details' => $service_details,
            ]);

        }
    }

    public function getBillingItem($id)
    {

       
        $service_details = [];
        $html = '';

        $data = BillingRecord::where('id', $id)->with('billingItem')->first();
        $encounter = PatientEncounter::where('id', $data['encounter_id'])->first();

        $service_details['service_total'] = 0; // Default value
        $service_details['total_tax'] = 0;
        $service_details['total_amount'] = 0;


        $service_details['final_discount'] = 0;
        $service_details['final_discount_value'] = 0;
        $service_details['final_discount_type'] = 'percentage';
        $service_details['final_discount_amount']=0;

        if (!empty($data->billingItem) && is_array($data->billingItem->toArray())) {

            $service_details['service_total'] = array_sum(array_column($data->billingItem->toArray(), 'total_amount'));

                $netAmount = $service_details['service_total'];
                $tax_details=$data->appointmentTransaction->tax_data ?? null;
                $taxDetails = getBookingTaxamount($netAmount,$tax_details);
                $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;


            if ($data['final_discount'] == 1 && $data['final_discount_value'] > 0) {


                  $service_details['final_discount'] =  $data['final_discount'] ;
                  $service_details['final_discount_value'] =  $data['final_discount_value'] ;
                  $service_details['final_discount_type'] =  $data['final_discount_type'] ;

                if ($data['final_discount_type'] == 'fixed') {

                    $service_details['final_discount_amount'] = $data['final_discount_value'];

                } else {

                    $service_details['final_discount_amount'] = ($data['final_discount_value'] * $service_details['service_total']) / 100;
                }
                
                // Calculate tax on the net amount (service_total - discount_amount)
                $netAmount = $service_details['service_total'] - $service_details['final_discount_amount'];
                $tax_details=$data->appointmentTransaction->tax_data ?? null;
                $taxDetails = getBookingTaxamount($netAmount,$tax_details);
                $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;
            }

            // Calculate final total: net amount + tax
            $service_details['total_amount'] = ($service_details['service_total'] - $service_details['final_discount_amount']) + $service_details['total_tax'];
        }

        $service_details['service_total'] = $service_details['service_total'] ;
        $service_details['total_amount'] = $service_details['total_amount'] ;

        return response()->json([
            'service_details' => $service_details,
        ]);


    }

    public function CalculateDiscount(Request $request)
    {

        $service_details = [];

        $data = BillingRecord::where('id', $request->billing_id)->with('billingItem')->first();
        $encounter = PatientEncounter::where('id', $data['encounter_id'])->first();


        $service_details['service_total'] = 0;  
        $service_details['total_tax'] = 0;
        $service_details['total_amount'] = 0;
        $service_details['final_discount_amount'] = 0;


        if (!empty($data->billingItem) && is_array($data->billingItem->toArray())) {
            $service_details['service_total'] = array_sum(array_column($data->billingItem->toArray(), 'total_amount'));
            $taxDetails = getBookingTaxamount($service_details['service_total'], null);
            $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;

            if ($request->discount_value > 0) {

                if ($request->discount_type == 'fixed') {

                    $service_details['final_discount_amount'] = $request->discount_value;

                } else {

                    $service_details['final_discount_amount'] = ($request->discount_value * $service_details['service_total']) / 100;
                }
                
                // Calculate tax on the net amount (service_total - discount_amount)
                $netAmount = $service_details['service_total'] - $service_details['final_discount_amount'];
                $taxDetails = getBookingTaxamount($netAmount, null);
                $service_details['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;
            }

            // Calculate final total: net amount + tax
            $service_details['total_amount'] = ($service_details['service_total'] - $service_details['final_discount_amount']) + $service_details['total_tax'];




        }

        return response()->json([

            'service_details' => $service_details,
        ]);


    }

    public function SaveBillingData(Request $request)
    {
        $data = $request->all();
        $billingData = BillingRecord::where('encounter_id', $data['encount_id'])->with('billingItem')->first();
        $encounter = PatientEncounter::find($data['encount_id']);
 

        $serviceDetails = [
            'service_total' => 0,
            'total_tax' => 0,
            'total_amount' => 0,
            'final_discount_amount' => 0,
        ];

        if (!empty($billingData->billingItem)) {
            $billingItems = $billingData->billingItem->toArray();
            $serviceDetails['service_total'] = array_sum(array_column($billingItems, 'total_amount'));

            $discountValue = $request->final_discount_value ?? 0;
            $discountType = $request->final_discount_type ?? 'fixed';

            if ($request->service_id == null) {
                $service_id = $billingItems[0]['item_id'] ?? null; // 👈 safe array access
            } else {
                $service_id = $request->service_id;
            }


            $data['service_details'] = ClinicsService::where('id', $service_id)->first() ?? null;



            $serviceDetails['final_discount_amount'] = $discountType === 'fixed'
                ? $discountValue
                : ($discountValue * $serviceDetails['service_total']) / 100;

            $netTotal = $serviceDetails['service_total'] - $serviceDetails['final_discount_amount'];
            $taxDetails = getBookingTaxamount($netTotal, null);


            $serviceDetails['total_tax'] = $taxDetails['total_tax_amount'] ?? 0;


            $serviceDetails['total_amount'] = $netTotal + $serviceDetails['total_tax'] ;
        }


        $billingData->update([
            'payment_status' => $data['payment_status'],
            'final_discount' => $data['final_discount'],
            'final_discount_type' => $data['final_discount_type'],
            'final_discount_value' => $data['final_discount_value'],
            'final_tax_amount' => $serviceDetails['total_tax'],
            'final_total_amount' => $serviceDetails['total_amount'],
        ]);

        if ($data['payment_status'] == 1) {
            $encounter?->update(['status' => 0]);
        }

        $encounter_details = PatientEncounter::find($data['encount_id']);
      
        if ($encounter_details['appointment_id'] !== null && $data['payment_status'] == 1) {
            $finalTotalAmount = $serviceDetails['total_amount'] ?? 0;
            $paymentStatus = $data['payment_status'];


            // Update the appointment transaction
            AppointmentTransaction::where('appointment_id', $encounter_details['appointment_id'])
                ->update([
                    'total_amount' => $finalTotalAmount,
                    'payment_status' => $paymentStatus,
                ]);

            if ($encounter_details['doctor_id'] && $earning_data = $this->commissionData($encounter_details)) {
                $appointment = Appointment::findOrFail($encounter_details['appointment_id']);

                // Save doctor commission
                $earning_data['commission_data']['user_type'] = 'doctor';
                $earning_data['commission_data']['commission_status'] = $paymentStatus == 1 ? 'unpaid' : 'pending';
                $commissionEarning = new CommissionEarning($earning_data['commission_data']);
                $appointment->commission()->save($commissionEarning);
 
                // $vendor_id = $data['service_details']['vendor_id'] ?? null;
                // Get vendor_id from the service through billing record relationship
                $vendor_id = null;
                if ($billingData && $billingData->service_id) {
                    $service = ClinicsService::find($billingData->service_id);
                    $vendor_id = $service ? $service->vendor_id : null;
                }

                $vendor = User::find($vendor_id);
//  dd($vendor);
                // Determine admin and vendor commission logic
                if (multiVendor() != 1) {
                    // Admin commission when not multi-vendor
                    $adminEarningData = [
                        'user_type' => $vendor->user_type ?? 'admin',
                        'employee_id' => $vendor->id ?? User::where('user_type', 'admin')->value('id'),
                        'commissions' => null,
                        'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                        'commission_amount' => $finalTotalAmount - $earning_data['commission_data']['commission_amount'],
                    ];
                    $adminCommissionEarning = new CommissionEarning($adminEarningData);

                    $appointment->commission()->save($adminCommissionEarning);
                } else {
                    // Logic for multi-vendor scenario
                    if ($vendor && $vendor->user_type == 'vendor') {
                        // Admin earning for vendor
                        $adminEarning = $this->AdminEarningData($encounter_details);
                        $adminEarning['user_type'] = 'admin';
                        $adminEarning['commission_status'] = $paymentStatus == 1 ? 'unpaid' : 'pending';

                        $adminCommissionEarning = new CommissionEarning($adminEarning);

                        $appointment->commission()->save($adminCommissionEarning);

                        // Vendor earning
                        $vendorEarningData = [
                            'user_type' => $vendor->user_type,
                            'employee_id' => $vendor->id,
                            'commissions' => null,
                            'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                            'commission_amount' => $finalTotalAmount - $adminEarning['commission_amount'] - $earning_data['commission_data']['commission_amount'],
                        ];
                        $vendorCommissionEarning = new CommissionEarning($vendorEarningData);
                        $appointment->commission()->save($vendorCommissionEarning);
                    } else {
                        // Fallback to admin earning if vendor is not found
                        $adminEarningData = [
                            'user_type' => 'admin',
                            'employee_id' => User::where('user_type', 'admin')->value('id'),
                            'commissions' => null,
                            'commission_status' => $paymentStatus == 1 ? 'unpaid' : 'pending',
                            'commission_amount' => $finalTotalAmount - $earning_data['commission_data']['commission_amount'],
                        ];


                        $adminCommissionEarning = new CommissionEarning($adminEarningData);
                        $appointment->commission()->save($adminCommissionEarning);
                    }
                }
            }
        }


        if ($data['payment_status'] == 1) {

            // PatientEncounter::where('id', $data['encounter_id'])->update(['status' => $request->encounter_status]);

            if ($encounter_details['appointment_id'] != null && $data['payment_status'] == 1) {

                $appointment = Appointment::where('id', $encounter_details['appointment_id'])->first();

                if ($appointment && $appointment->status == 'check_in') {
                    $finalTotalAmount = $data['final_total_amount'] ?? 0;
                    $appointment->update([
                        'total_amount' => $finalTotalAmount,
                        'status' => 'checkout',
                    ]);
                    $startDate = Carbon::parse($appointment['start_date_time']);
                    $notification_data = [
                        'id' => $appointment->id,
                        'description' => $appointment->description,
                        'appointment_duration' => $appointment->duration,
                        'user_id' => $appointment->user_id,
                        'user_name' => optional($appointment->user)->first_name ?? default_user_name(),
                        'doctor_id' => $appointment->doctor_id,
                        'doctor_name' => optional($appointment->doctor)->first_name,
                        'clinic_name'=>optional($appointment->cliniccenter)->name,
                        'clinic_id'=>optional($appointment->cliniccenter)->id,
                        'appointment_date' => $startDate->format('d/m/Y'),
                        'appointment_time' => $startDate->format('h:i A'),
                        'appointment_services_names' => ClinicsService::with('systemservice')->find($appointment->service_id)->systemservice->name ?? '--',
                        'appointment_services_image' => optional($appointment->clinicservice)->file_url,
                        'appointment_date_and_time' => $startDate->format('Y-m-d H:i'),
                        'latitude' => null,
                        'longitude' => null,
                    ];
                    $this->sendNotificationOnBookingUpdate('checkout_appointment', $notification_data);
                }
            }
        }


        return response()->json([
            'message' => 'Billing details saved successfully',
            'status' => true,
        ]);
    }

}

