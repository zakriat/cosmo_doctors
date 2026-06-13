<div class="d-flex gap-3 align-items-center">
    @hasPermission('edit_specialization') 
      <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" data-parent-id="{{$data->parent_id}}" data-bs-toggle="tooltip" title="{{__('Edit')}}"> <i class="ph ph-pencil-simple-line"></i> </button>
    @endhasPermission 
    @hasPermission('delete_specialization') 
      <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('Delete')}}"  data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->name) ?? __('Unknown'), 'module' => __('clinic.specialization')])  }}"> <i class="ph ph-trash"></i> </a>
    @endhasPermission 
</div>