@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        <x-backend.section-header>
            @if($patient_encounter->status == 1)
                <x-backend.quick-action url='{{ route("backend.bodychart.bodychart_bulk_action") }}'>
                    <div class="">
                        <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            <option value="delete">{{ __('messages.delete') }}</option>
                        </select>
                    </div>
                   
                </x-backend.quick-action>
          

            <x-slot name="toolbar">
                <!-- <a href="{{ route("backend.patient-record", ['id' => $appointment_id]) }}"" class=" btn btn-primary" data-type="ajax" data-bs-toggle="tooltip">Back</a> -->

                <div class="d-flex justify-content-end">
                    <a href="{{route("backend.bodychart.bodychart_form", $encounter_id)}}" class="btn btn-primary" data-type="ajax" data-bs-toggle="tooltip"><i class="fas fa-plus-circle"></i> Add BodyChart</a>

                </div>
            </x-slot>
            @endif
        </x-backend.section-header>


        <table id="datatable" class="table table-responsive">
        </table>
    </div>
</div>
{{-- <div data-render="app">

    <body-chart-offcanvas appointment_id="{{$appointment_id}}" patient_id="{{$patient_id}}"></body-chart-offcanvas>
</div> --}}



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
    const columns = [
        @if($patient_encounter->status == 1)
        {
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        @endif
        {
            data: 'id',
            name: 'id',
            title: "{{ __('appointment.lbl_id') }}",
            searchable: false,
            orderable: true,
        },
        {
            data: 'name',
            name: 'name',
            title: "{{ __('appointment.lbl_bodychart_name') }}"
        },
        {
            data: 'description',
            name: 'description',
            title: "{{ __('appointment.lbl_bodychart_description') }}"
        },
        {
            data: 'patient_id',
            name: 'patient_id',
            title: "{{ __('appointment.lbl_patient_name') }}",
        },
        {
            data: 'doctor_name',
            name: 'doctor_name',
            title: "{{ __('appointment.lbl_doctor') }}",
            orderable: true,
            searchable: true,
            width: '10%'
        },

    ]





    const actionColumn = [
        @if($patient_encounter->status == 1)
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('appointment.lbl_action') }}",
            width: '5%'
        }
        @endif
    ]



    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.bodychart.bodychart_datatable",$encounter_id) }}',
            finalColumns,
            advanceFilter: () => {
                return {}
            }
        });
    })

    function bodychartOpen(appoinrment_id) {
        console.log(appoinrment_id);

    }
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

    $(document).on('update_quick_action', function() {
        // resetActionButtons()
    })
</script>
@endpush