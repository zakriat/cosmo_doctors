<div class="d-flex gap-3 align-items-center">
 <button type='button' data-gallery-module="{{$data->id}}" data-gallery-target='#clinic-gallery-form' data-gallery-event='branch_gallery' class='btn btn-info-subtle btn-sm rounded text-nowrap' data-bs-toggle="tooltip" title="{{ __('clinic.gallery_for_clinic') }}"><i class="ph ph-image"></i></button>
 <button type="button" class="btn btn-primary--subtle btn-sm" data-crud-id="{{ $data->id }}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>


    <a href="{{ route('backend.clinics.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
        class="btn btn-danger-subtle btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
        data-bs-toggle="tooltip" title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
        <i class="ph ph-trash"></i></a>
</div>
