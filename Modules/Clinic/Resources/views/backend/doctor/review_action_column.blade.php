<div class="d-flex gap-3 align-items-center">
   
@hasPermission('delete_reviews')
        <a href="{{route('backend.doctor.destroy_review', $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="fs-4 text-danger" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->user->full_name) ?? __('Unknown'), 'module' => __('clinic.reviews')]) }}">  <i class="ph ph-trash align-middle"></i></a>
    @endhasPermission
</div>
