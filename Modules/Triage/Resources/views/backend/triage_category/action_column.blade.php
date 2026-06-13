<div class="d-flex gap-2 align-items-center">
    @hasPermission('edit_triage_category')
    <button type="button" class="btn btn-soft-primary btn-sm"
            onclick="editCategory({{ $data->id }})"
            data-bs-toggle="tooltip"
            title="{{ __('messages.edit') }}">
        <i class="fa-solid fa-pen-clip"></i>
    </button>
    @endhasPermission

    @hasPermission('delete_triage_category')
    <a href="{{ route('backend.triage-category.destroy', $data->id) }}"
       id="delete-{{ $module_name }}-{{ $data->id }}"
       class="btn btn-soft-danger btn-sm"
       data-type="ajax"
       data-method="DELETE"
       data-token="{{ csrf_token() }}"
       data-bs-toggle="tooltip"
       title="{{ __('messages.delete') }}"
       data-confirm-yes="{{ __('messages.action_warning_message') }}">
        <i class="fa-solid fa-trash-can"></i>
    </a>
    @endhasPermission
</div>
