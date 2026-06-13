@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->user()->can('edit_encounter') || auth()->user()->can('delete_encounter'))
            <x-backend.quick-action url="{{ route('backend.encounter.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('edit_encounter')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        @endcan
                        @can('delete_encounter')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endcan
                    </select>
                </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" id="status" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1">{{ __('messages.open') }}</option>
                        <option value="0">{{ __('messages.close') }}</option>
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
                            {{ __('appointment.close') }}
                        </option>
                        <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                            {{ __('messages.open') }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-describedby="addon-wrapping">
            </div>
            <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>

            @hasPermission('add_encounter')
            <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
            {{ __('messages.new') }}</x-buttons.offcanvas>
            @endhasPermission

        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">

    <patient-encounter-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}" edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
    </patient-encounter-offcanvas>

    <patient-encounter-dashboard  create-title="{{ __('appointment.encouter_dashboard') }}" >
    </patient-encounter-dashboard>

    <appointment-customform>
    </appointment-customform>

</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    <div class="form-group datatable-filter">
    <label for="patient_name">{{__('clinic.patient')}}</label>
    <div class="col-12">
        <select name="patient_name" id="patient_name" class="select2 form-select" data-filter="select"
            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'customers']) }}"
            data-ajax--cache="true" placeholder="Select a patient">
            <option value="" disabled selected>{{ __('clinic.lbl_select_patient') }}</option>
        </select>
    </div>
</div>

<div class="form-group datatable-filter">
    <label for="form-label"> {{ __('clinic.lbl_clinic') }}</label>
    <div class="col-12">
        <select id="clinic_name" name="clinic_name" data-filter="select"
            class="select2 form-select"
            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'clinic_name']) }}"
            data-ajax--cache="true" placeholder="Select a clinic">
            <option value="" disabled selected>{{ __('clinic.lbl_select_clinic') }}</option>
        </select>
    </div>
</div>

<div class="form-group datatable-filter">
    <label for="form-label"> {{ __('clinic.doctors') }}</label>
    <div class="col-12">
        <select id="doctor_name" name="doctor_name" data-filter="select"
            class="select2 form-select"
            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'doctors']) }}"
            data-ajax--cache="true" placeholder="Select a doctor">
            <option value="" disabled selected>{{ __('clinic.lbl_select_doctor') }}</option>
        </select>
    </div>
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
            data: 'encounter_id',
            name: 'encounter_id',
            title: "{{ __('sidebar.id') }}"
        },
        {
            data: 'appointment_id',
            name: 'appointment_id',
            title: "{{ __('sidebar.appoitment_id') }}",
            className: 'text-center'
        },

        {
            data: 'user_id',
            name: 'user_id',
            title: "{{ __('sidebar.patient') }}"
        },
        {
            data: 'encounter_date',
            name: 'encounter_date',
            title: "{{ __('appointment.date_time') }}",
        },

        @unless(auth()->user()->hasRole('receptionist'))
        {
            data: 'clinic_id',
            name: 'clinic_id',
            title: "{{ __('appointment.lbl_clinic') }}",
            orderable: true,
            searchable: true,
        },
        @endunless
        @unless(auth()->user()->hasRole('doctor'))
        {
            data: 'doctor_id',
            name: 'doctor_id',
            title: "{{ __('appointment.lbl_doctor') }}",
            orderable: true,
            searchable: true,
        },
        @endunless
        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('appointment.lbl_update_at') }}",
            orderable: true,
            visible: false,
        },
        {
            data: 'status',
            name: 'status',
            orderable: true,
            searchable: true,
            title: "{{ __('appointment.lbl_status') }}",
            width: '5%'
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
        console.log($('#user_id').val());
        initDatatable({
            url: '{{ route("backend.$module_name.index_data") }}',
            finalColumns,
            orderColumn:  @if(auth()->user()->hasRole('doctor'))
                            [[4, "desc"]]
                        @else
                            [[5, "desc"]]
                        @endif,
            advanceFilter: () => {
                return {
                    patient_name: $('#patient_name').val(),
                    clinic_name: $('#clinic_name').val(),
                    doctor_name: $('#doctor_name').val(),
                }
                },
            drawCallback: () => {
                // Reinitialize select2 for dynamically created elements in DataTable
                $('.change-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            minimumResultsForSearch: -1
                        });
                    }
                });
            }
        });
        $('#reset-filter').on('click', function(e) {
            $('#patient_name').val('');
            $('#clinic_name').val('');
            $('#doctor_name').val('');
            window.renderedDataTable.ajax.reload(null, false);
        });
    })


    function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });
        $(document).ready(function() {
    $('.select2').select2({
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true,
        tags: true

    });
});

function dispatchCustomEvent(button) {
    const event = new CustomEvent('custom_form_assign', {
        detail: {
        appointment_type: button.getAttribute('data-appointment-type'),
        appointment_id: button.getAttribute('data-appointment-id'),
        form_id: button.getAttribute('data-form-id')
        }
    });

    document.dispatchEvent(event);

    const offcanvasSelector = button.getAttribute('data-assign-target');
    const offcanvasElement = document.querySelector(offcanvasSelector);
    if(offcanvasElement){
        const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        offcanvas.show();
    }
}

</script>
@endpush
