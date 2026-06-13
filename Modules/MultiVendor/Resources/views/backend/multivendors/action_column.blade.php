<div class="d-flex gap-3 align-items-center">
   
@hasPermission('customer_password')
<button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#vendor_change_password' data-assign-event='vendor_assign' class='btn text-info p-0 rounded text-nowrap fs-5' data-bs-toggle="tooltip" title="{{ __('messages.change_password') }}"><i class="ph ph-key"></i></button>
@endhasPermission

@hasPermission('edit_vendor_list')
        <button type="button"
    class="btn text-success p-0 fs-5 edit-vendor-btn"
    data-id="{{ $data->id }}"
    title="{{ __('messages.edit') }}"
    data-bs-toggle="tooltip">
    <i class="ph ph-pencil-simple-line"></i>
</button>
    @endhasPermission
    @hasPermission('delete_vendor_list')
         <a href="{{ route("backend.$module_name.destroy", $data->id) }}"
   id="delete-{{ $module_name }}-{{ $data->id }}"
   onclick="editClinic({{ $data->id }})"
   class="btn text-danger p-0 fs-5"
   data-type="ajax"
   data-method="DELETE"
   data-token="{{ csrf_token() }}"
   data-bs-toggle="tooltip"
   title="{{ __('messages.delete') }}"
   data-confirm="{{ __('messages.are_you_sure?', ['form' => $data->fullname ?? '', 'module' => __('clinic.clinic_admin')]) }}">
   <i class="ph ph-trash"></i>
</a>
    @endhasPermission
</div>



