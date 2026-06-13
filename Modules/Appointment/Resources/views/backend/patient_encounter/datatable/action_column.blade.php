<div class="text-end d-flex gap-3 align-items-center">
<button type="button" class="btn text-success p-0 fs-5" data-crud-id="{{ $data->id }}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line align-middle"></i></button>
{{-- <button type='button' data-assign-module="{{$data->id}}" data-assign-target='#patient-encounter-offcanvas' data-assign-event='patient-dashboard' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='Patient Encounter'><i class="icon ph ph-squares-four align-middle"></i></button> --}}



<a href="{{ route('backend.encounter.encounter-detail-page', ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('appointment.patient_encounter') }}"><i class="icon ph ph-squares-four align-middle"></i></a>

@if(setting('view_patient_soap') == 1)
    <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax"  class='btn text-info p-0 fs-5'  data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="ph ph-notepad align-middle"></i></a>
@endif
    <a href="{{route("backend.appointments.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->user->full_name ?? default_user_name()), 'module' => __('appointment.patient_encounter')]) }}"><i class="ph ph-trash align-middle"></i></a>


@if($customform)

    @foreach($customform as $form)

        @php
            $formdata=json_decode($form->formdata);
            $appointment_status= json_decode($form->appointment_status);

        @endphp

        {{--@if(in_array($data->clinic, $appointment_status) && $data->status !== null) --}}

            <button type="button"
                data-assign-target="#appointment-customform"
                data-assign-event="custom_form_assign"
                data-appointment-type="appointment"
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
