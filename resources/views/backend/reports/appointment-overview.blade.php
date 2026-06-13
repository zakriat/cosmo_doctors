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
            <h3 class="mb-0">{{ __('dashboard.lbl_title_appointment_overview') }}</h3>
            <div class="d-flex  align-items-center">
                <form action="" class="d-flex align-items-center gap-2">
                    <div class="form-group my-0 ms-3">
                        <input type="text"  name="appointment_date" id="appointment_date" value="" class="booking-date-range form-control" placeholder="{{ __('setting_sidebar.select_date') }}" readonly="readonly">  
                    </div>
                    <button id="reset" class="btn bg-primary rounded" data-bs-toggle="tooltip" title="Reset"> <i class="ph ph-circle-notch"></i> </button>
                    <!-- <button type="submit" name="action" value="filter" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_filter') }}</button> -->
                </form>
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
            data: 'id',
            name: 'id',
            title: "{{ __('report.lbl_inv_no') }}",
            orderable: false,
            searchable: false
        },
        {
            data: 'user_id',
            name: 'user_id',
            title: "{{ __('sidebar.patient') }}",
            orderable: false,
        },
        {
            data: 'clinic_id',
            name: 'clinic_id',
            title: "{{ __('clinic.singular_title') }}",
        },
        {
            data: 'doctor_id',
            name: 'doctor_id',
            title: "{{ __('appointment.lbl_doctor') }}",
        },
        {
            data: 'status',
            name: 'status',
            title: "{{ __('appointment.singular_title') }} {{ __('appointment.lbl_status') }}",
        },
        {
            data: 'payment_status',
            name: 'payment_status',
            title: "{{ __('report.lbl_payment_status') }}",
            orderable: false,
        },
        {
            data: 'service_amount',
            name: 'service_amount',
            title: "{{ __('report.lbl_price') }}",
        },
        {
            data: 'total_amount',
            name: 'total_amount',
            title: "{{ __('clinic.total') }}",
        },
        {
            data: 'start_date_time',
            name: 'start_date_time',
            title: "{{ __('report.lbl_date') }}",
            width: '10%'
        },
        {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('service.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
        
    ]

    let finalColumns = [
        ...columns
    ]

    $('#appointment_date').on('change', function() {
        window.renderedDataTable.ajax.reload(null, false)
    })

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route('backend.reports.appointment-overview.index_data') }}',
            finalColumns,
            orderColumn: [[ 9, 'desc' ]],
            advanceFilter: () => {
                return {
                    appointment_date: $('#appointment_date').val(),

                }
            }
        })
    })
</script>
@endpush