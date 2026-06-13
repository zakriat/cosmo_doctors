@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
{{ $module_title }}
@endsection



@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">{{ __('dashboard.lbl_title_commission_revenue') }}</h3>
            <div class="d-flex  align-items-center">
                <form class="d-flex align-items-center gap-2">
                    <div class="card-header">
                        <x-backend.section-header>
                            <x-slot name="toolbar">
                                <div class="input-group gap-2 flex-nowrap">
                                    <input type="text" name="appointment_date" id="appointment_date" placeholder="{{ __('setting_sidebar.select_date') }}" class="booking-date-range form-control ps-3" readonly />
                                    <button id="reset" class="btn bg-primary rounded" data-bs-toggle="tooltip" title="Reset">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21.4799 12.2424C21.7557 12.2326 21.9886 12.4482 21.9852 12.7241C21.9595 14.8075 21.2975 16.8392 20.0799 18.5506C18.7652 20.3986 16.8748 21.7718 14.6964 22.4612C12.518 23.1505 10.1711 23.1183 8.01299 22.3694C5.85488 21.6205 4.00382 20.196 2.74167 18.3126C1.47952 16.4293 0.875433 14.1905 1.02139 11.937C1.16734 9.68346 2.05534 7.53876 3.55018 5.82945C5.04501 4.12014 7.06478 2.93987 9.30193 2.46835C11.5391 1.99683 13.8711 2.2599 15.9428 3.2175L16.7558 1.91838C16.9822 1.55679 17.5282 1.62643 17.6565 2.03324L18.8635 5.85986C18.945 6.11851 18.8055 6.39505 18.549 6.48314L14.6564 7.82007C14.2314 7.96603 13.8445 7.52091 14.0483 7.12042L14.6828 5.87345C13.1977 5.18699 11.526 4.9984 9.92231 5.33642C8.31859 5.67443 6.8707 6.52052 5.79911 7.74586C4.72753 8.97119 4.09095 10.5086 3.98633 12.1241C3.8817 13.7395 4.31474 15.3445 5.21953 16.6945C6.12431 18.0446 7.45126 19.0658 8.99832 19.6027C10.5454 20.1395 12.2278 20.1626 13.7894 19.6684C15.351 19.1743 16.7062 18.1899 17.6486 16.8652C18.4937 15.6773 18.9654 14.2742 19.0113 12.8307C19.0201 12.5545 19.2341 12.3223 19.5103 12.3125L21.4799 12.2424Z" fill="#ffffff"></path>
                                            <path d="M20.0941 18.5594C21.3117 16.848 21.9736 14.8163 21.9993 12.7329C22.0027 12.4569 21.7699 12.2413 21.4941 12.2512L19.5244 12.3213C19.2482 12.3311 19.0342 12.5633 19.0254 12.8395C18.9796 14.283 18.5078 15.6861 17.6628 16.8739C16.7203 18.1986 15.3651 19.183 13.8035 19.6772C12.2419 20.1714 10.5595 20.1483 9.01246 19.6114C7.4654 19.0746 6.13845 18.0534 5.23367 16.7033C4.66562 15.8557 4.28352 14.9076 4.10367 13.9196C4.00935 18.0934 6.49194 21.37 10.008 22.6416C10.697 22.8908 11.4336 22.9852 12.1652 22.9465C13.075 22.8983 13.8508 22.742 14.7105 22.4699C16.8889 21.7805 18.7794 20.4073 20.0941 18.5594Z" fill="#ffffff"></path>
                                        </svg>
                                    </button>
                                </div>
                            </x-slot>
                        </x-backend.section-header>
                    </div>
                    <!-- <button type="submit" name="action" value="filter" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_filter') }}</button> -->
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="card dashboard-cards appointments">
                    <div class="card-body">
                        <p class="mb-0">{{__('appointment.total_number_appointment')}} </p>
                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                            <h2 class="mb-0">{{ $totalAppointments }}</h2>
                            <img src="{{ asset('img/dashboard/clender.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card dashboard-cards appointments">
                    <div class="card-body">
                        <p class="mb-0">{{__('appointment.doctor_commission')}}</p>
                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                            <h2 class="mb-0">{{ \Currency::format($totalDoctorCommission) }}</h2>
                            <img src="{{ asset('img/dashboard/services.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card dashboard-cards appointments">
                    <div class="card-body">
                        <p class="mb-0">{{__('appointment.admin_commission')}}</p>
                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                            <h2 class="mb-0">{{ \Currency::format($totalAdminCommission) }}</h2>
                            <img src="{{ asset('img/dashboard/active-vendor.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card dashboard-cards appointments">
                    <div class="card-body">
                        <p class="mb-0">{{__('appointment.total_clinic_revenue')}}</p>
                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                            <h2 class="mb-0">{{ \Currency::format($totalClinicRevenue) }}</h2>
                            <img src="{{ asset('img/dashboard/product-sale.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>Advanced Filter</h4>
    </x-slot>
</x-backend.advance-filter>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const range_flatpicker = document.querySelectorAll('.booking-date-range')
    Array.from(range_flatpicker, (elem) => {
        if (typeof flatpickr !== typeof undefined) {
            flatpickr(elem, {
                mode: "range",
                dateFormat: "d-m-Y",
            })
        }
    })

    $('#reset').on('click', function(e) {
        $('#appointment_date').val('');
        window.renderedDataTable.ajax.reload(null, false);
    });
    const columns = [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            title: "{{ __('report.lbl_no') }}",
            orderable: false,
            searchable: false,
            width: '5%',
        },
        {
            data: 'user_name',
            name: 'user_name',
            title: "{{ __('report.lbl_patient_name') }}",
            orderable: false,
        },
        {
            data: 'start_date_time',
            name: 'start_date_time',
            title: "{{ __('report.lbl_date') }}",
            width: '10%'
        },
        {
            data: 'service_name',
            name: 'service_name',
            title: "{{ __('report.lbl_service') }}",
        },
        {
            data: 'employee_name',
            name: 'employee_name',
            title: "{{ __('report.lbl_dr_name') }}",
        },
        {
            data: 'clinic_name',
            name: 'clinic_name',
            title: "{{ __('report.lbl_clinic_name') }}",
        },
        {
            data: 'price',
            name: 'price',
            title: "{{ __('report.lbl_price') }}",
        },
        {
            data: 'doctor_pay',
            name: 'doctor_pay',
            title: "{{ __('report.lbl_dr_pay') }}",
        },
        {
            data: 'admin_pay',
            name: 'admin_pay',
            title: "{{ __('report.lbl_admin_pay') }}",
        },
        {
            data: 'clinic_pay',
            name: 'clinic_pay',
            title: "{{ __('report.lbl_clinic_income') }}",
        },


        {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('service.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
        // {
        //     data: 'action',
        //     name: 'action',
        //     title: "{{ __('report.lbl_action') }}",
        //     orderable: false,
        //     searchable: false,
        //     visiable:false,
        // }

    ]

    let finalColumns = [
        ...columns
    ]

    $('#appointment_date').on('change', function() {
        window.renderedDataTable.ajax.reload(null, false)
    })

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route('backend.reports.commission-revenue.index_data') }}',
            finalColumns,
            orderColumn: [[ 10, 'desc' ]],
            advanceFilter: () => {
                return {
                    appointment_date: $('#appointment_date').val(),

                }
            }
        })
    })
</script>
@endpush