@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="{{ $module_icon }}"></i> {{ __($module_title) }}
                    </h4>
                <div class="small text-medium-emphasis">{{__('messages.backup_management')}}</div>
                </div>
                <div class="btn-toolbar d-block" role="toolbar" aria-label="Toolbar with buttons">
                    @hasPermission('add_backup')
                        <a id="create-backup-btn" href="{{ route("backend.$module_name.create-db-bk") }}" class="btn btn-outline-success m-1" data-bs-toggle="tooltip"
                         data-loader="true" data-spinner-id="create-backup-spinner" data-icon-id="backup-icon" data-text-id="backup-btn-text" title="{{ __('messages.create_backup') }}" onclick="handleLoader(event)">
                            <span id="create-backup-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                            <i class="fas fa-plus-circle me-1" id="backup-icon"></i>
                            <span id="backup-btn-text">{{ __('messages.create_backup') }}</span>
                        </a>
                    @endhasPermission

                    @hasPermission('add_backup')
                        @hasPermission('add_backup')
                            <a id="create-new-backup-btn" href="{{ route("backend.$module_name.create") }}" class="btn btn-outline-success m-1" data-bs-toggle="tooltip"
                             data-loader="true" data-spinner-id="create-new-backup-spinner" data-icon-id="new-backup-icon"
                                data-text-id="new-backup-btn-text" title="{{ __('messages.create_new_backup') }}" onclick="handleLoader(event)">
                                <span id="create-new-backup-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                                <i class="fas fa-plus-circle me-1" id="new-backup-icon"></i>
                                <span id="new-backup-btn-text">{{ __('messages.create_new_backup') }}</span>
                            </a>
                        @endhasPermission
                    @endhasPermission
                </div>
            </div>

            <div class="row mt-4">
                <div class="col">

                    @if (count($backups))
                        <table id="datatable" class="table table-bordered table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        {{ __('backup.file') }}
                                    </th>
                                    <th>
                                        {{ __('backup.size') }}
                                    </th>
                                    <th>
                                        {{ __('report.lbl_date') }}
                                    </th>
                                    <th>
                                        {{ __('backup.age') }}
                                    </th>
                                    <th class="text-end">
                                        {{ __('service.lbl_action') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                        @foreach($backups as $key => $backup)
                                    <tr>
                                        <td>
                                            {{ count($backups) - $key }}
                                        </td>
                                        <td>
                                            {{ $backup['file_name'] }}
                                        </td>
                                        <td>
                                            {{ $backup['file_size'] }}
                                        </td>
                                        <td>
                                            {{ formatDate($backup['date_created']) }}
                                        </td>
                                        <td>
                                            {{ $backup['date_ago'] }}
                                        </td>
                                        <td class="text-end">
                                <a href="{{ route("backend.$module_name.download", $backup['file_name']) }}" class="btn btn-primary m-1 btn-sm" data-bs-toggle="tooltip" title="{{ __('backup.download') }}"><i class="fas fa-cloud-download-alt"></i>&nbsp;{{ __('backup.download') }}</a>
                                            @hasPermission('delete_backup')
                                <a href="{{ route("backend.$module_name.delete", $backup['file_name']) }}" class="btn btn-danger m-1 btn-sm" data-bs-toggle="tooltip" title="{{ __('backup.delete') }}"><i class="fas fa-trash"></i>&nbsp;{{ __('backup.delete') }}</a>
                                            @endhasPermission
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                    <h4>{{__('messages.no_backup')}}</h4>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push ('after-scripts')
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
