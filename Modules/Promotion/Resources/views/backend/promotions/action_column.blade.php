<div class="d-flex gap-3 align-items-center">
    <a href="{{route("backend.coupons-view", $data->id)}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-bs-toggle="tooltip"> <i class="fa-solid fa-table"></i></a>
    @hasPermission('edit_promotions')
  <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
  @endhasPermission
  @hasPermission('delete_promotions')
      <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>
      @endhasPermission
</div>
