@extends('backend.layouts.app')

@section('title')
   {{ __($module_title) }}
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/clinic/style.css') }}">
@endpush

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->user()->can('edit_clinics_category') || auth()->user()->can('delete_clinics_category'))
            <x-backend.quick-action url='{{route("backend.category.bulk_action")}}'>
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('edit_clinics_category')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        <option value="change-featured">{{ __('messages.featured') }}</option>
                        @endcan
                        @can('delete_clinics_category')
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
                {{-- <button type="button" class="btn btn-secondary" data-modal="import">--}}
                {{-- <i class="fa-solid fa-upload"></i> Import--}}
                {{-- </button>--}}
            </div>
        </div>
        <x-slot name="toolbar">
            <div>
                <div class="datatable-filter" style="width: 100%; display: inline-block;">
                    {{$filter['status']}}
                    <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width:100%">
                        <option value="" disabled selected>{{ __('messages.change_status') }}</option>
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>{{ __('messages.active') }}</option>
                        <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>{{ __('messages.inactive') }}</option>
                    </select>
                </div>
            </div>
            <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
            </div>
            <!-- <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button> -->
            @hasPermission('add_clinics_category')
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1" onclick="createNewCategory()"><i class="ph ph-plus-circle"></i> {{ __('messages.new') }} </button>
            @endhasPermission

        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
    @include('clinic::backend.categories.clinic_category_offcanvas', ['offcanvasData' => null])
<div data-render="app">
</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    

    <div class="form-group datatable-filter">
        <label class="form-label" for="form-label"> {{ __('category.lbl_category') }}</label>
        <select id="clinic_category" name="clinic_category" data-filter="select" class="select2 form-select" data-ajax--url="{{ route('backend.get_search_data', ['type' => 'clinic_category']) }}" data-ajax--cache="true" data-placeholder="{{ __('category.lbl_category') }}">
        </select>
    </div>

   {{-- @if($parentcategory->isNotEmpty())
     <div class="form-group datatable-filter" id="subcategory-group">
        <label class="form-label" for="column_subcategory">{{ __('service.lbl_subcategory') }}</label>
        <select name="column_subcategory" id="column_subcategory" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{ __('service.lbl_subcategory') }}</option>
            @foreach ($parentcategory as $parentcategory)
            <option value="{{ $parentcategory->id }}">{{ $parentcategory->name }}</option>
            @endforeach
        </select>
    </div>
    @endif --}}
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
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
            width: '2%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        {
            data: 'name',
            name: 'name',
            title: "{{ __('category.lbl_name') }}",
            width: '15%'
        },

        {
            data: 'parent_id',
            name: 'parent_id',
            title: "{{ __('category.parent_category') }}",
            width: '15%'
        },

        {
            data: 'featured',
            name: 'featured',
            orderable: true,
            searchable: false,
            title: "{{ __('messages.featured') }}",
            width: '5%'
        },

        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false,
            title: "{{ __('category.lbl_status') }}",
            width: '5%'
        },
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
        title: "{{ __('category.lbl_action') }}",
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
                placeholder: '{{ __("messages.no_action") }}'
            });
            
            // Status dropdown inside bulk action
            $('#status').select2({ 
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: '{{ __("messages.select_status") }}'
            });
            
            // Featured dropdown inside bulk action
            $('#featured').select2({ 
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: '{{ __("messages.select_featured") }}'
            });
            
            // Status filter dropdown
            $('#column_status').select2({ 
                width: '100%',
                minimumResultsForSearch: Infinity, // Disable search for small dropdown
                placeholder: '{{ __("messages.change_status") }}'
            });
            
            // Clinic category dropdown with AJAX
            $('#clinic_category').select2({
                width: '100%',
                allowClear: true,
                minimumResultsForSearch: 0, // Enable search
                ajax: {
                    url: '{{ route("backend.get_search_data", ["type" => "clinic_category"]) }}',
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
                placeholder: '{{ __("category.lbl_category") }}',
                dropdownParent: $('#offcanvasExample')
            });
        }
        
        initDatatable({
            url: '{{ route("backend.$module_name.index_data") }}',
            finalColumns,
            orderColumn: [
                [5, "desc"]
            ],
            advanceFilter: () => {
                return {
                    parent_category: $('#column_subcategory').val(),
                    vendor_id: $('#vendor').val(),

                    clinic_category: $('#clinic_category').val(),

                };
            }
        });
        $('#reset-filter').on('click', function(e) {
            $('#column_subcategory').val('');
            $('#clinic_category').val('');
            window.renderedDataTable.ajax.reload(null, false);
        });
    })


    const formOffcanvas = document.getElementById('clinic-category-offcanvas')
    const instance = bootstrap.Offcanvas.getOrCreateInstance(formOffcanvas)

    $(document).on('click', '[data-crud-id]', function() {
        setEditID($(this).attr('data-crud-id'), $(this).attr('data-parent-id'))
    })

    function setEditID(id, parent_id) {
        if (id !== '' || parent_id !== '') {
            const idEvent = new CustomEvent('crud_change_id', {
                detail: {
                    form_id: id,
                    parent_id: parent_id
                }
            })
            document.dispatchEvent(idEvent)
        } else {
            removeEditID()
        }
        instance.show()
    }

    function removeEditID() {
        const idEvent = new CustomEvent('crud_change_id', {
            detail: {
                form_id: 0,
                parent_id: null
            }
        })
        document.dispatchEvent(idEvent)
    }

    formOffcanvas?.addEventListener('hidden.bs.offcanvas', event => {
        removeEditID()
        // Let the offcanvas's own resetForm function handle the reset
        // to avoid conflicts with select2
    })


    function resetQuickAction() {
        const actionValue = $('#quick-action-type').val();
        if (actionValue != '') {
            $('#quick-action-apply').removeAttr('disabled');

            if (actionValue == 'change-status') {
                $('.quick-action-field').addClass('d-none');
                $('#change-status-action').removeClass('d-none');
            } else if (actionValue == 'change-featured') {

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
        resetQuickAction()
    });

    $(document).on('update_quick_action', function() {
        // resetActionButtons()
    })
    document.addEventListener('DOMContentLoaded', function() {
        var type = '<?php echo isset($type) ? $type : ''; ?>';
        var data = '<?php echo isset($data) ? json_encode($data) : ''; ?>';
        console.log(type === 'category')
        if (type === 'category') {
            var myOffcanvas = document.getElementById('clinic-category-offcanvas')
            var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
            bsOffcanvas.show()
        } else {

        }
    });

const baseUrl = "{{ url('/') }}";
    function editCategory(id, parentId) {
        if (id > 0) {
            // Use the offcanvas form's loadCategoryData function
            if (typeof loadCategoryData === 'function') {
                loadCategoryData(id);
                const offcanvas = document.getElementById('clinic-category-offcanvas');
                const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                bsOffcanvas.show();
            } else {
                // Fallback to direct fetch if loadCategoryData is not available
                fetch(baseUrl + '/app/category/' + id + '/edit')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            populateForm(data.data);
                            const offcanvas = document.getElementById('clinic-category-offcanvas');
                            const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                            bsOffcanvas.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching category data:', error);
                    });
            }
        }
    }

    function populateForm(categoryData) {
        // Update form action for update
        const form = document.getElementById('clinic-category-form');
        form.action = `{{ route('backend.category.update', '') }}/${categoryData.id}`;
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        if (categoryData.name) {
            const nameInput = form.querySelector('input[name="name"]');
            if (nameInput) nameInput.value = categoryData.name;
        }
        
        if (categoryData.description) {
            const descInput = form.querySelector('textarea[name="description"]');
            if (descInput) descInput.value = categoryData.description;
        }
        
        if (categoryData.parent_id) {
            const parentSelect = form.querySelector('select[name="parent_id"]');
            if (parentSelect) parentSelect.value = categoryData.parent_id;
        }
        
        if (categoryData.featured) {
            const featuredInput = form.querySelector('input[name="featured"]');
            if (featuredInput) featuredInput.checked = categoryData.featured == 1;
        }
        
        if (categoryData.status) {
            const statusInput = form.querySelector('input[name="status"]');
            if (statusInput) statusInput.checked = categoryData.status == 1;
        }
        if (categoryData.file_url) {
            const imgPreview = form.querySelector('.avatar-preview img');
            if (imgPreview) imgPreview.src = categoryData.file_url;
        }
        const titleElement = form.querySelector('.offcanvas-title');
        if (titleElement) {
            titleElement.textContent = categoryData.parent_id ? '{{ __("clinic.edit_nested_category") }}' : '{{ __("clinic.edit_category") }}';
        }
    }

    // Function to create new category
    function createNewCategory() {
        const form = document.getElementById('clinic-category-form');
        form.action = '{{ route("backend.category.store") }}';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        form.reset();
        const imgPreview = form.querySelector('.avatar-preview img');
        if (imgPreview) imgPreview.src = '/path/to/default-avatar.jpg';
        const titleElement = form.querySelector('.offcanvas-title');
        if (titleElement) {
            titleElement.textContent = '{{ __("clinic.create_new_category") }}';
        }
        const offcanvas = document.getElementById('clinic-category-offcanvas');
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
        bsOffcanvas.show();
    }
</script>
@endpush