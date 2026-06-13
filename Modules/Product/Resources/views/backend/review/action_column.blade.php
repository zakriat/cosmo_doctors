<div class="d-flex gap-3 align-items-center">

      <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.show') }} " data-bs-toggle="tooltip">  <i class="fa-solid fa-eye"></i></button>


      <a href="{{route('backend.reviews.destroy', $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash"></i></a>

</div>
