@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <div class="row">
        <x-backend.section-header>
            <x-slot name="toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('backend.appointments.index') }}" class="btn btn-primary" data-type="ajax"
                        data-bs-toggle="tooltip">
                        {{ __('appointment.back') }}
                    </a>
                </div>
                @php
                    $id = $appointment ? $appointment->id : 0;
                    $status = $appointment ? $appointment->status : null;
                    $pay_status = $appointment ? optional($appointment->appointmenttransaction)->payment_status : 0;
                @endphp
                @if ($pay_status == 1 && $status == 'checkout')
                    <div class="d-flex justify-content-end align-items-center ">

                        <a class="btn btn-primary"
                            href="{{ route('backend.appointments.download_invoice', ['id' => $appointment->id]) }}">
                            <i class="fa-solid fa-download"></i>
                            {{ __('appointment.lbl_download') }}
                        </a>
                    </div>
                @endif
            </x-slot>
        </x-backend.section-header>
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_patient_name') }}</span>
                            <div class="d-flex gap-3 align-items-center">
                                <img src="{{ optional($appointment->user)->profile_image ?? default_user_avatar() }}"
                                    alt="avatar" class="avatar avatar-70 rounded-pill">
                                <div class="text-start">
                                    <h5 class="m-0">{{ optional($appointment->user)->full_name ?? default_user_name() }}</h5>
                                    <span>{{ optional($appointment->user)->email ?? '--' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div>
                                <span class="d-block mb-2">{{ __('clinic.lbl_clinic_name') }}</span>
                                <img src="{{ optional($appointment->cliniccenter)->file_url ?? 'default_file_url()' }}" alt="avatar" class="avatar avatar-30 rounded-pill me-2">
                                <h6 class="m-0">
                                    {{ $appointment->cliniccenter ? optional($appointment->cliniccenter)->name : '--' }}
                                </h6>
                            </div>

                            <div>
                                <span class="d-block mb-2">{{ __('appointment.lbl_status') }}</span>
                                <h6 class="m-0">
                                    {{ ucwords(str_replace('_', ' ', $appointment->status === 'checkout' ? 'complete' : $appointment->status)) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($appointment->status === 'cancelled' && $appointment->reason)
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <h5 class="mb-2">{{ __('messages.lbl_reason_for_cancellation') }}</h5>
                    <p class="mb-0">{{ $appointment->reason }}</p>
                </div>
            </div>
        </div>
        @endif
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="row gy-3 mb-5">
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_appointment_date') }}</span>
                            <h6 class="m-0">{{ date($dateformate, strtotime($appointment->appointment_date ?? '--')) }}
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_appointment_time') }}</span>
                            <h6 class="m-0">{{ __('appointment.at') }} {{ $appointment->appointment_time ? \Carbon\Carbon::createFromFormat('H:i:s', $appointment->appointment_time)->format('h:i A') : '--' }}
                        </div>
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_doctor') }}
                                {{ __('appointment.lbl_name') }}</span>

                            @if ($appointment->doctor === null)
                                <h6 class="m-0">--</h6>
                            @else
                                <div class="d-flex gap-3 align-items-center">
                                    <img src="{{ optional($appointment->doctor)->profile_image ?? default_user_avatar() }}"
                                        alt="avatar" class="avatar avatar-50 rounded-pill">
                                    <div class="text-start">
                                        <h6 class="m-0">
                                            {{ optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name }}
                                        </h6>
                                        <span>{{ optional($appointment->doctor)->email ?? '--' }}</span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="border-top"></div>
                    <div class="row gy-3 pt-5">
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_payment_status') }}</span>
                            @if (isset($appointment->appointmenttransaction->payment_status))
                                @if ($appointment->status === 'cancelled' && optional($appointment->appointmenttransaction)->payment_status == 1)
                                    <h6 class="m-0 mb-2 text-success">{{ getLocalizedPaymentStatus(3) }}</h6>
                                @elseif (optional($appointment->appointmenttransaction)->payment_status == 1)
                                    <h6 class="m-0 mb-2 text-success">{{ getLocalizedPaymentStatus(1) }}</h6>
                                @elseif($appointment->status == 'cancelled' && $appointment->advance_paid_amount != 0)
                                    <h6 class="m-0 mb-2 text-success">{{ getLocalizedPaymentStatus(3) }}</h6>
                                @elseif(optional($appointment->appointmenttransaction)->payment_status == 0 &&
                                        optional($appointment->appointmenttransaction)->advance_payment_status == 1)
                                    <h6 class="m-0 mb-2 text-success">{{ getLocalizedPaymentStatus(5) }}</h6>
                                @else
                                    <h6 class="m-0 mb-2 text-secondary">{{ getLocalizedPaymentStatus(0) }}</h6>

                                    <span class="d-block mb-1">{{ __('appointment.lbl_payment_method') }}</span>

                                    <h6 class="m-0  mb-2">{{ __('appointment.paid_with') }}
                                        {{ ucfirst(optional($appointment->appointmenttransaction)->transaction_type) }}
                                    </h6>
                                @endif
                            @else
                                <h6 class="m-0 text-danger">{{ __('appointment.failed') }}</h6>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_contact_number') }}</span>
                            <h6 class="m-0">{{ optional($appointment->user)->mobile ?? '--' }}</h6>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block mb-1">{{ __('appointment.lbl_duration') }}</span>
                            <h6 class="m-0">{{ $appointment->duration ?? '--' }} {{ __('appointment.min') }}</h6>
                        </div>

                        <div class="col-md-3">
                            @if($appointment->otherPatient)
                                <span class="d-block mb-1">{{ __('appointment.booked_for') }}</span>
                                <h6 class="m-0">
                                    <span> <img  src={{ $appointment->otherPatient->profile_image}}
                                        class="img-fluid rounded-circle me-2 avatar-40" /></span>
                                    {{ $appointment->otherPatient->first_name }} {{ $appointment->otherPatient->last_name }}
                                </h6>
                            @endif
                        </div>

                        @if ($appointment->media->isNotEmpty())
                            <div class="col-md-3">
                                <span class="d-block mb-1">{{ __('appointment.lbl_medical_report') }}</span>
                                <ul>
                                @foreach ($appointment->media as $media)
                                  <li>
                                      <a href="{{ asset($media->getUrl()) }}" target="_blank">
                                         {{ __('appointment.view_medical_report') }}
                                      </a>
                                  </li>
                              @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if ($appointment->appointment_extra_info != '')
            <div class="col-md-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="flex-column">
                            <h5>{{ __('appointment.lbl_medical_history') }}</h5>
                            <span class="m-0">{{ $appointment->appointment_extra_info }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class=" card-header">
                    <h5 class="card-title mb-0">{{ __('appointment.price') }} {{ __('appointment.detail') }} </h5>
                </div>
                <div class="card-body">
                    @if ($appointment->patientEncounter == null)
                        <div class="d-flex align-items-sm-center bg-body p-4 rounded flex-sm-row flex-column gap-3">
                            <div class="detail-box card m-0 rounded">
                                <img src="{{ optional($appointment->clinicservice)->file_url ?? default_file_url() }}"
                                    alt="avatar" class="avatar avatar-80 rounded-pill">
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div>
                                            <b>{{ optional($appointment->clinicservice)->name ?? '--' }}</b>
                                        </div>
                                        @if(optional($appointment->clinicservice)->description)
                                            <div class="text-body small">
                                                {{ optional($appointment->clinicservice)->description }}
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        // Direct appointment service calculation
                                        $baseServiceAmount = $appointment->service_price;
                                        $inclusiveTaxAmount = optional($appointment->appointmenttransaction)->inclusive_tax_price ?? 0;
                                        $serviceTotalWithTax = $baseServiceAmount + $inclusiveTaxAmount; // Original service total (no discount)
                                        
                                        // Calculate discount if present (service-level discount)
                                        $discount_amount = 0;
                                        if (optional($appointment->appointmenttransaction)->discount_value > 0) {
                                            if (optional($appointment->appointmenttransaction)->discount_type === 'percentage') {
                                                $discount_amount = $serviceTotalWithTax * (optional($appointment->appointmenttransaction)->discount_value / 100);
                                            } else {
                                                $discount_amount = optional($appointment->appointmenttransaction)->discount_value;
                                            }
                                        }
                                        $serviceAfterDiscount = $serviceTotalWithTax - $discount_amount;
                                    @endphp
                                    
                                    @if (optional($appointment->appointmenttransaction)->discount_value > 0)
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                                                <div class="d-flex align-items-center gap-1">
                                                    <del class="text-body">{{ Currency::format($serviceTotalWithTax) }}</del>
                                                    <h5 class="mb-0">{{ Currency::format($serviceAfterDiscount) }}</h5>
                                                </div>
                                                @if(optional($appointment->appointmenttransaction)->inclusive_tax_price != null && $appointment->patientEncounter == null)
                                                    <small class="text-secondary">{{ __('messages.lbl_with_inclusive_tax') }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 justify-content-end">
                                                <span class="fw-medium">
                                                    @if (optional($appointment->appointmenttransaction)->discount_type === 'percentage')
                                                        {{ optional($appointment->appointmenttransaction)->discount_value ?? '--' }}% {{ __('messages.discount') }}
                                                    @else
                                                        {{ Currency::format(optional($appointment->appointmenttransaction)->discount_value) ?? '--' }} {{ __('messages.discount') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <h5 class="mb-0 text-lg-end text-sm-start">
                                            {{ Currency::format($serviceTotalWithTax) }}
                                        </h5>
                                    @endif
                                   
                                </div>
                                <!-- <div class="col-md-3 d-flex justify-content-end">
                                                                                                                                                                                                                                                                                                                                                                                                                            <h5 class="mb-0">{{ Currency::format($appointment->service_price) }}</h5>
                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->
                            </div>
                        </div>
                    @endif
                    @if (
                        $appointment->patientEncounter !== null &&
                            optional(optional($appointment->patientEncounter)->billingrecord)->billingItem != null)
                        @foreach (optional(optional($appointment->patientEncounter)->billingrecord)->billingItem as $billingItem)
                            <div class="d-flex align-items-sm-center bg-body p-4 rounded flex-sm-row flex-column gap-3">
                                <div class="detail-box card m-0 rounded">
                                    <img src="{{ optional($billingItem->clinicservice)->file_url ?? default_file_url() }}"
                                        alt="avatar" class="avatar avatar-80 rounded-pill">
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <div>
                                                <b>{{ optional($billingItem->clinicservice)->name }}</b>
                                            </div>
                                            <div class="text-body small">
                                                {{ optional($billingItem->clinicservice)->description ?? ' ' }}
                                            </div>
                                        </div>                      

                                        @php
                                            // Billing item calculation breakdown
                                            $quantity = $billingItem->quantity ?? 1;
                                            $service_price = $billingItem->service_amount;
                                            $inclusive_tax = $billingItem->inclusive_tax_amount ?? 0;
                                            
                                            // Original service total with tax per unit (no discount)
                                            $price_per_unit = $service_price + $inclusive_tax;
                                            
                                            // Original total for this item (quantity × price per unit)
                                            $item_original_total = $price_per_unit * $quantity;
                                            
                                            // Apply discount if any (item-level discount)
                                            $discount = 0;
                                            if ($billingItem->discount_value > 0) {
                                                if ($billingItem->discount_type === 'percentage') {
                                                    $discount = $item_original_total * ($billingItem->discount_value / 100);
                                                } else {
                                                    $discount = $billingItem->discount_value;
                                                }
                                            }
                                            
                                            $final_total = $item_original_total - $discount;
                                        @endphp
                                        <div class="flex-grow-1">
                                            <span class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                                                <span>
                                                    {{ Currency::format($price_per_unit) }}
    
                                                    {{-- Show label if inclusive tax exists --}}
                                                    @if(($inclusive_tax ?? 0) > 0)
                                                        <small class="text-danger ms-1">({{ __('messages.lbl_with_inclusive_tax') }})</small>
                                                    @endif
    
                                                    <span class="text-body">×</span>
                                                    <span>{{ $quantity }}</span>
                                                    <span class="mx-1">=</span>
                                                    <span class="fw-bold text-primary">{{ Currency::format($final_total) }}</span>
    
                                                    {{-- Show discount label if any --}}
                                                    @if ($billingItem->discount_value > 0)
                                                        <span class="ms-2">
                                                            (
                                                            @if ($billingItem->discount_type === 'percentage')
                                                                {{ $billingItem->discount_value }}% {{ __('off') }}
                                                            @else
                                                                {{ Currency::format($billingItem->discount_value) }} {{ __('off') }}
                                                            @endif
                                                            )
                                                        </span>
                                                    @endif
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- @if (!empty($billingItem->clinicservice->inclusive_tax_price))
                                        @php
                                            $quantity = $billingItem->quantity;
                                            $inclusive_tax_price_per_unit = $billingItem->clinicservice->inclusive_tax_price;
                                            $inclusive_tax_price = $inclusive_tax_price_per_unit * $quantity;
                                            $inclusive_tax_data = json_decode($billingItem->clinicservice->inclusive_tax, true);

                                            $service_total = $payable_Amount * $quantity;
                                            $item_subtotal = ($payable_Amount + $inclusive_tax_price_per_unit) * $quantity;
                                            // dd($inclusive_tax_price_per_unit);
                                            $total_item_tax = 0;
                                        @endphp

                                        <ul class="ps-4 w-100">
                                            <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                <span>{{ __('appointment.service_price') }}</span>
                                                <span class="text-primary">{{ Currency::format($service_total) }}</span>
                                            </li>

                                            @if (!empty($inclusive_tax_data))
                                                @foreach ($inclusive_tax_data as $t)
                                                    @if ($t['type'] == 'percent')
                                                        @php
                                                            $tax_per_unit = $payable_Amount * $t['value'] / 100 ;
                                                            $tax_total = $tax_per_unit * $quantity;
                                                            $total_item_tax += $tax_total;
                                                        @endphp
                                                        <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                            <span>{{ $t['title'] }} ({{ $t['value'] }}% of {{ Currency::format($payable_Amount) }} × {{ $quantity }})</span>
                                                            <span class="text-primary">{{ Currency::format($tax_total) }}</span>
                                                        </li>
                                                    @elseif ($t['type'] == 'fixed')
                                                        @php
                                                            $tax_total = $t['value'] * $quantity;
                                                            $total_item_tax += $tax_total;
                                                        @endphp
                                                        <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                            <span>{{ $t['title'] }} ({{ Currency::format($t['value']) }} × {{ $quantity }})</span>
                                                            <span class="text-primary">{{ Currency::format($tax_total) }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                                <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                    <span>{{ __('appointment.sub_total') }}</span>
                                                    <span class="text-primary">{{ Currency::format($item_subtotal) }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif --}}
                                </div>
                            </div>
                        @endforeach
                    @endif
                    @php
                    /*
                    ================================================================================
                    APPOINTMENT BILLING CALCULATION SYSTEM
                    ================================================================================
                    
                    This system handles two calculation methods:
                    1. NEW METHOD: Enhanced encounter service pricing with base/additional service distinction
                    2. OLD METHOD: Traditional calculation for backward compatibility
                    
                    CALCULATION FLOW:
                    ==================
                    1. Calculate Service Totals (with/without discounts)
                    2. Apply Service-Level Discounts (individual service discounts)
                    3. Apply Encounter-Level Discounts (overall encounter discounts)
                    4. Calculate Sub Total and Final Amount
                    5. Display Results with proper formatting
                    
                    DISCOUNT TYPES:
                    ===============
                    - Service Discount: Applied to individual services (base service or additional services)
                    - Encounter Discount: Applied to total service amount after service discounts
                    
                    DISPLAY LOGIC:
                    ==============
                    - When encounter discount exists: Service Total shows amount after service discount, encounter discount shown separately
                    - When only service discount exists: Service Total shows original amount, service discount shown separately
                    - When no discounts: Service Total shows original amount, no discount line
                    */
                    
                    // ============================================================================
                    // STEP 1: INITIALIZE CALCULATION VARIABLES
                    // ============================================================================
                    
                    // NEW CALCULATION METHOD VARIABLES
                    $service_total_amount = 0;           // Total of all services (base + additional)
                    $base_service_total = 0;             // Base service amount (first item in encounter)
                    $additional_services_total = 0;      // Additional services amount (encounter-added services)
                    $base_service_discount = 0;          // Discount applied to base service
                    
                    // OLD CALCULATION METHOD VARIABLES (for backward compatibility)
                    $old_service_total_amount = 0;       // Traditional total calculation
                    
                    // ============================================================================
                    // STEP 2: CALCULATE SERVICE TOTALS BASED ON APPOINTMENT TYPE
                    // ============================================================================
                    
                    if ($appointment->patientEncounter !== null && optional($appointment->patientEncounter->billingrecord)->billingItem) {
                        /*
                        PATIENT ENCOUNTER CALCULATION:
                        - First item = Base service (original appointment service)
                        - Remaining items = Additional services added during encounter
                        - Each service can have individual discounts
                        */
                        
                        $is_first_item = true;
                        
                        foreach ($appointment->patientEncounter->billingrecord->billingItem as $item) {
                            // Calculate item details
                            $quantity = $item->quantity ?? 1;
                            $service_price = $item->service_amount;
                            $inclusive_tax = $item->inclusive_tax_amount ?? 0;
                            $item_total = ($service_price + $inclusive_tax) * $quantity;
                            
                            if ($is_first_item) {
                                /*
                                BASE SERVICE PROCESSING:
                                - First item is the original appointment service
                                - Apply service discount if it has one
                                - Store both original amount and discounted amount
                                */
                                $base_service_total = $item_total;
                                
                                // Apply service discount to base service if applicable
                                if ($item->discount_value > 0) {
                                    if ($item->discount_type === 'percentage') {
                                        $base_service_discount = $item_total * ($item->discount_value / 100);
                                    } else {
                                        $base_service_discount = $item->discount_value;
                                    }
                                    // Apply discount to base service total
                                    $base_service_total = $item_total - $base_service_discount;
                                }
                                
                                $is_first_item = false;
                    } else {
                                /*
                                ADDITIONAL SERVICES PROCESSING:
                                - Services added during encounter
                                - No discount applied here (handled separately)
                                */
                                $additional_services_total += $item_total;
                            }
                            
                            // OLD METHOD: Sum all items without distinction (backward compatibility)
                            $old_service_total_amount += $item_total;
                        }
                        
                        // Calculate total service amount (base service with discount + additional services)
                        $service_total_amount = $base_service_total + $additional_services_total;
                        
                    } else {
                        /*
                        DIRECT APPOINTMENT CALCULATION:
                        - No patient encounter (simple appointment)
                        - Single service with optional inclusive tax
                        */
                        $base_service_total = $appointment->service_price + (optional($appointment->appointmenttransaction)->inclusive_tax_price ?? 0);
                        $service_total_amount = $base_service_total;
                        $old_service_total_amount = $base_service_total;
                    }
                    @endphp
                     
                    {{-- @if(optional($appointment->appointmenttransaction)->inclusive_tax_price != null && $appointment->patientEncounter == null)
                            @php
                                $sub_total = $payable_Amount + $appointment->appointmenttransaction->inclusive_tax_price;
                                $inclusive_tax_data = json_decode($appointment->appointmenttransaction->inclusive_tax, true); // decode tax details
                            @endphp
                            <li class="d-flex align-items-center justify-content-between pb-2 mb-2 mt-2 border-bottom">
                                <span>{{ __('appointment.service_price') }}</span>
                                <span class="text-primary">{{ Currency::format($payable_Amount) }}</span>
                            </li>
                            @if(!empty($inclusive_tax_data))
                                @foreach ($inclusive_tax_data as $t)
                                    @if ($t['type'] == 'percent')
                                        @php
                                            $tax_amount = $payable_Amount * $t['value'] / 100 ; // for inclusive, this is reverse calculated
                                            $total_tax += $tax_amount;
                                        @endphp
                                        <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                            <span>{{ $t['title'] }} ({{ $t['value'] }}%)</span>
                                            <span class="text-primary">{{ Currency::format($tax_amount) }}</span>
                                        </li>
                                    @elseif($t['type'] == 'fixed')
                                        @php
                                            $tax_amount = $t['value'];
                                            $total_tax += $tax_amount;
                                        @endphp
                                        <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                            <span>{{ $t['title'] }}</span>
                                            <span class="text-primary">{{ Currency::format($tax_amount) }}</span>
                                        </li>
                                    @endif
                                @endforeach
                                <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                    <span>{{ __('appointment.sub_total') }}</span>
                                    <span class="text-primary">{{ Currency::format($sub_total) }}</span>
                                </li>
                            @endif
                    @endif  --}}
                    @php
                        // Use direct data from appointment and transaction
                        $transaction = $appointment->appointmenttransaction ? $appointment->appointmenttransaction : null;
                        $discount_amount = 0;

                        if ($appointment->patientEncounter !== null) {
                            // For patient encounters, use billing record
                            $transaction = optional($appointment->patientEncounter)->billingrecord;
                            
                            if ($transaction && $transaction['final_discount'] == 1) {
                                if ($transaction['final_discount_type'] == 'percentage') {
                                    $discount_amount = $service_total_amount * ($transaction['final_discount_value'] / 100);
                                } else {
                                    $discount_amount = $transaction['final_discount_value'];
                                }
                            }
                            
                            // Get tax data from billing record
                            $taxData = json_decode(optional($transaction)->tax_data, true)
                                ?: json_decode(optional($transaction)->tax_percentage, true)
                                ?: [];
                        } else {
                            // For direct appointments, get exclusive taxes
                            $taxData = Modules\Tax\Models\Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('status', 1)->where('tax_type', 'exclusive')->get();
                        }
                    @endphp

                    <div class="row gy-3 mt-4">
                        <div class="col-sm-12">
                         
                            <div class="card">
                                <div class="card-body">
                                     {{-- 
                                     CALCULATION BREAKDOWN:
                                     ====================
                                     1. Service Total = Service Price + Inclusive Tax (with service-level discount if applied)
                                     2. Total Discount = Service-level Discount + Encounter-level Discount (combined display)
                                     3. Sub Total = Service Total (with service discount) - Encounter Discount
                                     4. Taxes = Applied on Sub Total (exclusive taxes only)
                                     5. Grand Total = Sub Total + Taxes
                                     
                                     DATA SOURCES:
                                     - Direct Appointment: Uses appointment->service_price + appointmenttransaction->inclusive_tax_price
                                     - Patient Encounter: Uses billingrecord->billingItem (service_amount + inclusive_tax_amount) × quantity
                                     - Service Discounts: Applied on individual services/items (calculated but not shown separately)
                                     - Encounter Discounts: Applied on total service amount (calculated but not shown separately)
                                     - Taxes: Applied on subtotal, excludes inclusive taxes
                                     
                                     IMPORTANT: Service Total shows price WITH service-level discount applied (if any)
                                     DISPLAY: Only one discount line shown (combines both service and encounter discounts)
                                     --}}
                                     @php
                                         // ============================================================================
                                         // STEP 3: APPLY SERVICE-LEVEL DISCOUNTS
                                         // ============================================================================
                                         
                                         // Initialize discount calculation variables
                                         $original_service_total = $service_total_amount;
                                         $service_discount_amount = 0;              // Additional services discount only
                                         $service_total_with_discount = $original_service_total;
                                         
                                         // OLD METHOD: Initialize variables for backward compatibility
                                         $old_original_service_total = $old_service_total_amount;
                                         $old_service_discount_amount = 0;          // All service discounts combined
                                         $old_service_total_with_discount = $old_original_service_total;
                                         
                                         /*
                                         SERVICE DISCOUNT CALCULATION:
                                         - NEW METHOD: Base service discount already applied, calculate additional services discount
                                         - OLD METHOD: Calculate all service discounts combined
                                         */
                                         
                                         if ($appointment->patientEncounter !== null && optional($appointment->patientEncounter->billingrecord)->billingItem) {
                                             /*
                                             PATIENT ENCOUNTER DISCOUNT CALCULATION:
                                             - Process each billing item to calculate individual discounts
                                             - Base service discount already applied in previous step
                                             - Calculate additional services discounts
                                             */
                                             
                                             $is_first_item = true;
                                             
                                             foreach ($appointment->patientEncounter->billingrecord->billingItem as $item) {
                                                 if ($item->discount_value > 0) {
                                                     // Calculate item total and discount amount
                                                     $quantity = $item->quantity ?? 1;
                                                     $item_original_total = ($item->service_amount + ($item->inclusive_tax_amount ?? 0)) * $quantity;
                                                     
                                                     if ($item->discount_type === 'percentage') {
                                                         $item_discount = $item_original_total * ($item->discount_value / 100);
                                                     } else {
                                                         $item_discount = $item->discount_value;
                                                     }
                                                     
                                                     if ($is_first_item) {
                                                         /*
                                                         BASE SERVICE DISCOUNT:
                                                         - Already applied to base_service_total in previous step
                                                         - Skip adding to service_discount_amount
                                                         */
                                                         $is_first_item = false;
                                                     } else {
                                                         /*
                                                         ADDITIONAL SERVICES DISCOUNT:
                                                         - Add to service_discount_amount for display
                                                         */
                                                         $service_discount_amount += $item_discount;
                                                     }
                                                     
                                                     // OLD METHOD: Add all discounts together
                                                     $old_service_discount_amount += $item_discount;
                                                 } else {
                                                     $is_first_item = false;
                                                 }
                                             }
                                             
                                         } elseif (optional($appointment->appointmenttransaction)->discount_value > 0) {
                                             /*
                                             DIRECT APPOINTMENT DISCOUNT CALCULATION:
                                             - Single service with discount applied
                                             - Calculate discount on base service amount
                                             */
                                             
                                             if (optional($appointment->appointmenttransaction)->discount_type === 'percentage') {
                                                 $service_discount_amount = $base_service_total * (optional($appointment->appointmenttransaction)->discount_value / 100);
                                                 $old_service_discount_amount = $old_original_service_total * (optional($appointment->appointmenttransaction)->discount_value / 100);
                                             } else {
                                                 $service_discount_amount = optional($appointment->appointmenttransaction)->discount_value;
                                                 $old_service_discount_amount = optional($appointment->appointmenttransaction)->discount_value;
                                             }
                                         }
                                         
                                         // Calculate final service totals with discounts applied
                                         $service_total_with_discount = $original_service_total - $service_discount_amount;
                                         $old_service_total_with_discount = $old_original_service_total - $old_service_discount_amount;
                                         
                                         // ============================================================================
                                         // STEP 4: APPLY ENCOUNTER-LEVEL DISCOUNTS
                                         // ============================================================================
                                         
                                         // Initialize encounter discount variables
                                         $encounter_discount_amount = 0;
                                         $encounter_discount_percent = 0;
                                         $encounter_discount_type = '';
                                         $old_encounter_discount_amount = 0;
                                         
                                         /*
                                         ENCOUNTER DISCOUNT CALCULATION:
                                         - Applied to total service amount after service-level discounts
                                         - Can be percentage or fixed amount
                                         - Only applies to patient encounters with final_discount enabled
                                         */
                                         
                                         if ($appointment->patientEncounter !== null && $transaction && ($transaction['final_discount'] ?? null) == 1) {
                                             // Get encounter discount details
                                             $encounter_discount_percent = $transaction['final_discount_value'] ?? 0;
                                             $encounter_discount_type = $transaction['final_discount_type'] ?? 'percentage';
                                             
                                             /*
                                             NEW METHOD: Calculate encounter discount on service total with service discounts applied
                                             */
                                             if ($encounter_discount_type === 'percentage') {
                                                 $encounter_discount_amount = $service_total_with_discount * ($encounter_discount_percent / 100);
                                             } else {
                                                 $encounter_discount_amount = $encounter_discount_percent;
                                             }
                                             
                                             /*
                                             OLD METHOD: Calculate encounter discount on old service total
                                             */
                                             if ($encounter_discount_type === 'percentage') {
                                                 $old_encounter_discount_amount = $old_service_total_with_discount * ($encounter_discount_percent / 100);
                                             } else {
                                                 $old_encounter_discount_amount = $encounter_discount_percent;
                                             }
                                         }
                                         
                                         // ============================================================================
                                         // STEP 5: CALCULATE FINAL TOTALS AND SUB TOTALS
                                         // ============================================================================
                                         
                                         /*
                                         SUB TOTAL CALCULATION LOGIC:
                                         
                                         NEW METHOD DISPLAY SCENARIOS:
                                         1. When encounter discount exists: 
                                            - Service Total shows amount after service discount
                                            - Encounter discount shown separately
                                            - Sub Total = Service Total (with service discount) - Encounter Discount
                                            
                                         2. When only service discount exists:
                                            - Service Total shows original amount
                                            - Service discount shown separately
                                            - Sub Total = Original Service Total - Service Discount
                                            
                                         3. When no discounts:
                                            - Service Total shows original amount
                                            - No discount line shown
                                            - Sub Total = Original Service Total
                                         */
                                         
                                         if ($encounter_discount_amount > 0) {
                                             // Scenario 1: Encounter discount exists
                                         $sub_total = $service_total_with_discount - $encounter_discount_amount;
                                         } else {
                                             // Scenario 2: Only service discount or no encounter discount
                                             $sub_total = $original_service_total - $service_discount_amount;
                                         }
                                         
                                         /*
                                         OLD METHOD: Sub Total calculation for backward compatibility
                                         */
                                         if ($old_encounter_discount_amount > 0) {
                                             $old_sub_total = $old_service_total_with_discount - $old_encounter_discount_amount;
                                         } else {
                                             $old_sub_total = $old_original_service_total - $old_service_discount_amount;
                                         }
                                     @endphp

                                     <!-- ============================================================================ -->
                                     <!-- DISPLAY SECTION: BILLING BREAKDOWN -->
                                     <!-- ============================================================================ -->
                                     
                                     <!-- 
                                     DISPLAY LOGIC SUMMARY:
                                     =====================
                                     1. Service Total: Shows amount after service discount when encounter discount exists
                                     2. Discount Line: Shows encounter discount OR service discount (never both)
                                     3. Sub Total: Final amount after all discounts applied
                                     4. Taxes: Applied on sub total (exclusive taxes only)
                                     5. Grand Total: Sub total + taxes
                                     -->
                                     
                                     <!-- STEP 1: SERVICE TOTAL DISPLAY -->
                                     <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                        <span>
                                            {{ __('appointment.service_total') }}
                                            @if ($appointment->patientEncounter !== null || optional($appointment->appointmenttransaction)->inclusive_tax_price > 0)
                                                <span class="text-danger small">({{ __('messages.lbl_with_inclusive_tax') }})</span>
                                            @endif
                                        </span>
                                        <!-- 
                                        SERVICE TOTAL AMOUNT LOGIC:
                                        - If encounter discount exists: Show service total with service discount applied
                                        - If no encounter discount: Show original service total
                                        -->
                                         <span class="heading-color">{{ Currency::format($encounter_discount_amount > 0 ? $service_total_with_discount : $original_service_total) }}</span>
                                     </div>

                                     <!-- STEP 2: DISCOUNT DISPLAY -->
                                     @if ($encounter_discount_amount > 0)
                                         <!-- 
                                         SCENARIO 1: ENCOUNTER DISCOUNT EXISTS
                                         - Show only encounter discount (service discount already included in service price)
                                         - Display percentage if it's a percentage discount
                                         -->
                                         <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                             <span>{{ __('messages.discount') }} @if ($encounter_discount_type === 'percentage')({{ $encounter_discount_percent }}%)@endif</span>
                                             <span class="heading-color">- {{ Currency::format($encounter_discount_amount) }}</span>
                                         </div>
                                         
                                     @elseif ($service_discount_amount > 0 || (isset($base_service_discount) && $base_service_discount > 0))
                                         <!-- 
                                         SCENARIO 2: ONLY SERVICE DISCOUNT EXISTS
                                         - Show total service discount (base + additional services)
                                         - Calculate and display percentage for primary discount
                                         -->
                                         @php
                                             // Calculate total service discount amount
                                             $total_service_discount = $service_discount_amount + (isset($base_service_discount) ? $base_service_discount : 0);
                                             
                                             // Calculate service discount percentage for display
                                             $service_discount_percentage = 0;
                                             if ($appointment->patientEncounter !== null && optional($appointment->patientEncounter->billingrecord)->billingItem) {
                                                 // Get percentage from first item with discount (base service)
                                                 $is_first_item = true;
                                                 foreach ($appointment->patientEncounter->billingrecord->billingItem as $item) {
                                                     if ($item->discount_value > 0) {
                                                         if ($is_first_item) {
                                                             $service_discount_percentage = $item->discount_value;
                                                             $is_first_item = false;
                                                         }
                                                     } else {
                                                         $is_first_item = false;
                                                     }
                                                 }
                                             } elseif (optional($appointment->appointmenttransaction)->discount_value > 0) {
                                                 // Direct appointment discount percentage
                                                 $service_discount_percentage = optional($appointment->appointmenttransaction)->discount_value;
                                             }
                                         @endphp
                                         <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                             <span>{{ __('messages.discount') }} @if ($service_discount_percentage > 0)({{ $service_discount_percentage }}%)@endif</span>
                                             <span class="heading-color">- {{ Currency::format($total_service_discount) }}</span>
                                         </div>
                                     @endif
                                     <!-- 
                                     SCENARIO 3: NO DISCOUNTS
                                     - No discount line displayed
                                     - Service total shows original amount
                                     -->

                                     <!-- STEP 3: SUB TOTAL DISPLAY -->
                                     <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                         <span>{{ __('messages.sub_total') }}</span>
                                         <span class="heading-color">{{ Currency::format($sub_total) }}</span>
                                     </div>

                                     <!-- STEP 4: Taxes (calculated on Sub Total) -->
                                     @php
                                         $total_tax = 0;
                                         $has_taxes = false;
                                     @endphp
                                     @if (!empty($taxData))
                                        @foreach ($taxData as $tax)
                                            @php
                                                $tax_name = $tax['title'] ?? $tax['name'] ?? 'Tax';
                                                $tax_value = $tax['value'] ?? 0;
                                                $tax_type = $tax['type'] ?? 'percentage';
                                                
                                                // Skip inclusive taxes as they're already included in service total
                                                if (isset($tax['tax_type']) && $tax['tax_type'] === 'inclusive') {
                                                    continue;
                                                }
                                                if (isset($tax['tax_scope']) && $tax['tax_scope'] === 'inclusive') {
                                                    continue;
                                                }
                                                
                                                // Calculate tax on sub total
                                                if ($tax_type == 'fixed') {
                                                    $tax_amount = $tax_value;
                                                } else {
                                                    $tax_amount = ($sub_total * $tax_value) / 100;
                                                }
                                                $total_tax += $tax_amount;
                                                $has_taxes = true;
                                            @endphp
                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                <span>
                                                    @if ($tax_type == 'fixed')
                                                        {{ $tax_name }}
                                                    @else
                                                        {{ $tax_name }} ({{ $tax_value }}%)
                                                    @endif
                                                </span>
                                                <span class="heading-color">{{ Currency::format($tax_amount) }}</span>
                                            </div>
                                        @endforeach
                                     @endif
                                     
                                     @if (!$has_taxes)
                                         <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                             <span>{{ __('messages.no_tax') }}</span>
                                             <span class="heading-color">{{ Currency::format(0) }}</span>
                                         </div>
                                     @endif

                                     @php
                                         // STEP 5: Grand Total = Sub Total + Total Tax
                                         $calculated_grand_total = $sub_total + $total_tax;
                                         
                                         // Use database total if available, otherwise use calculated total
                                         if ($appointment->patientEncounter !== null && $transaction && !empty($transaction['final_total_amount'])) {
                                             $final_amount = $transaction['final_total_amount'];
                                         } elseif ($appointment->total_amount && $appointment->total_amount > 0) {
                                             $final_amount = $appointment->total_amount;
                                         } else {
                                             $final_amount = $calculated_grand_total;
                                         }
                                         
                                         $remaining_amount = $final_amount - ($appointment->advance_paid_amount ?? 0);
                                     @endphp

                                     <!-- STEP 5: Grand Total -->
                                     <hr class="border-top border-gray">
                                     <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                         <span class="heading-color">{{ __('messages.grand_total') }}</span>
                                         <span class="text-secondary">{{ Currency::format($final_amount) ?? '--' }}</span>
                                     </div>
                                     
                                     {{-- Debug Information (remove in production) --}}
                                     {{-- @if(config('app.debug'))
                                     <div class="mt-3 p-2 bg-light rounded small">
                                         <strong>NEW Calculation Debug:</strong><br>
                                         @if ($appointment->patientEncounter !== null)
                                             Base Service Total (with discount): {{ Currency::format($base_service_total) }}<br>
                                             @if (isset($base_service_discount) && $base_service_discount > 0)
                                                 Base Service Discount: {{ Currency::format($base_service_discount) }}<br>
                                             @endif
                                             Additional Services Total: {{ Currency::format($additional_services_total) }}<br>
                                         @endif
                                         Original Service Total: {{ Currency::format($original_service_total) }}<br>
                                         Service Discount (additional services only): {{ Currency::format($service_discount_amount) }}<br>
                                         Service Total (with service discount): {{ Currency::format($service_total_with_discount) }}<br>
                                         Encounter Discount: {{ Currency::format($encounter_discount_amount) }}<br>
                                         Sub Total: {{ Currency::format($sub_total) }}<br>
                                         <br>
                                         <strong>OLD Calculation Debug:</strong><br>
                                         Old Original Service Total: {{ Currency::format($old_original_service_total) }}<br>
                                         Old Service Discount: {{ Currency::format($old_service_discount_amount) }}<br>
                                         Old Service Total (with service discount): {{ Currency::format($old_service_total_with_discount) }}<br>
                                         Old Encounter Discount: {{ Currency::format($old_encounter_discount_amount) }}<br>
                                         Old Sub Total: {{ Currency::format($old_sub_total) }}<br>
                                         <br>
                                         Total Tax: {{ Currency::format($total_tax) }}<br>
                                         Calculated Grand Total: {{ Currency::format($calculated_grand_total) }}<br>
                                         Final Amount (from DB): {{ Currency::format($final_amount) }}
                                     </div>
                                     @endif --}}

                                    @if (optional($appointment->appointmenttransaction)->advance_payment_status == 1)
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <span>{{ __('service.advance_payment_amount') }}({{ $appointment->advance_payment_amount }}%)</span>
                                            <span>{{ Currency::format($appointment->advance_paid_amount) ?? '--' }}</span>
                                        </div>
                                    @endif

                                    @if (optional($appointment->appointmenttransaction)->advance_payment_status == 1 && $appointment->status == 'checkout')
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-success p-2 ms-2">{{ __('appointment.paid') }}</span></span>
                                            <span class="heading-color">{{ Currency::format($remaining_amount) }}</span>
                                        </div>
                                    @elseif (optional($appointment->appointmenttransaction)->advance_payment_status == 1 && optional($appointment->appointmenttransaction)->payment_status != 1 && $appointment->status != 'cancelled')
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-warning p-2 ms-2">{{ __('appointment.pending') }}</span></span>
                                            <span class="heading-color">{{ Currency::format($remaining_amount) }}</span>
                                        </div>
                                    @endif

                                    @if(optional($appointment->appointmenttransaction)->transaction_type)
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span class="fw-semibold">{{ __('appointment.lbl_payment_method') }}</span>
                                            <span class="text-primary fw-bold">
                                                {{ ucfirst(optional($appointment->appointmenttransaction)->transaction_type) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($appointment->status === 'cancelled' && ($appointment->cancellation_charge_amount != null || $appointment->advance_paid_amount > 0))
                                        @php
                                            // Use direct data from appointment
                                            $payment_status = optional($appointment->appointmenttransaction)->payment_status;
                                            $advance_paid_amount = $appointment->advance_paid_amount ?? 0;
                                            $total_paid = $appointment->total_amount ?? 0;
                                            $cancellation_charge_amount = $appointment->cancellation_charge_amount ?? 0;
                                            
                                            // Calculate refund amount
                                            if($payment_status == 0 || $advance_paid_amount > 0) {
                                                $refundAmount = max(0, $advance_paid_amount - $cancellation_charge_amount);
                                                $paidAmount = $advance_paid_amount;
                                            } else {
                                                $refundAmount = max(0, $total_paid - $cancellation_charge_amount);
                                                $paidAmount = $total_paid;
                                            }
                                            
                                            $paymentMethod = optional($appointment->appointmenttransaction)->transaction_type ?? 'cash';
                                        @endphp
                                        
                                        <!-- Refund Details Section -->
                                        <div class="mt-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-bold">{{ __('messages.refund_detail') ?? 'Refund Detail' }}</h6>
                                            </div>
                                            
                                            <div class="border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 fw-bold">{{ __('messages.refund_of_amount', ['amount' => Currency::format($refundAmount)]) ?? 'Refund of ' . Currency::format($refundAmount) }}</h6>
                                                    <span class="badge bg-success ms-3">{{ __('messages.refund_completed') ?? 'Refund Completed' }}</span>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <span class="text-muted">{{ __('appointment.lbl_payment_method') }}:</span>
                                                        <span class="text-primary fw-semibold">{{ ucfirst($paymentMethod) }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <div class="d-flex justify-content-between py-1">
                                                        <span>{{ __('messages.price') ?? 'Price' }}:</span>
                                                        <span class="fw-semibold">{{ Currency::format($paidAmount) }}</span>
                                                    </div>
                                                    
                                                   {{-- @if($advance_paid_amount > 0 && $payment_status == 0)
                                                    <div class="d-flex justify-content-between py-1">
                                                        <span>{{ __('service.advance_payment_amount') ?? 'Advanced Payment' }}:</span>
                                                        <span class="fw-semibold">{{ Currency::format($advance_paid_amount) }}</span>
                                                    </div>
                                                    @endif --}}
                                                    
                                                    @if($cancellation_charge_amount > 0)
                                                    <div class="d-flex justify-content-between py-1">
                                                        <span>
                                                            {{ __('messages.cancellation_fee') }}
                                                            @if($appointment->cancellation_type === 'percentage')
                                                                ({{ $appointment->cancellation_charge }}%)
                                                            @elseif($appointment->cancellation_type === 'fixed')
                                                                ({{ Currency::format($appointment->cancellation_charge) }})
                                                            @endif
                                                            :
                                                        </span>
                                                        <span class="fw-semibold">{{ Currency::format($cancellation_charge_amount) }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <hr class="my-2">
                                                    
                                                    <div class="d-flex justify-content-between py-2 bg-light rounded px-3" style="background-color: #e8f5e8 !important;">
                                                        <span class="fw-bold text-success">{{ __('service.refund_amount') ?? 'Refund Amount' }}:</span>
                                                        <span class="fw-bold text-success">{{ Currency::format($refundAmount) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($appointment->appointmenttransaction == null && $appointment->clinicservice && $appointment->clinicservice->is_enable_advance_payment == 1)
                                        @php
                                            $pending_amount = ($appointment->total_amount * $appointment->clinicservice->advance_payment_amount) / 100;
                                        @endphp
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span>{{ __('appointment.pending_advance_payment_amount') }}</span>
                                            <span class="heading-color">{{ Currency::format($pending_amount) ?? '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endsection

    @push('after-styles')
        <style>
            .detail-box {
                padding: 0.625rem 0.813rem;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    @endpush

    @push('after-scripts')
        <script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
        <script src="{{ mix('modules/appointment/script.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    @endpush