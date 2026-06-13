@extends('backend.layouts.app')

@section('title')
  {{ __($module_title) }}
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
<div class="table-content mb-5">
  <x-backend.section-header>
    <div class="d-flex flex-wrap gap-3">
      @if(auth()->user()->can('edit_clinic_nurse_list') || auth()->user()->can('delete_clinic_nurse_list'))
      <x-backend.quick-action url='{{route("backend.$module_name.bulk_action")}}'>
        <div class="">
          <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
            <option value="">{{ __('messages.no_action') }}</option>
            @can('edit_clinic_nurse_list')
            <option value="change-status">{{ __('messages.status') }}</option>
            @endcan
            @can('delete_clinic_nurse_list')
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
      <div class="input-group flex-nowrap">
        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
          aria-describedby="addon-wrapping">
      </div>
      @hasPermission('add_clinic_nurse_list')
        <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
      @endhasPermission
    </x-slot>
  </x-backend.section-header>
  <table id="datatable" class="table table-responsive">
  </table>
</div>

<div data-render="app">
  <nurse-offcanvas default-image="{{default_file_url()}}" create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __('nurse.singular_title') }}"
    edit-title="{{ __('messages.edit') }} {{ __('nurse.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
  </nurse-offcanvas>
  <nurse-change-password create-title="{{ __('messages.change_password') }}"></nurse-change-password>
</div>
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
        width: '0%',
        exportable: false,
        orderable: false,
        searchable: false,
      },
      {
          data: 'nurse_id',
          name: 'nurse_id',
          title: "{{__('customer.lbl_name')}}",
          orderable: true,
          searchable: true,
      },
      {
        data: 'mobile',
        name: 'mobile',
        orderable: false,
        searchable: true,
        title: "{{ __('customer.lbl_phone_number') }}"
      },
      {
          data: 'clinic_id',
          name: 'clinic_id',
          title: "{{__('clinic.singular_title')}}",
          orderable: false,
          searchable: false,
      },
      {
          data: 'specialization',
          name: 'specialization',
          title: "{{__('nurse.lbl_specialization')}}",
          orderable: false,
          searchable: false,
      },
      @if(auth()->user()->can('edit_clinic_nurse_list'))
      {
        data: 'status',
        name: 'status',
        orderable: false,
        searchable: true,
        title: "{{ __('customer.lbl_status') }}"
      },
      @endif
      {
        data: 'updated_at',
        name: 'updated_at',
        title: "{{ __('customer.lbl_update_at') }}",
        orderable: true,
        visible: false,
       },
    ]

    const actionColumn = [{
      data: 'action',
      name: 'action',
      orderable: false,
      searchable: false,
      title: "{{ __('customer.lbl_action') }}"
    }]

    const customFieldColumns = JSON.parse(@json($columns))

    let finalColumns = [
      ...columns,
      ...customFieldColumns,
      ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
      // Initialize Select2
      if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
          width: '100%'
        });
      }

      initDatatable({
        url: '{{ route("backend.$module_name.index_data") }}',
        finalColumns,
        orderColumn: [[ 4, "desc" ]],
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

    $(document).on('update_quick_action', function() {
      // resetActionButtons()
    })
</script>
@endpush