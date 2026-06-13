@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="table-content">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
        @if(auth()->user()->can('edit_doctors') || auth()->user()->can('delete_doctors'))
            <x-backend.quick-action url='{{ route("backend.$module_name.bulk_action") }}'>
                <div class="">
                    <select name="action_type" class="form-select col-12" id="quick-action-type">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @hasPermission('edit_doctors')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        @endhasPermission
                        @hasPermission('delete_doctors')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endhasPermission
                    </select>
                </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="form-select" id="status">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1">{{ __('messages.active') }}</option>
                        <option value="0">{{ __('messages.inactive') }}</option>
                    </select>
                </div>
            </x-backend.quick-action>
        @endif
            <div>
                <button type="button" class="btn btn-primary" data-modal="export">
                <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>
            </div>
        </div>

        <x-slot name="toolbar">
            <div>
                <div class="datatable-filter">
                    <select name="column_status" id="column_status" class="form-select"
                        data-filter="select">
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
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                    aria-label="Search" aria-describedby="addon-wrapping">
            </div>

            <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>

            {{-- New Button --}}
            <button type="button"
                class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                data-mode="create"
                data-bs-toggle="offcanvas"
                data-bs-target="#form-offcanvas"
                id="create-doctor-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none"
                viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <circle cx="12" cy="12" r="9" stroke="white"/>
                <path d="M12 8v8M8 12h8" stroke-linecap="round"/>
                </svg>
                {{ __('messages.new') }}
            </button>

            <!-- Offcanvas -->
            <div class="offcanvas offcanvas-end offcanvas-w-40"
                tabindex="-1"
                id="form-offcanvas"
                aria-labelledby="form-offcanvas-label">

                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="form-offcanvas-label">
                        <span id="offcanvas-title">{{ __('messages.create') }} {{ __('clinic.doctor_title') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-3 offcanvas-body-wide">
                    {{-- Include form partial --}}
                    <form id="doctor-form" method="POST">
                        @csrf
                        @include('clinic::backend.doctor.form')
                    </form>
                </div>
            </div>
        </x-slot>
    </x-backend.section-header>

    <table id="datatable" class="table table-responsive"></table>
</div>

<div data-render="app">

    {{-- <doctor-offcanvas type="{{ __('staff') }}"  
    default-image="{{default_file_url()}}"
    create-title="{{ __('messages.create') }} {{ __($create_title) }}" edit-title="{{ __('messages.edit') }} {{ __($create_title) }}" :customefield="{{ json_encode($customefield) }}">
    </doctor-offcanvas> --}}
    {{-- <doctor-details-offcanvas>
    </doctor-details-offcanvas> --}}
    @include('clinic::backend.doctor.doctor-details')


    <clinic-list-form-offcanvas></clinic-list-form-offcanvas>
    <employee-slot-mapping-form-offcanvas></employee-slot-mapping-form-offcanvas>
    @include('clinic::backend.doctor.change-password')

    <customform-offcanvas>
    </customform-offcanvas>

    <div data-render="app">
    <div class="offcanvas offcanvas-end offcanvas-w-40"
        tabindex="-1"
        id="doctor--form-offcanvas"
        aria-labelledby="doctorSessionsLabel">
    <div class="offcanvas-header border-bottom">
        <h6 class="m-0 h5" id="doctorSessionsLabel">{{ __('clinic.doctor_sessions') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('clinic::backend.doctor.doctor-session-form')
    </div>
    </div>

    <send-push-notification create-title="{{ __('clinic.send_push_notification') }}"></send-push-notification>
</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    @unless(auth()->user()->hasRole('doctor'))
    <div class="form-group datatable-filter">
        <label class="form-label" for="doctor_name">{{__('clinic.doctor_name')}}</label>
        <input type="text" name="doctor_name" id="doctor_name" class="form-control" placeholder="{{__('clinic.doctors')}}">

      
    </div>
    <div class="form-group datatable-filter">
        <label class="form-label" for="email">{{__('clinic.lbl_Email')}}</label>
        <input type="text" name="email" id="email" class="form-control" placeholder="{{__('clinic.Emails')}}">
      
    </div>
    <div class="form-group datatable-filter">
        <label class="form-label" for="contact">{{__('clinic.lbl_contact_number')}}</label>
        <input type="text" name="contact" id="contact" class="form-control" placeholder="{{__('clinic.contact_numbers')}}">
      
    </div>
    
    <div class="form-group datatable-filter">
        <label class="form-label w-100" for="column_clinic">{{__('clinic.lbl_gender')}}</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" id="male" value="male" data-filter="select"/> 
            <label class="form-check-label" for="male"> {{__('clinic.lbl_male')}} </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" id="female" value="female" data-filter="select"/>
            <label class="form-check-label" for="female"> {{__('clinic.lbl_female')}} </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" id="intersex" value="intersex" data-filter="select"/>
            <label class="form-check-label" for="intersex"> {{__('Intersex')}} </label>
        </div>
    </div>
    @endunless
    @unless(auth()->user()->hasRole('receptionist'))
    <div class="form-group datatable-filter">
        <label class="form-label" for="column_clinic">{{__('clinic.lbl_clinic')}}</label>
        <select name="column_clinic" id="column_clinic" class="form-control">
            <option value="">{{ __('service.all') }} {{__('clinic.singular_title')}}</option>
        </select>
    </div>
    @endunless
    @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
    <div class="form-group datatable-filter">
        <label class="form-label" for="vendor">{{__('clinic.clinic_admin')}}</label>
        <select name="vendor" id="vendor" class="form-control">
            <option value="">{{ __('service.all') }} {{__('clinic.clinic_admin')}}</option>
        </select>
    </div> 
    @endif
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter>
</div>

@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<style>
    /* Force hide search box for clinic and vendor Select2 */
    .select2-container--default .select2-search--dropdown {
        display: none !important;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        display: none !important;
    }
</style>
@endpush

@push('after-scripts')
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
        {
            data: 'doctor_id',
            name: 'doctor_id',
            title: "{{__('clinic.lbl_name')}}",
            orderable: true,
            searchable: true,
        },

        {
            data: 'mobile',
            name: 'mobile',
            title: "{{__('clinic.lbl_phone_number')}}"
        },
        {
            data: 'gender',
            name: 'gender',
            title: "{{__('clinic.lbl_gender')}}"
        },

        @unless(auth()->user()->hasRole('receptionist'))
        {
            data: 'clinic_id',
            name: 'clinic_id',
            title: "{{__('clinic.lbl_clinic_center')}}",
            orderable: false,
            searchable: false,
        },
        @endunless
        {
            data: 'email_verified_at',
            name: 'email_verified_at',
            orderable: false,
            searchable: false,
            title: "{{ __('clinic.lbl_verification_status') }}"
        },



        @if(auth()->user()->can('edit_doctors'))

        {
            data: 'status',
            name: 'status',
            orderable: true,
            searchable: true,
            title: "{{ __('clinic.lbl_status') }}"
        },

        @endif

        {
            data: 'updated_at',
            name: 'updated_at',
            width: '15%',
            visible: false
        },
    ]

      const actionColumn = [{
        data: 'action',
        name: 'action',
        width:'5%',
        orderable: false,
        searchable: false,
        title: "{{ __('clinic.lbl_action') }}"
    }]

    const customFieldColumns = JSON.parse(@json($columns))

    let finalColumns = [
        ...columns,
        ...customFieldColumns,
        ...actionColumn

    ]
    document.addEventListener('DOMContentLoaded', (event) => {
        // Initialize Select2 for all select elements
        $('#quick-action-type').select2({
            width: '100%',
            minimumResultsForSearch: Infinity,
        });

        $('#status').select2({
            width: '100%',
            minimumResultsForSearch: Infinity,
        });

        $('#column_status').select2({
            width: '100%',
            minimumResultsForSearch: Infinity,
        });

        let selectedGender = null;

        // Initialize datatable first
        initDatatable({
            url: '{{ route("backend.$module_name.index_data") }}',
            finalColumns,
            orderColumn: [[ 6, 'desc' ]],
            advanceFilter: () => {
                const doctorNameFilter = $('#doctor_name').val();
                const emailFilter = $('#email').val();
                const contactFilter = $('#contact').val();
                return {
                    clinic_name: $('#column_clinic').val(),
                    doctor_name: doctorNameFilter,
                    contact : contactFilter,
                    email : emailFilter,
                    vendor_id : $('#vendor').val(),
                    gender: selectedGender
                }
            }
        });

        // Destroy any existing Select2 on clinic filter before re-initializing
        if ($('#column_clinic').hasClass('select2-hidden-accessible')) {
            $('#column_clinic').select2('destroy');
        }
        
        // Initialize Select2 for clinic filter with AJAX but NO search box
        $('#column_clinic').select2({
            width: '100%',
            placeholder: '{{ __("service.all") }} {{ __("clinic.singular_title") }}',
            allowClear: false,
            minimumResultsForSearch: Infinity,  // Hide search box
            ajax: {
                url: '{{ route("backend.doctor.get-clinics") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: '',  // No search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // Destroy any existing Select2 on vendor filter before re-initializing
        if ($('#vendor').hasClass('select2-hidden-accessible')) {
            $('#vendor').select2('destroy');
        }
        
        // Initialize Select2 for vendor filter with AJAX but NO search box
        $('#vendor').select2({
            width: '100%',
            placeholder: '{{ __("service.all") }} {{ __("clinic.clinic_admin") }}',
            allowClear: false,
            minimumResultsForSearch: Infinity,  // Hide search box
            ajax: {
                url: '{{ route("backend.doctor.get-vendors") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: '',  // No search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // Add event handlers for filters
        $('#doctor_name').on('input', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });
        
        $('#email').on('input', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });
        
        $('#contact').on('input', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });

        // Gender filter change handler
        $('input[name="gender"]').change(function() {
            selectedGender = $(this).val();
            window.renderedDataTable.ajax.reload(null, false);
        });

        // Trigger datatable reload on clinic/vendor filter change
        $('#column_clinic').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });

        $('#vendor').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });

        // Reset filter handler
        $('#reset-filter').on('click', function(e) {
            e.preventDefault();
            
            // Reset Select2 filters without triggering change event
            $('#column_clinic').val(null).trigger('change.select2');
            $('#vendor').val(null).trigger('change.select2');
            
            // Reset text inputs
            $('#doctor_name, #contact, #email').val('');
            
            // Reset gender radio buttons
            $('input[name="gender"]').prop('checked', false); 
            selectedGender = null; 
            
            // Reload datatable once
            window.renderedDataTable.ajax.reload(null, false);
        });
    });

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



 