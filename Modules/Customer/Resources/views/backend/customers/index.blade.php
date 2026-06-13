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
        @if(auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer'))
        <x-backend.quick-action url='{{route("backend.$module_name.bulk_action")}}'>
          <div class="">
            <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
              <option value="">{{ __('messages.no_action') }}</option>
              @can('edit_customer')
              <option value="change-status">{{ __('messages.status') }}</option>
              @endcan
              @can('delete_customer')
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
           <button type="button" class="btn btn-primary" data-modal="import">
           <i class="ph ph-export me-1"></i>{{ __('messages.import') }}
         </button>
        </div>
      </div>

      <x-slot name="toolbar">
        <div class="input-group flex-nowrap">
          <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
            aria-describedby="addon-wrapping">
        </div>
        <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>

        @hasPermission('add_customer')
          <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
        @endhasPermission
      </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">
  <customer-offcanvas default-image="{{user_avatar()}}" create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __('customer.singular_title') }}"
    edit-title="{{ __('messages.edit') }} {{ __('customer.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
  </customer-offcanvas>
  <change-password create-title="{{ __('messages.change_password') }}"></change-password>
  <customer-details-offcanvas></customer-details-offcanvas>
  <add-other-patient-offcanvas>  </add-other-patient-offcanvas>
  <customform-offcanvas>
  </customform-offcanvas>
</div>

<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    <!-- <div class="form-group datatable-filter">
        <label class="form-label" for="patient_name">{{__('customer.singular_title')}}</label>
        <select name="patient_name" id="patient_name" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{__('customer.singular_title')}}</option>
            @foreach ($patientNames as $patientName)
                <option value="{{ $patientName}}">{{ $patientName}}</option>
            @endforeach
        </select>
    </div> -->

    <div class="form-group datatable-filter">
        <label class="form-label" for="email">{{__('customer.lbl_Email')}}</label>
        <select name="email" id="email" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{__('customer.lbl_Email')}}</option>
            @foreach ($email as $email)
                <option value="{{ $email }}">{{ $email }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group datatable-filter">
        <label class="form-label" for="contact">{{__('customer.lbl_phone_number')}}</label>
        <select name="contact" id="contact" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{__('customer.lbl_phone_number')}}</option>
            @foreach ($contact as $contact)
                <option value="{{ $contact }}">{{ $contact }}</option>
            @endforeach
        </select>
    </div>

    <!-- Add Other Patient Filter -->
    <div class="form-group datatable-filter">
        <label class="form-label" for="other_patient">{{__('customer.other_patient')}}</label>
        <select name="other_patient" id="other_patient" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{__('customer.other_patient')}}</option>
            @foreach ($otherPatients ?? [] as $otherPatient)
                <option value="{{ $otherPatient->id }}">
                    {{ $otherPatient->first_name }} {{ $otherPatient->last_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Add Status Filter -->
    <div class="form-group datatable-filter">
        <label class="form-label" for="column_status">{{__('customer.lbl_status')}}</label>
        <select name="column_status" id="column_status" class="select2 form-select" data-filter="select">
            <option value="">{{ __('service.all') }} {{__('customer.lbl_status')}}</option>
            <option value="1">{{ __('messages.active') }}</option>
            <option value="0">{{ __('messages.inactive') }}</option>
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
<script src="{{ mix('modules/customer/script.js') }}"></script>
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
          data: 'customer_id',
          name: 'customer_id',
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
      @if(auth()->user()->can('edit_customer'))
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
        advanceFilter: () => {
               
            return {
                patient_name: $('#patient_name').val(),
                contact : $('#contact').val(),
                email : $('#email').val(),
                other_patient: $('#other_patient').val(),
                column_status: $('#column_status').val(),
            }
        }
      });
      $('#reset-filter').on('click', function(e) {
          $('#patient_name').val('');
          $('#contact').val('');
          $('#email').val('');
          $('#other_patient').val('');
          $('#column_status').val('');
          window.renderedDataTable.ajax.reload(null, false);
      });
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
