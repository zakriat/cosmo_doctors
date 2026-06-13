@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-3">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">

        
           @php
            $permissionsToCheck = ['edit_clinics_center', 'delete_clinics_service'];
           @endphp

           @if(collect($permissionsToCheck)->contains(fn ($permission) => auth()->user()->can($permission)))

            <x-backend.quick-action url="{{ route('backend.clinics.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                        style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @hasPermission('edit_clinics_center')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        @endhasPermission

                        @hasPermission('delete_clinics_service')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endhasPermission
                    </select>
                </div>

                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" id="status" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1" >{{ __('messages.active') }}</option>
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
            <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>
            @hasPermission('add_clinics_center')
                <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __('clinic.singular_title') }}">
                {{ __('messages.new') }}  </x-buttons.offcanvas>
            @endhasPermission

        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>

{{-- Blade Template Form Offcanvas --}}
@include('clinic::backend.clinic.form_offcanvas', [
    'clinic' => null,
    'vendors' => $vendors,
    'systemservicecategories' => $systemservicecategories,
    'countries' => $countries,
    'states' => $states,
    'cities' => $cities,
    'customfields' => $customfields
])

<div data-render="app">
     <clinic-gallery-offcanvas></clinic-gallery-offcanvas>
   <div id="clinic-session-offcanvas-container"></div>
    <div id="clinic-details-offcanvas-container"></div>
</div>

<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>

     {{-- <div class="form-group datatable-filter">
         <label class="form-label" for="form-label"> {{ __('clinic.lbl_clinic') }}</label>
         <select  id="clinic_name" name="clinic_name" data-filter="select"
             class="select2 form-select"
             data-ajax--url="{{ route('backend.get_search_data', ['type' => 'clinic_name']) }}"
             data-ajax--cache="true" data-placeholder="{{ __('clinic.lbl_select_clinic') }}">
         </select>
     </div> --}}

     @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))

    <div class="form-group datatable-filter">
        <label class="form-label" for="form-label">{{ __('clinic.clinic_admin') }}</label>
        <select  id="column_clinic_admin" name="column_clinic_admin" data-filter="select"
            class="select2 form-select"
            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'clinic_admin']) }}"
            data-ajax--cache="true" data-placeholder="{{ __('clinic.select_clinic_admin') }}">
        </select>
    </div>

    @endif

     <div class="form-group datatable-filter">
         <label class="form-label" for="form-label"> {{ __('clinic.speciality') }}</label>
         <select  id="column_category" name="column_category" data-filter="select"
             class="select2 form-select"
             data-ajax--url="{{ route('backend.get_search_data', ['type' => 'system_category']) }}"
             data-ajax--cache="true" data-placeholder="{{ __('clinic.specialization_list') }}">
         </select>
     </div>

     <div class="form-group datatable-filter">
         <label class="form-label" for="form-label"> {{ __('clinic.country') }}</label>
         <select  id="column_country" name="column_country" data-filter="select"
             class="select2 form-select"
             data-ajax--url="{{ route('backend.get_search_data', ['type' => 'country']) }}"
             data-ajax--cache="true" data-placeholder="{{ __('clinic.select_country') }}">
         </select>
     </div>

     <div class="form-group datatable-filter">
         <label class="form-label" for="form-label"> {{ __('clinic.state') }}</label>
         <select  id="column_state" name="column_state" data-filter="select"
             class="select2 form-select"
             data-placeholder="{{ __('clinic.select_state') }}">
         </select>
     </div>

     <div class="form-group datatable-filter">
         <label class="form-label" for="form-label"> {{ __('clinic.city') }}</label>
         <select  id="column_city" name="column_city" data-filter="select"
             class="select2 form-select"
             data-placeholder="{{ __('clinic.select_city') }}">
         </select>
     </div>

    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter>
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
    const columns = [
             @unless(auth()->user()->hasRole('doctor'))
             {
                 name: 'check',
                 data: 'check',
                 title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                 width: '0%',
                 exportable: false,
                 orderable: false,
                 searchable: false,
             },
             @endunless
             {
                 data: 'updated_at',
                 name: 'updated_at',
                 width: '15%',
                 visible: false
             },
             {
                 data: 'clinic_name',
                 name: 'clinic_name',
                 title: "{{ __('clinic.lbl_name') }}"
             },
 
             {
                 data: 'system_service_category',
                 name: 'system_service_category',
                 title: "{{ __('clinic.speciality') }}"
             },
             @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
             {
                 data: 'vendor_id',
                 name: 'vendor_id',
                 title: "{{ __('clinic.clinic_admin') }}"
             },
             @endif
             {
                 data: 'contact_number',
                 name: 'contact_number',
                 title: "{{ __('clinic.lbl_contact_number') }}"
             },
             { 
                 data: 'description',
                 name: 'description',
                 title: "{{ __('clinic.lbl_description') }}",
                 className: 'description-column'
             },
             @unless(auth()->user()->hasRole('doctor'))
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: true,
                    title: "{{ __('clinic.status') }}",
                    width: '5%'
                },
             @endunless
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
        // Initialize Select2 for bulk action and status dropdowns
        if (window.jQuery && $.fn.select2) {
            // Bulk action dropdown
            $('#quick-action-type').select2({
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: "{{ __('messages.no_action') }}"
            });

            // Status dropdown inside bulk action
            $('#status').select2({
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: "{{ __('messages.select_status') }}"
            });

            // Status filter dropdown
            $('#column_status').select2({
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: "{{ __('messages.all') }}"
            });

            // Initialize Select2 for advance filter dropdowns with AJAX
            @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
            $('#column_clinic_admin').select2({
                width: '100%',
                placeholder: "{{ __('clinic.select_clinic_admin') }}",
                allowClear: true,
                minimumResultsForSearch: 0, // Enable search
                ajax: {
                    url: "{{ route('backend.get_search_data', ['type' => 'clinic_admin']) }}",
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                },
                dropdownParent: $('#offcanvasExample')
            });
            @endif

            $('#column_category').select2({
                width: '100%',
                placeholder: "{{ __('clinic.specialization_list') }}",
                allowClear: true,
                minimumResultsForSearch: 0, // Enable search
                ajax: {
                    url: "{{ route('backend.get_search_data', ['type' => 'system_category']) }}",
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                },
                dropdownParent: $('#offcanvasExample')
            });

            $('#column_country').select2({
                width: '100%',
                placeholder: "{{ __('clinic.select_country') }}",
                allowClear: true,
                minimumResultsForSearch: 0, // Enable search
                ajax: {
                    url: "{{ route('backend.get_search_data', ['type' => 'country']) }}",
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                },
                dropdownParent: $('#offcanvasExample')
            });

            // State and City are populated dynamically via cascade, initialized in stateName() and cityName() functions
        }

        initDatatable({
            url: '{{ route("backend.clinics.index_data") }}',
            finalColumns,
            orderColumn: [
                @if(auth()->user()->hasRole('doctor'))
                [0, "desc"]
                @else
                [1, "desc"]
                @endif
            ],
            advanceFilter: () => {
                return {
                    // clinic_name: $('#clinic_name').val(), // Commented out since field is disabled
                    category_name: $('#column_category').val(),
                    clinic_admin: $('#column_clinic_admin').val(),
                    country: $('#column_country').val(),
                    state: $('#column_state').val(),
                    city: $('#column_city').val()
                };
            }
        });

        // Reset filter button functionality
        $('#reset-filter').on('click', function(e) {
            $('#column_category, #column_clinic_admin, #column_country, #column_state, #column_city')
                .val(null).trigger('change'); // clear Select2 properly
            window.renderedDataTable.ajax.reload(null, false);
        });
    });
 
 
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
 
 
     // Country -> State cascade
     $(document).on('change', '#column_country', function() {
         var country = $(this).val();
         $('#column_state').empty().trigger('change');
         $('#column_city').empty().trigger('change');
         if (country) stateName(country);
     });
 
     // State -> City cascade
     $(document).on('change', '#column_state', function() {
         var state = $(this).val();
         $('#column_city').empty().trigger('change');
         if (state) cityName(state);
     });
 
     // Load states
     function stateName(country) {
         var $state = $('#column_state');
         var state_route = "{{ route('backend.get_search_data', [ 'type' => 'state','sub_type' =>'']) }}" + country;
         state_route = state_route.replace('amp;', '');
         $.ajax({
             url: state_route,
             success: function(result) {
                 if ($state.data('select2')) {
                     $state.select2('destroy');
                 }
                 $state.empty();
                 $state.append('<option value="">{{ __("clinic.select_state") }}</option>');
                 $.each(result.results, function(index, st) {
                     $state.append(new Option(st.text, st.id, false, false));
                 });
                 $state.select2({
                     width: '100%',
                     placeholder: "{{ __('clinic.select_state') }}",
                     allowClear: false,
                     minimumResultsForSearch: 0, // Enable search always
                     dropdownParent: $('#offcanvasExample') // Ensure proper dropdown positioning
                 });
             }
         });
     }
 
     // Load cities
     function cityName(state) {
         var $city = $('#column_city');
         var city_route = "{{ route('backend.get_search_data', [ 'type' => 'city','sub_type' =>'']) }}" + state;
         city_route = city_route.replace('amp;', '');
         $.ajax({
             url: city_route,
             success: function(result) {
                 if ($city.data('select2')) {
                     $city.select2('destroy');
                 }
                 $city.empty();
                 $city.append('<option value="">{{ __("clinic.select_city") }}</option>');
                 $.each(result.results, function(index, ct) {
                     $city.append(new Option(ct.text, ct.id, false, false));
                 });
                 $city.select2({
                     width: '100%',
                     placeholder: "{{ __('clinic.select_city') }}",
                     allowClear: false,
                     minimumResultsForSearch: 0, // Enable search always
                     dropdownParent: $('#offcanvasExample') // Ensure proper dropdown positioning
                 });
             }
         });
     }
 
     const baseUrl = "{{ url('/') }}";
 
     function editClinic(clinicId) {
         $.ajax({
             url: baseUrl + '/app/clinics/' + clinicId + '/edit',
             type: 'GET',
             success: function(response) {
                 $('#form-offcanvas').remove();
                 $('body').append(response);
                 var offcanvas = document.getElementById('form-offcanvas');
                 if (offcanvas) {
                     var bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                     bsOffcanvas.show();
                 }
             },
             error: function() {
                 alert('{{ __("clinic.error_loading_clinic_data") }}');
             }
         });
     }
 
     $(document).on('click', '[data-assign-event="clinic-details"]', function() {
         var clinicId = $(this).data('assign-module');
         $.ajax({
             url: baseUrl + '/app/clinics/clinic-details/' + clinicId,
             type: 'GET',
             success: function(response) {
                 $('#clinicDetails-offcanvas').remove();
                 $('#clinic-details-offcanvas-container').html(response);
                 var offcanvas = document.getElementById('clinicDetails-offcanvas');
                 if (offcanvas) {
                     var bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                     bsOffcanvas.show();
                 }
             }
         });
     });
 
     $(document).on('click', '[data-assign-event="clinic-session"]', function() {
         var clinicId = $(this).data('assign-module');
         $.ajax({
             url: baseUrl + '/app/clinics/clinic-session/' + clinicId,
             type: 'GET',
             success: function(response) {
                 $('#clinic-session-detail').remove();
                 $('#clinic-session-offcanvas-container').html(response);
                 var offcanvas = document.getElementById('clinic-session-detail');
                 if (offcanvas) {
                     var bsOffcanvas = new bootstrap.Offcanvas(offcanvas, { backdrop: false });
                     bsOffcanvas.show();
                 }
             }
         });
     });

     // Debug: Test AJAX endpoints when advance filter opens
     $(document).on('shown.bs.offcanvas', '#offcanvasExample', function() {
         console.log('Advance filter opened');
         
         // Test AJAX endpoints
         $('#offcanvasExample .select2').each(function() {
             var $select = $(this);
             var ajaxUrl = $select.data('ajax--url');
             var selectId = $select.attr('id');
             
             console.log('Select ID:', selectId);
             console.log('AJAX URL:', ajaxUrl);
             
             if (ajaxUrl) {
                 // Test the AJAX endpoint
                 $.ajax({
                     url: ajaxUrl,
                     dataType: 'json',
                     data: { q: '', page: 1 },
                     success: function(data) {
                         console.log('AJAX Success for', selectId, ':', data);
                     },
                     error: function(xhr, status, error) {
                         console.error('AJAX Error for', selectId, ':', error, xhr.responseText);
                     }
                 });
             }
         });
     });
  </script>
@endpush
    