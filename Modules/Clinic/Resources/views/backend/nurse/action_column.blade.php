@php
$isDemo = env('IS_DEMO', false);
@endphp

<div class="d-flex gap-2 align-items-center">
    @hasPermission('edit_clinic_nurse_list')
    <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} {{ __('nurse.singular_title') }}" data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endhasPermission
    
    <button type="button" class="btn text-primary p-0 fs-5" data-assign-module="nurse" data-assign-target="#nurse-change-password" data-assign-event="nurse_change_password" data-nurse-id="{{$data->id}}" title="{{ __('messages.change_password') }}" data-bs-toggle="tooltip"> <i class="fa-solid fa-key"></i></button>
    
    @hasPermission('delete_clinic_nurse_list')
    @if(!$isDemo)
    <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-primary p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm-yes="{{__('messages.action_warning_message')}}">
        <i class="fa-solid fa-trash"></i>
    </a>
    @endif
    @endhasPermission
</div>