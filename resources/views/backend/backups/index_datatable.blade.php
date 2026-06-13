@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="table-content mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="card-title mb-0">
                    <i class="{{ $module_icon ?? 'fas fa-archive' }}"></i> {{ __($module_title) }}
                </h4>
                <div class="small text-medium-emphasis">{{ __('messages.backup_management') }}</div>
            </div>
            <div class="btn-toolbar d-block" role="toolbar" aria-label="Toolbar with buttons">
                @hasPermission('add_backup')
                    <a id="create-backup-btn" href="{{ route("backend.$module_name.create-db-bk") }}" class="btn btn-outline-success m-1" data-bs-toggle="tooltip"
                        data-loader="true" data-spinner-id="create-backup-spinner" data-icon-id="backup-icon" data-text-id="backup-btn-text" title="{{ __('messages.create_backup') }}" onclick="handleLoader(event)">
                        <span id="create-backup-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="fas fa-plus-circle me-1" id="backup-icon"></i>
                        <span id="backup-btn-text">{{ __('messages.create_backup') }}</span>
                    </a>
                    <a id="create-new-backup-btn" href="{{ route("backend.$module_name.create") }}" class="btn btn-outline-success m-1" data-bs-toggle="tooltip"
                        data-loader="true" data-spinner-id="create-new-backup-spinner" data-icon-id="new-backup-icon"
                        data-text-id="new-backup-btn-text" title="{{ __('messages.create_new_backup') }}" onclick="handleLoader(event)">
                        <span id="create-new-backup-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="fas fa-plus-circle me-1" id="new-backup-icon"></i>
                        <span id="new-backup-btn-text">{{ __('messages.create_new_backup') }}</span>
                    </a>
                @endhasPermission
            </div>
        </div>
        {{-- <table id="datatable" class="table table-bordered table-hover table-responsive"></table> --}}

        <table id="datatable" class="table table-responsive" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('backup.file') }}</th>
                    <th>{{ __('backup.size') }}</th>
                    <th>{{ __('report.lbl_date') }}</th>
                    <th>{{ __('backup.age') }}</th>
                    <th class="text-end">{{ __('service.lbl_action') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript">
        const columns = [{
                data: 'index',
                name: 'index',
                title: '#',
                orderable: false
            },
            {
                data: 'file_name',
                name: 'file_name',
                title: '{{ __('backup.file') }}',
                orderable: false
            },
            {
                data: 'file_size',
                name: 'file_size',
                title: '{{ __('backup.size') }}',
                orderable: false
            },
            {
                data: 'date_created',
                name: 'date_created',
                title: '{{ __('report.lbl_date') }}',
                orderable: false
            },
            {
                data: 'date_ago',
                name: 'date_ago',
                title: '{{ __('backup.age') }}',
                orderable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: '{{ __('service.lbl_action') }}',
                className: 'text-end'
            },
        ];

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route('backend.backups.index_data') }}',
                finalColumns: columns,
                // orderColumn: [[3, 'desc']],
                advanceFilter: () => {
                    return {};
                }
            });
        });
    </script>
    <script>
        function handleLoader(event) {
            event.preventDefault();
            const btn = event.currentTarget;
            const spinnerId = btn.getAttribute('data-spinner-id');
            const iconId = btn.getAttribute('data-icon-id');
            const textId = btn.getAttribute('data-text-id');
            const spinner = document.getElementById(spinnerId);
            const icon = document.getElementById(iconId);
            const text = document.getElementById(textId);
            if (spinner && icon && text) {
                spinner.classList.remove('d-none');
                icon.classList.add('d-none');
                text.textContent = "{{ __('appointment.loading') }}";
                setTimeout(() => {
                    window.location.href = btn.href;
                }, 300);
            }
        }
    </script>
@endpush
