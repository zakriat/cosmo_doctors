@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
    <div class="table-content mb-3">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
              <x-backend.quick-action url='{{route("backend.specializations.bulk_action")}}'>

                <div class="">
                  <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                      <option value="">{{ __('messages.no_action') }}</option>
                      <option value="change-status">{{ __('messages.status') }}</option>
                      <option value="delete">{{ __('messages.delete') }}</option>
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
              <div>
                <button type="button" class="btn btn-primary" data-modal="export">
                <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>

              </div>

            </div>
            <x-slot name="toolbar">
              <div>
                  <div class="datatable-filter" style="width: 100%; display: inline-block;">
                      {{$filter['status']}}
                    <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width:100%">
                      <option value="">{{__('messages.all')}}</option>
                      <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>{{ __('messages.active') }}</option>
                      <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>{{ __('messages.inactive') }}</option>
                    </select>
                  </div>
                </div>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                  <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">

                </div>
                <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>
                @hasPermission('add_specialization')
                <x-buttons.offcanvas target='#system-service-category-form-offcanvas' title="{{ __('Create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
                @endhasPermission
            </x-slot>

        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>

    <div data-render="app">
    <system-service-category-form-offcanvas custom-data="{{ isset($data) ? json_encode($data) : '{}' }}"
    default-image="{{default_file_url()}}"
    create-title="{{ __('messages.create') }} {{ __($module_title) }}"
    edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"
    :customefield="{{ json_encode($customefield) }}">
        </system-service-category-form-offcanvas>
    </div>
    <x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    <div class="form-group datatable-filter">
        <label class="form-label" for="column_category">{{ __('service.lbl_category') }}</label>
        <select name="column_category" id="column_category" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.lbl_category') }}</option>

            @foreach ($systemcategory as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter> 
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/service/script.js') }}"></script>
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
                searchable: false
            },
            
            { data: 'name', name: 'name', title: `{{ __('category.lbl_name') }}`, width: '15%'},
            { data: 'parent_id', name: 'parent_id', title: `{{ __('category.lbl_category') }}`, width: '15%'},
            { data: 'status', name: 'status', orderable: true,  searchable: true, title: `{{ __('category.lbl_status') }}`,width: '5%'},
        ]

        const actionColumn = [
            { data: 'action', name: 'action', orderable: false, searchable: false, title: `{{ __('category.lbl_action') }}`, width: '5%'}
        ]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.specializations.index_data") }}',
                finalColumns,
                orderColumn: [[ 2, 'desc' ]],
                advanceFilter: () => {
                return {
                    category_name: $('#column_category').val()
                }
            }
        });
        $('#reset-filter').on('click', function(e) {
    $('#column_category').val('');
    window.renderedDataTable.ajax.reload(null, false);
});
});


        $(document).on('click', '[data-toggle="sub-category"]', function () {
            const categoryId = $(this).data('category-id')
		        const table = window.renderedDataTable;
            const tr = $(this).closest('tr');
            const row = table.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                tr.find('[data-icon="plus"]').show()
                tr.find('[data-icon="minus"]').hide()
                window.renderedDataTable['category_id_'+categoryId] = undefined
            } else {
                row.child(nestedTable(categoryId), 'bg-secondary-subtle p-3' ).show();
                ajaxNestedTable(categoryId)
                tr.addClass('shown');
                tr.find('[data-icon="plus"]').hide()
                tr.find('[data-icon="minus"]').show()
            }
        });

        function ajaxNestedTable(data_id){
          $.ajax({
                    type: 'get',
                    url: '{{ route("backend.specializations.index_nested")}}',
                    data: {'category_id': data_id},
                    success: function (data) {
                        $('#nested-table-'+data_id).html(data);
                        $('#nested-table-'+data_id).addClass('fadeIn-animation');
                        $('#nested-table-'+data_id).css('min-height', '9rem')
                    }
                });
        }
        function nestedTable(id) {
            // `d` is the original data object for the row
            return '<div id="nested-table-'+id+'" class="card  card-body mb-0"></div>';
		}

        const formOffcanvas = document.getElementById('system-service-category-form-offcanvas')
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

      document.addEventListener('DOMContentLoaded', function() {
        var type = '<?php echo isset($type) ? $type : ''; ?>';
        var data = '<?php echo isset($data) ? json_encode($data) : ''; ?>';
        if (type === 'specialization') {
            var myOffcanvas = document.getElementById('system-service-category-form-offcanvas')
            var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
            bsOffcanvas.show()

        }
    });

    </script>
@endpush
