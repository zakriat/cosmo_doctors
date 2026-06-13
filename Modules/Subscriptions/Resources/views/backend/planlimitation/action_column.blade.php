<div class="d-flex gap-3 align-items-center">
    @hasPermission('edit_plan_limitation')
    <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
    @endhasPermission
    @hasPermission('delete_plan_limitation')
    <a href="{{route('backend.locations.destroy', $data->id)}}" id="delete-locations-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
@endhasPermission
</div>
