<div class="d-flex gap-3 align-items-center">
    <a href="{{ route('backend.customers.patient_detail', ['id' => $data->id]) }}" 
        class="btn p-0 btn-icon text-danger text-primary border-0 bg-transparent text-nowrap" 
        data-bs-toggle="tooltip" title="{{ __('messages.view') }}">
        <i class="ph ph-eye"></i>
     </a>
  
     <button type="button" 
        class="btn p-0 btn-icon text-success border-0 bg-transparent text-nowrap"
        data-assign-target="#addOtherPatientOffcanvas"
        data-assign-module="{{ $data->id }}"
        data-bs-toggle="tooltip" 
        data-assign-event='employee_assign'
        title="{{ __('customer.add_other_patient') }}">
        <i class="ph ph-user-plus"></i>
    </button>
    {{-- <button type='button' data-assign-module="{{$data->id}}" data-assign-target='#customerDetails-offcanvas' data-assign-event='customer-details' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='View'><i class="ph ph-eye"></i> --}}
@hasPermission('customer_password')
<button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password' data-assign-event='employee_assign' class='btn text-info p-0 rounded text-nowrap fs-5' data-bs-toggle="tooltip" title="{{ __('messages.change_password') }}"><i class="ph ph-key"></i></button>
@endhasPermission

    @hasPermission('edit_customer')
        <button type="button" class="btn text-success p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
    @endhasPermission
    @hasPermission('delete_customer')
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->full_name) ?? __('Unknown'), 'module' => __('clinic.patient')])  }}">  <i class="ph ph-trash"></i></a>
    @endhasPermission

    @if($customform)

        @foreach($customform as $form)

            @php
            $formdata=json_decode($form->formdata);

            @endphp

            {{--@if(in_array($data->clinic, $appointment_status) && $data->status !== null) --}}

            <button type="button"
            data-assign-target="#customform-offcanvas"
            data-assign-event="custom_form_assign"
            data-appointment-type="doctor"
            data-appointment-id="{{ $data->id }}"
            data-form-id="{{ $form->id }}"
            class="btn text-info p-0 fs-5"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ $formdata->form_title }}"
            onclick="dispatchCustomEvent(this)">
            <i class="icon ph ph-file align-middle"></i>
            </button>
            {{-- @endif --}}
        @endforeach
    @endif

</div>



