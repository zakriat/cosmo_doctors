@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <b-row>
        <b-col sm="12">
            <div id="bill">

                @foreach ($data as $info)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        
                                        <p class="mb-0">{{ __('messages.invoice_id') }} :<span class="text-secondary">
                                                {{ setting('inv_prefix') }}{{ $info['id'] ?? '--' }}</span> 
                                        <p class="mb-0">
                                            {{ __('messages.payment_status') }}
                                            @if ($info['appointmenttransaction']['payment_status'] == 1)
                                                <span
                                                    class="text-capitalize badge bg-success-subtle p-2">{{ __('messages.paid') }}</span>
                                            @else
                                                <span
                                                    class="text-capitalize badge bg-soft-danger p-2">{{ __('messages.unpaid') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    @php
                                        $setting = App\Models\Setting::where('name', 'date_formate')->first();
                                        $dateformate = $setting ? $setting->val : 'Y-m-d';
                                        $setting = App\Models\Setting::where('name', 'time_formate')->first();
                                        $timeformate = $setting ? $setting->val : 'h:i A';
                                        $createdDate = date($dateformate, strtotime($info['appointment_date'] ?? '--'));
                                        $createdTime = date($timeformate, strtotime($info['appointment_time'] ?? '--'));
                                    @endphp

                                    <p class="mb-0 mt-1">
                                        {{ __('messages.appointment_at') }}: <span class="font-weight-bold heading-color">
                                            {{ $createdDate }}</span>
                                    </p>
                                    <p class="mb-0 mt-1">
                                        {{ __('messages.appointment_time') }}: <span class="font-weight-bold heading-color">
                                            {{ $createdTime }}</span>
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gy-3">
                        <div class="col-md-6 col-lg-6">
                            <h5 class="mb-3">{{ __('appointment.clinic_info') }}</h5>
                            <div class="card card-block card-stretch card-height mb-0">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="image-block">
                                            <img src="{{ $info['cliniccenter']['file_url'] ?? '--' }}"
                                                class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                        </div>
                                        <div class="content-detail">
                                            <h5 class="mb-2">{{ $info['cliniccenter']['name'] ?? '--' }}</h5>
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <i class="ph ph-envelope"></i>
                                                <u class="text-secondary">{{ $info['cliniccenter']['email'] ?? '--' }}</u>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-map-pin"></i>
                                                <span>{{ $info['cliniccenter']['address'] ?? '--' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <h5 class="mb-3">{{ __('messages.patient_detail') }}</h5>
                            <div class="card card-block card-stretch card-height mb-0">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="image-block">
                                            <img src="{{ $info['user']['profile_image'] ?? '--' }}"
                                                class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                        </div>
                                        <div class="content-detail">
                                            <h5 class="mb-2">
                                                {{ $info['user']['first_name'] . '' . $info['user']['last_name'] ?? '--' }}
                                            </h5>
                                            <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                                @if ($info['user']['gender'] !== null)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="ph ph-user heading-color"></i>
                                                        <span class="">{{ $info['user']['gender'] ?? '--' }}</span>
                                                    </div>
                                                @endif
                                                @if ($info['user']['date_of_birth'] !== null)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="ph ph-cake heading-color"></i>
                                                        <span
                                                            class="">{{ date($dateformate, strtotime($info['user']['date_of_birth'])) ?? '--' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <h5 class="mb-3">{{ __('messages.doctor_detail') }}</h5>
                            <div class="card card-block card-stretch card-height mb-0">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="image-block">
                                            <img src="{{ $info['doctor']['profile_image'] ?? default_file_url() }}"
                                                class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                        </div>
                                        <div class="content-detail">
                                            <h5 class="mb-2">
                                                {{ $info['doctor']['first_name'] . ' ' . $info['doctor']['last_name'] ?? '--' }}
                                            </h5>
                                            <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                                @if ($info['doctor']['email'] !== null)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="ph ph-envelope heading-color"></i>
                                                        <span class="">{{ $info['doctor']['email'] ?? '--' }}</span>
                                                    </div>
                                                @endif
                                                
                                            </div>
                                             
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row gy-3">
                        <div class="col-md-12 col-lg-12">
                            <h5 class="mb-3 mt-3">{{ __('messages.service') }}</h5>
                            <div class="card card-block card-stretch card-height mb-0">
                                <div class="card-body">

                                    @if (!isset($info['patient_encounter']))
                                        <div class="content-detail">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                                <span>{{ __('messages.item_name') }}</span>
                                                <span class="heading-color">{{ $info['clinicservice']['name'] ?? '--' }}</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                                <span>{{ __('messages.price') }}</span>
                                                <span
                                                    class="heading-color">{{ Currency::format($info['service_price']) ?? '--' }}</span>
                                            </div>
                                            <!-- <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                                                                                                                                                                <span>{{ __('messages.total') }}</span>
                                                                                                                                                                                                <span class="heading-color">{{ Currency::format($info['service_amount']) ?? '--' }}</span>
                                                                                                                                                                                            </div> -->
                                        </div>
                                    @endif

                                    @if (isset($info['patient_encounter']) &&
                                            !empty($info['patient_encounter']['billingrecord']) &&
                                            !empty($info['patient_encounter']['billingrecord']['billing_item']))
                                        @foreach ($info['patient_encounter']['billingrecord']['billing_item'] as $billingItem)
                                            <div class="d-flex align-items-center bg-body p-4 rounded">
                                                <div class="detail-box bg-white rounded">
                                                    <img src="{{ $billingItem['clinicservice']['file_url'] ?? default_file_url() }}"
                                                        alt="avatar" class="avatar avatar-80 rounded-pill">
                                                </div>

                                                <div
                                                    class="ms-3 w-100 d-flex align-items-center justify-content-between flex-wrap">
                                                    <div class="d-flex flex-column flex-grow-1">
                                                        <div class="fs-5">
                                                            <b>{{ $billingItem['clinicservice']['name'] ?? 'N/A' }}</b>
                                                        </div>
                                                        @if(!empty($billingItem['clinicservice']['description']))
                                                            <div class="text-muted mt-1" style="font-size: 1rem;">
                                                                {{ $billingItem['clinicservice']['description'] }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @php
                                                        // Show service price, add inclusive tax if present
                                                        $service_amount = $billingItem['service_amount'] ?? 0;
                                                        $inclusive_tax = $billingItem['inclusive_tax_amount'] ?? 0;

                                                        $service_amount_with_inclusive = $service_amount + $inclusive_tax;

                                                        // Figure out the payable amount after discount if applicable
                                                        if (!empty($billingItem['discount_type']) && $billingItem['discount_type'] === 'percentage') {
                                                            $payable_Amount = $service_amount_with_inclusive - ($service_amount_with_inclusive * ($billingItem['discount_value'] / 100));
                                                        } else {
                                                            $payable_Amount = $service_amount_with_inclusive - ($billingItem['discount_value'] ?? 0);
                                                        }
                                                    @endphp

                                                    <div class="d-flex flex-column align-items-end">
                                                       
                                                        @if ($billingItem['discount_value'] > 0)
                                                            <div class="text-end mt-1">
                                                                <span class="fw-bold">{{ Currency::format($payable_Amount) }}</span>
                                                                @if ($billingItem['discount_type'] === 'percentage')
                                                                    <span class="fw-bold"> ({{ $billingItem['discount_value'] ?? '--' }}% {{ __('messages.off') }} {{ Currency::format($service_amount_with_inclusive) }})</span>
                                                                @else
                                                                    <span class="fw-bold"> ({{ Currency::format($billingItem['discount_value']) ?? '--' }} {{ __('service.off') }} {{ Currency::format($service_amount_with_inclusive) }})</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $service_total_amount = 0;
                        $total_tax = 0;
                    @endphp
                    @foreach (optional(optional($info['patient_encounter'])['billingrecord'])['billing_item'] as $item)
                        @php

                            $service_total_amount += $item['total_amount'];
                        @endphp
                    @endforeach
                    @if ($info['appointmenttransaction']['tax_percentage'] !== null)
                        @php
                            $inclusiveTaxTotal = 0;
                            $tax = $info['appointmenttransaction']['tax_percentage'];
                            $taxData = json_decode($tax, true);
                            if (is_array($taxData)) {
                                foreach ($taxData as $t) {
                                    if (
                                        (isset($t['tax_type']) && $t['tax_type'] === 'inclusive') ||
                                        (isset($t['tax_scope']) && $t['tax_scope'] === 'inclusive')
                                    ) {
                                        if ($t['type'] === 'percent') {
                                            $inclusiveTaxTotal += $service_total_amount * ($t['value'] / 100);
                                        } elseif ($t['type'] === 'fixed') {
                                            $inclusiveTaxTotal += $t['value'];
                                        }
                                    }
                                }
                            }

                            $total_amount = $info['service_amount'] ?? 0;
                            $transaction = optional(optional($info['patient_encounter'])['billingrecord'])
                                ? optional(optional($info['patient_encounter'])['billingrecord'])
                                : null;
                            if ($transaction['final_discount_type'] == 'percentage') {
                                $discount_amount = $service_total_amount * ($transaction['final_discount_value'] / 100);
                            } else {
                                $discount_amount = $transaction['final_discount_value'];
                            }
                            $sub_total = $service_total_amount - $discount_amount;
                        @endphp
                        <div class="row gy-3 mt-4">
                            <div class="col-sm-12">
                                <h5 class="mb-3">{{ __('report.lbl_taxes') }}</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                            <span>{{ __('messages.total') }}
                                                @if ($inclusiveTaxTotal > 0)
                                                    {{-- <button data-bs-toggle="modal" data-bs-target="#exampleModalCenter"
                                                        class="btn btn-link ms-2 p-0">
                                                        (Included Tax)
                                                    </button> --}}
                                                @endif
                                            </span>
                                            <span
                                                class="heading-color">{{ Currency::format($service_total_amount - $inclusiveTaxTotal) }}</span>

                                        </div>
                                        @if ($transaction['final_discount'] == 1)
                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                <span>{{ __('messages.discount') }}(
                                                    @if ($transaction['final_discount_type'] === 'percentage')
                                                        <span
                                                            class="heading-color">{{ $transaction['final_discount_value'] ?? '--' }}%</span>
                                                    @else
                                                        <span
                                                            class="heading-color">{{ Currency::format($transaction['final_discount_value']) ?? '--' }}</span>
                                                    @endif
                                                    )
                                                </span>
                                                <span
                                                    class="heading-color">{{ Currency::format($discount_amount) ?? '--' }}</span>
                                            </div>

                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                <span>{{ __('messages.sub_total') }}</span>
                                                <span
                                                    class="heading-color">{{ Currency::format($sub_total - $inclusiveTaxTotal) }}</span>
                                            </div>
                                        @endif

                                        @foreach ($taxData as $taxPercentage)
                                            @php
                                                $taxTitle = $taxPercentage['title'];
                                            @endphp
                                            @if ($taxPercentage['type'] == 'fixed')
                                                <?php
                                                $total_tax += $taxPercentage['value'];
                                                ?>
                                            @else
                                                <?php
                                                $tax_amount = ($sub_total * $taxPercentage['value']) / 100;
                                                if ($sub_total > 0) {
                                                    $tax_amount = ($sub_total * $taxPercentage['value']) / 100;
                                                }
                                                $total_tax += $tax_amount;
                                                ?>
                                            @endif
                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                <span>
                                                    @if ($taxPercentage['type'] == 'fixed')
                                                        {{ $taxTitle }}
                                                       
                                                    @else
                                                        {{ $taxTitle }} ({{ $taxPercentage['value'] ?? '--' }}%)
                                                    @endif
                                                </span>

                                                <span class="heading-color">
                                                    @if ($taxPercentage['type'] == 'fixed')
                                                        {{ Currency::format($taxPercentage['value']) ?? '--' }}
                                                    @else
                                                        {{ Currency::format($tax_amount) ?? '--' }}
                                                    @endif

                                                </span>
                                            </div>
                                        @endforeach

                                        @php
                                            $amount_total = 0;
                                            $discount_amount = 0;

                                            $transaction = optional(
                                                optional($info['patient_encounter'])['billingrecord'],
                                            )
                                                ? optional(optional($info['patient_encounter'])['billingrecord'])
                                                : null;
                                            if ($transaction['final_discount_type'] == 'percentage') {
                                                $discount_amount =
                                                    $service_total_amount *
                                                    ($transaction['final_discount_value'] / 100);
                                            } else {
                                                $discount_amount = $transaction['final_discount_value'];
                                            }

                                            $amount_due = $service_total_amount + $total_tax - $discount_amount;

                                            $remaining_payable_amount = $amount_due - $info['advance_paid_amount'];
                                        @endphp
                                        <hr class="border-top border-gray">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                            <span class="heading-color">{{ __('messages.grand_total') }}</span>
                                            <span
                                                class="text-secondary">{{ Currency::format($amount_due - $inclusiveTaxTotal) ?? '--' }}</span>
                                        </div>


                                        @if ($info['appointmenttransaction']['advance_payment_status'] == 1)
                                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                <span>{{ __('service.advance_payment_amount') }}({{ $info['advance_payment_amount'] }}%)</span>
                                                <span>{{ Currency::format($info['advance_paid_amount']) ?? '--' }}</span>
                                            </div>
                                        @endif

                                        @if ($info['appointmenttransaction']['advance_payment_status'] == 1 && $info['status'] == 'checkout')
                                            <li class="d-flex align-items-center justify-content-between pt-2 pb-2 mb-2">
                                                <span>{{ __('service.remaining_amount') }}<span
                                                        class="text-capitalize badge bg-success p-2">{{ __('appointment.paid') }}</span></span>
                                                <span
                                                    class="heading-color">{{ Currency::format($remaining_payable_amount) }}</span>
                                            </li>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end align-items-center ">
                        <a class="btn btn-primary"
                            href="{{ route('backend.appointments.download_invoice', ['id' => $info['id']]) }}">
                            <i class="fa-solid fa-download"></i>
                            {{ __('appointment.lbl_download') }}
                        </a>
                    </div>







                    <hr class="my-3" />


                    <!-- <div class="d-flex justify-content-end align-items-center">
                                                                                                                                                                        <a href="{{ route('backend.appointments.download_invoice', ['id' => $info['id']]) }}" class="btn text-info p-0 fs-4" data-bs-toggle="tooltip" title="{{ __('clinic.invoice_detail') }}"><i class="ph ph-file-pdf"></i></a>
                                                                                                                                                                    </div> -->
                @endforeach
            </div>

        </b-col>
    </b-row>

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
