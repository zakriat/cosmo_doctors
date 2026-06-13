<div class="text-end d-flex gap-3 align-items-center">
<!-- <button type="button" class="btn btn-success-subtle btn-sm rounded text-nowrap font-size-body" data-crud-id="{{ $data->id }}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line align-middle"></i></button>
<button type='button' data-assign-module="{{$data->id}}" data-assign-target='#patient-encounter-offcanvas' data-assign-event='patient-dashboard' class=' rounded text-nowrap font-size-body' data-bs-toggle='tooltip' title='Patient Encounter'><i class="icon ph ph-squares-four align-middle"></i></button>

    <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax"  class='btn btn-info-subtle btn-sm rounded text-nowrap font-size-body'  data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="ph ph-plus align-middle"></i></a> -->
    {{-- <button type='button' data-assign-module="{{$data->id}}" data-assign-target='#encounter-template-offcanvas' data-assign-event='patient-dashboard' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='Encounter Dashbaord'><i class="icon ph ph-squares-four align-middle"></i></button> --}}

    <a href="{{ route('backend.encounter-template.template-detail', ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('appointment.patient_encounter') }}"><i class="icon ph ph-squares-four align-middle"></i></a>

    @hasPermission('delete_encounter_template')
    <a href="{{route("backend.encounter-template.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}"    data-confirm="{{ __('messages.are_you_sure?', ['form' => $data->name ?? __('Unknown'), 'module' => __('appointment.encounter_template')]) }}"><i class="ph ph-trash"></i></a>
    @endhasPermission
</div>
