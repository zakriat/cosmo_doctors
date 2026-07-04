@extends('frontend::layouts.master')

@section('title', __('frontend.appointment_detail'))

@section('content')
@include('frontend::components.section.breadcrumb')
<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-5">
                <h6 class="font-size-18 mb-0">{{ __('frontend.appointment_detail') }}
                </h6>
            @php
            $id = $appointment ? $appointment->id : 0;
            $status = $appointment ? $appointment->status : null;
            $pay_status = $appointment ? optional($appointment->appointmenttransaction)->payment_status : 0;
        @endphp
        @if ($pay_status == 1 && $status == 'checkout')
            <div class="d-flex justify-content-end align-items-center ">
                <a class="btn btn-secondary"
                    href="{{ route('download_invoice', ['id' => $appointment->id]) }}">
                    <i class="fa-solid fa-download"></i>
                    {{ __('frontend.lbl_download_invoice') }}
                </a>
            </div>
        @endif
            </div>

            <div class="row">
                <div class="col-lg-8">

                    @if(empty($appointment->serviceRating) && $appointment->status == 'checkout' && optional($appointment->appointmenttransaction)->payment_status)
                        <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-5 pb-3">
                            <h6 class="font-size-18 mb-0">{{ __('frontend.havent_rated') }}
                            </h6>
                            <button class="btn btn-secondary d-flex gap-2 align-items-center" data-bs-toggle="modal" data-service-id="{{ optional($appointment->clinicservice)->id }}"
                                        data-doctor-id="{{ optional($appointment->doctor)->id }}"
                                        data-bs-target="#review-service">
                                        <i class="ph-fill ph-star"></i>{{ __('frontend.rate_us') }}
                            </button>
                        </div>
                    @endif
                    <div class="section-bg payment-box rounded">
                        <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap">
                            <h6 class="mb-0">{{ __('frontend.appointment_id') }}
                            </h6>
                            <h6 class="mb-0 text-primary">#{{ $appointment->id }}</h6>
                        </div>
                    </div>
                    <div class="mt-5 pt-3">
                        <h6 class="font-size-18">{{ __('frontend.booking_detail') }}
                        </h6>
                        <div class="section-bg payment-box rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="font-size-14">{{ __('frontend.appointment_date_time') }}
                                    </span>
                                    <p class="mb-0"> <span class="mb-0 h6">{{ DateFormate($appointment->appointment_date) }}</span> at <span class="mb-0 h6 text-uppercase">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format(setting('time_formate') ?? 'h:i A') }}</span></p>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-2">
                                    <span class="font-size-14">{{ __('frontend.service_name') }}
                                    </span>

                                    @if($appointment->clinicservice && $appointment->clinicservice->id)
                                        <a href="{{ route('service-details', ['id' => $appointment->clinicservice->id]) }}">
                                            <h6 class="mb-0">{{ $appointment->clinicservice->name }}</h6>
                                        </a>
                                    @else
                                        <h6 class="mb-0">{{ optional($appointment->category)->name ?? '-' }}</h6>
                                    @endif
                                </div>
                                <div class="col-md-4 mt-md-0 mt-2">
                                    <span class="font-size-14">{{ __('frontend.doctor') }}</span>

                                    @if ($appointment->doctor === null)
                                        <h6 class="m-0">-</h6>
                                    @else
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ optional($appointment->doctor)->profile_image ?? default_user_avatar() }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill">
                                            <div class="text-start">
                                                <h6 class="m-0">

                                                {{getDisplayName($appointment->doctor)}}

                                                </h6>
                                            @php
                                                $doctorEmail = optional($appointment->doctor)->email;
                                            @endphp

                                            @if ($doctorEmail)
                                                <a href="mailto:{{ $doctorEmail }}">{{ $doctorEmail }}</a>
                                            @else
                                                <span>-</span>
                                            @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="clinic-desc-box mt-4 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.clinic_name') }}</span>
                                        <h6 class="m-0 line-count-1"> <img
                                                src="{{ optional($appointment->cliniccenter)->file_url ?? 'default_file_url()' }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill me-2">
                                            {{ $appointment->cliniccenter ? optional($appointment->cliniccenter)->name : '-' }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.booking_status') }}</span>
                                        @php
                                            $status = $appointment->status;
                                            $statusText = $status === 'checkout' ? 'Complete' : \Illuminate\Support\Str::title(str_replace('_', ' ', $status));
                                            $statusClass = $status === 'cancelled' ? 'text-danger' : ($status === 'pending' ? 'text-danger' : 'text-success');
                                        @endphp
                                        <h6 class="mb-0 {{ $statusClass }}">
                                            {{ $statusText }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.payment_status') }}</span>
                                        <h6 class="mb-0">
                                            @if($appointment->appointmenttransaction && $appointment->appointmenttransaction->payment_status)
                                                @if($appointment->status == 'cancelled')
                                                    @if($appointment->advance_paid_amount > 0)
                                                        <span class="text-warning">{{ __('frontend.advance_refunded') }}
                                                        </span>
                                                    @else
                                                        <span class="text-warning">{{ __('frontend.payment_refunded') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    @if($appointment->appointmenttransaction->payment_method == 'cash')
                                                        <span class="text-danger">{{ __('frontend.pending') }}
                                                        </span>
                                                    @else
                                                        <span class="text-success">{{ __('frontend.paid') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            @elseif($advancePaid && optional($appointment->appointmenttransaction)->advance_payment_status == 1)
                                                @if($appointment->status == 'cancelled')
                                                    <span class="text-warning">{{ __('frontend.advance_refunded') }}
                                                    </span>
                                                @else
                                                    <span class="text-success">{{ __('frontend.advance_paid') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-danger">{{ __('frontend.pending') }}
                                                </span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                            <span class="font-size-14">{{ __('frontend.booked_for') }}
                                            </span>
                                        </div>

                                        @if ($appointment->user === null)
                                            <h6 class="m-0">-</h6>
                                        @elseif($appointment->otherPatient)
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ optional($appointment->otherPatient)->profile_image ?? default_user_avatar() }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill">
                                            <div class="text-start">
                                                <h6 class="m-0">
                                                    {{ optional($appointment->otherPatient)->first_name . ' ' . optional($appointment->otherPatient)->last_name ?? '-' }}
                                                </h6>
                                            </div>
                                        </div>
                                        @else
                                            <div class="d-flex gap-3 align-items-center">
                                                <img src="{{ optional($appointment->user)->profile_image ?? default_user_avatar() }}"
                                                    alt="avatar" class="avatar avatar-50 rounded-pill">
                                                <div class="text-start">
                                                    <h6 class="m-0">
                                                        {{ optional($appointment->user)->first_name . ' ' . optional($appointment->user)->last_name ?? '-' }}
                                                    </h6>
                                                    @php
                                                        $userEmail = optional($appointment->user)->email;
                                                    @endphp

                                                    @if ($userEmail)
                                                        <a href="mailto:{{ $userEmail }}">{{ $userEmail }}</a>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.payment_type') }}</span>
                                        <h6 class="mb-0">
                                            @if($appointment->appointmenttransaction && $appointment->appointmenttransaction->transaction_type)
                                                <span class="text-primary">{{ ucfirst($appointment->appointmenttransaction->transaction_type) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                

                    <div class="mt-5 pt-3">
                        <h6 class="font-size-18">{{ __('frontend.service_detail') }}
                        </h6>
                        <div class="section-bg payment-box rounded">

                            @if ($appointment->patientEncounter == null)
                                <div class="d-flex align-items-md-center bg-body p-4 rounded flex-md-row flex-column gap-3 payment-box-info">
                                    <div class="detail-box">
                                        <img src="{{ optional($appointment->clinicservice)->file_url ?? default_file_url() }}"
                                            alt="avatar" class="avatar avatar-80 rounded-pill">
                                    </div>

                                    <div class="row">
                                        <div class="">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    @if($appointment->clinicservice && $appointment->clinicservice->id)
                                                        <a href="{{ route('service-details', ['id' => $appointment->clinicservice->id]) }}">
                                                            <b>{{ $appointment->clinicservice->name }}</b>
                                                        </a>
                                                        <div class="mt-2">
                                                            {{ $appointment->clinicservice->description ?? ' ' }}
                                                        </div>
                                                    @else
                                                        <b>{{ optional($appointment->category)->name ?? 'Service' }}</b>
                                                        <div class="mt-2">
                                                            {{ optional($appointment->category)->description ?? ' ' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $inclusive_tax_price = optional($appointment->appointmenttransaction)->inclusive_tax_price ?? 0;
                                                $original_price = $appointment->service_price + $inclusive_tax_price;
                                                
                                                if (optional($appointment->appointmenttransaction)->discount_value > 0) {
                                                    if (optional($appointment->appointmenttransaction)->discount_type === 'percentage') {
                                                        $discount_amount = $original_price * (optional($appointment->appointmenttransaction)->discount_value / 100);
                                                    } else {
                                                        $discount_amount = optional($appointment->appointmenttransaction)->discount_value;
                                                    }
                                                    $final_price = $original_price - $discount_amount;
                                                } else {
                                                    $final_price = $original_price;
                                                }
                                            @endphp
                                            @if (optional($appointment->appointmenttransaction)->discount_value > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0">
                                                    {{ Currency::format($final_price) }}
                                                    <span class="text-success ms-2">
                                                        ({{ optional($appointment->appointmenttransaction)->discount_value }}{{ optional($appointment->appointmenttransaction)->discount_type === 'percentage' ? '%' : '' }} off)
                                                    </span>
                                                </h6>
                                                <del class="text-muted">{{ Currency::format($original_price) }}</del>
                                            </div>
                                            {{-- @if($appointment->appointmenttransaction->inclusive_tax_price != null && $appointment->patientEncounter == null)
                                                    @php
                                                        $total_tax = 0;
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
                                                        <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                            <span>{{ __('messages.sub_total') }}</span>
                                                            <span class="text-primary">{{ Currency::format($sub_total) }}</span>
                                                        </li>
                                                    @endif
                                            @endif   --}}

                                            @else
                                                <h6 class="mb-0">
                                                    {{ Currency::format($final_price) }}</h6>
                                            @endif
                                            @if(optional($appointment->appointmenttransaction)->inclusive_tax_price != null && $appointment->patientEncounter == null)

                                                    <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (
                                $appointment->patientEncounter !== null &&
                                    optional(optional($appointment->patientEncounter)->billingrecord)->billingItem != null)
                                @foreach (optional(optional($appointment->patientEncounter)->billingrecord)->billingItem as $billingItem)
                                    <div class="d-flex align-items-md-center bg-body p-4 rounded flex-md-row flex-column gap-3 payment-box-info">
                                        <div class="detail-box rounded">
                                            <img src="{{ optional($billingItem->clinicservice)->file_url ?? default_file_url() }}"
                                                alt="avatar" class="avatar avatar-80 rounded-pill">
                                        </div>

                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span><b>{{ optional($billingItem->clinicservice)->name }}</b>
                                                    {{ optional($billingItem->clinicservice)->description ?? ' ' }}</span>
                                            </div>
                                            @php
                                                // Simple billing item calculation
                                                $quantity = $billingItem->quantity ?? 1;
                                                $service_price = $billingItem->service_amount;
                                                $inclusive_tax = $billingItem->inclusive_tax_amount ?? 0;
                                                
                                                // Service total with tax per unit
                                                $price_per_unit = $service_price + $inclusive_tax;
                                                
                                                // Total for this item (quantity × price per unit)
                                                $item_total = $price_per_unit * $quantity;
                                                
                                                // Apply discount if any
                                                $discount = 0;
                                                if ($billingItem->discount_value > 0) {
                                                    if ($billingItem->discount_type === 'percentage') {
                                                        $discount = $item_total * ($billingItem->discount_value / 100);
                                                    } else {
                                                        $discount = $billingItem->discount_value;
                                                    }
                                                }
                                                
                                                $final_total = $item_total - $discount;
                                            @endphp
                                            
                                            @if ($billingItem->discount_value > 0)
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0">
                                                        <span class="fw-normal">
                                                            {{ Currency::format($price_per_unit) }}
                                                            <span class="text-muted">×</span>
                                                            {{ $quantity }}
                                                            <span class="mx-1">=</span>
                                                        </span>
                                                        <span class="text-primary fw-bold">{{ Currency::format($final_total) }}</span>
                                                        <span>
                                                            @if ($billingItem->discount_type === 'percentage')
                                                                (<span>{{ $billingItem->discount_value ?? '--' }}%</span> off)
                                                            @else
                                                                (<span>{{ Currency::format($billingItem->discount_value) ?? '--' }}</span> off)
                                                            @endif
                                                            @if($billingItem->inclusive_tax_amount > 0)
                                                                <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                                                            @endif
                                                        </span>
                                                    </h6>
                                                    <del>{{ Currency::format($item_total) }}</del>
                                                </div>
                                            @else
                                                <h6 class="mb-0">
                                                    <span class="fw-normal">
                                                        {{ Currency::format($price_per_unit) }}
                                                        <span class="text-muted">×</span>
                                                        {{ $quantity }}
                                                        <span class="mx-1">=</span>
                                                    </span>
                                                    <span class="text-primary fw-bold">{{ Currency::format($final_total) }}</span>
                                                    @if($billingItem->inclusive_tax_amount > 0)
                                                        <small class="text-secondary">
                                                            <i>{{ __('messages.lbl_with_inclusive_tax') }}</i>
                                                        </small>
                                                    @endif
                                                </h6>
                                            @endif

                                            {{-- @if (!empty($billingItem->clinicservice->inclusive_tax_price))
                                        @php
                                            $quantity = $billingItem->quantity;
                                            $inclusive_tax_price_per_unit = $billingItem->clinicservice->inclusive_tax_price;
                                            $inclusive_tax_price = $inclusive_tax_price_per_unit * $quantity;
                                            $inclusive_tax_data = json_decode($billingItem->clinicservice->inclusive_tax, true);

                                            $service_total = $payable_Amount * $quantity;
                                            $item_subtotal = ($payable_Amount + $inclusive_tax_price_per_unit) * $quantity;
                                            $total_item_tax = 0;
                                        @endphp

                                        <ul class="ps-0 w-100 mt-1">
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
                        </div>
                    </div>
                    @if($appointment->status === 'cancelled')
                    <div class="mt-5 pt-3">
                        <h6 class="font-size-18">{{ __('frontend.cancel_reason') }}
                        </h6>
                        <div class="section-bg payment-box rounded">
                            <div class="d-flex align-items-md-center bg-body p-4 rounded flex-md-row flex-column gap-3">
                                <div class="detail-box">
                                    <h6 class="mb-0">
                                        {{ $appointment->reason }}
                                    </h6>
                                </div> 
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- medical_history --}}
                    <div class="mt-5 pt-3" id="medical-history-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="font-size-18 mb-0">{{ __('frontend.medical_history') }}</h6>
                            @if($appointment->status !== 'checkout' && $appointment->status !== 'cancelled' && auth()->id() === $appointment->user_id)
                                <button type="button" id="toggle-edit-mode-btn" class="btn btn-outline-primary btn-sm">
                                    <i class="ph ph-pencil"></i> <span id="toggle-btn-text">{{ __('frontend.edit_add_more') }}</span>
                                </button>
                            @endif
                        </div>
                        
                        {{-- Read-only view --}}
                        <div id="medical-history-view">
                            @if($appointment->appointment_extra_info)
                                <div class="section-bg payment-box rounded p-3">
                                    <p class="mb-0 white-space-pre-line">{{ $appointment->appointment_extra_info }}</p>
                                </div>
                            @else
                                <div class="section-bg payment-box rounded p-3">
                                    <p class="mb-0 text-muted">{{ __('frontend.no_medical_history') }}</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Edit mode (hidden by default) - EXACT SAME AS BOOKING FORM --}}
                        @if($appointment->status !== 'checkout' && $appointment->status !== 'cancelled' && auth()->id() === $appointment->user_id)
                        <div id="medical-history-edit" class="d-none">
                            <div class="section-bg payment-box rounded p-4">
                                {{-- Speech-to-Text Controls --}}
                                <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                                    <button type="button" id="record-audio-btn" class="btn btn-outline-primary btn-sm">
                                        <i class="ph ph-microphone"></i> {{ __('frontend.record_audio') }}
                                    </button>
                                    <button type="button" id="stop-recording-btn" class="btn btn-danger btn-sm d-none">
                                        <i class="ph ph-stop"></i> {{ __('frontend.stop_recording') }}
                                    </button>
                                    <button type="button" id="cancel-recording-btn" class="btn btn-outline-secondary btn-sm d-none">
                                        {{ __('frontend.cancel_recording') }}
                                    </button>
                                    <span id="recording-timer" class="text-muted d-none fw-bold">00:00</span>
                                </div>

                                {{-- Audio Player --}}
                                <div id="audio-player-container" class="mb-3 d-none">
                                    <div class="audio-player-wrapper p-3 border rounded bg-light">
                                        <audio id="audio-player" controls class="w-100 mb-2"></audio>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" id="transcribe-btn" class="btn btn-primary btn-sm">
                                                <i class="ph ph-text-aa"></i> {{ __('frontend.transcribe_audio') }}
                                            </button>
                                            <button type="button" id="delete-recording-btn" class="btn btn-outline-danger btn-sm">
                                                <i class="ph ph-trash"></i> {{ __('frontend.delete_recording') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Transcription Status --}}
                                <div id="transcription-status" class="alert alert-info d-none mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="ph ph-spinner ph-spin me-2"></i>
                                        <span>{{ __('frontend.transcribing') }}...</span>
                                    </div>
                                </div>

                                {{-- Enhanced Dual Transcription Display --}}
                                <div id="transcription-cards" class="mb-3 d-none">
                                    {{-- Original Text Card --}}
                                    <div class="transcription-card mb-3 border rounded overflow-hidden">
                                        <div class="card-header d-flex justify-content-between align-items-center bg-light p-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-microphone text-primary me-2"></i>
                                                <strong class="mb-0">Original Speech</strong>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-original-btn" title="Copy to main textarea">
                                                    <i class="ph ph-copy"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="original-text" class="transcription-text" contenteditable="true" 
                                                 style="min-height: 60px; padding: 12px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; line-height: 1.5;"
                                                 placeholder="Your original speech will appear here..."></div>
                                        </div>
                                    </div>

                                    {{-- Medical Version Card --}}
                                    <div class="transcription-card mb-3 border rounded overflow-hidden">
                                        <div class="card-header d-flex justify-content-between align-items-center bg-success-subtle p-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-hospital text-success me-2"></i>
                                                <strong class="mb-0">Medical Enhancement</strong>
                                                <span id="gemini-status" class="badge bg-success ms-2 d-none">AI Enhanced</span>
                                                <span id="fallback-status" class="badge bg-warning ms-2 d-none">Fallback Mode</span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-success" id="copy-medical-btn" title="Copy to main textarea">
                                                    <i class="ph ph-copy"></i> Copy
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success" id="use-medical-btn" title="Use this version">
                                                    <i class="ph ph-check"></i> Use This
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="medical-text" class="transcription-text" contenteditable="true" 
                                                 style="min-height: 60px; padding: 12px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; line-height: 1.5;"
                                                 placeholder="AI-enhanced medical terminology will appear here..."></div>
                                        </div>
                                    </div>

                                    {{-- Combined Action Buttons --}}
                                    <div class="d-flex gap-2 mb-3 flex-wrap">
                                        <button type="button" class="btn btn-primary btn-sm" id="use-combined-btn">
                                            <i class="ph ph-arrows-merge"></i> Use Both Versions
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="collapse-cards-btn">
                                            <i class="ph ph-caret-up"></i> Hide Details
                                        </button>
                                    </div>
                                </div>

                                {{-- Color Legend --}}
                                <div id="color-legend" class="mb-3 d-none">
                                    <div class="p-3 bg-light border rounded">
                                        <small class="text-muted fw-semibold d-block mb-2">Medical Categories:</small>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge medical-category-badge" style="background: #ff6b6b; color: white;">
                                                <i class="ph ph-warning-circle me-1"></i>Symptoms
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #51cf66; color: white;">
                                                <i class="ph ph-clock-clockwise me-1"></i>Medical History
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #ffd43b; color: #000;">
                                                <i class="ph ph-pill me-1"></i>Medications
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #339af0; color: white;">
                                                <i class="ph ph-user me-1"></i>Personal Info
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #9775fa; color: white;">
                                                <i class="ph ph-test-tube me-1"></i>Tests & Treatments
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #ff922b; color: white;">
                                                <i class="ph ph-warning me-1"></i>Allergies
                                            </span>
                                            <span class="badge medical-category-badge" style="background: #c92a2a; color: white;">
                                                <i class="ph ph-warning-diamond me-1"></i>Urgent
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Main Textarea (Final Version) --}}
                                <div class="position-relative mb-3">
                                    <label for="appointment_extra_info" class="form-label fw-semibold">
                                        Medical History Details
                                        <small class="text-muted">(You can edit this text directly)</small>
                                    </label>
                                    <textarea class="form-control" id="appointment_extra_info" name="appointment_extra_info" 
                                              placeholder="{{ __('frontend.enter_medical_history') }}" rows="6"
                                              style="transition: all 0.3s ease;">{{ $appointment->appointment_extra_info }}</textarea>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="ph ph-info me-1"></i>
                                        Record your voice above or type directly. AI will enhance medical terminology automatically.
                                    </small>
                                </div>


                                 <div class="mb-3">
                            <label class="form-label">Upload medical records</label>
                            <input type="file" id="medical-record-files" name="file_url[]" class="form-control"
                                accept=".pdf,.png,.jpg,.jpeg" multiple>
                            <small class="text-muted">Allowed: PDF, PNG, JPG, JPEG. Max 20MB each.</small>
                            </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" id="cancel-edit-btn" class="btn btn-outline-secondary">
                                        <i class="ph ph-x"></i> {{ __('frontend.cancel') }}
                                    </button>
                                    <button type="button" id="save-medical-history-btn" class="btn btn-primary">
                                        <i class="ph ph-check"></i> {{ __('frontend.save_changes') }}
                                    </button>
                                </div>
                            </div>

                           

                        </div>
                        @endif
                    </div>
                    <!-- Medical Reports Card Grid -->
                    @if(isset($medical_reports) && $medical_reports->count())
                    <div class="container my-5">
                        <h5 class="mb-3">{{ __('appointment.medical_report') }}</h5>
                        <div class="row g-3">
                            @foreach($medical_reports as $report)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="card h-100 shadow-sm border-primary" style="cursor:pointer" onclick="window.open('{{ $report->file_url }}', '_blank')">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="card-title mb-2">{{ $report->name }}</h6>
                                                <p class="card-text text-muted mb-0" style="font-size:13px;">{{ $report->date }}</p>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button class="btn btn-outline-primary btn-sm" type="button" onclick="event.stopPropagation();window.open('{{ $report->file_url }}', '_blank')">
                                                    <i class="ph ph-eye align-middle"></i> {{__('messages.lbl_view')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($appointment->status == 'checkout' || $appointment->status == 'check_in')
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center justify-content-between gap-3 section-bg p-3 rounded">
                                    <h6 class="font-size-18 mb-0">{{ __('frontend.encounter_details') }}
                                    </h6>
                                    <a data-bs-toggle="modal" data-bs-target="#encounter-details-view"
                                    class="font-size-14 fw-semibold text-secondary">{{__('messages.lbl_view')}}</a>
                            </div>
                        </div>
                    @endif
                    <!-- review -->
                    <!-- rate us modal -->
                    <x-frontend::section.review />

                    @if($review)
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-2">
                                <h6 class="font-size-18">{{ __('frontend.your_review') }}
                                </h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap rate-us-btn">
                                    <button class="btn p-0" data-bs-toggle="modal" data-service-id="{{ optional($appointment->clinicservice)->id }}"
                                        data-doctor-id="{{ optional($appointment->doctor)->id }}"
                                        data-review-id="{{ $review->id }}"
                                        data-rating="{{ $review->rating }}"
                                        data-review-msg="{{ $review->review_msg }}"
                                        data-bs-target="#review-service">
                                        <i class="ph ph-pencil-simple-line"></i>
                                    </button>
                                    <!-- rate us modal -->
                                    <x-frontend::section.review />
                                    <button class="delete-rating-btn btn p-0" data-review-id="{{ $review->id }}">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <ul class="list-inline m-0 p-0">
                                <li class="review-card">
                                    <div class="review-detail section-bg rounded">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-2 rounded-pill bg-primary-subtle badge">
                                                    <i class="ph-fill ph-star text-warning"></i>
                                                    <span class="font-size-14 fw-bold">{{ $review->rating }}</span>
                                                </div>
                                                <h6 class="m-0">{{ $review->title }}</h6>
                                            </div>
                                            <span class="bg-secondary-subtle badge rounded-pill">{{ optional($review->clinic_service)->name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-column flex-wrap gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ optional($review->user)->profile_image }}" alt="user"
                                                    class="img-fluid user-img rounded-circle">
                                                <div>
                                                    <h6 class="line-count-1 font-size-14">By {{ optional($review->user)->gender == 'female' ? 'Miss.' : 'Mr.' }}
                                                        {{ optional($review->user)->first_name.' '.optional($review->user)->last_name }}
                                                    </h6>
                                                    <small class="mb-0 font-size-14">{{ $review->updated_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <p class="mb-0 mt-2 font-size-14">{{ $review->review_msg }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>

                @php
                    // Calculate service total from billing items (includes all services added in encounter)
                    $service_total_amount = 0;
                    
                    if ($appointment->patientEncounter !== null && optional($appointment->patientEncounter->billingrecord)->billingItem) {
                        // For encounters: Sum all billing items (original service + encounter-added services)
                        foreach ($appointment->patientEncounter->billingrecord->billingItem as $item) {
                            $service_total_amount += $item->total_amount;
                        }
                    } else {
                        // For direct appointment without encounter
                        $service_total_amount = $appointment->service_price + (optional($appointment->appointmenttransaction)->inclusive_tax_price ?? 0);
                    }
                @endphp

                @php
                    // Get transaction and tax data
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
                        if ($appointment->appointmenttransaction == null) {
                            $taxData = Modules\Tax\Models\Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('status', 1)->where('tax_type', 'exclusive')->get();
                        } else {
                            $taxData = json_decode(optional($transaction)->tax_percentage, true) ?: [];
                        }
                    }
                    
                    // Set $tax variable for compatibility with existing code
                    $tax = $taxData;
                @endphp

                <div class="col-lg-4 mt-lg-0 mt-5">
                    <h6 class="pb-1">{{ __('frontend.payment_details') }}</h6>
                    @if(
                        $appointment->status == 'cancelled' &&
                        optional($appointment->appointmenttransaction)->payment_status != 0 &&
                        optional($appointment->appointmenttransaction)->transaction_type != 'cash'
                    )
                    @php
                            $refundAmount = $appointment->getRefundAmount(); // Assumes this returns positive or negative amount
                            
                    @endphp
                    <div class="payment-box section-bg rounded">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted small">{{ formatDate($appointment->updated_at) }}</div>
                                <span class="badge {{ $refundAmount >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                                    {{ $refundAmount >= 0 ? __('frontend.refund_completed') : __('frontend.wallet_deducted') }}
                                </span>
                            </div>

                            <h6 class="fw-bold mb-4">
                                {{ $refundAmount >= 0 ? __('messages.refund_of').' '. \Currency::format($refundAmount) :  __('messages.wallet_deduction').' '. \Currency::format(abs($refundAmount)) }}
                            </h6>

                            <div class="row mb-2">
                            <div class="col-6 text-muted">{{ __('earning.lbl_payment_method') }}</div>
                            <div class="col-6 text-end text-primary">{{ __('messages.wallet') }}</div>
                            </div>

                            <div class="row mb-2">
                            <div class="col-6 text-muted">{{ __('clinic.price') }}</div>
                            <div class="col-6 text-end">{{ \Currency::format($appointment->total_amount) }}</div>
                            </div>
                            @if ($appointment->advance_paid_amount !=0 )
                            <div class="row mb-2">
                                <div class="col-6 text-muted">{{ __('messages.advanced_payment') }} </div>
                                <div class="col-6 text-end">{{ \Currency::format($appointment->advance_paid_amount) }}</div>
                            </div>
                            @endif

                            @if($appointment->cancellation_charge_amount != 0)
                            <div class="row mb-2">
                                <div class="col-6 text-muted">
                                    {{ __('messages.cancellation_fee') }}
                                    @if($appointment->cancellation_type === 'percentage')
                                        ({{ $appointment->cancellation_charge }}%)
                                    @else
                                        ({{ Currency::format($appointment->cancellation_charge) }})
                                    @endif
                                </div>
                                <div class="col-6 text-end">
                                    {{ Currency::format($appointment->cancellation_charge_amount) }}
                                </div>
                            </div>
                            @endif
                            <hr class="my-3">

                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center px-4 py-2 rounded"
                                style="background-color: {{ $refundAmount >= 0 ? '#e6f4ea' : '#fdecea' }};">

                            <span class="fw-semibold {{ $refundAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $refundAmount >= 0 ? __('messages.refund_amount') : __('frontend.wallet_deducted') }}
                            </span>

                            <span class="fw-semibold heading-color">
                                {{ \Currency::format(abs($refundAmount)) }}
                            </span>
                        </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="payment-box section-bg rounded">
                        @php
                            // STEP 1: Service Total = Service Price + Inclusive Tax (includes all billing items)
                            $service_total = $service_total_amount;

                            // STEP 2: Calculate Discount
                            $discount_amount = 0;
                            $discount_percent = 0;
                            $discount_type = '';

                            // Use correct billing record for patient encounter / direct appointment
                            if ($appointment->patientEncounter !== null && $transaction && ($transaction['final_discount'] ?? null) == 1) {
                                // Patient encounter discount
                                $discount_percent = $transaction['final_discount_value'] ?? 0;
                                $discount_type = $transaction['final_discount_type'] ?? 'percentage';
                                if ($discount_type === 'percentage') {
                                    $discount_amount = $service_total * ($discount_percent / 100);
                                } else {
                                    $discount_amount = $discount_percent;
                                }
                            } elseif (
                                optional($appointment->appointmenttransaction)->discount_value > 0
                            ) {
                                // Direct appointment discount
                                $discount_percent = optional($appointment->appointmenttransaction)->discount_value;
                                $discount_type = optional($appointment->appointmenttransaction)->discount_type ?? 'percentage';

                                if ($discount_type === 'percentage') {
                                    $discount_amount = $service_total * ($discount_percent / 100);
                                } else {
                                    $discount_amount = $discount_percent;
                                }
                            }

                            // STEP 3: Sub Total = Service Total - Discount
                            $sub_total = $service_total - $discount_amount;
                        @endphp
                        {{-- frontend side appoitment detials calulation --}}
                        <!-- STEP 1: Service Total -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <span>
                                {{ __('appointment.service_total') }}
                                @if ($appointment->patientEncounter !== null || optional($appointment->appointmenttransaction)->inclusive_tax_price > 0)
                                    <span class="text-danger small">({{ __('messages.lbl_with_inclusive_tax') }})</span>
                                @endif
                            </span>
                            <span class="text-primary fw-bold">{{ Currency::format($service_total) }}</span>
                        </div>

                        <!-- STEP 2: Discount (if any) -->
                        @if ($discount_amount > 0)
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <span>{{ __('messages.discount') }}(
                                    @if ($discount_type === 'percentage')
                                        <span class="text-success">{{ $discount_percent ?? '--' }}%</span>
                                    @else
                                        <span class="text-success">{{ Currency::format($discount_percent) ?? '--' }}</span>
                                    @endif
                                    )
                                </span>
                                <span class="text-success fw-bold">- {{ Currency::format($discount_amount) ?? '--' }}</span>
                            </div>
                        @endif

                        <!-- STEP 3: Sub Total -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <span>{{ __('messages.sub_total') }}</span>
                            <span class="text-dark fw-bold">{{ Currency::format($sub_total) }}</span>
                        </div>

                        <!-- STEP 4: Taxes (calculated on Sub Total) -->
                        @php
                            $total_tax = 0;
                            // Calculate total tax first
                            if (!empty($tax)) {
                                foreach ($tax as $t) {
                                    if (!($appointment->is_quick_booking == 1 && strpos(strtolower($t['title']), 'vat') !== false)) {
                                        $tax_value = $t['value'] ?? 0;
                                        $tax_type = $t['type'] ?? 'percentage';
                                        
                                        if ($tax_type == 'fixed') {
                                            $tax_amount = $tax_value;
                                        } else {
                                            $tax_amount = ($sub_total * $tax_value) / 100;
                                        }
                                        $total_tax += $tax_amount;
                                    }
                                }
                            }
                        @endphp
                        @if (!empty($tax) && $total_tax > 0)
                            <!-- Total Tax with Expandable Breakdown -->
                            <div class="mb-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between tax-total-line" style="cursor: pointer;" onclick="toggleTaxBreakdown()">
                                    <span>{{ __('frontend.total_tax') }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-caret-up text-danger" id="taxArrow" style="transition: transform 0.3s ease;"></i>
                                        <span class="text-danger fw-bold" id="totalTaxDisplay">{{ Currency::format($total_tax) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Collapsible Tax Breakdown -->
                                <div id="taxBreakdown" class="tax-breakdown-container" style="display: none; margin-top: 10px;">
                                    <div class="bg-light rounded p-3" style="border: 1px solid #e9ecef;">
                                        @foreach ($tax as $t)
                                            {{-- Skip VAT tax calculation for quick bookings --}}
                                            @if(!($appointment->is_quick_booking == 1 && strpos(strtolower($t['title']), 'vat') !== false))
                                                @php
                                                    $tax_name = $t['title'] ?? $t['name'] ?? 'Tax';
                                                    $tax_value = $t['value'] ?? 0;
                                                    $tax_type = $t['type'] ?? 'percentage';
                                                    
                                                    // Calculate tax on sub total
                                                    if ($tax_type == 'fixed') {
                                                        $tax_amount = $tax_value;
                                                    } else {
                                                        $tax_amount = ($sub_total * $tax_value) / 100;
                                                    }
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mb-2 text-muted">
                                                    <span>
                                                        @if ($tax_type == 'fixed')
                                                            {{ $tax_name }}
                                                        @else
                                                            {{ $tax_name }} ({{ $tax_value }}%)
                                                        @endif
                                                    </span>
                                                    <span>{{ Currency::format($tax_amount) }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @elseif (!empty($tax))
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <span>{{ __('messages.no_tax') }}</span>
                                <span class="heading-color">{{ Currency::format(0) }}</span>
                            </div>
                        @endif

                    <!-- <div class="modal fade " id="taxDetailsModal" tabindex="-1" aria-labelledby="taxDetailsModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="taxDetailsModalLabel">{{ __('frontend.tax_detail') }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul>
                                        @foreach($tax_percentage as $tax)
                                            @php
                                                if ($tax['type'] == 'percent' && $sub_total > 0) {
                                                    $tax_amount = ($sub_total * $tax['value']) / 100;
                                                } else {
                                                    $tax_amount = $tax['value'];
                                                }
                                            @endphp
                                            <li>
                                            <strong>
                                                {{ $tax['title'] }}
                                                @if($tax['type'] == 'percent')
                                                    ({{ $tax['value'] }}%)
                                                @endif
                                                :
                                            </strong>
                                            <span id="{{ strtolower(str_replace(' ', '', $tax['title'])) }}">
                                                {{ Currency::format($tax_amount) ?? '--' }}
                                            </span>
                                        </li>

                                        @endforeach
                                    </ul>
                                    <p ><strong>{{ __('frontend.total_tax') }}</strong> <span id="totalTaxAmount">{{ Currency::format($total_tax) ?? '--' }}</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('frontend.close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> -->


                    <div class="modal" id="taxDetailsModal">
            <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content section-bg position-relative rounded">
                <div class="modal-body modal-body-inner">
                    <div class="close-modal-btn" data-bs-dismiss="modal">
                        <i class="ph ph-x align-middle"></i>
                    </div>
                    <h5 class="mb-3" id="taxDetailsModalLabel">{{ __('frontend.tax_detail') }}</h5>
                    </strong></p>
                    <ul id="taxBreakdownList" class="p-0 mb-3 list-inline">

                    @foreach($tax_percentage as $tax)
                                                @php
                                                    if ($tax['type'] == 'percent' && $sub_total > 0) {
                                                        $tax_amount = ($sub_total * $tax['value']) / 100;
                                                    } else {
                                                        $tax_amount = $tax['value'];
                                                    }
                                                @endphp
                                                {{-- Hide VAT tax for quick bookings --}}
                                                @if(!($appointment->is_quick_booking == 1 && strpos(strtolower($tax['title']), 'vat') !== false))
                                                <li class=" d-flex justify-content-between gap-3">
                                                    <strong>
                                                        {{ $tax['title'] }}
                                                        @if($tax['type'] == 'percent')
                                                            ({{ $tax['value'] }}%)
                                                        @endif

                                                    </strong>
                                                    <span id="{{ strtolower(str_replace(' ', '', $tax['title'])) }}">
                                                        {{ Currency::format($tax_amount) ?? '--' }}
                                                    </span>
                                                </li>
                                                @endif

                                            @endforeach
                    </ul>
                    <p class="mb-0 mt-3 d-flex flex-wrap justify-content-between gap-3"><strong>{{ __('frontend.total_tax') }}
                    </strong> <span id="totalTaxAmount" class="fw-bold text-secondary">{{ Currency::format($total_tax) ?? '--' }}</span></p>
                </div>
                </div>
            </div>
            </div>

                        @php
                            // STEP 5: Grand Total = Sub Total + Total Tax
                            $grand_total = $sub_total + $total_tax;
                            
                            // Use database total if available, otherwise use calculated total
                            if ($appointment->patientEncounter !== null && $transaction && !empty($transaction['final_total_amount'])) {
                                $final_amount = $transaction['final_total_amount'];
                            } elseif ($appointment->total_amount && $appointment->total_amount > 0) {
                                $final_amount = $appointment->total_amount;
                            } else {
                                $final_amount = $grand_total;
                            }
                        @endphp

                        <!-- STEP 5: Grand Total -->
                        <hr class="border-top border-gray">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark">{{ __('messages.grand_total') }}</span>
                            <span class="text-success fw-bold">{{ Currency::format($final_amount) ?? '--' }}</span>
                        </div>
                        @if (optional($appointment->appointmenttransaction)->advance_payment_status == 1)
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <span>{{ __('service.advance_payment_amount') }}({{ $appointment->advance_payment_amount }}%)</span>
                                <span>{{ Currency::format($appointment->advance_paid_amount) ?? '--' }}</span>
                            </div>
                        @endif

                        @if (optional($appointment->appointmenttransaction)->advance_payment_status == 1 && $appointment->status == 'checkout')
                            <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-success p-2 ms-2">{{ __('appointment.paid') }}</span></span>
                                <span class="heading-color">{{ Currency::format($final_amount - $appointment->advance_paid_amount) }}</span>
                            </div>
                        @elseif (optional($appointment->appointmenttransaction)->advance_payment_status == 1 && optional($appointment->appointmenttransaction)->payment_status != 1 && $appointment->status != 'cancelled')
                            <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-warning p-2 ms-2">{{ __('appointment.pending') }}</span></span>
                                <span class="heading-color">{{ Currency::format($final_amount - $appointment->advance_paid_amount) }}</span>
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
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        @if($advancePaid && $appointment->status == 'check_in' && optional($appointment->appointmenttransaction)->payment_status == 0 && optional($appointment->patientEncounter)->status == 1)
                            <a href="#" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#paymentModal">{{ __('frontend.pay_now') }} {{ Currency::format($final_amount - $appointment->advance_paid_amount) }}</a>
                        @elseif($appointment->status == 'check_in' && optional($appointment->appointmenttransaction)->payment_status == 0 && optional($appointment->patientEncounter)->status == 1)
                            <a href="#" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#paymentModal">{{ __('frontend.pay_now') }} {{ Currency::format($final_amount) }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Encounter modal --}}
<div class="modal modal-xl fade" id="encounter-details-view">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg position-relative rounded">
            <div class="modal-body modal-body-inner modal-enocunter-detail">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
                <div class="encounter-info">
                    <h6>{{ __('frontend.basic_information') }}
                    </h6>
                    <div class="encounter-basic-info rounded">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.appointment_id') }}
                                    </p>
                                    <span class="text-primary font-size-14 fw-bold">#{{ $appointment->id }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.doctor_name') }}
                                    </p>
                                    <span class="encounter-desc font-size-14 fw-bold">{{ optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name ?? '-'}}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.clinic_name') }}
                                    </p>
                                    <span class="encounter-desc font-size-14 fw-bold">{{ optional($appointment->cliniccenter)->name ?? '-' }}</span>
                                </div>
                            </div>
                            <span
                                class="bg-success-subtle badge rounded-pill text-uppercase text-uppercase font-size-10">{{ optional($appointment->patientEncounter)->status ? 'Active': 'Closed' }}</span>
                        </div>
                        <div class="encounter-descrption border-top">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="font-size-14 flex-shrink-0">{{ __('frontend.description') }}
                                </span>
                                <p class="font-size-14 fw-semibold detail-desc mb-0">{{ optional($appointment->patientEncounter)->descrtiption ?? 'No records found' }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $problems = $medical_history->get('encounter_problem', collect());
                        $observations = $medical_history->get('encounter_observations', collect());
                        $notes = $medical_history->get('encounter_notes', collect());
                    @endphp

                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#problem"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.problem') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="problem" class="collapse rounded encounter-inner-box">
                            @if($problems->isNotEmpty())
                                @foreach($problems as $problem)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $problem->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_problems_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#observation"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.observation') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="observation" class="collapse  encounter-inner-box rounded">
                            @if($observations->isNotEmpty())
                                @foreach($observations as $observation)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $observation->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_observation_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#notes"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.notes') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="notes" class="collapse  encounter-inner-box rounded">
                            @if($observations->isNotEmpty())
                                @foreach($notes as $note)
                                    <p class="font-size-14 mb-0">{{ $loop->iteration }}. {{ $note->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_note_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                        href="#medical-report-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Medical Report</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="medical-report-{{ $appointment->id }}" class="collapse encounter-inner-box rounded">
                            @if ($medical_report && $medical_report->file_url)
                                <a href="{{ asset($medical_report->file_url) }}" download class="btn btn-primary">
                                Download Report
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#body_chart-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Body chart</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="body_chart-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                        @if ($bodychart->isNotEmpty())
                            <div class="d-flex  flex-wrap gap-3">
                                @foreach ($bodychart as $chart)
                                    @foreach ($chart->media as $media) <!-- Iterate through the media collection -->
                                        <div class="body-chart-content text-center">
                                            <div class="image mb-2">
                                                <img src="{{ asset($media->getUrl()) }}" alt="{{ $media->name }}" class="img-fluid" width="100" height="100">
                                            </div>
                                            <a href="{{ asset($media->getUrl()) }}" download >
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                @endforeach
                        </div>
                        @else
                            <p class="font-size-12 mb-0 text-danger text-center">No report found</p>
                        @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.prescription') }}</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="prescription" class="collapse encounter-inner-box rounded">
                            @if($prescriptions->isNotEmpty())
                                @foreach($prescriptions as $prescription)
                                    <h6>{{ $prescription->name }}</h6>
                                    @if($prescription->instruction)
                                        <p class="font-size-14 mb-0">{{ $prescription->instruction }}</p>
                                    @endif
                                    <div class="mt-3 pt-3 border-top mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span class="font-size-14 mb-2">{{ __('frontend.frequency') }}
                                                </span>
                                                <h6 class="font-size-14">{{ $prescription->frequency }}</h6>
                                            </div>
                                            <div class="col-md-6 mt-md-0 mt-4">
                                                <span class="font-size-14 mb-2">{{ __('frontend.days') }}:
                                                </span>
                                                <h6 class="font-size-14">{{ $prescription->duration }} {{ __('frontend.days') }}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_prescription_found') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#soap"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.soap') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="soap" class="collapse encounter-inner-box rounded">
                            @if($soap)

                                    <div class="border-top mb-3">
                                        <div class="row">
                                            <div class="col-md-6 ">

                                                <h6 class="font-size-14">{{ __('frontend.subjective') }}</h6>

                                                <span class="font-size-14 mb-2">{{ $soap->subjective }}</span>

                                            </div>
                                            <div class="col-md-6 ">
                                                <h6 class="font-size-14 mb-2">{{ __('frontend.objective') }}
                                                </h6>
                                                <span class="font-size-14">{{ $soap->objective }}</span>

                                            </div>

                                            <div class="col-md-6 ">
                                            <h6 class="font-size-14">{{ __('frontend.assessment') }}
                                            </h6>
                                                <span class="font-size-14 mb-2">
                                                    {{$soap->assessment}}
                                                </span>

                                            </div>
                                            <div class="col-md-6 ">
                                            <h6 class="font-size-14">{{ __('frontend.plan') }}
                                            </h6>
                                                <span class="font-size-14 mb-2">
                                                {{$soap->plan}}
                                                </span>

                                            </div>
                                        </div>
                                    </div>

                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_soap_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg rounded">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
            <div class="modal-body modal-payemnt-inner">
                <h6 class="mb-3 font-size-18" id="paymentModalLabel">{{ __('frontend.payment_method') }}</h6>
                    <div class="payment-modal-box rounded">
                        @foreach ($paymentMethods as $method)
                            <div class="form-check payment-method-items ps-0 d-flex justify-content-between align-items-center gap-3">
                                <label class="form-check-label d-flex gap-2 align-items-center"
                                    for="method-{{ $method }}">
                                    <img src="{{ asset('dummy-images/payment_icons/' . strtolower($method) . '.svg') }}"
                                        alt="{{ $method }}" style="width: 20px; height: 20px;">
                                    <span class="h6 fw-semibold m-0">{{ $method }}</span>
                                </label>
                                <input class="form-check-input" type="radio" name="payment_method"
                                    value="{{ $method }}" id="method-{{ $method }}"
                                    @if ($method === 'cash') checked @endif>
                            </div>
                        @endforeach
                    </div>
                <div class="text-end mt-5">
                    <button class="btn btn-secondary" id="pay_now"
                        data-bs-dismiss="modal">{{ __('frontend.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="{{ asset('js/enhanced-medical-transcription.js') }}" defer></script>
<style>
    .tax-total-line {
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s ease;
    }
    
    .white-space-pre-line {
        white-space: pre-line;
    }
    
    .tax-total-line:hover {
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 8px 12px;
        margin: 0 -12px;
    }
    
    #taxArrow {
        font-size: 14px;
    }
    
    .tax-breakdown-container {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .tax-breakdown-container .bg-light {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
    }
    
    .tax-breakdown-container .d-flex {
        font-size: 14px;
    }
    
    .tax-breakdown-container .d-flex:last-child {
        margin-bottom: 0 !important;
    }
</style>
<script>
    $(document).ready(function () {
        $('.delete-rating-btn').on('click', function () {
            const reviewId = $(this).data('review-id');

            Swal.fire({
                title: 'Are you sure you want to remove your review?',
                text: 'Once deleted, your review cannot be recovered',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--bs-secondary)',
                cancelButtonColor: 'var(--bs-gray-500)',
                confirmButtonText: 'Delete Review',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('delete-rating') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: reviewId
                        },
                        success: function (data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message
                            });
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'There was an error deleting the review. Please try again.'
                            });
                        }
                    });
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('paymentDetails'))
            const paymentDetails = @json(session('paymentDetails'));
            Swal.fire({
                title: 'Payment Success',
                html: `
                    <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
                    <strong>${paymentDetails.clinicName}</strong> has been confirmed on
                    <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
                    <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
                    <div>
                        <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
                        <p><strong>Payment via:</strong>${paymentDetails.paymentVia}</p>
                        <p><strong>Total Payment:</strong>${paymentDetails.currency} ${paymentDetails.totalAmount}</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Close',
                confirmButtonColor: '#FF6F61',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('appointment-list') }}";
                }
            });
        @endif
    });

    document.querySelector('#pay_now').addEventListener('click', async function () {
        const appointmentId = "{{ $appointment->id }}";
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const baseUrl = "{{ url('/') }}";
        const totalAmount = parseFloat("{{ $appointment->total_amount }}");
        const advancePaymentAmount = parseFloat("{{ $appointment->advance_payment_amount }}");
        const advancePaymentStatus = parseInt("{{ $appointment->advance_payment_status }}");

        // Check wallet balance if wallet is selected payment method
        if (selectedPaymentMethod === 'Wallet') {
            try {
                const response = await fetch("{{ route('check.wallet.balance') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        totalAmount: advancePaymentStatus === 1 ? advancePaymentAmount : totalAmount
                    })
                });

                const data = await response.json();

                if (!data.success || (advancePaymentStatus === 1 ? data.balance < advancePaymentAmount : data.balance < totalAmount)) {
                    successSnackbar('Insufficient balance. Please add funds in wallet')
                    return;
                }
            } catch (error) {
                console.error('Error checking wallet balance:', error);
                return;
            }
        }

        fetch(`${baseUrl}/pay-now`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                transaction_type: selectedPaymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.status) {
                if (selectedPaymentMethod === 'Wallet') {
                    const paymentDetails = {
                        doctorName: "{{ optional($appointment->doctor)->first_name }} {{ optional($appointment->doctor)->last_name }}",
                        clinicName: "{{ optional($appointment->cliniccenter)->name }}",
                        appointmentDate: "{{ $appointment->appointment_date }}",
                        appointmentTime: "{{ $appointment->appointment_time }}",
                        bookingId: appointmentId,
                        paymentVia: selectedPaymentMethod,
                        currency: "{{ $appointment->currency_symbol }}",
                        totalAmount: advancePaymentStatus === 1 ? advancePaymentAmount.toFixed(2) : totalAmount.toFixed(2)
                    };

                    Swal.fire({
                        title: 'Payment Success',
                        html: `
                            <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
                            <strong>${paymentDetails.clinicName}</strong> has been confirmed on
                            <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
                            <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
                            <div>
                                <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
                                <p><strong>Payment via:</strong>${paymentDetails.paymentVia}</p>
                                <p><strong>Total Payment:</strong>${paymentDetails.currency} ${paymentDetails.totalAmount}</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#FF6F61',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `${baseUrl}/appointment-list`;
                        }
                    });
                } else {
                    window.location.href = `${baseUrl}/appointment-list`;
                }
            } else {
                alert(data.message || 'Payment failed.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during payment processing.');
        });
    });

    // Toggle tax breakdown function
    function toggleTaxBreakdown() {
        const breakdown = document.getElementById('taxBreakdown');
        const arrow = document.getElementById('taxArrow');
        
        if (breakdown.style.display === 'none' || breakdown.style.display === '') {
            // Show breakdown
            breakdown.style.display = 'block';
            arrow.style.transform = 'rotate(0deg)'; // Keep arrow pointing up when expanded
        } else {
            // Hide breakdown
            breakdown.style.display = 'none';
            arrow.style.transform = 'rotate(180deg)'; // Rotate arrow down when collapsed
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
    const saveBtn = document.getElementById('save-medical-history-btn');

    if (!saveBtn) return;

    saveBtn.addEventListener('click', function () {
        const formData = new FormData();

        formData.append(
            'appointment_extra_info',
            document.getElementById('appointment_extra_info')?.value || ''
        );

        const files = document.getElementById('medical-record-files')?.files || [];

        console.log('files selected:', files.length, files[0]);

        for (let i = 0; i < files.length; i++) {
            formData.append('file_url[]', files[i]);
        }

        (window.appointmentAudioIds || []).forEach(function (id) {
            formData.append('audio_ids[]', id);
        });

        for (const pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        fetch("{{ route('appointments.update-medical-history', $appointment->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Saved', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Could not save medical history', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Something went wrong while saving.', 'error');
        });
    });
});
</script>

@endpush

