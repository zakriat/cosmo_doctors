<a href="{{ route('backend.backups.download', $backup['file_name']) }}" class="btn btn-primary m-1 btn-sm"
    data-bs-toggle="tooltip" title="{{ __('backup.download') }}"><i
        class="fas fa-cloud-download-alt"></i>&nbsp;{{ __('backup.download') }}</a>
@hasPermission('delete_backup')
    <a href="{{ route('backend.backups.delete', $backup['file_name']) }}" class="btn btn-danger m-1 btn-sm"
        data-method="DELETE" data-token="{{ csrf_token() }}" data-type="ajax"
        data-confirm="{{ __('messages.are_you_sure?', ['form' => $backup['file_name'], 'module' => __('backup.file')]) }}"
        data-bs-toggle="tooltip" title="{{ __('backup.delete') }}">
        <i class="fas fa-trash"></i>&nbsp;{{ __('backup.delete') }}
    </a>
@endhasPermission
