@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="table-content mb-5">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                @if (auth()->user()->can('edit_' . $module_title) ||
                        auth()->user()->can('delete_' . $module_title))
                    <x-backend.quick-action url="{{ route('backend.services.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_Appointment')
                                    <option value="change-status">{{ __('messages.status') }}</option>
                                @endcan
                                @can('delete_Appointment')
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                    @dd('$module_title')
                                @endcan
                            </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="select2 form-select" id="status" style="width:100%">
                                <option value="" selected>{{ __('messages.select_status') }}</option>
                                <option value="1">{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                    </x-backend.quick-action>
                @endif
                <div>
                    <button type="button" class="btn btn-primary" data-modal="export">
                        <i class="ph ph-export me-1"></i>{{ __('messages.export')('sssss') }}
                    </button>
                     <button type="button" class="btn btn-secondary" data-modal="import"> 
                    <i class="fa-solid fa-upload"></i> Import
                     </button>
                </div>
            </div>
            <x-slot name="toolbar">

                <div>
                    <div class="datatable-filter">
                        <select name="column_status" id="column_status" class="select2 form-select" data-filter="select"
                            style="width: 100%">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}
                            </option>
                            <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                {{ __('messages.active') }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>
                <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                        class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}</button>
                @hasPermission('add_service')
                    <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
                        {{ __('messages.create') }} {{ __('appointment.singular_title') }}</x-buttons.offcanvas>
                @endhasPermission

            </x-slot>
        </x-backend.section-header>
        
        <table id="datatable" class="table table-responsive">
        </table>
    </div>
    
    <!-- Include Appointment Details Modal -->
    @include('appointment::backend.appointment.details_modal')
    
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
    </x-backend.advance-filter>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/appointment/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>

    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
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
                title: "{{ __('appointment.lbl_id') }}",
                searchable: false,
                orderable: true,

            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('appointment.lbl_patient_name') }}",
                orderable: true,
            },
            {
                data: 'start_date_time',
                name: 'start_date_time',
                title: "{{ __('appointment.lbl_date_time') }}",
            },
            {
                data: 'services',
                name: 'services',
                title: "{{ __('appointment.lbl_services') }}",
                orderable: true,
                searchable: true,
                width: '10%'
            },
            {
                data: 'service_amount',
                name: 'service_amount',
                title: "{{ __('appointment.price') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'employee_id',
                name: 'employee_id',
                title: "{{ __('appointment.lbl_doctor') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('appointment.lbl_update_at') }}",
                orderable: true,
                visible: true,
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('appointment.lbl_status') }}",
                width: '5%'
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                orderable: false,
                searchable: false,
                title: "{{ __('appointment.lbl_payment_status') }}",
                width: '10%',
            },

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('appointment.lbl_action') }}",
            width: '5%'
        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                advanceFilter: () => {
                    // Get active type filter
                    const activeTab = document.querySelector('.nav-pills .nav-link.active');
                    const type = activeTab ? activeTab.getAttribute('data-type') : 'all';
                    
                    return {
                        type: type !== 'all' ? type : null
                    };
                },
                drawCallback: () => {
                    // Reinitialize select2 for dynamically created elements in DataTable
                    $('.change-select').each(function() {
                        if (!$(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2({
                                width: '100%',
                                minimumResultsForSearch: -1
                            });
                        }
                    });
                }
            });
        })
        
        // Function to view appointment details in modal
        function viewAppointmentDetails(appointmentId) {
            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
            const contentDiv = document.getElementById('appointmentDetailsContent');
            
            // Show loading state
            contentDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading appointment details...</p>
                </div>
            `;
            
            modal.show();
            
            // Fetch appointment details
            fetch(`{{ url('app/appointment/view-details') }}/${appointmentId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status) {
                        contentDiv.innerHTML = renderAppointmentDetails(result.data);
                    } else {
                        contentDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ph ph-warning-circle me-2"></i>
                                Failed to load appointment details. Please try again.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ph ph-warning-circle me-2"></i>
                            An error occurred while loading appointment details.
                        </div>
                    `;
                });
        }
        
        function renderAppointmentDetails(data) {
            const patientName = data.other_patient ? data.other_patient.name : data.patient.name;
            const patientEmail = data.other_patient ? data.other_patient.email : data.patient.email;
            const patientPhone = data.other_patient ? data.other_patient.phone : data.patient.phone;
            
            let html = `
                <div class="row">
                    <!-- Appointment Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-calendar-check me-2"></i>Appointment Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Appointment ID:</span>
                                <span class="detail-value">#${data.appointment.id}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${data.appointment.appointment_date}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${data.appointment.appointment_time}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">${data.appointment.duration} minutes</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge bg-${getStatusColor(data.appointment.status)}">${data.appointment.status}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-user me-2"></i>Patient Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${patientName}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${patientEmail || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">${patientPhone || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Doctor & Service Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-user-circle me-2"></i>Doctor Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Doctor:</span>
                                <span class="detail-value">${data.doctor.name}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${data.doctor.email || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-hospital me-2"></i>Service & Clinic</h6>
                            <div class="detail-row">
                                <span class="detail-label">Service:</span>
                                <span class="detail-value">${data.service.name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value">${data.category.name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Clinic:</span>
                                <span class="detail-value">${data.clinic.name || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-currency-circle-dollar me-2"></i>Payment Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Total Amount:</span>
                                <span class="detail-value">${data.appointment.total_amount || '0'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Advance Paid:</span>
                                <span class="detail-value">${data.appointment.advance_paid_amount || '0'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Payment Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge bg-${data.payment.payment_status == 1 ? 'success' : 'warning'}">
                                        ${data.payment.payment_status == 1 ? 'Paid' : 'Pending'}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Medical History -->
                    <div class="col-12">
                        <div class="detail-card">
                            <h6><i class="ph ph-file-text me-2"></i>Medical History</h6>
                            ${data.medical_data.medical_history_text ? 
                                `<div class="medical-history-text">${data.medical_data.medical_history_text}</div>` : 
                                '<div class="no-data">No medical history recorded</div>'
                            }
                        </div>
                    </div>
                    
                    <!-- Audio Recordings -->
                    ${data.medical_data.audio_recordings && data.medical_data.audio_recordings.length > 0 ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-microphone me-2"></i>Audio Recordings</h6>
                                ${data.medical_data.audio_recordings.map((recording, index) => `
                                    <div class="mb-3">
                                        <label class="form-label">Recording ${index + 1}</label>
                                        <audio controls class="audio-player">
                                            <source src="${recording.url}" type="audio/webm">
                                            <source src="${recording.url}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                        ${recording.transcription ? `
                                            <div class="mt-2">
                                                <small class="text-muted">Transcription:</small>
                                                <p class="mb-0">${recording.transcription}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- Documents -->
                    ${data.documents && data.documents.length > 0 ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-file me-2"></i>Uploaded Documents</h6>
                                ${data.documents.map(doc => `
                                    <div class="document-item">
                                        <i class="ph ph-file-pdf document-icon"></i>
                                        <div class="document-info">
                                            <div class="document-name">${doc.name}</div>
                                            <div class="document-size">${formatFileSize(doc.size)}</div>
                                        </div>
                                        <a href="${doc.download_url}" class="btn btn-sm btn-primary" download>
                                            <i class="ph ph-download"></i> Download
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- Additional Notes -->
                    ${data.appointment.appointment_extra_info ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-note me-2"></i>Additional Notes</h6>
                                <div class="medical-history-text">${data.appointment.appointment_extra_info}</div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            return html;
        }
        
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'confirmed': 'info',
                'check_in': 'primary',
                'checkout': 'success',
                'cancelled': 'danger',
                'rejected': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        // Type filter functionality
        function filterByType(type) {
            // Update active tab
            document.querySelectorAll('.nav-pills .nav-link').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Apply filter to DataTable
            window.renderedDataTable.ajax.reload();
        }
        
        // Sync blood tests functionality
        function syncBloodTests() {
            const btn = document.getElementById('sync-blood-tests-btn');
            const originalHtml = btn.innerHTML;
            
            // Show loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin me-1"></i> Syncing...';
            
            fetch('{{ route("backend.appointments.sync_blood_tests") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    window.successSnackbar(data.message || 'Blood tests synced successfully!');
                    
                    // Reload DataTable
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
                // Restore button
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
    </script>
@endpush
