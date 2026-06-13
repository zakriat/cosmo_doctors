@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if (auth()->user()->can('edit_service_provider') ||
                            auth()->user()->can('delete_service_provider'))
                        <x-backend.quick-action url="{{ route('backend.services.bulk_action') }}">
                            <div class="">
                                <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                                    style="width:100%">
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    @can('edit_Vital')
                                        <option value="change-status">{{ __('messages.status') }}</option>
                                    @endcan
                                    @can('delete_Vital')
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                </select>
                            </div>

                            <div class="select-status d-none quick-action-field" id="change-status-action">
                                <select name="status" class="select2 form-select" id="status" style="width:100%">
                                    <option value="" selected>{{ __('messages.select_status') }}</option>
                                    <option value="1">{{ __('messages.active') }}</option>
                                    <option value="0">{{ __('messages.inactive') }}</option>
                                </select>
                            </div>
                        </x-backend.quick-action>
                    @endif
                    <div>
                        <button type="button" class="btn btn-primary" data-modal="export">
                        <i class="ph ph-export me-1"></i>> {{ __('messages.export') }}
                        </button>
                        {{--          <button type="button" class="btn btn-secondary" data-modal="import"> --}}
                        {{--            <i class="fa-solid fa-upload"></i> Import --}}
                        {{--          </button> --}}
                    </div>
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
                    @hasPermission('add_vital')
                        <x-buttons.offcanvas target='#form-offcanvas'
                            title="{{ __('messages.create') }} {{ __($module_title) }}">
                            {{ __('messages.create') }} {{ __('vital.singular_title') }}</x-buttons.offcanvas>
                    @endhasPermission
                    <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}</button>
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <form-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"
            :customefield="{{ json_encode($customefield) }}" style="width: 60%">
        </form-offcanvas>
    </div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <form action="javascript:void(0)" class="datatable-filter" id="advance-filter-form">
            <div class="form-group">
                <label for="height">{{ __('booking.lbl_height') }}</label>
                <input type="number" name="height_cm" id="height_cm" class="form-control" placeholder="" />
            </div>

            <div class="form-group">
                <label for="weight">{{ __('booking.lbl_weight') }}</label>
                <input type="number" name="weight_kg" id="weight_kg" class="form-control" placeholder="" />
            </div>
            <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
        </form>
    </x-backend.advance-filter>

@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/vital/style.css') }}">
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/vital/script.js') }}"></script>
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
                data: 'image',
                name: 'image',
                width: '0%',
                orderable: false,
                searchable: false,
                title: "{{ __('vital.lbl_profile') }}"
            },
            {
                data: 'height_cm',
                name: 'height',
                title: "{{ __('vital.lbl_height_CM') }}"
            },
            {
                data: 'weight_kg',
                name: 'weight',
                title: "{{ __('vital.lbl_weight_KG') }}"
            },
            {
                data: 'bmi',
                name: 'bmi',
                title: "{{ __('vital.lbl_bmi') }}"
            },
            {
                data: 'tbf',
                name: 'tbf',
                title: "{{ __('vital.lbl_tbf') }}"
            },
            {
                data: 'vfi',
                name: 'vfi',
                title: "{{ __('vital.lbl_vfi') }}"
            },
            {
                data: 'tbw',
                name: 'tbw',
                title: "{{ __('vital.lbl_tbw') }}"
            },
            {
                data: 'sm',
                name: 'sm',
                title: "{{ __('vital.lbl_sm') }}"
            },
            {
                data: 'bmc',
                name: 'bmc',
                title: "{{ __('vital.lbl_bmc') }}"
            },
            {
                data: 'bmr',
                name: 'bmr',
                title: "{{ __('vital.lbl_bmr') }}"
            },
            // {
            //     data: 'status',
            //     name: 'status',
            //     orderable: true,
            //     searchable: true,
            //     title: "{{ __('service.lbl_status') }}",
            //     width: '5%'
            // },

            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('service.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
        ]

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('service.lbl_action') }}",
            width: '5%'
        }]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                advanceFilter: () => {
                    return {
                        height_cm: $('#height_cm').val(),
                        weight_kg: $('#weight_kg').val(),
                    }
                }
            });

            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                $('#advance-filter-form')[0].reset();
                window.renderedDataTable.ajax.reload(null, false);
            });

            $('#height_cm, #weight_kg').on('change', function() {
                window.renderedDataTable.ajax.reload(null, false);
            });
        });
    </script>
@endpush
