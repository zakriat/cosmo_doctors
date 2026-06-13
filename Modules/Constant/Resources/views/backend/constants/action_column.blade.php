<div class="text-end d-flex gap-3 align-items-center">
    @hasPermission('edit_constant')
        <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
    @endhasPermission
    <a href="{{route("backend.$module_name.show", $data->id)}}" class="btn text-succes p-0 fs-5" data-bs-toggle="tooltip" title="{{__('labels.backend.show')}}"><i class="ph ph-eye"></i></a>
    @hasPermission('delete_constant')
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn btn-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
    @endhasPermission
</div>
