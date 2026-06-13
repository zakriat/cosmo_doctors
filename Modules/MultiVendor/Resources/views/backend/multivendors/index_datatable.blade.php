@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="">
        <div class="">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if(auth()->user()->can('edit_vendor_list') || auth()->user()->can('delete_vendor_list'))
                    <x-backend.quick-action url="{{ route('backend.multivendors.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_vendor_list')
                                <option value="change-status">{{ __('messages.status') }}</option>
                                @endcan
                                @can('delete_vendor_list')
                                <option value="delete">{{ __('messages.delete') }}</option>
                                @endcan
                            </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="select2 form-select" id="status" style="width:100%">
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
        {{--          <button type="button" class="btn btn-secondary" data-modal="import">--}}
        {{--            <i class="fa-solid fa-upload"></i> Import--}}
        {{--          </button>--}}
                    </div>
                </div>
                <x-slot name="toolbar">

                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-select"
                                data-filter="select" style="width: 100%">
                                <option value="">{{__('messages.all')}}</option>
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
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                        <i class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}
                    </button>
                    @hasPermission('add_vendor_list')
                    <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
                    @endhasPermission
                </div>
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-responsive">
            </table>
        </div>
    </div>
            <!-- <form-offcanvas create-title="{{ __('messages.create') }} {{ __('clinic.clinic_admin') }}"
            edit-title="{{ __('messages.edit') }} {{ __('clinic.clinic_admin') }}" :customefield="{{ json_encode($customefield) }}">
        </form-offcanvas> -->
    @include('multivendor::backend.multivendors.multivendor', [
    'vendor' => null,
    'countries' => $countries,
    'states' => $states,
    'cities' => $cities,
    'genders' => ['male'=>'Male','female'=>'Female','intersex'=>'Intersex'],
])

    <div data-render="app">
    <div id="offcanvas-placeholder">
    </div>
    <change-password create-title="{{ __('messages.change_password') }}"></change-password>
</div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <div class="form-group datatable-filter">
            <label class="form-label" for="filter_gender">{{ __('clinic.lbl_gender') }}</label>
            <select name="filter_gender" id="filter_gender" class="select2 form-select" data-filter="select">
                <option value="">{{ __('messages.all') }}</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="intersex">Intersex</option>
            </select>
        </div>
        <div class="form-group datatable-filter">
            <label class="form-label" for="filter_verified">{{ __('multivendor.lbl_verification_status') }}</label>
            <select name="filter_verified" id="filter_verified" class="select2 form-select" data-filter="select">
                <option value="">{{ __('messages.all') }}</option>
                <option value="1">{{ __('messages.verified') }}</option>
                <option value="0">{{ __('messages.not_verified') }}</option>
            </select>
        </div>
        <div class="form-group datatable-filter">
            <label class="form-label" for="filter_blocked">{{ __('customer.lbl_blocked') }}</label>
            <select name="filter_blocked" id="filter_blocked" class="select2 form-select" data-filter="select">
                <option value="">{{ __('messages.all') }}</option>
                <option value="1">{{ __('messages.yes') }}</option>
                <option value="0">{{ __('messages.no') }}</option>
            </select>
        </div>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
    </x-backend.advance-filter>
@endsection

@push ('after-styles')
{{-- <link rel="stylesheet" href="{{ mix('modules/multivendors/style.css') }}"> --}}
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/multivendor/script.js') }}"></script>
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
                data: 'vendor_id',
                name: 'vendor_id',
                title: "{{ __('multivendor.title') }}"
            },
            {
                data: 'mobile',
                name: 'mobile',
                title: "{{__('clinic.lbl_phone_number')}}"
            },
            {
                data: 'gender',
                name: 'gender',
                title: "{{__('clinic.lbl_gender')}}",
                render: function(data, type, row) {
                    if (!data) return '';
                    return data.charAt(0).toUpperCase() + data.slice(1).toLowerCase();
                }
            },
            {
                data: 'email_verified_at',
                name: 'email_verified_at',
                orderable: false,
                searchable: true,
                title: "{{ __('multivendor.lbl_verification_status') }}"
            },
            {
                data: 'is_banned',
                name: 'is_banned',
                orderable: false,
                searchable: true,
                title: "{{ __('customer.lbl_blocked') }}"
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('multivendor.lbl_status') }}",
                width: '5%'
            },

            {
              data: 'updated_at',
              name: 'updated_at',
              title: "{{ __('multivendor.lbl_update_at') }}",
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
                        gender: $('#filter_gender').val(),
                        email_verified: $('#filter_verified').val(),
                        is_banned: $('#filter_blocked').val(),
                    }
                }
            });
            // Global helper to reload datatable after form submit
            window.reloadDatatable = function() {
                if (window.renderedDataTable && window.renderedDataTable.ajax) {
                    window.renderedDataTable.ajax.reload(null, false);
                }
            }
            $('#reset-filter').on('click', function(e) {
                $('#filter_gender').val('');
                $('#filter_verified').val('');
                $('#filter_blocked').val('');
                window.renderedDataTable.ajax.reload(null, false);
            });
        })
    const baseUrl = "{{ url('/') }}";
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
        $(document).on('click', '.edit-vendor-btn', function() {
    var vendorId = $(this).data('id');
    $.ajax({
        url: baseUrl + '/app/multivendors/' + vendorId + '/edit',
        type: 'GET',
        success: function(response) {
            $('#offcanvas-placeholder').html(response);
            var offcanvas = document.querySelector('#offcanvas-placeholder #form-offcanvas');
            var bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
            bsOffcanvas.show();

            // After form is loaded, trigger plugin init and population using the injected offcanvas root
            if (typeof window.vendorFormInit === 'function') {
                window.vendorFormInit(offcanvas);
            }
        },
        error: function() {
            alert('Error loading vendor form');
        }
    });
});

// For create button (if you have one)
$(document).on('click', '#create-vendor-btn', function() {
    $.ajax({
        url: baseUrl + '/app/multivendors/create',
        type: 'GET',
        success: function(response) {
            $('#offcanvas-placeholder').html(response);
            var offcanvas = document.querySelector('#offcanvas-placeholder #form-offcanvas');
            var bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
            bsOffcanvas.show();
        },
        error: function() {
            alert('Error loading vendor form');
        }
    });
});
</script>
@endpush
