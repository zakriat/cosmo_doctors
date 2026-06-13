@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->user()->can('edit_'.$module_title) || auth()->user()->can('delete_'.$module_title))
            <x-backend.quick-action url="{{ route('backend.services.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('edit_Appointment')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        @endcan
                        @can('delete_Appointment')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endcan
                    </select>
                </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" id="status" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1" selected>{{ __('messages.active') }}</option>
                        <option value="0">{{ __('messages.inactive') }}</option>
                    </select>
                </div>
            </x-backend.quick-action>
            @endif
            <div>
                <button type="button" class="btn btn-primary" data-modal="export">
                <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>
                {{-- <button type="button" class="btn btn-secondary" data-modal="import">--}}
                {{-- <i class="fa-solid fa-upload"></i> Import--}}
                {{-- </button>--}}
            </div>
        </div>
        <x-slot name="toolbar">

            <div>
                <div class="datatable-filter">
                    <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width: 100%">
                        <option value="">{{__('messages.all')}}</option>
                        <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                        <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
            </div>
            <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>
        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">
    <clinic-appointment-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}" edit-title="{{ __('messages.edit') }} {{ __($module_title) }}" >
    </clinic-appointment-offcanvas>
    <patient-list-details-offcanvas>
    </patient-list-details-offcanvas>
</div>

<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    <div class="form-group datatable-filter">
        <label class="form-label" for="column_category">{{ __('service.lbl_category') }}</label>
        <select name="column_category" id="column_category" class="select2 form-select" data-filter="select">
            <option value="">Doctor</option>
            @foreach ($doctor as $doctor)
                <option value="{{ $doctor->doctor_id }}">{{ $doctor->user->full_name }}</option>
            @endforeach
        </select>
    </div>
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter>
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/appointment/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const columns = [{
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        {
            data: 'id',
            name: 'id',
            title: "{{ __('appointment.lbl_id') }}",
            searchable: false,
            orderable: true,

        },
        {
            data: 'user_id',
            name: 'user_id',
            title: "{{ __('appointment.lbl_patient_name') }}"
        },
        {
            data: 'start_date',
            name: 'start_date',
            title: "{{ __('appointment.lbl_date') }}",
        },
        {
            data: 'start_time',
            name: 'start_time',
            title: "{{ __('appointment.lbl_time') }}",
        },
        {
            data: 'services',
            name: 'services',
            title: "{{ __('appointment.lbl_services') }}",
            orderable: true,
            searchable: true,
            width: '10%'
        },
        {
            data: 'employee_id',
            name: 'employee_id',
            title: "{{ __('appointment.lbl_doctor') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'clinic_name',
            name: 'clinic_name',
            title: "{{ __('clinic name') }}",
            orderable: true,
            searchable: true,
        },
    ]
    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('appointment.lbl_action') }}",
        width: '5%'
    }]
    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        // Initialize Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                width: '100%'
            });
        }

        initDatatable({
            url: '{{ route("backend.$module_name.index_patientdata") }}',
            finalColumns,
            advanceFilter: () => {
                return {
                    category_id: $('#column_category').val()
                }
            }
        });
        $('#reset-filter').on('click', function(e) {
    $('#column_category').val('');
    window.renderedDataTable.ajax.reload(null, false);
});
});
</script>
@endpush
