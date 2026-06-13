@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/clinic/style.css') }}">
@endpush

@section('content')
    <div class="table-content mb-5">
        <x-backend.section-header>
          <div class="d-flex flex-wrap gap-3">
            <x-backend.quick-action url='{{route("backend.category.bulk_action")}}'>
              <div class="">
                <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                    <option value="">{{ __('messages.no_action') }}</option>
                    @can('edit_{{module}}')
                    <option value="change-status">{{ __('messages.status') }}</option>
                    @endcan
                    <option value="delete">{{ __('messages.delete') }}</option>
                </select>
              </div>
              <div class="select-status d-none quick-action-field" id="change-status-action">
                  <select name="status" class="select2 form-select" id="status" style="width:100%">
                    <option value="1">{{ __('messages.active') }}</option>
                    <option value="0">{{ __('messages.inactive') }}</option>
                  </select>
              </div>
            </x-backend.quick-action>
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
                      <select name="column_category" id="column_category" class="select2 form-select" data-filter="select" style="width: 100%">
                        <option value="">{{ __('category.all_categories') }}</option>
                        @foreach($categories as $category)
                          <option value="{{ $category->id }}" @if($category->id == $parentID) selected @endif>{{$category->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                <div>
                    <div class="datatable-filter">
                      <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width: 100%">
                        <option value="">{{__('messages.all')}}</option>
                        <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>{{ __('messages.inactive') }}</option>
                        <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>{{ __('messages.active') }}</option>
                      </select>
                    </div>
                  </div>

              <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
              </div>
              @hasPermission('add_clinics_category')
              <button type="button" class="btn btn-primary" onclick="createNewCategory()"><i class="fas fa-plus-circle"></i>{{ __('messages.create') }} {{ __('category.singular_title') }}</button>
            @endhasPermission
                <!-- <button class="btn btn-outline-primary btn-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="fa-solid fa-filter"></i></button> -->
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>

    <div data-render="app">
        <clinic-category-offcanvas
            :is-sub-category="true"
            default-image="{{default_file_url()}}"
            create-nested-title="{{ __('messages.create') }}  {{ __('category.sub_category') }}" edit-nested-title="{{ __('messages.edit') }} {{ __('category.sub_category') }}"
            :category-id="{{ isset($category->id) ? $category->id : null }}"
            ></clinic-category-offcanvas>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/clinic/script.js') }}"></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>


    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript">

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
            { data: 'image', name: 'image', title: "{{ __('category.lbl_image') }}" , width: '5%', orderable: false, },
            { data: 'name', name: 'name',  title: "{{ __('category.lbl_name') }}" ,width: '15%' },
            { data: 'mainCategory.name', name: 'mainCategory.name', title: "{{ __('category.lbl_category') }}" ,width: '15%', searchable: false },
            { data: 'created_at', name: 'created_at',  title: "{{ __('category.lbl_created_at') }}",width: '15%' },
            { data: 'updated_at', name: 'updated_at', title: "{{ __('category.lbl_updated_at') }}" ,width: '15%' },
            { data: 'status', name: 'status',  searchable: true, title: "{{ __('category.lbl_status') }}" ,width: '5%' },
        ]

        const actionColumn = [
            { data: 'action', name: 'action', orderable: false, searchable: false, title: "{{ __('category.lbl_action') }}" ,width: '5%'}
        ]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.category.index_nested_data") }}',
                finalColumns,
                orderColumn: [[ 5, "desc" ]],
                advanceFilter: () => {
                  return {
                    column_category: $('#column_category').val()
                  }
                }
            })
        })


        const formOffcanvas = document.getElementById('clinic-category-offcanvas')

        const instance = bootstrap.Offcanvas.getOrCreateInstance(formOffcanvas)

        // Function to edit category directly without using the event system
        function editCategory(id, parentId) {
            if (id > 0) {
                // Fetch category data
                fetch(`{{ route('backend.category.edit', '') }}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // Populate the form with the fetched data
                            populateForm(data.data);
                            // Show the offcanvas
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

        // Function to populate the form with category data
        function populateForm(categoryData) {
            // Update form action for update
            const form = document.getElementById('clinic-category-form');
            form.action = `{{ route('backend.category.update', '') }}/${categoryData.id}`;
            
            // Add method override for PUT
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

        formOffcanvas?.addEventListener('hidden.bs.offcanvas', event => {
            const form = document.getElementById('clinic-category-offcanvas');
            if (form) {
                form.reset();
                const imgPreview = form.querySelector('.avatar-preview img');
                if (imgPreview) imgPreview.src = '/path/to/default-avatar.jpg';
            }
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
