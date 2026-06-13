 

@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection
@section('content')


    <style type="text/css" media="print">
        @page :footer {
            display: none !important;
        }

        @page :header {
            display: none !important;
        }

        @page {
            size: landscape;
        }

        /* @page { margin: 0; } */

        .pr-hide {
            display: none;
        }


        .order_table tr td div {
            white-space: normal;
        }


        * {
            -webkit-print-color-adjust: none !important;
            /* Chrome, Safari 6 – 15.3, Edge */
            color-adjust: none !important;
            /* Firefox 48 – 96 */
            print-color-adjust: none !important;
            /* Firefox 97+, Safari 15.4+ */
        }
    </style>

    <b-row>
        <b-col sm="12">
            <div id="bill">


                <div class="row pr-hide mb-4">


                    <div class="d-flex justify-content-end align-items-center ">
                        <a class="btn btn-primary" onclick="invoicePrint(this)">
                            <i class="fa-solid fa-download"></i>
                            {{ __('messages.print') }}
                        </a>
                    </div>
                </div>
                @php
                    use Carbon\Carbon;
                @endphp

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="mb-0">{{ __('messages.invoice_id') }} :<span class="text-secondary">
                                        {{ setting('inv_prefix') }}{{ $billing['encounter_id'] ?? '--' }}</span> </h3> 
                                    <p class="mb-0">
                                        {{ __('messages.payment_status') }}
                                        @if ($billing['payment_status'] == 1)
                                            <span
                                                class="badge booking-status bg-success-subtle p-2">{{ __('messages.paid') }}</span>
                                        @elseif(optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status)
                                            <span
                                                class="badge booking-status bg-success-subtle py-2 px-3">{{ __('appointment.advance_paid') }}
                                            </span>
                                        @elseif(optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction) == null)
                                            <span
                                                class="badge booking-status bg-danger-subtle py-2 px-3">{{ __('appointment.failed') }}
                                            </span>
                                        @else
                                            <span
                                                class="badge booking-status bg-danger-subtle p-2">{{ __('messages.unpaid') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <p class="mt-1 mb-0">{{ __('messages.date') }}: <span class="font-weight-bold heading-color">
                                        {{ isset($billing['created_at'])
                                            ? Carbon::parse($billing['created_at'])->timezone($timezone)->format($dateformate) .
                                                ' At ' .
                                                Carbon::parse($billing['created_at'])->timezone($timezone)->format($timeformate)
                                            : '-- At --' }}</span>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row gy-3">
                    <div class="col-md-12 col-lg-12">
                        <h5 class="mb-3">{{ __('messages.clinic_info') }}</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="image-block">
                                        <img src="{{ $billing['clinic']['file_url'] ?? '--' }}"
                                            class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                    </div>
                                    <div class="content-detail">
                                        <h5 class="mb-2">{{ $billing['clinic']['name'] ?? '--' }}</h5>
                                        <div class="d-flex flex-wrap gap-4">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-envelope heading-color"></i>
                                                <u class="text-secondary">{{ $billing['clinic']['email'] ?? '--' }}</u>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-map-pin heading-color"></i>
                                                <span>{{ $billing['clinic']['address'] ?? '--' }}</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-phone-call heading-color"></i>
                                                <span>{{ $billing['clinic']['contact_number'] ?? '--' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <h5 class="mb-3">{{ __('messages.doctor_details') }}</h5>
                        <div class="card card-block card-stretch card-height mb-0">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center h-100 gap-3">
                                    <div class="image-block">
                                        <img src="{{ $billing['doctor']['profile_image'] ?? '--' }}"
                                            class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                    </div>
                                    <div class="content-detail">
                                        <h5 class="mb-2">Dr. {{ $billing['doctor']['full_name'] ?? '--' }}</h5>
                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-envelope heading-color"></i>
                                                <u class="text-secondary">{{ $billing['doctor']['email'] ?? '--' }}</u>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <i class="ph ph-phone-call heading-color"></i>
                                                <span>{{ $billing['doctor']['mobile'] ?? '--' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <h5 class="mb-3">{{ __('messages.patient_detail') }}</h5>
                        <div class="card card-block card-stretch card-height mb-0">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center h-100 gap-3">
                                    <div class="image-block">
                                        <img src="{{ $billing['user']['profile_image'] ?? '--' }}"
                                            class="img-fluid avatar avatar-50 rounded-circle" alt="image">
                                    </div>
                                    <div class="content-detail">
                                        <h5 class="mb-2">
                                            {{ $billing['user']['first_name'] . '' . $billing['user']['last_name'] ?? '--' }}
                                        </h5>
                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                            @if ($billing['user']['gender'] !== null)
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ph ph-user heading-color"></i>
                                                    <span class="">{{ $billing['user']['gender'] ?? '--' }}</span>
                                                </div>
                                            @endif
                                            @if ($billing['user']['date_of_birth'] !== null)
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ph ph-cake heading-color"></i>
                                                    <span
                                                        class="">{{ date($dateformate, strtotime($billing['user']['date_of_birth'])) ?? '--' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <h5 class="mb-3">{{ __('messages.service') }}</h5>
                        <div class="card card-block card-stretch card-height mb-0">
                            <div class="card-body">
                                <div class="content-detail">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                        <span>{{ __('messages.service_name') }}</span>
                                        <span class="heading-color">{{ $billing['clinicservice']['name'] ?? '--' }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                        <span>{{ __('messages.price') }}</span>
                                        <span
                                            class="heading-color">{{ Currency::format($billing['clinicservice']['charges'] ?? $billing['service_amount']) ?? '--' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3" />
                @if (!empty($billing['billingItem']) && $billing['billingItem']->isNotEmpty())
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('messages.service') }}</h5>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered border-top order_table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('messages.sr_no') }}</th>
                                            <th>{{ __('messages.service_name') }}</th>
                                            <th>{{ __('messages.price') }}</th>
                                            <th>{{ __('messages.discount') }}</th>
                                            <th>{{ __('appointment.inclusive_tax') }}</th>
                                            <th>{{ __('messages.total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $index = 1 @endphp
                                        @foreach ($billing['billingItem'] as $item)
                                            @php
                                                // Simple billing item calculation
                                                $quantity = $item->quantity ?? 1;
                                                $service_price = $item->service_amount;
                                                $inclusive_tax = $item->inclusive_tax_amount ?? 0;
                                                
                                                // Service total with tax per unit
                                                $price_per_unit = $service_price + $inclusive_tax;
                                                
                                                // Total for this item (quantity × price per unit)
                                                $item_total = $price_per_unit * $quantity;
                                                
                                                // Apply discount if any
                                                $discount = 0;
                                                if ($item->discount_value > 0) {
                                                    if ($item->discount_type === 'percentage') {
                                                        $discount = $item_total * ($item->discount_value / 100);
                                                    } else {
                                                        $discount = $item->discount_value;
                                                    }
                                                }
                                                
                                                $final_total = $item_total - $discount;
                                            @endphp
                                            <tr>
                                                <td>{{ $index }}</td>
                                                <td>{{ $item->item_name ?? '--' }}</td>
                                                <td class="text-end">
                                                    {{ Currency::format($service_price) }} × {{ $quantity }}
                                                </td>
                                                <td class="text-end">
                                                    @if ($item->discount_value > 0)
                                                        @if ($item->discount_type === 'percentage')
                                                            {{ $item->discount_value ?? '--' }}% ({{ Currency::format($discount) }})
                                                        @else
                                                            {{ Currency::format($item->discount_value) ?? '--' }}
                                                        @endif
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ Currency::format($inclusive_tax * $quantity) ?? '--' }}</td>
                                                <td class="text-end">{{ Currency::format($final_total) ?? '--' }}
                                                </td>
                                            </tr>
                                            @php $index++ @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-0">{{ __('messages.no_record_found') }}</h4>
                        </div>
                    </div>
                @endif

                @if ($billing['tax_data'] !== null)
                    @php
                        $tax = $billing['tax_data'];
                        $taxData = json_decode($tax, true);
                        $total_amount = 0;
                        // $total_amount = $billing['service_amount'] ?? 0;
                    @endphp


                    @php
                    // ============================================================================
                    // BILLING CALCULATION SYSTEM (Following appointment_detail.blade.php logic)
                    // ============================================================================
                    
                    // STEP 1: Calculate Service Total from billing items
                    $service_total_amount = 0;
                    $hasInclusiveTax = false;
                    
                    if (!empty($billing['billingItem']) && $billing['billingItem']->isNotEmpty()) {
                        foreach ($billing['billingItem'] as $item) {
                            $quantity = $item->quantity ?? 1;
                            $service_price = $item->service_amount;
                            $inclusive_tax = $item->inclusive_tax_amount ?? 0;
                            
                            if ($inclusive_tax > 0) {
                                $hasInclusiveTax = true;
                            }
                            
                            // Item total (before item discount)
                            $item_total = ($service_price + $inclusive_tax) * $quantity;
                            
                            // Apply item-level discount if any
                            $item_discount = 0;
                            if ($item->discount_value > 0) {
                                if ($item->discount_type === 'percentage') {
                                    $item_discount = $item_total * ($item->discount_value / 100);
                                } else {
                                    $item_discount = $item->discount_value;
                                }
                            }
                            
                            $service_total_amount += ($item_total - $item_discount);
                        }
                    } else {
                        // Fallback to database value if no billing items
                        $service_total_amount = $billing['service_amount'] ?? 0;
                    }
                    
                    // STEP 2: Calculate Encounter-Level Discount
                    $encounter_discount_amount = 0;
                    $encounter_discount_percent = 0;
                    $encounter_discount_type = '';
                    
                    if (isset($billing['final_discount']) && $billing['final_discount'] == 1) {
                        $encounter_discount_percent = $billing['final_discount_value'] ?? 0;
                        $encounter_discount_type = $billing['final_discount_type'] ?? 'percentage';
                        
                        if ($encounter_discount_type === 'percentage') {
                            $encounter_discount_amount = $service_total_amount * ($encounter_discount_percent / 100);
                        } else {
                            $encounter_discount_amount = $encounter_discount_percent;
                        }
                    }
                    
                    // STEP 3: Calculate Sub Total
                    $sub_total = $service_total_amount - $encounter_discount_amount;
                    @endphp
                    
                    <div class="row gy-3 mt-4">
                        <div class="col-sm-12">
                            <h5 class="mb-3">{{ __('report.lbl_taxes') }}</h5>
                            <div class="card">
                                <div class="card-body">
                                    <!-- STEP 1: Service Total -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                        <span>
                                            {{ __('appointment.service_total') }}
                                            @if ($hasInclusiveTax)
                                                <span class="text-danger small">({{ __('messages.lbl_with_inclusive_tax') }})</span>
                                            @endif
                                        </span>
                                        <span class="heading-color">{{ Currency::format($service_total_amount) }}</span>
                                    </div>

                                    <!-- STEP 2: Discount (if any) -->
                                    @if ($encounter_discount_amount > 0)
                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                            <span>{{ __('messages.discount') }}
                                                @if ($encounter_discount_type === 'percentage')
                                                    ({{ $encounter_discount_percent }}%)
                                                @endif
                                            </span>
                                            <span class="heading-color">- {{ Currency::format($encounter_discount_amount) }}</span>
                                        </div>
                                    @endif

                                    <!-- STEP 3: Sub Total -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                        <span>{{ __('messages.sub_total') }}</span>
                                        <span class="heading-color">{{ Currency::format($sub_total) }}</span>
                                    </div>

                                    <!-- STEP 4: Taxes (calculated on Sub Total) -->
                                    @php
                                        $total_tax = 0;
                                        $tax = $billing['tax_data'];
                                        $taxData = json_decode($tax, true) ?? [];
                                    @endphp
                                    
                                    @if (!empty($taxData))
                                        @foreach ($taxData as $tax)
                                            @php
                                                $tax_name = $tax['title'] ?? $tax['name'] ?? 'Tax';
                                                $tax_value = $tax['value'] ?? 0;
                                                $tax_type = $tax['type'] ?? 'percentage';
                                                
                                                // Skip inclusive taxes (already included in service total)
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
                                    @else
                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                            <span>{{ __('messages.no_tax') }}</span>
                                            <span class="heading-color">{{ Currency::format(0) }}</span>
                                        </div>
                                    @endif

                                    @php
                                        // STEP 5: Grand Total = Sub Total + Total Tax
                                        $calculated_grand_total = $sub_total + $total_tax;
                                        
                                        // Prioritize database value, fallback to calculated total
                                        if (isset($billing['final_total_amount']) && $billing['final_total_amount'] > 0) {
                                            $final_amount = $billing['final_total_amount'];
                                        } else {
                                            $final_amount = $calculated_grand_total;
                                        }
                                        
                                        $advance_paid_amount = optional(optional($billing->patientencounter)->appointmentdetail)->advance_paid_amount ?? 0;
                                        $remaining_payable_amount = $final_amount - $advance_paid_amount;
                                    @endphp

                                    <!-- STEP 5: Grand Total -->
                                    <hr class="border-top border-gray">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                        <span class="heading-color">{{ __('messages.grand_total') }}</span>
                                        <span class="text-secondary">{{ Currency::format($final_amount) ?? '--' }}</span>
                                    </div>

                                    @if (optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status == 1)
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <span>{{ __('service.advance_payment_amount') }}({{ optional(optional($billing->patientencounter)->appointmentdetail)->advance_payment_amount }}%)</span>
                                            <span>{{ Currency::format(optional(optional($billing->patientencounter)->appointmentdetail)->advance_paid_amount) ?? '--' }}</span>
                                        </div>
                                    @endif

                                    @if (optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status == 1 && optional(optional($billing->patientencounter)->appointmentdetail)->status == 'checkout')
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-success p-2 ms-2">{{ __('appointment.paid') }}</span></span>
                                            <span class="heading-color">{{ Currency::format($remaining_payable_amount) }}</span>
                                        </div>
                                    @elseif (optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status == 1 && optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->payment_status != 1 && optional(optional($billing->patientencounter)->appointmentdetail)->status != 'cancelled')
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span>{{ __('service.remaining_amount') }}<span class="text-capitalize badge bg-warning p-2 ms-2">{{ __('appointment.pending') }}</span></span>
                                            <span class="heading-color">{{ Currency::format($remaining_payable_amount) }}</span>
                                        </div>
                                    @endif

                                    @if(optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->transaction_type)
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 pb-2 mb-2">
                                            <span class="fw-semibold">{{ __('appointment.lbl_payment_method') }}</span>
                                            <span class="text-primary fw-bold">
                                                {{ ucfirst(optional(optional(optional($billing->patientencounter)->appointmentdetail)->appointmenttransaction)->transaction_type) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif







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
    <script>
        function invoicePrint() {
            window.print()
        }

        function updateStatusAjax(__this, url) {
            console.log(url);
            console.log($billing);
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: {{ $billing['id'] }},
                    status: __this.val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.status) {
                        window.successSnackbar(res.message)
                        setTimeout(() => {
                            location.reload()
                        }, 100);
                    }
                }
            });
        }
    </script>
@endpush