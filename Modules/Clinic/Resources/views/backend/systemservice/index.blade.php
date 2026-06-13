@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
    {{ __($module_title) }}
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if (auth()->user()->can('edit_system_service') || auth()->user()->can('delete_system_service'))
                <x-backend.quick-action url="{{ route('backend.system-service.bulk_action') }}">
                    <div>
                        <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            @can('edit_system_service')
                                <option value="change-status">{{ __('messages.status') }}</option>
                                <option value="change-featured">{{ __('messages.featured') }}</option>
                            @endcan
                            @can('delete_system_service')
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
                    <div class="select-featured d-none quick-action-field" id="change-featured-action">
                        <select name="featured" class="select2 form-select" id="featured" style="width:100%">
                            <option value="" selected>{{ __('messages.select_featured') }}</option>
                            <option value="1">{{ __('messages.yes') }}</option>
                            <option value="0">{{ __('messages.no') }}</option>
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
            <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                <i class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}
            </button>
            <!--
            @hasPermission('add_system_service')
            <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __('service.singular_title') }}">
                {{ __('messages.new') }}
            </x-buttons.offcanvas>
            @endhasPermission
            -->
            <!-- Offcanvas Trigger Button -->
            <button type="button"
                    class="btn d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                    style="background-color: #5670CC; color: white; font-weight: 500;"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#form-offcanvas"
                    id="create-service-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none"
                    viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <circle cx="12" cy="12" r="9" stroke="white"/>
                    <path d="M12 8v8M8 12h8" stroke-linecap="round"/>
                </svg>
                {{ __('messages.new') }}
            </button>
            <!-- Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvas-label" style="height: 100vh; max-width: 750px; width: 100vw; overflow: hidden;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="form-offcanvas-label">
                        {{ __('messages.create') }} {{ __('service.singular_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    @include('clinic::backend.systemservice.form')
                </div>
            </div>
        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive"></table>
</div>
{{-- 
<div data-render="app">
    <system-service-offcanvas custom-data="{{ isset($data) ? json_encode($data) : '{}' }}" create-title="{{ __('messages.create') }} {{ __('service.singular_title') }}" default-image="{{ default_file_url() }}" edit-title="{{ __('messages.edit') }} {{ __('service.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
    </system-service-offcanvas>
</div>
--}}

<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
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
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
    <div class="form-group custom-range">
        <div class="filter-slider slider-secondary"></div>
    </div>
</x-backend.advance-filter>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<style>
/* Disable sort icons for Action column */
#datatable th.no-sort.sorting::before,
#datatable th.no-sort.sorting::after,
#datatable th.no-sort.sorting_asc::before,
#datatable th.no-sort.sorting_asc::after,
#datatable th.no-sort.sorting_desc::before,
#datatable th.no-sort.sorting_desc::after {
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
            title: `{{ __('service.lbl_name') }}`
        },
        {
            data: 'category_id',
            name: 'category_id',
            title: `{{ __('service.lbl_category_id') }}`
        },
        // {
        //     data: 'subcategory_id',
        //     name: 'subcategory_id',
        //     title: `{{ __('category.parent_category') }}`
        // },
        {
            data: 'featured',
            name: 'featured',
            orderable: false,
            searchable: false,
            title: `{{ __('messages.featured') }}`,
            width: '5%'
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: true,
            title: `{{ __('service.lbl_status') }}`,
            width: '5%'
        },
        {
            data: 'updated_at',
            name: 'updated_at',
            title: `{{ __('service.lbl_update_at') }}`,
            orderable: true,
            visible: false,
        },
    ];

    const actionColumn = [
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'no-sort',
            title: `{{ __('service.lbl_action') }}`,
            width: '5%'
        }
    ];

    const customFieldColumns = JSON.parse(@json($columns));

    let finalColumns = [
        ...columns,
        ...customFieldColumns,
        ...actionColumn
    ];

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for all select fields
        if (window.$) {
            // Quick action select
            $('#quick-action-type').select2({
                placeholder: "{{ __('messages.no_action') }}",
                allowClear: false,
                minimumResultsForSearch: -1
            });
            
            // Status select in quick action
            $('#status').select2({
                placeholder: "{{ __('messages.select_status') }}",
                allowClear: false,
                minimumResultsForSearch: -1
            });
            
            // Featured select in quick action
            $('#featured').select2({
                placeholder: "{{ __('messages.select_featured') }}",
                allowClear: false,
                minimumResultsForSearch: -1
            });
            
            // Column status filter
            $('#column_status').select2({
                placeholder: "{{ __('messages.all') }}",
                allowClear: false,
                minimumResultsForSearch: -1
            });
            
            // Category filter in advance filter
            $('#column_category').select2({
                placeholder: "{{ __('service.all') }} {{ __('service.lbl_category') }}",
                allowClear: false,
                dropdownParent: $('.offcanvas.show').length ? $('.offcanvas.show') : $('body')
            });
            
            // Subcategory filter in advance filter
            $('#column_subcategory').select2({
                placeholder: "{{ __('service.all') }} {{ __('service.lbl_subcategory') }}",
                allowClear: false,
                dropdownParent: $('.offcanvas.show').length ? $('.offcanvas.show') : $('body')
            });
        }
        
        initDatatable({
            url: '{{ route("backend.system-service.index_data") }}',
            finalColumns,
            orderColumn: [
                [6, "desc"]
            ],
            advanceFilter: () => {
                return {
                    category_id: $('#column_category').val(),
                    sub_category_id: $('#column_subcategory').val(),
                }
            }
        });

        $('#reset-filter').on('click', function() {
            $('#column_category').val('').trigger('change');
            $('#column_subcategory').val('').trigger('change');
            window.renderedDataTable.ajax.reload(null, false);
        });
    });

    $('#column_category').on('change', function() {
        var selectedCategoryId = $(this).val();
        var subcategoryGroup = $('#subcategory-group');
        var subcategorySelect = $('#column_subcategory');

        subcategorySelect.html('<option value="">{{ __("service.all") }} {{ __("service.lbl_subcategory") }}</option>').trigger('change');

        if (selectedCategoryId !== "") {
            var subcategories = {!! $subcategories->toJson() !!};
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
                subcategorySelect.trigger('change');
            } else {
                subcategoryGroup.addClass('d-none');
            }
        } else {
            subcategoryGroup.addClass('d-none');
        }
    });

    function resetQuickAction() {
        const actionValue = $('#quick-action-type').val();
        if (actionValue !== '') {
            $('#quick-action-apply').removeAttr('disabled');
            if (actionValue === 'change-status') {
                $('.quick-action-field').addClass('d-none');
                $('#change-status-action').removeClass('d-none');
            } else if (actionValue === 'change-featured') {
                $('.quick-action-field').addClass('d-none');
                $('#change-featured-action').removeClass('d-none');
            } else {
                $('.quick-action-field').addClass('d-none');
            }
        } else {
            $('#quick-action-apply').attr('disabled', true);
            $('.quick-action-field').addClass('d-none');
        }
    }

    $('#quick-action-type').change(function() {
        resetQuickAction();
    });

    document.addEventListener('DOMContentLoaded', function() {
        var type = '{{ isset($type) ? $type : '' }}';
        if (type === 'system_service') {
            var myOffcanvas = document.getElementById('form-offcanvas');
            var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
            bsOffcanvas.show();
        }
    });
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const offcanvasEl = document.getElementById('form-offcanvas');
    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);

    $('#system-service-form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const submitButton = form.find('[type="submit"]');
        const url = form.attr('action');
        const data = new FormData(this);

        submitButton.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || 'Saved successfully!');
                    form[0].reset();
                    $('#preview-image').attr('src', '');
                    $('.text-danger').empty();

                    offcanvas.hide();

                    // Reload DataTable
                    if (typeof window.renderedDataTable?.ajax?.reload === 'function') {
                        window.renderedDataTable.ajax.reload(null, false);
                    }
                } else {
                    toastr.error(response.message || 'Something went wrong');
                }
            },
            error: function (xhr) {
                $('.text-danger').empty();

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON?.errors || {};
                    $.each(errors, function (field, messages) {
                        $(`#error-${field}`).html(messages[0]);
                    });
                }

                let msg = xhr.responseJSON?.message || 'Validation error';
                toastr.error(msg);
            },
            complete: function () {
                submitButton.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush