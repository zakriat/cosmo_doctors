@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                <x-backend.quick-action url="{{ route('backend.clinics.bulk_action') }}">
                    <div class="">
                        <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                            style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            <option value="change-status">{{ __('messages.status') }}</option>
                            <option value="delete">{{ __('messages.delete') }}</option>
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
            </div>
            <x-slot name="toolbar">

                <div>
                    <div class="datatable-filter">
                        <select name="column_status" id="column_status" class="select2 form-select"
                            data-filter="select" style="width: 100%">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}</option>
                            <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                {{ __('messages.active') }}</option>
                        </select>
                    </div>
                </div>

                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>

                    <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
                    {{ __('messages.create') }} {{ __($module_title) }} </x-buttons.offcanvas>

            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>
</div>
<div data-render="app">
    <clinic-appointment-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
        edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
    </clinic-appointment-offcanvas>
</div>
@endsection


@push ('after-styles')
{{-- <link rel="stylesheet" href="{{ mix('modules/world/style.css') }}"> --}}
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/clinic/script.js') }}"></script>
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

    //  {
    //     data: 'image',
    //     name: 'image',
    //     title: "{{ __('Clinic Image') }}"
    // },
    {
        data: 'clinic_name',
        name: 'clinic_name',
        title: "{{ __('Clinic name') }}",
        orderable: true,
    },
    {
        data: 'description',
        name: 'description',
        title: "{{ __('Description') }}",
        orderable: true,
    },
    {
        data: 'address',
        name: 'address',
        title: "{{ __('Address') }}",
        orderable: true,
    },

    {
        data: 'pincode',
        name: 'pincode',
        title: "{{ __('Pincode') }}",
        orderable: true,
    },
    {
        data: 'contact_number',
        name: 'contact_number',
        title: "{{ __('Contact number') }}",
        orderable: true,
    },
    {
        data: 'status',
        name: 'status',
        orderable: true,
        searchable: true,
        title: "{{ __('Status') }}",
        width: '5%'
    }

    ]

    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('service.lbl_action') }}",
        width: '5%'
    }]

    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.clinics.index_data") }}',
            finalColumns,
            orderColumn: [[ 4, "desc" ]],
            advanceFilter: () => {
                return {
                }
            }
        });
    })


    function resetQuickAction () {
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

        $('#quick-action-type').change(function () {
            resetQuickAction()
        });

        $(document).on('update_quick_action', function() {
            // resetActionButtons()
        })
</script>
@endpush
