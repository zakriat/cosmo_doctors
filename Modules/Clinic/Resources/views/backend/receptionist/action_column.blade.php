<div class="d-flex gap-3 align-items-center">
    {{-- <button type='button' data-assign-module="{{$data->id}}" data-assign-event='clinic-details' class='btn text-secondary p-0 fs-5' data-bs-toggle='tooltip' title='View'><i class="ph ph-eye align-middle"></i> --}}
@hasPermission('customer_password')
<button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#receptionist_change_password' data-assign-event='receptionist_assign' class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('messages.change_password') }}"><i class="ph ph-key"></i></button>
@endhasPermission

@hasPermission('edit_clinic_receptionist_list')
        <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
    @endhasPermission
    @hasPermission('delete_clinic_receptionist_list')
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->full_name) ?? __('Unknown'), 'module' => __('receptionist.singular_title')])  }}"> <i class="ph ph-trash"></i></a>
    @endhasPermission
</div>



