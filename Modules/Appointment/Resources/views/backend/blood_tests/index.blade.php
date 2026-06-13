@extends('backend.layouts.app')

@section('title')
    🩸 {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@section('content')
    <div class="table-content mb-5">

        {{-- Filter banner when coming from an appointment link --}}
        @if(request('linked_appointment_id'))
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-3">
            <span>
                <i class="ph ph-test-tube me-2"></i>
                Showing blood tests linked to <strong>Appointment #{{ request('linked_appointment_id') }}</strong>
            </span>
            <a href="{{ route('backend.blood-tests.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="ph ph-x me-1"></i> Clear Filter
            </a>
        </div>
        @endif
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                <div>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#orderBloodTestModal">
                        <i class="ph ph-test-tube me-1"></i> Order Blood Test
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="syncBloodTests()">
                        <i class="ph ph-arrows-clockwise me-1"></i> Sync from WordPress
                    </button>
                </div>
            </div>
            <x-slot name="toolbar">
                <div>
                    <div class="datatable-filter">
                        <select name="column_status" id="column_status" class="select2 form-select" data-filter="select"
                            style="width: 100%">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="check_in">Check In</option>
                            <option value="check_out">Check Out</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="Search..." aria-label="Search" aria-describedby="addon-wrapping">
                </div>
            </x-slot>
        </x-backend.section-header>

        <table id="datatable" class="table table-striped border table-responsive"></table>

        {{-- ── Order Blood Test Modal ──────────────────────────────────────────── --}}
        <div class="modal fade" id="orderBloodTestModal" tabindex="-1" aria-labelledby="orderBloodTestLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderBloodTestLabel">
                            <i class="ph ph-test-tube me-2 text-danger"></i>Order Blood Test
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="orderBloodTestForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                                <select id="obt_patient" name="patient_id" class="select2 form-select" style="width:100%" required>
                                    <option value="">Search patient...</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Link to (optional)</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="link_type" id="link_none" value="none" checked>
                                    <label class="btn btn-outline-secondary" for="link_none">No Link</label>
                                    <input type="radio" class="btn-check" name="link_type" id="link_appt" value="appointment">
                                    <label class="btn btn-outline-primary" for="link_appt">Appointment</label>
                                    <input type="radio" class="btn-check" name="link_type" id="link_triage" value="triage">
                                    <label class="btn btn-outline-info" for="link_triage">Triage Record</label>
                                </div>
                            </div>
                            <div id="obt_appt_wrap" class="mb-3 d-none">
                                <label class="form-label">Select Appointment</label>
                                <select id="obt_appointment" name="linked_appointment_id" class="select2 form-select" style="width:100%">
                                    <option value="">Select patient first...</option>
                                </select>
                            </div>
                            <div id="obt_triage_wrap" class="mb-3 d-none">
                                <label class="form-label">Select Triage Record</label>
                                <select id="obt_triage" name="triage_id" class="select2 form-select" style="width:100%">
                                    <option value="">Select patient first...</option>
                                </select>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Test Type <span class="text-danger">*</span></label>
                                    <input type="text" name="test_type" class="form-control" placeholder="e.g. Full Blood Count, HbA1c..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="appointment_date" class="form-control" required value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Time <span class="text-danger">*</span></label>
                                    <input type="time" name="appointment_time" class="form-control" required value="09:00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Amount (£)</label>
                                    <input type="number" name="total_amount" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger" id="obt-submit-btn">
                                <i class="ph ph-test-tube me-1"></i>Order Blood Test
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('after-scripts')
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [
            {
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                data: 'id',
                name: 'id',
                title: "ID",
                searchable: false,
                orderable: true,
                width: '5%'
            },
            {
                data: 'patient_name',
                name: 'patient_name',
                title: "Patient",
                orderable: true,
                width: '20%'
            },
            {
                data: 'test_type',
                name: 'test_type',
                title: "Test Type",
                orderable: true,
                searchable: true,
                width: '15%'
            },
            {
                data: 'appointment_datetime',
                name: 'appointment_datetime',
                title: "Date & Time",
                orderable: true,
                width: '15%'
            },
            {
                data: 'amount',
                name: 'amount',
                title: "Amount",
                orderable: true,
                searchable: false,
                width: '10%'
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "Status",
                width: '10%'
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                orderable: false,
                searchable: false,
                title: "Payment",
                width: '10%',
            },
            {
                data: 'report_status',
                name: 'report_status',
                orderable: false,
                searchable: false,
                title: "Report",
                width: '10%',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: "Action",
                width: '10%'
            }
        ];

        document.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            const linkedAppointmentId = urlParams.get('linked_appointment_id');

            initDatatable({
                url: '{{ route("backend.blood-tests.index_data") }}',
                finalColumns: columns,
                advanceFilter: () => {
                    return linkedAppointmentId ? { linked_appointment_id: linkedAppointmentId } : {};
                },
            });
        });
        
        // Sync blood tests function
        function syncBloodTests() {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin me-1"></i> Syncing...';
            
            fetch('{{ route("backend.blood-tests.sync") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.successSnackbar(data.message || 'Blood tests synced successfully!');
                    window.renderedDataTable.ajax.reload();
                } else {
                    window.errorSnackbar(data.message || 'Sync failed');
                }
            })
            .catch(error => {
                console.error('Sync error:', error);
                window.errorSnackbar('Sync failed. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
        
        // Delete blood test function
        function deleteBloodTest(id) {
            if (!confirm('Are you sure you want to delete this blood test? This action cannot be undone.')) {
                return;
            }
            
            fetch('{{ url("app/blood-tests") }}/' + id, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.successSnackbar(data.message || 'Blood test deleted successfully!');
                    window.renderedDataTable.ajax.reload();
                } else {
                    window.errorSnackbar(data.message || 'Delete failed');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                window.errorSnackbar('Delete failed. Please try again.');
            });
        }
    </script>
@endpush

@push('after-scripts')
<script>
// ── Order Blood Test Modal JS ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Patient select2 — AJAX search, bound to modal
    $('#obt_patient').select2({
        dropdownParent: $('#orderBloodTestModal'),
        ajax: {
            url: '{{ url("app/customers/index_list") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                return {
                    results: data.map(function (u) {
                        return { id: u.id, text: u.name + (u.email ? ' — ' + u.email : '') };
                    })
                };
            },
            cache: true,
        },
        minimumInputLength: 2,
        placeholder: 'Type to search patients...',
        allowClear: true,
    });

    // When patient changes, reload appointment/triage dropdowns
    $('#obt_patient').on('change', function () {
        var userId = $(this).val();
        var linkType = document.querySelector('[name="link_type"]:checked');
        if (!userId || !linkType) return;
        if (linkType.value === 'appointment') loadPatientAppointments(userId);
        if (linkType.value === 'triage') loadPatientTriages(userId);
    });

    // Link type radio toggle
    document.querySelectorAll('[name="link_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.getElementById('obt_appt_wrap').classList.toggle('d-none', this.value !== 'appointment');
            document.getElementById('obt_triage_wrap').classList.toggle('d-none', this.value !== 'triage');
            var userId = document.getElementById('obt_patient').value;
            if (!userId) return;
            if (this.value === 'appointment') loadPatientAppointments(userId);
            if (this.value === 'triage') loadPatientTriages(userId);
        });
    });

    // Reset modal on close
    document.getElementById('orderBloodTestModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('orderBloodTestForm').reset();
        $('#obt_patient').val(null).trigger('change');
        $('#obt_appointment').empty().append('<option value="">Select patient first...</option>');
        $('#obt_triage').empty().append('<option value="">Select patient first...</option>');
        document.getElementById('obt_appt_wrap').classList.add('d-none');
        document.getElementById('obt_triage_wrap').classList.add('d-none');
        document.getElementById('link_none').checked = true;
    });

    // Form submit
    document.getElementById('orderBloodTestForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = document.getElementById('obt-submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ph ph-spinner me-1"></i>Ordering...';

        var data = {};
        new FormData(this).forEach(function (v, k) { data[k] = v; });

        fetch('{{ route("backend.blood-tests.order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                window.successSnackbar(res.message);
                bootstrap.Modal.getInstance(document.getElementById('orderBloodTestModal')).hide();
                if (window.renderedDataTable) window.renderedDataTable.ajax.reload();
            } else {
                window.errorSnackbar(res.message || 'Failed to create order');
            }
        })
        .catch(function () { window.errorSnackbar('Network error'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ph ph-test-tube me-1"></i>Order Blood Test';
        });
    });
});

function loadPatientAppointments(userId) {
    fetch('{{ route("backend.blood-tests.patient_appointments") }}?user_id=' + userId)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var sel = document.getElementById('obt_appointment');
            sel.innerHTML = '<option value="">-- No link --</option>';
            data.forEach(function (a) {
                var opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.text;
                sel.appendChild(opt);
            });
        });
}

function loadPatientTriages(userId) {
    fetch('{{ route("backend.blood-tests.patient_triages") }}?user_id=' + userId)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var sel = document.getElementById('obt_triage');
            sel.innerHTML = '<option value="">-- No link --</option>';
            data.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.text;
                sel.appendChild(opt);
            });
        });
}
</script>
@endpush
