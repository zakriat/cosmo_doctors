@extends('backend.layouts.app', ['isBanner' => false])

@section('title')
    {{ 'Dashboard' }}
@endsection

@section('content')
    <div class="user-info mb-50">
        <h1 class="fs-37">
            <span class="left-text text-capitalize fw-light">{{ greeting() }} </span>
            <span class="right-text text-capitalize">{{ $current_user }}</span>
        </h1>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">{{ __('dashboard.lbl_performance') }}</h3>
                <div class="d-flex  align-items-center">
                    {{-- <form action="{{ route('backend.home') }}" class="d-flex align-items-center gap-2">
                <div class="form-group my-0 ms-3">
                    <input type="text" name="date_range" value="{{ $date_range }}" class="form-control dashboard-date-range" placeholder="24 may 2023 to 25 June 2023" readonly="readonly">
                </div>
                <button type="submit" name="action" value="filter" class="btn btn-secondary" title="{{__('appointment.reset')}}" data-bs-placement="top" data-bs-toggle="tooltip" data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_submit') }}</button>
                </form> --}}
                    <form id="dateRangeForm" class="d-flex align-items-center gap-2">
                        <div class="form-group my-0 ms-3 d-flex gap-3">
                            <input type="text" name="date_range" id="revenuedateRangeInput" value="{{ $date_range }}"
                                class="form-control dashboard-date-range" placeholder="{{ __('messages.Select_Date') }}"
                                readonly="readonly">
                            <a href="{{ route('backend.home') }}" class="btn btn-primary" id="refreshRevenuechart"
                                title="{{ __('appointment.reset') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                                <i class="ph ph-arrow-counter-clockwise"></i>
                            </a>
                            <button type="submit" name="action" value="filter" class="btn btn-secondary"
                                data-bs-toggle="tooltip" data-bs-title="{{ __('messages.submit_date_filter') }}"
                                id="submitBtn" disabled>{{ __('dashboard.lbl_submit') }}</button>
                        </div>
                    </form>

                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4">
                            <a href="{{ route('backend.appointments.index') }}" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Filter Wise Total Appointment Count">
                                <div class="card dashboard-cards appointments">
                                    <div class="card-body">
                                        <p class="mb-0">{{ __('appointment.total_number_appointment') }} </p>
                                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                            <h2 class="mb-0" id="total_booking_count">{{ $data['total_appointments'] }}
                                            </h2>
                                            <img src="{{ asset('img/dashboard/clender.png') }}" alt="image">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <a href="{{ route('backend.services.index') }}" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Filter Wise Total Active Service Count">
                                <div class="card dashboard-cards appointments">
                                    <div class="card-body">
                                        <p class="mb-0">{{ __('dashboard.total_active_service') }}</p>
                                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                            <h2 class="mb-0" id="total_active_service_count">
                                                {{ $data['total_clinicservice'] }}</h2>
                                            <img src="{{ asset('img/dashboard/services.png') }}" alt="image">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if (multiVendor() == '1' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
                            <div class="col-sm-6 col-lg-4">
                                <a href="{{ route('backend.multivendors.index') }}" class="text-secondary">
                                    <div class="card dashboard-cards appointments">
                                        <div class="card-body">
                                            <p class="mb-0">{{ __('dashboard.total_active_vendors') }}</p>
                                            <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                                <h2 class="mb-0" id="total_active_vendor_count">
                                                    {{ $data['totalactivevendor'] }}</h2>
                                                <img src="{{ asset('img/dashboard/active-vendor.png') }}" alt="image">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                        <div class="col-sm-6 col-lg-4">
                            <a href="{{ route('backend.clinics.index') }}" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Filter Wise Total Clinic Count">
                                <div class="card dashboard-cards appointments">
                                    <div class="card-body">
                                        <p class="mb-0">{{ __('dashboard.total_clinics') }}</p>
                                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                            <h2 class="mb-0">{{ $data['total_clinics'] }}</h2>
                                            <img src="{{ asset('img/dashboard/product-sale.png') }}" alt="image">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 {{ multivendor() == 1 ? 'col-lg-4' : 'col-lg-6' }}">
                            <a href="{{ route('backend.customers.index') }}" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Filter Wise Total Patient Count">
                                <div class="card dashboard-cards appointments">
                                    <div class="card-body">
                                        <p class="mb-0">{{ __('dashboard.total_users') }}</p>
                                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                            <h2 class="mb-0" id="total_user">{{ $data['total_user'] }}</h2>
                                            <img src="{{ asset('img/dashboard/users.png') }}" alt="image">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 {{ multivendor() == 1 ? 'col-lg-4' : 'col-lg-6' }}">
                            <div class="card dashboard-cards appointments" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Filter Wise Total Revenue Generated">
                                <div class="card-body">
                                    <p class="mb-0">{{ __('dashboard.total_revenue') }}</p>
                                    <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                        <h2 class="mb-0" id="total_revenue_amount">
                                            {{ Currency::format($data['total_revenue']) }}</h2>
                                        <img src="{{ asset('img/dashboard/revenue.png') }}" alt="image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-block card-height">
                        <div class="card-header mb-5">
                            <h4 class="card-title mb-0">{{ __('dashboard.visited_patient') }}</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex align-items-end h-100">
                                <div class="d-flex align-items-end flex-grow-1">
                                    <div class="text-center flex-grow-1">
                                        <img src="{{ asset('img/dashboard/girl-and-boy-age-0-20.png') }}"
                                            class="img-fluid" alt="age-image-0-20">
                                        <h6 class="mb-0 mt-2 fw-normal">@lang('messages.0_25_age')</h6>
                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="mb-0" id="child_patient_count">
                                                {{ $data['child_patient_count'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="text-center flex-grow-1">
                                        <img src="{{ asset('img/dashboard/girl-and-boy-age-20-40.png') }}"
                                            class="img-fluid" alt="age-image-0-20">
                                        <h6 class="mb-0 mt-2 fw-normal">@lang('messages.26_50_age')</h6>
                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="mb-0" id="adult_patient_count">
                                                {{ $data['adult_patient_count'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="text-center flex-grow-1">
                                        <img src="{{ asset('img/dashboard/girl-and-boy-age-40-80.png') }}"
                                            class="img-fluid" alt="age-image-0-20">
                                        <h6 class="mb-0 mt-2 fw-normal">@lang('messages.50+_age')</h6>
                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="mb-0" id="old_patient_count">{{ $data['old_patient_count'] }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-8 col-lg-7 col-md-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <h4 class="card-title mb-0">{{ __('dashboard.lbl_tot_revenue') }}</h4>
                    <div id="date_range" class="dropdown d-none">
                        {{-- <button class="dropdown-toggle btn text-body bg-body border" id="dropdownTotalRevenue" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fw-500">Month</span>
                        <svg width="8" class="ms-1 transform-up" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6 5.08579L10.2929 0.792893C10.6834 0.402369 11.3166 0.402369 11.7071 0.792893C12.0976 1.18342 12.0976 1.81658 11.7071 2.20711L6.70711 7.20711C6.31658 7.59763 5.68342 7.59763 5.29289 7.20711L0.292893 2.20711C-0.0976311 1.81658 -0.0976311 1.18342 0.292893 0.792893C0.683418 0.402369 1.31658 0.402369 1.70711 0.792893L6 5.08579Z" fill="currentColor"></path>
                        </svg>
                    </button> --}}
                        <a href="#" class="dropdown-toggle btn text-body bg-body border total_revenue"
                            id="dropdownTotalRevenue" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('dashboard.year') }}
                            <svg width="8" class="ms-1 transform-up" viewBox="0 0 12 8" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6 5.08579L10.2929 0.792893C10.6834 0.402369 11.3166 0.402369 11.7071 0.792893C12.0976 1.18342 12.0976 1.81658 11.7071 2.20711L6.70711 7.20711C6.31658 7.59763 5.68342 7.59763 5.29289 7.20711L0.292893 2.20711C-0.0976311 1.81658 -0.0976311 1.18342 0.292893 0.792893C0.683418 0.402369 1.31658 0.402369 1.70711 0.792893L6 5.08579Z"
                                    fill="currentColor"></path>
                            </svg>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-soft-primary sub-dropdown"
                            aria-labelledby="dropdownTotalRevenue">
                            <li><a class="revenue-dropdown-item dropdown-item"
                                    data-type="Year">{{ __('dashboard.year') }}</a></li>
                            <li><a class="revenue-dropdown-item dropdown-item"
                                    data-type="Month">{{ __('dashboard.month') }}</a></li>
                            <li><a class="revenue-dropdown-item dropdown-item"
                                    data-type="Week">{{ __('dashboard.week') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div id="revenue_loader" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="total-revenue"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-lg-5 col-md-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <h4 class="card-title mb-0">{{ __('dashboard.lbl_upcoming_appointment') }} </h4>
                    @if (count($data['upcomming_appointments']) >= 5)
                        <a id="appointment_view_all_link" href="{{ route('backend.appointments.index') }}"
                            class="text-secondary d-none">{{ __('dashboard.view_all') }}</a>
                    @endif
                </div>
                <div class="card-body pt-0">
                    <div class="upcoming-appointments">
                        <ul id="upcoming-appointments" class="list-inline p-0 m-0">
                            @forelse ($data['upcomming_appointments'] as $upcomming_appointments)
                                <li class="mb-3">
                                    <div class="bg-body p-3 rounded-3">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                <p class="mb-0 text-primary">
                                                    {{ \Carbon\Carbon::parse($upcomming_appointments->appointment_date)->timezone($timeZone)->format($data['dateformate']) }}
                                                </p>
                                                <span class="mb-0 text-primary">
                                                    {{ $upcomming_appointments->appointment_time
                                                        ? \Carbon\Carbon::parse($upcomming_appointments->appointment_time)->timezone($timeZone)->format($data['timeformate'])
                                                        : '--' }}
                                                </span>
                                            </div>
                                            <div class="col-8 ps-0">
                                                <div class="border-start border-light ps-4 ms-sm-4">
                                                    <h6 class="mb-0">
                                                        {{ optional($upcomming_appointments->user)->full_name }}</h6>
                                                    <p>{{ __('clinic.lbl_clinic_name') }}:
                                                        {{ optional($upcomming_appointments->cliniccenter)->name }}</p>
                                                    <span>{{ optional($upcomming_appointments->clinicservice)->name }} By
                                                        <b>{{ optional($upcomming_appointments->doctor)->full_name }}</b></span>
                                                </div>
                                            </div>
                                            <div class="col-1 px-0">
                                                <a href="{{ route('backend.appointments.clinicAppointmentDetail', ['id' => $upcomming_appointments->id]) }}"
                                                    class="text-body">
                                                    <i class="ph ph-caret-right transform-icon"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="d-flex align-items-center bg-body p-3 rounded-3">
                                <div class="flex-grow-1 flex-shrink-0">
                                    <p class="mb-0 text-primary flex-shrink-0 f-none">{{ date($data['dateformate'], strtotime($upcomming_appointments->start_date_time)) }}</p>
                                    <span class="mb-0 text-primary flex-shrink-0 f-none">{{ date($data['timeformate'], strtotime($upcomming_appointments->start_date_time)) }}</span>
                                </div>
                                <div class="border-start border-light ps-4 ms-4 flex-grow-1">
                                    <h6 class="mb-0">{{ optional($upcomming_appointments->user)->full_name }}</h6>
                                    <p>{{__('clinic.lbl_clinic_name')}}: {{optional($upcomming_appointments->cliniccenter)->name}}</p>
                                    <span>{{optional($upcomming_appointments->clinicservice)->name}} By <b>{{optional($upcomming_appointments->doctor)->full_name}}</b></span>
                                </div>
                                <div>
                                    <a href="{{ route('backend.appointments.clinicAppointmentDetail', ['id' => $upcomming_appointments->id]) }}" class="text-body">
                                        <i class="ph ph-caret-right transform-icon"></i>
                                    </a>
                                </div>

                            </div> --}}
                                </li>
                            @empty
                                <li class="text-center">{{ __('dashboard.no_data_available') }}</li>
                            @endforelse

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if (multiVendor() == '1' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
            <div class="col-xxl-4 col-lg-5 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                        <h4 class="card-title mb-0">{{ __('dashboard.register_vendor') }} </h4>
                        @if (count($data['register_vendor']) >= 4)
                            <a id="vendor_view_all_link" href="{{ route('backend.multivendors.index') }}"
                                class="text-secondary d-none cursor-pointer" contenteditable="false">{{ __('dashboard.view_all') }}</a>
                        @endif
                    </div>
                    <div class="card-body pt-0">
                        <ul id="register_vendors_list" class="list-inline m-0 p-0 register-vendors-list">

                            @forelse ($data['register_vendor'] as $register_vendors)
                                <li class="mb-3">
                                    <div
                                        class="bg-body d-flex align-items-center justify-content-between p-3 rounded-3 gap-3 flex-sm-row flex-column">
                                        <div
                                            class="d-flex align-items-center gap-3 flex-sm-row flex-lg-nowrap flex-md-wrap flex-column flex-nowrap">
                                            <div class="image flex-shrink-0">
                                                <img src="{{ $register_vendors->profile_image ?? default_user_avatar() }}"
                                                    class="avatar-50 rounded-circle" alt="user-image">
                                            </div>
                                            <div class="text-sm-start text-center">
                                                <h6 class="mb-0">{{ $register_vendors->full_name }}</h6>
                                                <small
                                                    class="m-0">{{ \Carbon\Carbon::parse($register_vendors->created_at)->timezone($timeZone)->format($data['dateformate']) }}
                                                    At
                                                    {{ \Carbon\Carbon::parse($register_vendors->created_at)->timezone($timeZone)->format($data['timeformate']) }}</small>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-success-subtle p-2">
                                                {{ $register_vendors->email_verified_at !== null ? __('messages.verified') : __('messages.unverified') }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center">{{ __('dashboard.no_data_available') }}</li>
                            @endforelse

                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (multiVendor() == '0')
            <div class="col-xxl-12 col-lg-12 col-md-12">
            @else
                <div class="col-xxl-8 col-lg-7 col-md-6">
        @endif
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <h4 class="card-title mb-0">{{ __('dashboard.payment_history') }}</h4>
                @if (count($data['payment_history']) >= 5)
                    <a id="payment_view_all_link" href="{{ route('backend.appointments.index') }}"
                        class="text-secondary d-none cursor-pointer" contenteditable="false">{{ __('dashboard.view_all') }}</a>
                @endif
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive rounded bg-body">
                    <table class="table border m-0">
                        <thead>
                            <tr class="bg-body">
                                <th scope="col" class="heading-color">{{ __('sidebar.patient') }}</th>
                                <th scope="col" class="heading-color">{{ __('messages.date_time') }}</th>
                                <th scope="col" class="heading-color">{{ __('clinic.singular_title') }}</th>
                                <th scope="col" class="heading-color">{{ __('messages.service') }}</th>
                                <th scope="col" class="heading-color">{{ __('appointment.price') }}</th>
                                <th scope="col" class="heading-color">{{ __('earning.lbl_payment_method') }}</th>
                                <th scope="col" class="heading-color">{{ __('appointment.lbl_payment_status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="payment_history_table_body">
                            @forelse ($data['payment_history'] as $paymenthistory)
                                @php
                                    $transaction = $paymenthistory->appointmenttransaction;

                                    if ($transaction) {
                                        if ($transaction->payment_status == 1) {
                                            $payment_status = __('dashboard.paid');
                                        } elseif (
                                            $transaction->payment_status == 0 &&
                                            $transaction->advance_payment_status == 1
                                        ) {
                                            $payment_status = __('dashboard.advance_paid');
                                        } else {
                                            $payment_status = __('dashboard.pending');
                                        }
                                    }
                                @endphp

                                @if ($transaction)
                                    <tr>
                                        <td>{{ optional($paymenthistory->user)->full_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($paymenthistory->appointment_date)->timezone($timeZone)->format($data['dateformate']) }}
                                            At
                                            {{ \Carbon\Carbon::parse($paymenthistory->appointment_time)->timezone($timeZone)->format($data['timeformate']) }}
                                        </td>
                                        <td>{{ optional($paymenthistory->cliniccenter)->name }}</td>
                                        <td>{{ optional($paymenthistory->clinicservice)->name }}</td>
                                        <td>{{ Currency::format($transaction->total_amount) }}</td>
                                        <td>{{ ucfirst($transaction->transaction_type) }}</td>
                                        <td>{{ $payment_status }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td class="text-center" colspan="6">
                                        {{ __('messages.payment_history_notavailable') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.40.0/apexcharts.min.js"
        integrity="sha512-Kr1p/vGF2i84dZQTkoYZ2do8xHRaiqIa7ysnDugwoOcG0SbIx98erNekP/qms/hBDiBxj336//77d0dv53Jmew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@php
$currency = GetCurrencySymbol();
@endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('revenuedateRangeInput');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('dateRangeForm');

            function isValidDateRange(dateRange) {
                if (!dateRange || dateRange.trim() === '') {
                    return false;
                }

                const datePattern = /^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/;
                if (!datePattern.test(dateRange.trim())) {
                    return false;
                }

                const dates = dateRange.split(' to ');
                const startDate = new Date(dates[0]);
                const endDate = new Date(dates[1]);

                // Check if dates are valid
                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    return false;
                }

                // Check if start date is before or equal to end date
                return startDate <= endDate;
            }

            function toggleSubmitButton() {
                if (isValidDateRange(dateInput.value)) {
                    submitBtn.removeAttribute('disabled');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                }
            }

            dateInput.addEventListener('input', toggleSubmitButton);

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                if (isValidDateRange(dateInput.value)) {
                    // Show loading state
                    submitBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Loading...';
                    submitBtn.disabled = true;

                    const encodedDateRange = encodeURIComponent(dateInput.value);
                    const formAction = `{{ route('backend.daterange', ['daterange' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', encodedDateRange);

                    // Redirect to the date range page
                    window.location.href = formAction;
                } else {
                    alert('Please select a valid date range. Make sure the start date is before or equal to the end date.');
                }
            });

            toggleSubmitButton();
        });
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            Scrollbar.init(document.querySelector('.upcoming-appointments'), {
                continuousScrolling: false,
                alwaysShowTracks: false
            })
            const range_flatpicker = document.querySelectorAll('.dashboard-date-range');
            Array.from(range_flatpicker, (elem) => {
                if (typeof flatpickr !== typeof undefined) {
                    flatpickr(elem, {
                        mode: "range",
                    })
                }
            })

        })

        revanue_chart('Year')


        var dateRangeValue = $('#revenuedateRangeInput').val();



        if (dateRangeValue != '') {
            var dates = dateRangeValue.split(" to ");
            var startDate = dates[0];
            var endDate = dates[1];

            if (startDate != null && endDate != null) {
                revanue_chart('Free', startDate, endDate);
                $('#refreshRevenuechart').removeClass('d-none');
                $('#date_range').addClass('d-none');
            }
        } else {
            revanue_chart('Year');
            $('#refreshRevenuechart').addClass('d-none');
            $('#date_range').removeClass('d-none');
        }

        $('#refreshRevenuechart').on('click', function() {
            $('#revenuedateRangeInput').val('');
            revanue_chart('Year');
            $('#date_range').removeClass('d-none');
        });



        var chart = null;
        let revenueInstance;
        const CURRENCY_CODE = "{{ $currency['code'] }}";
        const CURRENCY_SYMBOL = "{{ $currency['symbol'] }}";
        function revanue_chart(type, startDate, endDate) {
            var Base_url = "{{ url('/') }}";
            var url = Base_url + "/app/get_revnue_chart_data/" + type;

            $("#revenue_loader").removeClass('d-none');
            $("#total-revenue").hide();


            $.ajax({
                url: url,
                method: "GET",
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    $("#revenue_loader").addClass('d-none');
                    $("#total-revenue").show();
                    $(".total_revenue").text(type);
                    if (document.querySelectorAll('#total-revenue').length) {
                        const variableColors = IQUtils.getVariableColor();
                        const colors = [variableColors.primary, variableColors.info];
                        const monthlyTotals = response.data.chartData;
                        const category = response.data.category;
                        const formatCurrency = function(value) {
                            try {
                                const formatted = new Intl.NumberFormat('{{ app()->getLocale() }}', {
                                    style: 'decimal',
                                    minimumFractionDigits: 2
                                }).format(value);

                                // ✅ show only symbol if exists, else code
                                if (CURRENCY_SYMBOL && CURRENCY_SYMBOL.trim() !== "") {
                                    return CURRENCY_SYMBOL + formatted;
                                } else {
                                    return CURRENCY_CODE + ' ' + formatted;
                                }
                            } catch (e) {
                                return (CURRENCY_SYMBOL || CURRENCY_CODE) + ' ' + value;
                            }
                        };
                        const options = {
                            series: [{
                                name: "{{ __('messages.total_revenue') }}",
                                data: monthlyTotals
                            }],
                            chart: {
                                fontFamily: '"Inter", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                                height: 300,
                                type: 'area',
                                toolbar: {
                                    show: false
                                },
                                sparkline: {
                                    enabled: false,
                                },
                            },
                            colors: colors,
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 3,
                            },
                            yaxis: {
                                show: true,
                                labels: {
                                    show: true,
                                    style: {
                                        colors: "#8A92A6",
                                    },
                                    offsetX: -15,
                                    formatter: formatCurrency,
                                },
                            },
                            legend: {
                                show: false,
                            },
                            xaxis: {
                                labels: {
                                    minHeight: 22,
                                    maxHeight: 22,
                                    show: true,
                                },
                                lines: {
                                    show: false
                                },
                                categories: category
                            },
                            grid: {
                                show: true,
                                borderColor: 'var(--bs-body-bg)',
                                strokeDashArray: 0,
                                position: 'back',
                                xaxis: {
                                    lines: {
                                        show: true
                                    }
                                },
                                yaxis: {
                                    lines: {
                                        show: true
                                    }
                                },
                            },
                            fill: {
                                type: 'solid',
                                opacity: 0
                            },
                            tooltip: {
                                enabled: true,
                                y: {
                                    formatter: formatCurrency,
                                }
                            },
                        };

                        if (revenueInstance) {
                            revenueInstance.updateOptions(options);
                        } else {
                            revenueInstance = new ApexCharts(document.querySelector("#total-revenue"), options);
                            revenueInstance.render();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $("#revenue_loader").addClass('d-none');
                    $("#total-revenue").show();
                    console.error('Error loading revenue chart:', error);
                    alert('Error loading revenue chart data. Please try again.');
                }
            })
        };

        $(document).on('click', '.revenue-dropdown-item', function() {
            var type = $(this).data('type');
            $('#revenuedateRangeInput').val('');
            revanue_chart(type);
        });
    </script>
@endpush
