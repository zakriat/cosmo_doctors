<div class="text-end d-flex gap-3 align-items-center">

@if($data->payment_status === 0)
<button type="button" class="btn text-success p-0 fs-5" data-crud-id="{{ $data->id }}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i> </button>
@endif
@if($data->payment_status == 1)
    <a href="{{ route('backend.billing-record.billing_detail', ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.invoice_detail') }}">
    <i class="ph ph-file-pdf"></i>
    </a>
@endif
@if($data->encounter_id !=null)
<a href="{{ route('backend.encounter.encounter-detail-page', ['id' => $data->patientEncounter->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('appointment.patient_encounter') }}"><i class="icon ph ph-squares-four align-middle"></i></a>
    {{-- <button type='button' data-assign-module="{{$data->encounter_id}}" data-assign-target='#patient-encounter-offcanvas' data-assign-event='patient-dashboard' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='Patient Encounter'><i class="icon ph ph-squares-four align-middle"></i></button> --}}
@endif
<!-- 
    <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax"  class='btn text-info p-0 fs-4'  data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="ph ph-plus"></i></a>
    <a href="{{ route('backend.appointments.view') }}" class="btn btn-icon text-danger p-0 fs-4" data-bs-placement="top" data-bs-toggle="tooltip" title="{{ __('messages.view') }}"><i class="ph ph-eye"></i></a>
    <a href="{{route("backend.appointments.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a> -->
</div>
