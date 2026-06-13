@extends('backend.layouts.app')

@section('title')
    {{ __('triage.queue') }}
@endsection

@section('content')
<div class="table-content mb-5">

    {{-- Safety Banner --}}
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="ph ph-warning-circle fs-5 flex-shrink-0"></i>
        <span>{{ __('triage.safety_banner') }}</span>
    </div>

    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            {{-- Status Tabs --}}
            <ul class="nav nav-pills" id="triage-status-tabs">
                <li class="nav-item">
                    <button class="nav-link active" data-status="all" onclick="filterByStatus('all', this)">
                        {{ __('messages.all') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-status="new" onclick="filterByStatus('new', this)">
                        <span class="badge bg-primary me-1">●</span>{{ __('triage.status_new') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-status="in_progress" onclick="filterByStatus('in_progress', this)">
                        <span class="badge bg-warning me-1">●</span>{{ __('triage.status_in_progress') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-status="escalated" onclick="filterByStatus('escalated', this)">
                        <span class="badge bg-danger me-1">●</span>{{ __('triage.status_escalated') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-status="closed" onclick="filterByStatus('closed', this)">
                        <span class="badge bg-secondary me-1">●</span>{{ __('triage.status_closed') }}
                    </button>
                </li>
            </ul>
        </div>

        <x-slot name="toolbar">
            <div class="input-group flex-nowrap">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                    aria-label="Search">
            </div>
            @hasPermission('add_triage')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTriageModal">
                    <i class="ph ph-plus me-1"></i>{{ __('messages.new') }} {{ __('triage.singular_title') }}
                </button>
            @endhasPermission
        </x-slot>
    </x-backend.section-header>

    <table id="datatable" class="table table-responsive"></table>
</div>

{{-- New Triage Modal --}}
@hasPermission('add_triage')
<div class="modal fade" id="newTriageModal" tabindex="-1" aria-labelledby="newTriageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTriageModalLabel">
                    <i class="ph ph-clipboard-text me-2"></i>{{ __('messages.new') }} {{ __('triage.singular_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newTriageForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('triage.lbl_patient') }} <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_select" class="select2 form-select" style="width:100%" required>
                            <option value="">{{ 'Select' }}...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('triage.lbl_appointment') }}</label>
                        <select name="appointment_id" id="appointment_select" class="select2 form-select" style="width:100%">
                            <option value="">{{ 'Select' }}... (optional)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph ph-arrow-right me-1"></i>{{ __('messages.create') }} & Open
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endhasPermission
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<script>
let currentStatus = 'all';

const columns = [
    {
        name: 'check', data: 'check',
        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
        width: '0%', exportable: false, orderable: false, searchable: false,
    },
    { data: 'id', name: 'id', title: '#', orderable: true, searchable: false, width: '5%' },
    { data: 'patient_id', name: 'patient_id', title: "{{ __('triage.lbl_patient') }}", orderable: true },
    { data: 'patient_dob', name: 'patient_dob', title: "{{ __('triage.lbl_dob') }}", orderable: false, searchable: false },
    { data: 'appointment_type', name: 'appointment_type', title: "{{ __('triage.lbl_appointment') }}", orderable: false, searchable: false },
    { data: 'urgency_level', name: 'urgency_level', title: "{{ __('triage.lbl_urgency') }}", orderable: true },
    { data: 'status', name: 'status', title: "{{ __('triage.lbl_status') }}", orderable: true },
    { data: 'nurse_id', name: 'nurse_id', title: "{{ __('triage.lbl_nurse') }}", orderable: false },
    { data: 'created_at', name: 'created_at', title: "{{ __('appointment.lbl_date_time') }}", orderable: true },
    { data: 'action', name: 'action', orderable: false, searchable: false, title: "{{ __('messages.action') }}", width: '8%' },
];

document.addEventListener('DOMContentLoaded', () => {
    initDatatable({
        url: '{{ route("backend.triage.index_data") }}',
        finalColumns: columns,
        advanceFilter: () => ({ status: currentStatus !== 'all' ? currentStatus : null }),
    });

    // Patient select2 with AJAX
    $('#patient_select').select2({
        dropdownParent: $('#newTriageModal'),
        ajax: {
            url: '{{ url("app/customers/index_list") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(u => ({ id: u.id, text: u.name + ' — ' + (u.email || '') }))
            }),
        },
        minimumInputLength: 2,
        placeholder: '{{ __("messages.search") }}...',
    });

    // Appointment select2 with AJAX
    $('#appointment_select').select2({
        dropdownParent: $('#newTriageModal'),
        ajax: {
            url: '{{ route("backend.triage.appointment_search") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
        },
        minimumInputLength: 1,
        placeholder: '{{ __("messages.select") }}...',
        allowClear: true,
    });

    // New triage form submit
    document.getElementById('newTriageForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('[type=submit]');
        btn.disabled = true;

        fetch('{{ route("backend.triage.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                patient_id: document.getElementById('patient_select').value,
                appointment_id: document.getElementById('appointment_select').value || null,
            }),
        })
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                window.location.href = res.redirect;
            } else {
                window.errorSnackbar(res.message || 'Error');
                btn.disabled = false;
            }
        })
        .catch(() => { btn.disabled = false; });
    });
});

function filterByStatus(status, el) {
    currentStatus = status;
    document.querySelectorAll('#triage-status-tabs .nav-link').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    if (window.renderedDataTable) window.renderedDataTable.ajax.reload();
}
</script>
@endpush
