<div class="text-end d-flex gap-3 align-items-center">
    <!-- <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax"  class='btn text-info p-0 fs-4'  data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="ph ph-plus"></i></a> -->

    <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#PatientList-Deatils-form'class='btn btn-icon text-danger p-0 fs-4' data-assign-event='patient-details' class='btn text-info p-0 fs-4' data-bs-toggle="tooltip" title="{{ __('clinic.view') }}">  <i class="ph ph-eye"></i></button>


    <a href="{{route("backend.appointments.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
</div>

