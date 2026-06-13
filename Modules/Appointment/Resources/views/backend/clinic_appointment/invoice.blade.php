<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('appointment.medical_certificate') }}</title>
    <style>
        /* Add CSS styles here */
        .custom-table {
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .custom-table thead {
            background-color: #f0f0f0;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            font-size: 12px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .badge-success {
            background-color: #5cb85c;
            color: #fff;
        }

        .badge-danger {
            background-color: #d9534f;
            color: #fff;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .d-flex {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            border-radius: 4px;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        .btn-primary {
            background-color: #337ab7;
            color: #fff;
            border-color: #2e6da4;
        }

        .text-info {
            color: #31708f;
        }

        .fs-4 {
            font-size: 1.25rem;
        }

        .triage-section {
            margin: 20px 0;
        }

        .prescription-section {
            margin: 20px 0;
        }

        .section-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
        }
    </style>
</head>

<body style="font-size: 16px; color: #000;">
    <b-row>
        <b-col sm="12">
            <div id="bill">
                @foreach ($data as $info)
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-0">{{ $info['cliniccenter']['name'] ?? '--' }}</h2>
                            <h3 class="mb-0 font-weight-bold"> {{ setting('inv_prefix') ?: __('messages.invoice_id') }} <span
                                    class="text-primary">{{ $info['id'] ?? '--' }}</span></h3>
                            @php

                                $setting = App\Models\Setting::where('name', 'date_formate')->first();
                                $dateformate = $setting ? $setting->val : 'Y-m-d';
                                $setting = App\Models\Setting::where('name', 'time_formate')->first();
                                $timeformate = $setting ? $setting->val : 'h:i A';
                                $createdDate = date($dateformate, strtotime($info['appointment_date'] ?? '--'));
                                $createdTime = date($timeformate, strtotime($info['appointment_time'] ?? '--'));
                            @endphp
                            <h4 class="mb-0">
                                <span class="font-weight-bold"> {{ __('messages.appointment_at') }}: </span>
                                {{ $createdDate }}
                            </h4>
                            <h4 class="mb-0">
                                <span class="font-weight-bold"> {{ __('messages.appointment_time') }}: </span>
                                {{ $createdTime }}
                            </h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <p class="mb-0">{{ $info['cliniccenter']['address'] ?? '--' }}</p>
                            <p class="mb-0">{{ $info['cliniccenter']['email'] ?? '--' }}</p>
                            <p class="mb-0 mt-2">
                                {{ __('messages.payment_status') }}
                                @if ($info['appointmenttransaction']['payment_status'] == 1)
                                    <span class="badge badge-success">{{ __('messages.paid') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('messages.unpaid') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="my-3" />
                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{ __('messages.patient_detail') }}</h3>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-sm custom-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('messages.patient_name') }}</th>
                                        <th>{{ __('messages.patient_gender') }}</th>
                                        <th>{{ __('messages.patient_dob') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-capitalize">
                                    <tr>
                                        <td>{{ $info['user']['first_name'] . '' . $info['user']['last_name'] ?? '--' }}
                                        </td>
                                        <td>{{ $info['user']['gender'] ?? '--' }}</td>
                                        @if ($info['user']['date_of_birth'] !== null)
                                            <td>{{ date($dateformate, strtotime($info['user']['date_of_birth'])) ?? '--' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="my-3" />
                    @if (isset($info['patient_encounter']))
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{{ __('messages.service') }}</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('messages.sr_no') }}</th>
                                                <th>{{ __('messages.item_name') }}</th>
                                                <th style="text-align: right;">
                                                    {{ __('messages.price') }}
                                                    
                                                </th>
                                                <th style="text-align: right;">{{ __('messages.qty') }}</th>
                                                @php
                                                    // Check if any billing item has discount or inclusive tax to show the fields dynamically
                                                    $showDiscount = false;
                                                    $showInclusiveTax = false;
                                                    if (!empty($info['patient_encounter']['billingrecord']['billing_item'])) {
                                                        foreach ($info['patient_encounter']['billingrecord']['billing_item'] as $billingItemCheck) {
                                                            if (!empty($billingItemCheck['discount_value']) && $billingItemCheck['discount_value'] > 0) {
                                                                $showDiscount = true;
                                                            }
                                                            if (!empty($billingItemCheck['inclusive_tax_amount']) && $billingItemCheck['inclusive_tax_amount'] > 0) {
                                                                $showInclusiveTax = true;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if($showDiscount)
                                                    <th style="text-align: right;">{{ __('service.discount') }}</th>
                                                @endif
                                                {{-- @if($showInclusiveTax)
                                                    <th style="text-align: right;">{{ __('service.inclusive_tax') }}</th>
                                                @endif --}}
                                                <th style="text-align: right;">{{ __('messages.total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $index = 1; @endphp
                                            @foreach ($info['patient_encounter']['billingrecord']['billing_item'] as $billingItem)
                                                <tr>
                                                    <td>{{ $index }}</td>
                                                    <td>
                                                        {{ $billingItem['clinicservice']['name'] ?? '--' }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        @php
                                                            // Add inclusive tax in the price for clarity
                                                            $price_with_inclusive = ($billingItem['service_amount'] ?? 0) + ($billingItem['inclusive_tax_amount'] ?? 0);
                                                        @endphp
                                                        {{ Currency::format($price_with_inclusive) ?? '--' }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ $billingItem['quantity'] ?? '--' }}
                                                    </td>
                                                    @if($showDiscount)
                                                        <td style="text-align: right;">
                                                            @php
                                                                $discount_display = '-';
                                                                $discount_amount = 0;
                                                                $amount_for_discount = ($billingItem['service_amount'] ?? 0) + ($billingItem['inclusive_tax_amount'] ?? 0);

                                                                if (!empty($billingItem['discount_value']) && $billingItem['discount_value'] > 0) {
                                                                    if ($billingItem['discount_type'] === 'percentage') {
                                                                        $discount_amount = $amount_for_discount * ($billingItem['discount_value'] / 100);
                                                                        $discount_display = '-' . Currency::format($discount_amount) . ' (' . $billingItem['discount_value'] . '%)';
                                                                    } else {
                                                                        $discount_amount = $billingItem['discount_value'];
                                                                        $discount_display = '-' . Currency::format($discount_amount);
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ $discount_display }}
                                                        </td>
                                                    @endif
                                                    {{-- @if($showInclusiveTax)
                                                        <td style="text-align: right;">
                                                            @if(!empty($billingItem['inclusive_tax_amount']) && $billingItem['inclusive_tax_amount'] > 0)
                                                                {{ Currency::format($billingItem['inclusive_tax_amount']) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endif --}}
                                                    <td style="text-align: right;">
                                                        {{ Currency::format($billingItem['total_amount']) ?? '--' }}
                                                    </td>
                                                </tr>
                                                @php $index++ @endphp
                                            @endforeach
                                        </tbody>
                                        @if ($info['clinicservice'] == null)
                                            <tbody>
                                                <tr>
                                                    <td colspan="{{ 4 + ($showDiscount ? 1 : 0) + ($showInclusiveTax ? 1 : 0) + 1 }}">
                                                        <h4 class="text-primary mb-0">
                                                            {{ __('messages.no_record_found') }}
                                                        </h4>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    <hr class="my-3" />

                    {{-- Triage Notes Section --}}
                    @if (isset($info['patient_encounter']['medical_histroy']) || isset($info['patient_encounter']['encounter_other_details']))
                        <div class="row triage-section">
                            <div class="col-md-12">
                                <div class="section-header">
                                    <h3 style="margin: 0;">{{ __('messages.triage_notes', [], 'en') ?: 'Triage Notes' }}</h3>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <tbody>
                                            {{-- Problems Section --}}
                                            @php
                                                $problems = collect($info['patient_encounter']['medical_histroy'] ?? [])->where('type', 'encounter_problem');
                                            @endphp
                                            @if($problems->isNotEmpty())
                                                <tr>
                                                    <td style="width: 20%; font-weight: bold;">{{ __('messages.problems', [], 'en') ?: 'Problems' }}</td>
                                                    <td>
                                                        @foreach($problems as $problem)
                                                            <div>• {{ $problem['title'] }}</div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Observations Section --}}
                                            @php
                                                $observations = collect($info['patient_encounter']['medical_histroy'] ?? [])->where('type', 'encounter_observations');
                                            @endphp
                                            @if($observations->isNotEmpty())
                                                <tr>
                                                    <td style="width: 20%; font-weight: bold;">{{ __('messages.observations', [], 'en') ?: 'Observations' }}</td>
                                                    <td>
                                                        @foreach($observations as $observation)
                                                            <div>• {{ $observation['title'] }}</div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Notes Section --}}
                                            @php
                                                $notes = collect($info['patient_encounter']['medical_histroy'] ?? [])->where('type', 'encounter_notes');
                                            @endphp
                                            @if($notes->isNotEmpty())
                                                <tr>
                                                    <td style="width: 20%; font-weight: bold;">{{ __('messages.notes', [], 'en') ?: 'Notes' }}</td>
                                                    <td>
                                                        @foreach($notes as $note)
                                                            <div>• {{ $note['title'] }}</div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Other Information Section --}}
                                            @if(!empty($info['patient_encounter']['encounter_other_details']['other_details']))
                                                <tr>
                                                    <td style="width: 20%; font-weight: bold;">{{ __('messages.other_information', [], 'en') ?: 'Other Information' }}</td>
                                                    <td>{{ $info['patient_encounter']['encounter_other_details']['other_details'] }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3" />
                    @endif

                    {{-- Prescription Section --}}
                    @if (isset($info['patient_encounter']['prescriptions']) && count($info['patient_encounter']['prescriptions']) > 0)
                        <div class="row prescription-section">
                            <div class="col-md-12">
                                <div class="section-header">
                                    <h3 style="margin: 0;">{{ __('frontend.prescription') }}</h3>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('messages.sr_no') }}</th>
                                                <th>{{ __('messages.medicine_name', [], 'en') ?: 'Medicine Name' }}</th>
                                                <th>{{ __('clinic.lbl_frequency') }}</th>
                                                <th>{{ __('clinic.lbl_duration') }}</th>
                                                <th>{{ __('clinic.lbl_instruction') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $prescriptionIndex = 1; @endphp
                                            @foreach ($info['patient_encounter']['prescriptions'] as $prescription)
                                                <tr>
                                                    <td>{{ $prescriptionIndex }}</td>
                                                    <td>
                                                        @if(isset($prescription['medicine']) && !empty($prescription['medicine']['name']))
                                                            {{ $prescription['medicine']['name'] }}
                                                        @else
                                                            {{ $prescription['name'] ?? '--' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $prescription['frequency'] ?? '--' }}</td>
                                                    <td>{{ $prescription['duration'] ?? '--' }}</td>
                                                    <td>{{ $prescription['instruction'] ?? '--' }}</td>
                                                </tr>
                                                @php $prescriptionIndex++ @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3" />
                    @endif

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
                            $tax = $info['appointmenttransaction']['tax_percentage'];
                            $taxData = json_decode($tax, true);



                            $total_amount = $info['service_price'] ?? 0;
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

                        <div class="row">
                            <div class="col-md-12">
                                <h3>{{ __('report.lbl_tax_details') }}</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th colspan="3">{{ __('messages.sr_no') }}</th>

                                                <th colspan="3">{{ __('messages.tax_name') }}</th>

                                                <th colspan="2">
                                                    <div class="text-right">
                                                        {{ __('messages.charges') }}
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        @php
                                            $index = 1;
                                            $totalTax = 0;
                                        @endphp
                                        @foreach ($taxData as $taxPercentage)
                                            @php
                                                $taxTitle = $taxPercentage['title'];

                                            @endphp
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">{{ $index }}</td>

                                                    <td colspan="3">
                                                        @if ($taxPercentage['type'] == 'fixed')
                                                            {{ $taxTitle }}
                                                            ({{ Currency::format($taxPercentage['value']) ?? '--' }})
                                                        @else
                                                            {{ $taxTitle }}
                                                            ({{ $taxPercentage['value'] ?? '--' }}%)
                                                        @endif
                                                    </td>

                                                    <td colspan="2" style="text-align: right;">
                                                        @if ($taxPercentage['type'] == 'fixed')
                                                            @php
                                                                $totalTax += $taxPercentage['value'];
                                                            @endphp
                                                            {{ Currency::format($taxPercentage['value']) ?? '--' }}
                                                        @else
                                                            @php
                                                                $tax_amount =
                                                                    ($sub_total * $taxPercentage['value']) / 100;
                                                                $totalTax += $tax_amount;
                                                            @endphp
                                                            {{ Currency::format($tax_amount) ?? '--' }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php $index++ @endphp
                                            </tbody>
                                        @endforeach

                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{ __('report.lbl_taxes') }}</h3>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    {{-- <thead class="thead-light">
                                        <tr>
                                            <th colspan="3"> </th>

                                            <th colspan="3"> </th>

                                            <th colspan="2">
                                                <div class="text-right">
                                                    {{ __('messages.charges') }}
                                                </div>
                                            </th>
                                        </tr>
                                    </thead> --}}

                                    <thead class="thead-light">
                                        <tr>
                                            <th colspan="6"> </th>
                                            <th colspan="2">
                                                <div class="text-right">
                                                    {{ __('messages.charges') }}
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>

                                    @php
                                        $amount_total = 0;
                                        $discount_amount = 0;

                                        $transaction = optional(optional($info['patient_encounter'])['billingrecord'])
                                            ? optional(optional($info['patient_encounter'])['billingrecord'])
                                            : null;
                                        if ($transaction['final_discount_type'] == 'percentage') {
                                            $discount_amount =
                                                $service_total_amount * ($transaction['final_discount_value'] / 100);
                                        } else {
                                            $discount_amount = $transaction['final_discount_value'];
                                        }
                                        $amount_due = $sub_total + $totalTax;

                                        $remaining_payable_amount = $amount_due - $info['advance_paid_amount'];
                                    @endphp

                                    <tfoot>
                                        <!-- <tr>
                                        <th colspan="6" class="text-right">{{ __('messages.charges') }}</th>
                                        <th class="text-right">{{ Currency::format($amount_total) ?? '--' }}</th>
                                    </tr> -->

                                        <tr>
                                            <th colspan="6" class="text-right">{{ __('messages.total') }}</th>
                                            <th colspan="2" style="text-align: right;">
                                                <span>{{ Currency::format($service_total_amount ) }}</span>
                                            </th>
                                        </tr>

                                        @if ($transaction['final_discount'] == 1)
                                            <tr>
                                                <th colspan="6" class="text-right">{{ __('messages.discount') }}
                                                    ( @if ($transaction['final_discount_type'] === 'percentage')
                                                        <span
                                                            class="heading-color">{{ $transaction['final_discount_value'] ?? '--' }}%</span>
                                                    @else
                                                        <span
                                                            class="heading-color">{{ Currency::format($transaction['final_discount_value']) ?? '--' }}</span>
                                                    @endif
                                                    )


                                                </th>
                                                <th colspan="2" style="text-align: right;">
                                                    {{ Currency::format($discount_amount) ?? '--' }}</th>
                                            </tr>

                                            <tr>
                                                <th colspan="6" class="text-right">{{ __('messages.sub_total') }}
                                                </th>
                                                <th colspan="2" style="text-align: right;">
                                                    <span>{{ Currency::format($sub_total ) }}</span>
                                                </th>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th colspan="6" class="text-right">{{ __('messages.total_tax') }}</th>
                                            <th colspan="2" style="text-align: right;">
                                                <span>{{ Currency::format($totalTax) }}</span>
                                            </th>
                                        </tr>

                                        <tr>
                                            <th colspan="6" class="text-right">{{ __('messages.grand_total') }}</th>
                                            <th colspan="2" style="text-align: right;">
                                                {{ Currency::format($amount_due ) ?? '--' }}</th>
                                        </tr>

                                        @if ($info['appointmenttransaction']['advance_payment_status'] == 1)
                                            <tr>
                                                <th colspan="6" class="text-right">
                                                    {{ __('service.advance_payment_amount') }}({{ $info['advance_payment_amount'] }}%)
                                                </th>
                                                <th colspan="2" style="text-align: right;">
                                                    {{ Currency::format($info['advance_paid_amount']) ?? '--' }}</th>
                                            </tr>
                                        @endif

                                        @if ($info['appointmenttransaction']['advance_payment_status'] == 1 && $info['status'] == 'checkout')
                                            <tr>
                                                <th colspan="6" class="text-right">
                                                    {{ __('service.remaining_amount') }} <span
                                                        class="badge badge-success">{{ __('messages.paid') }}</span>
                                                </th>
                                                <th colspan="2" style="text-align: right;">
                                                    {{ Currency::format($remaining_payable_amount) }}</th>
                                            </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </b-col>
    </b-row>
    <div style="margin-top: 40px; text-align: center;">
        {{ setting('spacial_note') ?? '' }}
    </div>
</body>

</html>
