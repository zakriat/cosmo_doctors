<div class="text-end d-flex gap-3 align-items-center">
    <button type="button" class="btn btn-icon text-primary p-0 fs-4" data-bs-toggle="tooltip" title="View Full Details" onclick="viewAppointmentDetails({{ $data->id }})">
        <i class="ph ph-eye"></i>
    </button>
    <a href="{{ route('backend.appointments.view') }}" class="btn btn-icon text-info p-0 fs-4" data-bs-placement="top" data-bs-toggle="tooltip" title="{{ __('messages.view') }}"><i class="ph ph-file-text"></i></a>
    <a href="{{route("backend.appointments.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
</div>
<!-- Updated: {{ now() }} -->
