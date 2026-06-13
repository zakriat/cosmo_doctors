@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                <x-backend.quick-action url="{{ route('backend.medicines.bulk_action') }}">
                    <div class="">
                        <select name="action_type" class="form-control select2 col-12" id="quick-action-type" style="width: 100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            <option value="change-status">{{ __('messages.status') }}</option>
                            <option value="delete">{{ __('messages.delete') }}</option>
                        </select>
                    </div>
                    <div class="select-status d-none quick-action-field" id="change-status-action">
                        <select name="status" class="form-control select2" id="status" style="width: 100%">
                            <option value="1">{{ __('messages.active') }}</option>
                            <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </x-backend.quick-action>
                <div>
                    <button type="button" class="btn btn-secondary" data-modal="export">
                        <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
                    </button>
                </div>
            </div>
            <x-slot name="toolbar">
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}" aria-label="Search" aria-describedby="addon-wrapping">
                </div>
                <x-buttons.offcanvas target='#form-offcanvas'>{{ __('messages.new') }} {{ __('medicines.singular_title') }}</x-buttons.offcanvas>
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-striped border table-responsive">
        </table>
    </div>
</div>

<div data-render="app">
    <medicine-offcanvas 
        create-title="{{ __('messages.new') }} {{ __('medicines.singular_title') }}" 
        edit-title="{{ __('messages.edit') }} {{ __('medicines.singular_title') }}">
    </medicine-offcanvas>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="button" class="btn-close float-end" aria-label="Close" id="close-advance-filter"></button>
        <div class="advance-filter-row">
            <div class="col-md-12 mt-2">
                <div class="form-group">
                    <label class="form-label">{{ __('messages.status') }}</label>
                    <select name="column_status" id="column_status" class="select2 form-control" data-filter="select">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </x-backend.advance-filter>
</div>

@include('appointment::backend.medicines.export')
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
@endpush

@push('after-scripts')
<script src="{{ mix('modules/appointment/script.js') }}"></script>
<script>
    const columns = [
        {
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        {
            data: 'name',
            name: 'name',
            title: "{{ __('medicines.lbl_name') }}",
            orderable: true,
        },
        {
            data: 'generic_name',
            name: 'generic_name',
            title: "{{ __('medicines.lbl_generic_name') }}",
            orderable: true,
        },
        {
            data: 'brand_name',
            name: 'brand_name',
            title: "{{ __('medicines.lbl_brand_name') }}",
            orderable: true,
        },
        {
            data: 'strength',
            name: 'strength',
            title: "{{ __('medicines.lbl_strength') }}",
            orderable: true,
        },
        {
            data: 'dosage_form',
            name: 'dosage_form',
            title: "{{ __('medicines.lbl_dosage_form') }}",
            orderable: true,
        },
        {
            data: 'manufacturer',
            name: 'manufacturer',
            title: "{{ __('medicines.lbl_manufacturer') }}",
            orderable: true,
        },
        {
            data: 'category',
            name: 'category',
            title: "{{ __('medicines.lbl_category') }}",
            orderable: true,
        },
        {
            data: 'price',
            name: 'price',
            title: "{{ __('medicines.lbl_price') }}",
            orderable: true,
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false,
            title: "{{ __('medicines.lbl_status') }}",
            width: '5%',
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('medicines.lbl_action') }}",
            width: '5%',
        }
    ]

    const actionColumn = [
        { data: 'action', name: 'action', orderable: false, searchable: false, title: "{{ __('medicines.lbl_action') }}", width: '5%' }
    ]

    const customFieldColumns = JSON.parse(@json($columns))

    let finalColumns = [
        ...columns,
        ...customFieldColumns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.medicines.index_data") }}',
            finalColumns,
            advanceFilter: () => {
                return {
                    column_status: $('#column_status').val(),
                }
            }
        })
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

    $(document).on('click', '[data-assign]', function() {
        const id = $(this).attr('data-assign')
        const url = $(this).attr('data-url')

        const name = $(this).attr('data-name')

        $('#assignForm').attr('action', url)
        $('#assignModal').modal('show')
        $('#medicine_name').text(name)
    })

    $(document).on('click', '[data-detach]', function() {
        const id = $(this).attr('data-detach')
        const url = $(this).attr('data-url')

        const name = $(this).attr('data-name')

        $('#detachForm').attr('action', url)
        $('#detachModal').modal('show')
        $('#medicine_name_detach').text(name)
    })
</script>
@endpush