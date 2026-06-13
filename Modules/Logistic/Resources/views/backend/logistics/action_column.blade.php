

<div class="d-flex gap-3 align-items-center">
  <!-- <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password' data-assign-event='employee_assign' class='btn text-info p-0 fs-5 rounded text-nowrap' data-bs-toggle="tooltip" title="Change Password"><i class="ph ph-key"></i></button> -->
  @hasPermission('edit_logistics')
          <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
          @endhasPermission
          @hasPermission('delete_logistics')
          <a href="{{route("backend.logistics.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
          @endhasPermission
  </div>



