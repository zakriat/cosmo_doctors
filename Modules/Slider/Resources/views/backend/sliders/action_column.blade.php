<div class="d-flex gap-3 align-items-center">
  <!-- <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password' data-assign-event='employee_assign' class='btn text-info p-0 fs-5 rounded text-nowrap' data-bs-toggle="tooltip" title="Change Password"><i class="ph ph-key"></i></button> -->
      @hasPermission('edit_app_banner')
      <a href="javascript:void(0);"
   class="btn text-primary p-0 fs-5 edit-slider"
   data-url="{{ route('backend.app-banners.edit', $data->id) }}">
    <i class="ph ph-pencil-simple-line"></i>
</a>
      @endhasPermission
      @hasPermission('delete_app_banner')
          <a href="{{route("backend.app-banners.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->name) ?? __('Unknown'), 'module' => __('slider.title')])  }}"> <i class="ph ph-trash"></i></a>
      @endhasPermission
  </div>



