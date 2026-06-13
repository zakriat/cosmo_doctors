<div class="d-flex gap-3 align-items-center">
@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))
    @hasPermission('edit_request_service')
        @if($data->type == 'category')
            <a href="{{ route('backend.category.index', ['type' => 'category','id' => $data->id]) }}" data-bs-toggle="tooltip" title="Accept">
                <i class="ph ph-check-fat align-middle"></i>
            </a>
        @elseif($data->type == 'specialization')
            <a href="{{ route('backend.specializations.index', ['type' => 'specialization','id' => $data->id]) }}" data-bs-toggle="tooltip" title="Accept">
                <i class="ph ph-check-fat align-middle"></i>
            </a>
        @elseif($data->type == 'system_service')
            <a href="{{ route('backend.system-service.index', ['type' => 'system_service','id' => $data->id]) }}" data-bs-toggle="tooltip" title="Accept">
                <i class="ph ph-check-fat align-middle"></i>
            </a>
        @endif
    @endhasPermission
    @hasPermission('delete_request_service')
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="Reject" data-confirm="{{ __('messages.are_you_sure?', ['form' => $data->name ?? __('Unknown'), 'module' => __('service.request_title')]) }}"> <i class="ph ph-x align-middle"></i></a>
    @endhasPermission
@else
    --
@endif
</div>