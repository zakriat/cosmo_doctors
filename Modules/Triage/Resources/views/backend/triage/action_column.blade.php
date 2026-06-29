<div class="d-flex gap-2 align-items-center">
    @hasPermission('view_triage_queue')
    <a href="{{ route('backend.triage.show', $data->id) }}"
       class="btn btn-primary btn-sm"
       data-bs-toggle="tooltip"
       title="{{ __('messages.open') }}">
        <i class="ph ph-arrow-square-out"></i>
    </a>
    @endhasPermission

    @if($data->status !== 'closed')
    @hasPermission('edit_triage')
    <button type="button"
            class="btn btn-primary btn-sm"
            onclick="closeTriage({{ $data->id }})"
            data-bs-toggle="tooltip"
            title="{{ __('triage.status_closed') }}">
        <i class="ph ph-check-circle"></i>
    </button>
    @endhasPermission
    @endif
</div>

@once
@push('after-scripts')
<script>
function closeTriage(id) {
    if (!confirm('{{ __("messages.action_warning_message") }}')) return;
    fetch(`{{ url('app/triage') }}/${id}/close`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(res => {
        if (res.status) {
            window.successSnackbar(res.message);
            if (window.renderedDataTable) window.renderedDataTable.ajax.reload();
        } else {
            window.errorSnackbar(res.message);
        }
    });
}
</script>
@endpush
@endonce
