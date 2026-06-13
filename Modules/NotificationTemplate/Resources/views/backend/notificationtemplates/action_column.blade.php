<div class="d-flex gap-3 align-items-center">
  @hasPermission('edit_notification_template')
    <a href="{{route("backend.notification-templates.edit", $data->id)}}" class="btn text-primary p-0 fs-5" data-bs-toggle="tooltip" title="{{ __('messages.edit') }} "> <i class="ph ph-pencil-simple-line"></i></a>
  @endhasPermission
  {{-- @hasPermission('delete_notification_template')
    <a href="{{route("backend.notification-templates.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
  @endhasPermission --}}

</div>
