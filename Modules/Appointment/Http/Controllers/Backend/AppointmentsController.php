<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Authorizable;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Tax\Models\Tax;
use Google\Client;
use App\Models\Setting;
use App\Mail\AppointmentConfirmation;
use Illuminate\Support\Facades\Mail;
use Google\Service\Calendar;
use Google\Client as GoogleClient;
use Modules\Customer\Models\OtherPatient;;

use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Doctor;
use Modules\Commission\Models\CommissionEarning;
use Modules\Constant\Models\Constant;
use Modules\Appointment\Trait\EncounterTrait;
use Modules\Clinic\Models\SystemService;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Trait\BillingRecordTrait;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletHistory;
use Modules\Clinic\Models\Receptionist;

class AppointmentsController extends Controller
{
    use AppointmentTrait;
    use EncounterTrait;
    use BillingRecordTrait;

    protected string $exportClass = '\App\Exports\CustomerExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'appointment.title';
        // module name
        $this->module_name = 'appointment';

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
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Appointment());
        $customefield = CustomField::exportCustomFields(new Appointment());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ]
        ];
        $export_url = route('backend.appointment.export');

        return view('appointment::backend.appointment.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Appointment::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }
        return response()->json($data);
    }

    public function index_data()
    {
        $query = Appointment::query();
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $query;
        } else {
            $query->where('doctor_id', auth()->id());
        }
        $appointment_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $appointment_colors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');
        $payment_status = config('appointment.PAYMENT_STATUS');

        $query->orderBy('created_at', 'desc');

        $doctor = User::where('user_type', 'doctor')->get();

        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                $module_name = $this->module_name;
                return view('appointment::backend.appointment.datatable.action_column', compact('data', 'module_name'));
            })
            ->editColumn('status', function ($data) use ($appointment_status, $appointment_colors) {
                return view('booking::backend.appointment.datatable.select_column', compact('data', 'appointment_status', 'appointment_colors'));
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
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'is_banned', 'email_verified_at', 'check', 'image'], $customFieldColumns))
            ->toJson();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

    public function otherpatient(Request $request)
    {
        // dd($request->all());
        $data = $request->except('profile_image');

        $otherPatient = OtherPatient::create($data);


        if ($request->hasFile('profile_image')) {
            storeMediaFile($otherPatient, $request->file('profile_image'), 'profile_image');
        }
        return response()->json([
            'status' => true,
            'message' =>  __('messages.member_added'),
            'data' => [
                'id' => $otherPatient->id,
                'profile_image' => $otherPatient->profile_image,
            ],
        ], 201);
    }

    // public function store(Request $request)
    // {                                                                                                                                                                                       
    //     $data = $request->all();
         
    //     \Log::info($data);
    //     if (!empty($data['otherpatient_id'])) {
    //         $data['otherpatient_id'] = (int) $data['otherpatient_id'];
    //     } else {
    //         $data['otherpatient_id'] = null;
    //     }

    //     $data['start_date_time'] = Carbon::createFromFormat('Y-m-d H:i', $data['appointment_date'] . ' ' . $data['appointment_time'], setting('default_time_zone'));

    //     $serviceData = $this->getServiceAmount($data['service_id'], $data['doctor_id'], $data['clinic_id']);

    //     $taxes = Tax::active()
    //         ->whereNull('module_type')
    //         ->orWhere('module_type', 'services')
    //         ->where('tax_type', 'exclusive')
    //         ->where('status', 1)
    //         ->get();

    //     $data['service_price'] = $serviceData['service_price'];
    //     $data['service_amount'] = $serviceData['service_amount'];
    //     $data['total_amount'] = $serviceData['total_amount'];
    //     $data['duration'] = $serviceData['duration'];
    //     $data['status'] = $data['status'] ? $data['status'] : 'confirmed';

    //     $service = ClinicsService::where('id', $data['service_id'])->first();
    //     if ($service->is_enable_advance_payment == 1) {
    //         $data['advance_payment_amount'] = $service->advance_payment_amount;
    //     }
    //     $data['appointment_extra_info'] = $data['description'] ?? null;
    //     $data = Appointment::create($data);
    //     $is_telemet = ClinicsService::where('id', $data['service_id'])->pluck('is_video_consultancy')->first();
    //     if ($is_telemet == 1) {
    //         $setting = Setting::where('name', 'google_meet_method')->orwhere('name', 'is_zoom')->first();
    //         if ($data && $setting) {
    //             if ($setting->name == 'google_meet_method' && $setting->val == 1) {
    //                 $meetLink = $this->generateMeetLink($request, $data['start_date_time'], $data['duration'], $data);
    //             } else {
    //                 $zoom_url = getzoomVideoUrl($data);
    //                 if (!empty($zoom_url) && isset($zoom_url['start_url']) && isset($zoom_url['join_url'])) {
    //                     $startUrl = $zoom_url['start_url'];
    //                     $joinUrl = $zoom_url['join_url'];

    //                     $data->start_video_link = $startUrl;
    //                     $data->join_video_link = $joinUrl;
    //                     $data->save();
    //                 }
    //             }
    //         }
    //     }

    //     if ($request->hasFile('file_url')) {
    //         storeMediaFile($data, $request->file('file_url'));
    //     }

    //     if ($request->is('api/*')) {

    //         $service_data = ClinicsService::where('id', $data['service_id'])->with('systemservice')->first();

    //         $clinic_data = Clinics::where('id', $data['clinic_id'])->first();

    //         $data['service_name'] = $service_data->name ?? '--';
    //         $data['clinic_name'] = $clinic_data->name ?? '--';
    //         $notification_data = [
    //             'id' => $data->id,
    //             'description' => $data->description,
    //             'appointment_duration' => $data->duration,
    //             'user_id' => $data->user_id,
    //             'user_name' => optional($data->user)->first_name ?? default_user_name(),
    //             'doctor_id' => $data->doctor_id,
    //             'doctor_name' => optional($data->doctor)->first_name,
    //             'appointment_date' => $data->start_date_time->format('d/m/Y'),
    //             'appointment_time' => $data->start_date_time->format('h:i A'),
    //             'appointment_services_names' => optional($data->clinicservice)->name ?? '--',
    //             'appointment_services_image' => optional($data->clinicservice)->file_url,
    //             'appointment_date_and_time' => $data->start_date_time->format('Y-m-d H:i'),
    //             'latitude' => null,
    //             'longitude' => null,
    //             'clinic_name' => $data->clinic_name,
    //             'clinic_id' => $clinic_data->id

    //         ];
    //         $startTime = Carbon::parse($data->appointment_time);
    //         $endTime = $startTime->copy()->addMinutes($data->duration);
    //         $data['end_time'] = $endTime->format('H:i:s') ?? '-';
    //         $appointmentTime = new \DateTime($data->appointment_time);
    //         $data['appointment_time'] = $appointmentTime->format('H:i:s') ?? '-';
    //         $this->sendNotificationOnBookingUpdate('new_appointment', $notification_data);
    //         $message = 'Your Appointment has been booked successfully.';
    //         $service = ClinicsService::where('id', $data['service_id'])->first();
    //         if ($service->is_enable_advance_payment == 1) {
    //             $total_amount = $serviceData['total_amount'];
    //             $percentage = (float)  $service->advance_payment_amount;
    //             $data['advance_paid_amount'] = ($total_amount * $percentage) / 100;
    //         }
    //         $data['advance_paid_amount'] = $data['advance_paid_amount'] ?? 0;
    //         return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    //     } else {
    //         $clinic_data = Clinics::where('id', $data['clinic_id'])->first();
    //         $data['clinic_name'] = $clinic_data->name ?? '--';

    //         $notification_data = [
    //             'id' => $data->id,
    //             'description' => $data->description,
    //             'appointment_duration' => $data->duration,
    //             'user_id' => $data->user_id,
    //             'user_name' => optional($data->user)->first_name ?? default_user_name(),
    //             'doctor_id' => $data->doctor_id,
    //             'doctor_name' => optional($data->doctor)->first_name,
    //             'appointment_date' => $data->start_date_time->format('d/m/Y'),
    //             'appointment_time' => $data->start_date_time->format('h:i A'),
    //             'appointment_services_names' => optional($data->clinicservice)->name ?? '--',
    //             'appointment_services_image' => optional($data->clinicservice)->file_url,
    //             'appointment_date_and_time' => $data->start_date_time->format('Y-m-d H:i'),
    //             'latitude' => null,
    //             'longitude' => null,
    //             'clinic_name' => $data->clinic_name,
    //             'clinic_id' => $clinic_data->id,
    //             'clinic_address' => $clinic_data->address ?? '',

    //         ];
    //         $this->sendNotificationOnBookingUpdate('new_appointment', $notification_data);

    //         $message = __('messages.create_form', ['form' => __('apponitment.singular_title')]);
    //         return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    //     }
    // }

    public function store(Request $request)
{
    $formData = $request->all(); // keep original form data separate

    \Log::info($formData);

    $formData['otherpatient_id'] = !empty($formData['otherpatient_id']) ? (int) $formData['otherpatient_id'] : null;

    $formData['start_date_time'] = Carbon::createFromFormat(
        'Y-m-d H:i',
        $formData['appointment_date'] . ' ' . $formData['appointment_time'],
        setting('default_time_zone')
    );

    $serviceData = $this->getServiceAmount($formData['service_id'], $formData['doctor_id'], $formData['clinic_id']);
// dd($serviceData);
    $formData['service_price'] = $serviceData['service_price'];
    $formData['service_amount'] = $serviceData['service_amount'];
    $formData['total_amount'] = $serviceData['total_amount'];
    $formData['duration'] = $serviceData['duration'];
    $formData['status'] = $formData['status'] ?? 'confirmed';

    $service = ClinicsService::find($formData['service_id']);
    if ($service && $service->is_enable_advance_payment == 1) {
        $formData['advance_payment_amount'] = $service->advance_payment_amount;
    }
    $formData['appointment_extra_info'] = $formData['appointment_extra_info'] ?? null;
// dd($formData);
    // Create appointment record
    $appointment = Appointment::create($formData);

    // Link recent audio recordings to this appointment
    \App\Models\AudioTranscription::where('user_id', $appointment->user_id)
        ->whereNull('appointment_id')
        ->where('created_at', '>=', now()->subHours(2)) // Only recordings from last 2 hours
        ->update(['appointment_id' => $appointment->id]);

    // Telemedicine link creation
    if ($service && $service->is_video_consultancy == 1) {
        $setting = Setting::whereIn('name', ['google_meet_method', 'is_zoom'])->first();
        if ($setting) {
            if ($setting->name == 'google_meet_method' && $setting->val == 1) {
                $this->generateMeetLink($request, $appointment->start_date_time, $appointment->duration, $appointment);
            } else {
                $zoom_url = getzoomVideoUrl($appointment);
                if (!empty($zoom_url['start_url']) && !empty($zoom_url['join_url'])) {
                    $appointment->update([
                        'start_video_link' => $zoom_url['start_url'],
                        'join_video_link' => $zoom_url['join_url'],
                    ]);
                }
            }
        }
    }

    // Upload medical files
    // if ($request->hasFile('medical_report')) {
    //     storeMediaFile($appointment, $request->file('medical_report'));
    // }
    if ($request->hasFile('file_url')) {
        storeMediaFile($appointment, $request->file('file_url'));
    }

    // Common notification data
    $clinic_data = Clinics::with('receptionist')->where('id', $appointment->clinic_id)->first();
    $receptionist = Receptionist::with('users')->where('clinic_id',$appointment->clinic_id)->first();
    $appointment->clinic_name = $clinic_data->name ?? '--';

    $notification_data = [
        'id' => $appointment->id,
        'description' => $appointment->description,
        'appointment_duration' => $appointment->duration,
        'user_id' => $appointment->user_id,
        'user_name' => optional($appointment->user)->first_name ?? default_user_name(),
        'doctor_id' => $appointment->doctor_id,
        'doctor_name' => optional($appointment->doctor)->first_name,
        'appointment_date' => $appointment->start_date_time->format('d/m/Y'),
        'appointment_time' => $appointment->start_date_time->format('h:i A'),
        'appointment_services_names' => optional($appointment->clinicservice)->name ?? '--',
        'appointment_services_image' => optional($appointment->clinicservice)->file_url,
        'appointment_date_and_time' => $appointment->start_date_time->format('Y-m-d H:i'),
        'latitude' => null,
        'longitude' => null,
        'clinic_name' => $appointment->clinic_name,
        'clinic_id' => $clinic_data->id,
        'vendor_id' => $clinic_data->vendor_id,
        'receptionist_id' => $clinic_data->receptionist->receptionist_id ?? $receptionist->receptionist_id ?? null,
        'receptionist_name' => isset($receptionist) ? $receptionist->users->first_name.' '.$receptionist->users->last_name : 'unknown',
        'clinic_address' => $clinic_data->address ?? '',
    ];

    $this->sendNotificationOnBookingUpdate('new_appointment', $notification_data);

    // Advance payment calculation
    if ($service && $service->is_enable_advance_payment == 1) {
        $percentage = (float) $service->advance_payment_amount;
        $appointment->advance_paid_amount = ($serviceData['total_amount'] * $percentage) / 100;
    }
    $appointment->advance_paid_amount = $appointment->advance_paid_amount ?? 0;

    return response()->json([
        'message' => 'Your Appointment has been booked successfully.',
        'data' => $appointment,
        'status' => true
    ], 200);
}



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Appointment::findOrFail($id);
        $data['file_url'] = $data->file_url;
        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = Appointment::findOrFail($id);

        $data->update($request->all());
        if ($request->file_url == null) {
            $data->clearMediaCollection('file_url');
        }

        if ($request->hasFile('file_url')) {
            storeMediaFile($data, $request->file('file_url'), 'file_url');
        }
        if ($request->is('api/*')) {
            $message = __('messages.update_form', ['form' => __('apponiment.singular_title')]);;
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            $message = __('messages.update_form', ['form' => __('apponiment.singular_title')]);
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = Appointment::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('appointment.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function updatePaymentStatus($id, Request $request)
    {

        if (isset($request->action_type) && $request->action_type == 'update-payment-status') {
            $status = $request->value;
        }

        $appointment_status = Appointment::where('id', $id)->first();

        AppointmentTransaction::where('appointment_id', $id)->update(['payment_status' => $request->value]);

        if ($status == 1) {

            CommissionEarning::where('commissionable_id', $id)->update(['commission_status' => 'unpaid']);
        }
        $message = __('appointment.payment_status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }
    public function updateStatus($id, Request $request)
    {
        // dd('csd');
        \Log::info('Appointment status update triggered', [
            'appointment_id' => $id,
            'new_status'     => $request->value,
            'reason'         => $request->reason ?? null,
            'cancellation_charge' => $request->cancellation_charge_amount ?? 0,
            'user'           => auth()->user()->id,
        ]);
        $appointment = Appointment::where('id', $id)->with('user', 'doctor')->first();
        $clinic_data = Clinics::where('id', $appointment->clinic_id)->first();
        $receptionist = Receptionist::with('users')->where('clinic_id',$appointment->clinic_id)->first();
        $startDate = Carbon::parse($appointment['start_date_time']);
        $status = $request->status;

        $wallet_amount = 0;
        $user_wallet = Wallet::where('user_id', $appointment->user_id)->first();
        if ($user_wallet) {
            $wallet_amount = $user_wallet->amount;
        }

        if (isset($request->action_type) && $request->action_type == 'update-status') {

            $status = $request->value;
        }
        $appointment->status = $status;

        if ($status == 'check_in') {

            $encouter_details = $this->generateEncounter($appointment);
            $encounter_details = PatientEncounter::where('id', $encouter_details->id)->with('appointment')->first();
            if ($encounter_details) {
                $billing_record = $this->generateBillingRecord($encounter_details);
                $billing_record = BillingRecord::where('id', $billing_record->id)->with('clinicservice', 'patientencounter')->first();
                if ($billing_record) {
                    $billing_item = $this->generateBillingItem($billing_record);
                    
                    // Auto-pay for cash appointments on check-in
                    $this->autoPayCashAppointment($appointment, $billing_record);
                }
            }
        }

        if ($status == 'checkout') {

            if ($appointment) {
                $encounter_details = PatientEncounter::where('appointment_id', $appointment->id)
                    ->with('appointment')
                    ->first();

                if ($encounter_details) {
                    $billing_details = BillingRecord::where('encounter_id', $encounter_details->id)
                        ->where('payment_status', 1)
                        ->first();

                    if ($billing_details) {
                        $encounter_details->update(['status' => 0]);
                    } else {
                        return response()->json(['message' => 'Payment not completed. Please complete the payment before checkout.', 'status' => false]);
                    }
                }
            }
        }
        $appointment->update();

        if ($status == 'cancelled') {

            $cancellation_charge_amount = $request->cancellation_charge_amount ?? 0;
            $cancellation_reason = $request->reason ?? null;
            $appointment->cancellation_charge = $request->cancellation_charge ?? null;
            $appointment->cancellation_type = $request->cancellation_type ?? null;

            $advance_paid_amount = $appointment->advance_paid_amount;
            $total_paid = $appointment->total_amount;

            $payment_status = optional($appointment->appointmenttransaction)->payment_status;
            $refund_amount = 0;

            if ($payment_status == 0) { // Unpaid
                if ($advance_paid_amount >= $cancellation_charge_amount) {
                    $refund_amount = $advance_paid_amount - $cancellation_charge_amount;
                } else {
                    // Deduct remaining from wallet
                    $wallet_deduct = $cancellation_charge_amount - $advance_paid_amount;
                    if ($wallet_deduct > 0) {
                        $user_wallet->amount -= $wallet_deduct;
                        $user_wallet->update();

                        // Wallet history: deduction
                        $debitHistory = new WalletHistory;
                        $debitHistory->user_id = $user_wallet->user_id;
                        $debitHistory->datetime = Carbon::now();
                        $debitHistory->activity_type = 'wallet_deduction';
                        $debitHistory->activity_message = trans('messages.wallet_deduction', ['value' => $appointment->id]);

                        $debit_data = [
                            'title' => $user_wallet->title,
                            'user_id' => $user_wallet->user_id,
                            'amount' => $user_wallet->amount,
                            'credit_debit_amount' => $wallet_deduct,
                            'transaction_type' => __('messages.debit'),
                            'appointment_id' => $appointment->id,
                            'cancellation_charge_amount' => $cancellation_charge_amount,
                            'cancellation_reason' => $cancellation_reason,
                        ];

                        $debitHistory->activity_data = json_encode($debit_data);
                        $debitHistory->save();
                    }

                    $refund_amount = 0; // No refund in this case
                }
            } else { // Paid

                $refund_amount = $total_paid - $cancellation_charge_amount;
            }

            if ($refund_amount > 0) {
                $user_wallet->amount += $refund_amount;
                $user_wallet->update();

                // Wallet history: refund
                $creditHistory = new WalletHistory;
                $creditHistory->user_id = $user_wallet->user_id;
                $creditHistory->datetime = Carbon::now();
                $creditHistory->activity_type = 'wallet_refund';
                $creditHistory->activity_message = trans('messages.wallet_refund', ['value' => $appointment->id]);

                $credit_data = [
                    'title' => $user_wallet->title,
                    'user_id' => $user_wallet->user_id,
                    'amount' => $user_wallet->amount,
                    'credit_debit_amount' => $refund_amount,
                    'transaction_type' => __('messages.credit'),
                    'appointment_id' => $appointment->id,
                    'cancellation_charge_amount' => $cancellation_charge_amount,
                    'cancellation_reason' => $cancellation_reason,
                ];

                $creditHistory->activity_data = json_encode($credit_data);
                $creditHistory->save();
            }

            // Notification only for advance-paid unpaid cases
            if ($advance_paid_amount && $payment_status == 0) {
                $notification_data = [
                    'activity_type' => 'wallet_refund',
                    'payment_status' => 'Advance Payment',
                    'wallet' => $user_wallet,
                    'appointment_id' => $appointment->id,
                    'refund_amount' => $refund_amount,
                    'cancellation_charge' => $cancellation_charge_amount,
                    'reason' => $cancellation_reason,
                    'vendor_id' => $clinic_data->vendor_id,
                ];

                $this->sendNotificationOnBookingUpdate('wallet_refund', $notification_data);
            }

            $appointment->reason = $cancellation_reason;
            $appointment->cancellation_charge_amount = $cancellation_charge_amount;
            $appointment->update();
        }

        $notify_type = null;

        switch ($status) {
            case 'confirmed':
                $notify_type = 'accept_appointment';
                break;
            case 'rejected':
                $notify_type = 'reject_appointment';
                break;
            case 'checkout':
                $notify_type = 'checkout_appointment';
                break;
            case 'cancelled':
                $notify_type = 'cancel_appointment';
                break;
        }


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
            'latitude' => null,
            'longitude' => null,
            'clinic_id' => $appointment->clinic_id,
            'clinic_name' => $clinic_data->name ?? '--',
            'updated_by_role' => auth()->user()->user_type ?? '',
            'vendor_id' => $clinic_data->vendor_id,
            'receptionist_id' => $clinic_data->receptionist->receptionist_id ?? $receptionist->receptionist_id ?? null,
            'receptionist_name' => isset($receptionist) ? $receptionist->users->first_name.' '.$receptionist->users->last_name : 'unknown',
        ];
        $this->sendNotificationOnBookingUpdate($notify_type, $notification_data);
        $message = __('appointment.status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }

    public function view()
    {
        return view('appointment::backend.appointment.view');
    }


    public function savePayment(Request $request)
    {

        $data = $request->all();
        $data['tip_amount'] = $data['tip'] ?? 0;
        $appointment = Appointment::findOrFail($data['id']);
        $serviceDetails = ClinicsService::where('id', $appointment->service_id)->with('vendor')->first();
        $vendor = $serviceDetails->vendor ?? null;
        $serviceData = $this->getServiceAmount($appointment->service_id, $appointment->doctor_id, $appointment->clinic_id);

        $tax = $data['tax_percentage'] ?? Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('tax_type', 'exclusive')->where('status', 1)->get();
        $servicePrice = $serviceData['service_price'];
        $serviceAmount = $serviceData['service_price'];

        $data['service_price'] = $serviceAmount;

        $inclusiveTaxAmount = 0;
        $tax = collect($tax);
        // Filter taxes and calculate cumulative inclusive tax
        $filteredTaxes = $tax->filter(function ($taxItem) use ($servicePrice, &$inclusiveTaxAmount) {
            if (
                (isset($taxItem->tax_type) && $taxItem->tax_type === 'inclusive') ||
                (isset($taxItem->tax_scope) && $taxItem->tax_scope === 'inclusive')
            ) {
                $currentTaxAmount = 0;

                if (isset($taxItem->type) && $taxItem->type === 'percent') {
                    $currentTaxAmount = ($servicePrice * $taxItem->value) / 100;
                } else {
                    $currentTaxAmount = $taxItem->value;
                }

                if ($currentTaxAmount > $servicePrice) {
                    return false;
                }

                $inclusiveTaxAmount += $currentTaxAmount;
                return true;
            }
            return true;
        });
        $tax = $filteredTaxes;
        $data['service_price'] = $serviceAmount;
        $transactionData = [
            'appointment_id' => $appointment->id,
            'transaction_type' => $data['transaction_type'] ?? 'cash',
            'total_amount' => $serviceData['total_amount'],
            'payment_status' => $data['payment_status'] ?? 0,
            'discount_value' => $serviceData['discount_value'] ?? 0,
            'discount_type' => $serviceData['discount_type'] ?? null,
            'discount_amount' => $serviceData['discount_amount'] ?? 0,
            'external_transaction_id' => $data['external_transaction_id'] ?? null,
            'tax_percentage' => json_encode($tax),
            'inclusive_tax' => $serviceData['service_inclusive_tax'],
            'inclusive_tax_price' => $serviceData['total_inclusive_tax']
        ];


        if ($request->transaction_type == 'wallet') {
            $wallet = Wallet::where('user_id', $appointment->user_id)->first();
            $paid_amount = 0;

            if ($wallet !== null) {
                $wallet_amount = $wallet->amount;

                if ($request->advance_payment_status == 1 && $request->remaining_payment_amount == 0) {
                    if ($wallet_amount >= $request->advance_payment_amount) {
                        if ($request->advance_payment_status == 1) {
                            $wallet->amount = $wallet->amount - $request->advance_payment_amount;
                            $wallet->update();

                            $transactionData['total_amount'] = $request->advance_payment_amount;
                            $paid_amount = $request->advance_payment_amount;
                        }
                    } else {
                        $message = __('messages.wallent_balance_error');
                        return response()->json(['message' => $message], 400);
                    }
                } else if ($request->payment_status == 1 && $request->remaining_payment_amount > 0) {
                    if ($wallet_amount >= $request->remaining_payment_amount) {
                        $wallet->amount = $wallet->amount - $request->remaining_payment_amount;
                        $wallet->update();

                        $transactionData['total_amount'] = $request->remaining_payment_amount;
                        $paid_amount = $request->remaining_payment_amount;
                    } else {
                        $message = __('messages.wallent_balance_error');
                        return response()->json(['message' => $message], 400);
                    }
                } else if ($request->payment_status == 1) {
                    if ($request->payment_status == 1 && $wallet_amount >= $serviceData['total_amount']) {
                        $wallet->amount = $wallet->amount - $serviceData['total_amount'];
                        $wallet->update();

                        $transactionData['total_amount'] = $serviceData['total_amount'];
                        $paid_amount = $serviceData['total_amount'];
                    } else {
                        $message = __('messages.wallent_balance_error');
                        return response()->json(['message' => $message], 400);
                    }
                }


                $wallethistory = new WalletHistory;
                $wallethistory->user_id = $wallet->user_id;
                $wallethistory->datetime = Carbon::now();
                $wallethistory->activity_type = 'paid_for_appointment';
                $wallethistory->activity_message = trans('messages.paid_for_appointment', ['value' => $appointment->id]);

                $activity_data = [
                    'title' => $wallet->title,
                    'user_id' => $wallet->user_id,
                    'amount' => $wallet->amount,
                    'credit_debit_amount' => $paid_amount,
                    'transaction_type' => __('messages.debit'),
                    'appointment_id' => $appointment->id,
                ];

                $wallethistory->activity_data = json_encode($activity_data);
                $wallethistory->save();
            }
        }

        $payment = AppointmentTransaction::updateOrCreate(
            ['appointment_id' => $appointment->id],
            $transactionData
        );

        if (!empty($payment) && $request->advance_payment_status == 1) {
            $appointment->advance_paid_amount = $request->advance_payment_amount;
            $appointment->save();

            $payment->advance_payment_status = $request->advance_payment_status;
            $payment->total_amount = $request->advance_payment_amount;
            $payment->save();
        }


        $message = __('appointment.save_appointment');
        return response()->json(['message' => $message, 'data' => $payment, 'status' => true], 200);
    }
    public function joinGoogleMeet(Request $request)
    {
        $id = $request->id;
        $authUrl = Appointment::where('id', $id)->value('meet_link');
        return redirect($authUrl);
    }

    public function joinZoomMeet(Request $request)
    {
        $id = $request->id;
        $authUrl = Appointment::where('id', $id)->value('start_video_link');
        return redirect($authUrl);
    }


    public function generateMeetLink(Request $request, $startDateTime, $duration, $data)
    {
        $employee = User::find($data['doctor_id']);
        if ($employee) {
            $googleAccessToken = json_decode($employee->google_access_token, true);
            $accessToken = $googleAccessToken['access_token'] ?? null;
            $refreshToken = $googleAccessToken['refresh_token'] ?? null;
            $googleMeetSettings = Setting::whereIn('name', ['google_meet_method', 'google_clientid', 'google_secret_key'])
                ->pluck('val', 'name');
            $settings = $googleMeetSettings->toArray();
            $client = new GoogleClient([
                'client_id' => $settings['google_clientid'],
                'client_secret' => $settings['google_secret_key'],
                'redirect_uri' => 'postmessage',
                'access_type' => 'offline', // Use 'offline' for refresh token flow
                'prompt' => 'consent', // Use 'consent' to force user consent for refresh token
                'scopes' => ['https://www.googleapis.com/auth/calendar.events'],
            ]);

            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                if ($refreshToken) {
                    $client->refreshToken($refreshToken);
                    $newAccessToken = $client->getAccessToken();
                    User::where('id', $data['doctor_id'])->update(['google_access_token' => json_encode($newAccessToken)]);

                    $request->session()->put('google_access_token', json_encode($newAccessToken));
                } else {
                    $authUrl = $client->createAuthUrl();
                    return redirect()->away($authUrl);
                }
            }
        }
        $service = new Calendar($client);
        $user = User::find($data->user_id);
        $center = ClinicServiceMapping::with('service', 'center')
            ->where('service_id', $data->service_id)
            ->where('clinic_id', $data->center_id)
            ->first();
        if ($employee && $center) {
            $clinicService = $center->service;
            $clinic = $center->center;

            $emailData = [
                'service_name' => $clinicService->name,
                'user_name' => "{$user->first_name} {$user->last_name}",
                'clinic_name' => $clinic->name,
                'doctor_name' => "{$employee->first_name} {$employee->last_name}",
                'appointment_date' => $data->appointment_date,
                'appointment_time' => $data->appointment_time,
            ];
        }
        $contentSettings = Setting::whereIn('name', ['content', 'google_event'])->pluck('val', 'name');

        $content = $contentSettings['content'] ?? '';
        $googleEvent = $contentSettings['google_event'] ?? '';

        $placeholders = [
            '{{appointment_date}}' => $emailData['appointment_date'],
            '{{appointment_time}}' => $emailData['appointment_time'],
            '{{patient_name}}' => $emailData['user_name'],
            '{{clinic_name}}' => $emailData['clinic_name'],
            '{{appointment_desc}}' => $emailData['clinic_name'],
            '{{service_name}}' => $emailData['clinic_name'],
        ];

        foreach ($placeholders as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
            $googleEvent = str_replace($placeholder, $value, $googleEvent);
        }
        $startDateTime = Carbon::parse($data->appointment_date . ' ' . $data->appointment_time)->format('Y-m-d\TH:i:s');
        $endDateTime = Carbon::parse($startDateTime)->addHour()->format('Y-m-d\TH:i:s');

        $event = new \Google\Service\Calendar\Event([
            'summary' => $googleEvent,
            'description' => $content,
            'start' => [
                'dateTime' => $startDateTime,
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $endDateTime,
                'timeZone' => 'UTC',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet',
                    ],
                ],
            ],
        ]);
        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
        $meetingDetails = [
            'title' => $event->getSummary(),
            'description' => $event->getDescription(),
            'start' => $event->getStart()->getDateTime(),
            'end' => $event->getEnd()->getDateTime(),
            'location' => $event->getLocation(),
            'attendees' => $event->getAttendees(),
            'link' => $event->getHangoutLink(),
        ];
        $hangoutLink = $meetingDetails['link'];

        if (!empty($hangoutLink)) {
            $data->meet_link = $hangoutLink;
        }
        $data->save();

        $emails = User::whereIn('id', [$data['employee_id'], $data['user_id']])
            ->pluck('email')
            ->toArray();
        Mail::to($emails)->send(new AppointmentConfirmation($meetingDetails));
        return $meetingDetails;
    }



    public function otherpatientlist(Request $request)
    {
        $patientId = $request->input('patient_id');

        if (!$patientId) {
            return response()->json([]);
        }

        $data = OtherPatient::where('user_id', $patientId)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'profile_image' => $patient->profile_image,
                ];
            });

        return response()->json($data);
    }

    /**
     * Auto-pay cash appointments on check-in
     * 
     * @param Appointment $appointment
     * @param BillingRecord $billing_record
     * @return bool
     */
    private function autoPayCashAppointment($appointment, $billing_record)
    {
        try {
            // Get the appointment transaction to check payment method
            $appointmentTransaction = $appointment->appointmenttransaction;
            
            // Only auto-pay if transaction exists and payment method is cash
            if (!$appointmentTransaction || $appointmentTransaction->transaction_type !== 'cash') {
                return false;
            }
            
            // Update billing record payment status to paid
            $billing_record->update(['payment_status' => 1]);
            
            // Update appointment transaction payment status to paid
            $appointmentTransaction->update(['payment_status' => 1]);
            
            // Log the auto-payment for audit trail
            \Log::info('Auto-paid cash appointment on check-in', [
                'appointment_id' => $appointment->id,
                'billing_record_id' => $billing_record->id,
                'transaction_id' => $appointmentTransaction->id,
                'transaction_type' => $appointmentTransaction->transaction_type,
                'total_amount' => $appointmentTransaction->total_amount,
                'user_id' => auth()->user()->id ?? 'system',
                'timestamp' => now()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            // Log any errors but don't break the check-in process
            \Log::error('Failed to auto-pay cash appointment on check-in', [
                'appointment_id' => $appointment->id,
                'billing_record_id' => $billing_record->id ?? null,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'system'
            ]);
            
            return false;
        }
    }
}
