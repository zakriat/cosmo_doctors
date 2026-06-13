<div class="d-flex gap-3 align-items-center">
{{-- 
  @hasPermission('edit_clinics_service')
      <button type="button" class="btn text-success p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"><i class="ph ph-pencil-simple-line align-middle"></i></button>
  @endhasPermission --}}

  @hasPermission('edit_clinics_service')
  <button 
      type="button" 
      class="btn text-success p-0 fs-5 edit-service-btn" 
      data-crud-id="{{ $data->id }}" 
      title="{{ __('messages.edit') }}"
      data-bs-toggle="offcanvas" 
      data-bs-target="#createServiceForm"
  >
      <i class="ph ph-pencil-simple-line align-middle"></i>
  </button>
  @endhasPermission


  @hasPermission('delete_clinics_service')
     <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->name) ?? __('Unknown'), 'module' => __('service.title')])  }}"> 
 <i class="ph ph-trash align-middle"></i></a>
  @endhasPermission

</div>
