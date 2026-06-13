@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
   {{ __($module_title) }}
@endsection


@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
<style>
    .offcanvas.offcanvas-w-40 { --bs-offcanvas-width: 40vw; }
    @media (max-width: 576px) {
        .offcanvas.offcanvas-w-40 { --bs-offcanvas-width: 100vw; }
    }
    @media (min-width: 576px) and (max-width: 992px) {
        .offcanvas.offcanvas-w-40 { --bs-offcanvas-width: 60vw; }
    }
</style>
@endpush

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if (auth()->user()->can('edit_clinics_service') ||
            auth()->user()->can('delete_clinics_service'))
            <x-backend.quick-action url="{{ route('backend.services.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('edit_clinics_service')
                        <option value="change-status">{{ __('messages.status') }}</option>

                        @endcan
                        @can('delete_clinics_service')
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
                    <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>
            </div>
        </div>
        <x-slot name="toolbar">

            <div>
                <div class="datatable-filter">
                    <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width: 100%">
                        <option value="">{{ __('messages.all') }}</option>
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
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    <i class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}
                </button>
                {{-- @hasPermission('add_clinics_service')
                <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __('service.singular_title') }}">
                    {{ __('messages.new') }} </x-buttons.offcanvas>
                @endhasPermission --}}
                @hasPermission('add_clinics_service')
                <button 
                    class="btn btn-primary d-flex align-items-center gap-1" 
                    type="button"
                    data-bs-toggle="offcanvas" 
                    data-bs-target="#createServiceForm" 
                    aria-controls="createServiceForm"
                >
                    <i class="ph ph-plus-circle"></i> {{ __('messages.new') }}
                </button>
                @endhasPermission

                <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="createServiceForm">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">{{ __('clinic.create_service') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('clinic::backend.services.form')  
                    </div>
                </div>
                
                
            </div>

        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">
    {{-- <clinic-service-offcanvas create-title="{{ __('messages.create') }} {{ __('service.singular_title') }}" default-image="{{default_file_url()}}" edit-title="{{ __('messages.edit') }} {{ __('service.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
    </clinic-service-offcanvas> --}}



    
    <assign-doctor-form-offcanvas></assign-doctor-form-offcanvas>
    <assign-service-provider-form-offcanvas></assign-service-provider-form-offcanvas>
    <gallery-form-offcanvas></gallery-form-offcanvas>
</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
   
    <div class="form-group datatable-filter">
        <label class="form-label" for="price_range">{{ __('service.lbl_price') }}</label>
        <select name="price_range" id="price_range" class="select2 form-select" data-filter="select"
            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'price_range']) }}"
            data-ajax--cache="true" placeholder="{{ __('clinic.select_price_range') }}">
            <option value="" disabled selected>{{ __('service.all') }} {{ __('service.lbl_price') }}</option>
        </select>
    </div>
    <div class="form-group datatable-filter">
        <label class="form-label" for="column_category">{{ __('service.lbl_category') }}</label>
        <select name="column_category" id="column_category" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{ __('service.lbl_category') }}</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}" data-parent="{{ $category->parent_id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group datatable-filter d-none" id="subcategory-group">
        <label class="form-label" for="column_subcategory">{{ __('service.lbl_subcategory') }}</label>
        <select name="column_subcategory" id="column_subcategory" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{ __('service.lbl_subcategory') }}</option>
        </select>
    </div>

    @unless(auth()->user()->hasRole('doctor'))
    <div class="form-group datatable-filter">
        <label class="form-label" for="doctor">{{ __('service.lbl_doctor') }}</label>
        <select name="doctor_id" id="doctor_id" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{ __('service.lbl_doctor') }}</option>
            @foreach ($doctor as $doctors)
            <option value="{{ $doctors->id }}">{{ $doctors->full_name }}</option>
            @endforeach
        </select>
    </div>
    @endunless
    @unless(auth()->user()->hasRole('receptionist'))
    <div class="form-group datatable-filter">
        <label class="form-label" for="clinic">{{ __('service.lbl_clinic') }}</label>
        <select name="clinic" id="clinic" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{ __('service.lbl_clinic') }}</option>
            @foreach ($clinic as $clinics)
            <option value="{{ $clinics->id }}">{{ $clinics->name }}</option>
            @endforeach
        </select>
    </div>
    @endunless
    @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
        <div class="form-group datatable-filter">
            <label for="form-label">{{ __('clinic.clinic_admin') }}</label>
            <select  id="column_clinic_admin" name="column_clinic_admin" data-filter="select"
                class="select2 form-select"
                data-ajax--url="{{ route('backend.get_search_data', ['type' => 'clinic_admin']) }}"
                data-ajax--cache="true">
                <option value="">{{ __('service.all') }} {{ __('clinic.clinic_admin') }}</option>
            </select>
        </div>
    @endif
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
    <div class="form-group custom-range">
        <div class="filter-slider slider-secondary"></div>
    </div>
</x-backend.advance-filter>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<!-- Select2 CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
@endpush

@push('after-scripts')
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
            data: 'name',
            name: 'name',
            title: "{{ __('service.lbl_name') }}"
        },
        {
            data: 'charges',
            name: 'charges',
            title: "{{ __('service.lbl_price') }}",
            searchable: false,
            orderable: true,
        },
        {
            data: 'duration_min',
            name: 'duration_min',
            title: "{{ __('service.lbl_duration') }}"
        },

        {
            data: 'category_id',
            name: 'category_id',
            title: "{{ __('service.lbl_category_id') }}"
        },
        @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
        {
            data: 'vendor_id',
            name: 'vendor_id',
            title: "{{ __('multivendor.singular_title') }}"
        },
        @endif
        {
            data: 'doctor',
            name: 'doctor',
            title: "{{ auth()->user()->hasRole('doctor') ? __('clinic.price_change') : __('service.lbl_doctor')}}",
            searchable: false,
            orderable: false,
        },
        @if( auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin') || auth()->user()->hasRole('vendor') || auth()->user()->hasRole('receptionist') )
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: true,
            title: "{{ __('service.lbl_status') }}",
            width: '5%'
        },
        @endif

        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('service.lbl_update_at') }}",
            orderable: true,
            visible: false,
        },

    ]


    const actionColumn = [
    @unless( auth()->user()->hasRole('doctor'))
    {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('service.lbl_action') }}",
        width: '5%'
    }
    @endunless
]

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

        $('#price_range').select2({
            width: '100%',
            placeholder: "{{ __('clinic.select_price_range') }}",
            ajax: {
                url: "{{ route('backend.get_search_data', ['type' => 'price_range']) }}",
                dataType: 'json',
                delay: 250,
                cache: true,
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#column_category').select2({
            width: '100%',
            placeholder: "{{ __('service.all') }} {{ __('service.lbl_category') }}",
        });

        $('#column_subcategory').select2({
            width: '100%',
            placeholder: "{{ __('service.all') }} {{ __('service.lbl_subcategory') }}",
        });

        $('#doctor_id').select2({
            width: '100%',
            placeholder: "{{ __('service.all') }} {{ __('service.lbl_doctor') }}",
        });

        $('#clinic').select2({
            width: '100%',
            placeholder: "{{ __('service.all') }} {{ __('service.lbl_clinic') }}",
        });

        @if(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
        $('#column_clinic_admin').select2({
            width: '100%',
            placeholder: "{{ __('service.all') }} {{ __('clinic.clinic_admin') }}",
            ajax: {
                url: "{{ route('backend.get_search_data', ['type' => 'clinic_admin']) }}",
                dataType: 'json',
                delay: 250,
                cache: true,
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
        @endif

        initDatatable({
            url: '{{ route("backend.$module_name.index_data", ["doctor_id" => $doctor_id ]) }}',
            finalColumns,
            orderColumn: [
                @if(auth()->user()->hasRole('doctor'))
                [5, "desc"]
                @elseif(multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')))
                [8, "desc"]
                @else
                [7, "desc"]
                @endif
            ],
            advanceFilter: () => {
                return {
                    service_id: $('#service_name').val(),
                    price: $('#price_range').val(),
                    category_id: $('#column_category').val(),
                    sub_category_id: $('#column_subcategory').val(),
                    doctor_id: $('#doctor_id').val(),
                    clinic_id: $('#clinic').val(),
                    clinic_admin: $('#column_clinic_admin').val(),
                }
            }
        });

        $('#reset-filter').on('click', function(e) {
            $('#column_category').val('').trigger('change');
            $('#column_subcategory').val('').trigger('change');
            $('#service_name').val('');
            $('#price_range').val(null).trigger('change');
            $('#doctor_id').val('').trigger('change');
            $('#clinic').val('').trigger('change');
            $('#column_clinic_admin').val(null).trigger('change');

            window.renderedDataTable.ajax.reload(null, false);
        });

        // filterSubcategories($('#column_category').val());
    });

    $('#column_category').on('change', function() {
        var selectedCategoryId = $(this).val();
        var subcategoryGroup = $('#subcategory-group');
        var subcategorySelect = $('#column_subcategory');

        subcategorySelect.html('<option value="">All Sub Categories</option>');

        if (selectedCategoryId !== "") {
            var subcategories = {!!$subcategories->toJson()!!};
            var hasSubcategories = subcategories.some(function(subcategory) {
                return subcategory.parent_id == selectedCategoryId;
            });

            if (hasSubcategories) {
                subcategoryGroup.removeClass('d-none');
                subcategories.forEach(function(subcategory) {
                    if (subcategory.parent_id == selectedCategoryId) {
                        $('<option></option>').attr('value', subcategory.id).text(subcategory.name).appendTo(subcategorySelect);
                    }
                });
            } else {
                subcategoryGroup.addClass('d-none');
            }
        } else {
            subcategoryGroup.addClass('d-none');
        }
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
</script>
@endpush