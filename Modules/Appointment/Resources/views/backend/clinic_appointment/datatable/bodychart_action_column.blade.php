<div class="text-end d-flex gap-3 align-items-center">

<a href="{{ $data['file_url'] }}" class="btn text-primary p-0 fs-5"
                                    target="_blank">
                                    <i class="ph ph-eye align-middle"></i>
                                </a>


    @if($data->patient_encounter && $data->patient_encounter->status == 1)
    <a href="{{route("backend.bodychart.editbodychartview", $data->id)}}" class="btn text-success p-0 fs-4" data-crud-id="{{ $data->id }}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i> </a>

    <a href="{{route("backend.bodychart.bodychartdestroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>

    @endif
</div>

