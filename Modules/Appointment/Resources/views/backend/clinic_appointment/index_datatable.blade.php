    @extends('backend.layouts.app')

    @section('title')
        {{ __($module_title) }}
    @endsection

    @section('content')
        <div class="table-content mb-5">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if (auth()->user()->can('delete_clinic_appointment_list'))
                        <x-backend.quick-action url="{{ route('backend.appointments.bulk_action') }}">
                            <div class="">
                                <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                                    style="width:100%">
                                    <option value="">{{ __('messages.no_action') }}</option>

                                    @can('delete_clinic_appointment_list')
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                </select>
                            </div>

                        </x-backend.quick-action>
                    @endif
                    <div>
                        <button type="button" class="btn btn-primary" data-modal="export">
                            <i class="ph ph-export me-1"></i>{{ __('messages.export') }}
                        </button>
                        {{-- <button type="button" class="btn btn-primary" data-modal="import">
                            <i class="ph ph-export me-1"></i>{{ __('messages.import') }}
                        </button> --}}
                    </div>
                </div>
                <x-slot name="toolbar">

                    <div>
                        <div class="datatable-filter status-filter">
                            <select name="column_status" id="column_status" class="select2 form-select" data-filter="select"
                                style="width: 100%">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="pending" {{ $filter['status'] == 'pending' ? 'selected' : '' }}>
                                    {{ __('appointment.pending') }}
                                </option>
                                <option value="confirmed" {{ $filter['status'] == 'confirmed' ? 'selected' : '' }}>
                                    {{ __('appointment.confirmed') }}
                                </option>
                                <option value="check_in" {{ $filter['status'] == 'check_in' ? 'selected' : '' }}>
                                    {{ __('appointment.check_in') }}
                                </option>
                                <option value="checkout" {{ $filter['status'] == 'checkout' ? 'selected' : '' }}>
                                    {{ __('appointment.checkout') }}
                                </option>
                                <option value="cancelled" {{ $filter['status'] == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('appointment.cancelled') }}
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

                    {{-- @hasPermission('add_clinic_appointment_list')
                        <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
                            {{ __('messages.new') }} </x-buttons.offcanvas>
                    @endhasPermission --}}
                    
                    @hasPermission('add_clinic_appointment_list')
                        {{-- Button to open offcanvas --}}
                        <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#form-offcanvas"
                            aria-controls="form-offcanvas">
                            <i class="ph ph-plus-circle me-1"></i>{{ __('messages.new') }}
                        </button>

                        {{-- Offcanvas with form --}}
                        <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas" aria-labelledby="formOffcanvasLabel">
                            <div class="offcanvas-header border-bottom">
                                <h6 class="m-0 h5" id="formOffcanvasLabel">
                                    {{ __('messages.create') }} {{ __($module_title) }}
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>

                            <div class="offcanvas-body">
                                {{-- Include your form directly --}}
                                @include('appointment::backend.clinic_appointment.new_appoitment', [
                                    'customers' => $customer,   // <- rename while passing
                                ])
                            </div>
                        </div>
                    @endhasPermission

                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table position-relative">
            </table>
        </div>
        <div data-render="app">
            <clinic-appointment-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
                edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"
                :customefield="{{ json_encode($customefield) }}" :role="{{ json_encode(auth()->user()->role) }}"
                :user-id="{{ auth()->user()->id }}">
            </clinic-appointment-offcanvas>

            <patient-encounter-dashboard create-title="{{ __('appointment.encouter_dashboard') }}">
            </patient-encounter-dashboard>
            <appointment-offcanvas>
            </appointment-offcanvas>

            <appointment-customform>
            </appointment-customform>
        </div>

        <x-backend.advance-filter>
            <x-slot name="title">
                <h4>{{ __('service.lbl_advanced_filter') }}</h4>
            </x-slot>
            <div class="form-group datatable-filter">
                <label class="form-label" for="patient_name">{{ __('clinic.patient') }}</label>
                <select name="patient_name" id="patient_name" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.select_patient') }}</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                            @if($patient->otherPatients->count() > 0)
                                ({{ $patient->otherPatients->count() }} {{ __('appointment.other_patients') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group datatable-filter d-none" id="other_patient_div">
                <label class="form-label" for="other_patient">{{ __('messages.other_patient') }}</label>
                <select name="other_patient" id="other_patient" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.select_other_patient') }}</option>
                </select>
            </div>
            <div class="form-group datatable-filter">
                <label class="form-label" for="service_name">{{ __('service.singular_title') }}</label>
                <select name="service_name" id="service_name" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('service.all') }} {{ __('service.singular_title') }}</option>
                    @foreach ($service as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="datatable-filter">
                <label class="form-label" for="other_patient">{{ __('messages.status') }}</label>
                <select name="Status" id="appointment_status" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="pending">{{ __('appointment.pending') }}</option>
                    <option value="confirmed">{{ __('appointment.confirmed') }}</option>
                    <option value="check_in">{{ __('appointment.check_in') }}</option>
                    <option value="checkout">{{ __('appointment.checkout') }}</option>
                    <option value="cancelled">{{ __('appointment.cancelled') }}</option>
                </select>
            </div>
            
        
            @unless (auth()->user()->hasRole('doctor'))
                <div class="form-group datatable-filter">
                    <label class="form-label" for="doctor_id">{{ __('clinic.doctor_title') }}</label>
                    <select name="doctor_id" id="doctor_id" class="select2 form-select" data-filter="select">
                        <option value="">{{ __('service.lbl_doctor') }} </option>
                        @foreach ($doctor as $doctor)
                            <option value="{{ $doctor->doctor_id }}">{{ optional($doctor->user)->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endunless
            <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
        </x-backend.advance-filter>
        
        <!-- Include Appointment Details Modal -->
        @include('appointment::backend.appointment.details_modal')
    @endsection

    @push('after-styles')
        <link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
        <!-- DataTables Core and Extensions -->
        <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    @endpush
    <style>
        .disabled-cell {
            background-color: #e9ecef;
            pointer-events: none;
            opacity: 0.5;
        }
    </style>
    @push('after-scripts')
        <script src="{{ mix('modules/appointment/script.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
        <script src="{{ asset('js/form-modal/index.js') }}" defer></script>

        <!-- DataTables Core and Extensions -->
        <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
        <script>
            const userRoles = @json(auth()->user()->roles->pluck('name')->toArray());
        </script>
        <script type="text/javascript" defer>
            const columns = [
                @unless (auth()->user()->hasRole('doctor') || auth()->user()->hasRole('receptionist') || auth()->user()->hasRole('user'))
                    {
                        name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                        width: '0%',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                @endunless {
                    data: 'id',
                    name: 'id',
                    title: "{{ __('appointment.lbl_id') }}",
                    searchable: false,
                    orderable: true,

                },
                {
                    data: 'user_id',
                    name: 'user_id',
                    title: "{{ __('sidebar.patient') }}",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'start_date_time',
                    name: 'start_date_time',
                    title: "{{ __('appointment.lbl_date_time') }}",
                    orderable: true,
                },
                {
                    data: 'services',
                    name: 'services',
                    title: "{{ __('appointment.lbl_service') }}",
                    orderable: true,
                    searchable: true,
                    width: '10%'
                },

                    {
                data: 'triage_status',
                name: 'triage_status',
                title: 'Triage',
                orderable: false,
                searchable: false,
            },
               
            // {
            //     data: 'service_amount',
            //     name: 'service_amount',
            //     title: "{{ __('appointment.price') }}",
            //     orderable: true,
            //     searchable: true,
            // },
            @if(!auth()->user()->hasRole('doctor') && auth()->user()->user_type !== 'doctor')
            {
                data: 'service_amount',
                name: 'service_amount',
                title: "{{ __('appointment.price') }}",
                orderable: true,
                searchable: true,
            },
            @endif
                // {
                //     data: 'service_amount',
                //     name: 'service_amount',
                //     title: "{{ __('appointment.price') }}",
                //     orderable: true,
                //     searchable: true,
                // },
                @unless (auth()->user()->hasRole('doctor'))
                    {
                        data: 'doctor_id',
                        name: 'doctor_id',
                        title: "{{ __('appointment.lbl_doctor') }}",
                        orderable: true,
                        searchable: true,
                    },
                @endunless
                 {
                    data: 'updated_at',
                    name: 'updated_at',
                    title: "{{ __('appointment.lbl_update_at') }}",
                    orderable: true,
                    visible: false,
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true,
                    title: "{{ __('appointment.lbl_status') }}",
                    width: '5%',
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (userRoles.includes('user')) {
                            $(td).addClass('disabled-cell');
                            $(td).attr('title', '{{ __('messages.no_permission_edit_field') }}');
                        }
                    }
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    orderable: false,
                    searchable: false,
                    title: "{{ __('appointment.lbl_payment_status') }}",
                    width: '10%',
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (userRoles.includes('user')) {
                            $(td).addClass('disabled-cell');
                            $(td).attr('title', '{{ __('messages.no_permission_edit_field') }}');
                        }
                    }
                },

            ]


            const actionColumn = [
                @unless (auth()->user()->hasRole('user'))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('appointment.lbl_action') }}",
                        width: '5%'
                    }
                @endunless
            ]


            const customFieldColumns = JSON.parse(@json($columns))

            let finalColumns = [
                ...columns,
                ...customFieldColumns,
                ...actionColumn
            ]

            document.addEventListener('DOMContentLoaded', (event) => {
                // Initialize datatable first
                initDatatable({
                    url: '{{ route("backend.$module_name.index_data", ['user_id' => $user_id, 'doctor_id' => $doctor_id, 'clinic_id' => $clinic_id]) }}',
                    finalColumns,
                    orderColumn: @if (auth()->user()->hasRole('doctor'))
                        [
                            [5, "desc"]
                        ]
                    @else
                        [
                            [7, "desc"]
                        ]
                    @endif ,
                    advanceFilter: () => {
                        return {
                            doctor_id: $('#doctor_id').val(),
                            service_id: $('#service_name').val(),
                            other_patient_id: $('#other_patient').val(),
                            patient_id: $('#patient_name').val(),
                            status: $('#appointment_status').val()
                        }
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

                // Add change event listeners for all filter dropdowns
                $('#patient_name, #doctor_id, #service_name, #appointment_status, #other_patient').on('change', function() {
                    if (window.renderedDataTable) {
                        window.renderedDataTable.ajax.reload(null, false);
                    }
                });

                // Reset filter functionality
                $('#reset-filter').on('click', function(e) {
                    e.preventDefault();
                    $('#doctor_id, #patient_name, #service_name, #appointment_status').val('').trigger('change');
                    $('#other_patient').val('').trigger('change');
                    $('#other_patient_div').addClass('d-none');
                    if (window.renderedDataTable) {
                        window.renderedDataTable.ajax.reload(null, false);
                    }
                });
            });

            function resetQuickAction() {
                const actionValue = $('#quick-action-type').val();
                if (actionValue != '') {
                    $('#quick-action-apply').removeAttr('disabled');

                    if (actionValue == 'change-status') {
                        $('.quick-action-field').addClass('d-none');
                        $('#change-status-action').removeClass('d-none');
                    } else {
                        $('.quick-action-field').addClass('d-none');
                    }
                } else {
                    $('#quick-action-apply').attr('disabled', true);
                    $('.quick-action-field').addClass('d-none');
                }
            }

            $('#quick-action-type').change(function() {
                resetQuickAction()
            });

            function dispatchCustomEvent(button) {
                const event = new CustomEvent('custom_form_assign', {
                    detail: {
                        appointment_type: button.getAttribute('data-appointment-type'),
                        appointment_id: button.getAttribute('data-appointment-id'),
                        form_id: button.getAttribute('data-form-id')
                    }
                });

                document.dispatchEvent(event);

                const offcanvasSelector = button.getAttribute('data-assign-target');
                const offcanvasElement = document.querySelector(offcanvasSelector);
                if (offcanvasElement) {
                    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                    offcanvas.show();
                }
            }
        </script>
        
        <script>
            $(document).ready(function() {
                // Initialize select2
                $('.select2').select2();
                // Handle patient selection change
                $('#patient_name').on('change', function() {
                    const patientId = $(this).val();
                    const $otherPatientDiv = $('#other_patient_div');
                    const $otherPatientSelect = $('#other_patient');

                    if (!patientId) {
                        $otherPatientDiv.addClass('d-none');
                        $otherPatientSelect.empty();
                        return;
                    }

                    // Fetch other patients for selected patient
                    $.ajax({
                        url: '{{ route('backend.appointment.other_patientlist') }}',
                        method: 'GET',
                        data: {
                            patient_id: patientId
                        },
                        success: function(response) {
                            $otherPatientSelect.empty().append(
                                '<option value="">{{ __('messages.select_other_patient') }}</option>'
                            );

                            if (response && response.length > 0) {
                                $otherPatientSelect.append(`
                                        <option value="">{{ __('messages.all_patients') }}</option>
                                        <option value="you">{{ __('messages.you') }}</option>
                                    `);
                                response.forEach(function(patient) {
                                    // Create the option with image and name
                                    const option = `
                                                                        <option value="${patient.id}" >
                                            ${patient.first_name}
                                        </option>
                                    `;
                                    $otherPatientSelect.append(option);
                                });

                                // Initialize Select2 with custom template
                                $otherPatientSelect.select2({
                                    templateResult: formatPatient,
                                    templateSelection: formatPatient
                                });

                                $otherPatientDiv.removeClass('d-none');
                            } else {
                                $otherPatientDiv.addClass('d-none');
                            }
                        },
                        error: function() {
                            console.error('Failed to fetch other patients');
                            $otherPatientDiv.addClass('d-none');
                        }
                    });
                });

                // Add custom format function
                function formatPatient(patient) {
                    if (!patient.id) {
                        return patient.text;
                    }

                    const $container = $(
                        `<div class="select2-patient-option d-flex align-items-center gap-2">
                            <img src="${$(patient.element).data('image')}" class="patient-avatar" style="width: 30px; height: 30px; border-radius: 50%;"/>
                            <span>${patient.text}</span>
                        </div>`
                    );

                    return $container;
                }
            });
        </script>
        <script>
            let previousStatus = null;
            let currentSelect = null;
            let selectedStatus = null;
            let appointmentId = null;
            let token = null;
            let cancellation_charge = @json(setting('cancellation_charge'));
            let cancelltion_Type = @json(setting('cancellation_type'));

            $(document).on('focus', '.change-select', function () {
                previousStatus = $(this).val();
            });

            // Handle Select2 change events
            $(document).on('select2:select', '.change-select', function (e) {
                currentSelect = $(this);
                selectedStatus = currentSelect.val();
                appointmentId = currentSelect.data('id');
                token = currentSelect.data('token');

                if (selectedStatus === 'cancelled') {
                let charge = currentSelect.data('charge');

                if (charge > 0) {
                    $('#cancel_charge_info').html(`{{ __('messages.cancellation_charges_applied') }}: <strong>${currencyFormat(charge)}</strong>`);
                } else {
                    $('#cancel_charge_info').html(`{{ __('messages.no_cancellation_charge') }}`);
                }
                $('#cancelModal').css('display', 'flex');
                $('#cancelModal').fadeIn();
                } else {
                updateStatus(selectedStatus);
                }
            });

            // Fallback for regular change events (in case Select2 is not initialized)
            $(document).on('change', '.change-select', function (e) {
                // Only handle if Select2 is not initialized
                if (!$(this).hasClass('select2-hidden-accessible')) {
                currentSelect = $(this);
                selectedStatus = currentSelect.val();
                appointmentId = currentSelect.data('id');
                token = currentSelect.data('token');

                if (selectedStatus === 'cancelled') {
                    let charge = currentSelect.data('charge');

                    if (charge > 0) {
                    $('#cancel_charge_info').html(`{{ __('messages.cancellation_charges_applied') }}: <strong>${currencyFormat(charge)}</strong>`);
                    } else {
                    $('#cancel_charge_info').html(`{{ __('messages.no_cancellation_charge') }}`);
                    }
                    $('#cancelModal').css('display', 'flex');
                    e.preventDefault();
                    currentSelect.blur();
                    $('#cancelModal').fadeIn();
                } else {
                    updateStatus(selectedStatus);
                }
                }
            });

            function updateStatus(status, reason = null, charge = 0) {


                let url = "{{ route('backend.appointments.updateStatus', ['id' => '__id__', 'action_type' => 'update-status']) }}".replace('__id__', appointmentId);

                $.ajax({
                type: 'POST',
                url: url,
                headers: { 'X-CSRF-TOKEN': token },
                data: {
                    value: status,
                    ...(status === 'cancelled' ? {
                    reason: reason,
                    cancellation_charge_amount: charge,
                    cancellation_type: cancelltion_Type,
                    cancellation_charge: cancellation_charge
                    } : {})
                },
                success: function (res) {
                    window.successSnackbar(res.message);
                    closeCancelModal();
                    $('#datatable').DataTable().ajax.reload();
                },
                error: function () {
                    console.error('{{ __('messages.something_went_wrong') }}');
                }
                });
            }

            function closeCancelModal() {
                $('#cancelModal').hide();
                $('#cancel_reason').val('');
            }

            function cancelAbort() {
                closeCancelModal();
                $('#datatable').DataTable().ajax.reload();
            }

            function submitCancellation() {
                const reason = $('#cancel_reason').val();
                let charge = currentSelect.data('charge');

                $('#cancel_reason').removeClass('is-invalid');
                $('#cancel_reason_error').remove();

                if (!reason) {
                $('#cancel_reason').addClass('is-invalid');
                $('#cancel_reason').after('<div id="cancel_reason_error" class="invalid-feedback d-block">{{ __("messages.please_enter_reason") }}</div>');
                return;
                }

                $('#confirm_btn').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('appointment.loading') }}');

                updateStatus('cancelled', reason, charge);
            }

            // Expose cancel and submit functions globally if needed
            window.cancelAbort = cancelAbort;
            window.submitCancellation = submitCancellation;

            // Use global Select2 system - no custom initialization needed
            // The global system in backend-custom.js will handle .select2 elements automatically

        </script>
        
        <script>
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

                                            ${(() => {
                                                const highlighted = highlightMedicalTerms(recording.transcription);

                                                return `
                                                    <div class="medical-history-text mb-2">
                                                        ${highlighted.html}
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span class="badge bg-danger text-white">Symptoms: ${highlighted.counts.Symptoms || 0}</span>
                                                        <span class="badge bg-warning text-dark">Allergies: ${highlighted.counts.Allergies || 0}</span>
                                                        <span class="badge bg-info text-white">Medications: ${highlighted.counts.Medications || 0}</span>
                                                        <span class="badge bg-success text-white">Triggers: ${highlighted.counts.Triggers || 0}</span>
                                                    </div>
                                                `;
                                            })()}
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
                                        <div class="d-flex gap-2">
                                            <a href="${doc.url}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="ph ph-eye"></i> View
                                            </a>
                                            <a href="${doc.download_url}" class="btn btn-sm btn-primary" download>
                                                <i class="ph ph-download"></i> Download
                                            </a>
                                        </div>
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

       function highlightMedicalTerms(text) {
    if (!text) {
        return {
            html: '',
            counts: {}
        };
    }

    let safeText = String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const categories = {
        Symptoms: {
            className: 'bg-danger text-white',
            words: ['pain', 'ache', 'fever', 'cough', 'bleeding', 'swelling', 'vomiting', 'nausea', 'dizziness', 'headache', 'tooth', 'teeth', 'sensitive', 'sensitivity']
        },
        Allergies: {
            className: 'bg-warning text-dark',
            words: ['allergy', 'allergic', 'rash', 'reaction']
        },
        Medications: {
            className: 'bg-info text-white',
            words: ['medicine', 'medication', 'antibiotic', 'tablet', 'numbing', 'solution']
        },
        Triggers: {
            className: 'bg-success text-white',
            words: ['hot', 'cold', 'drink', 'eating']
        }
    };

    const counts = {};

    Object.entries(categories).forEach(([categoryName, category]) => {
        counts[categoryName] = 0;

        category.words.forEach(word => {
            const regex = new RegExp(`\\b(${word})\\b`, 'gi');

            safeText = safeText.replace(regex, function(match) {
                counts[categoryName]++;
                return `<span class="badge ${category.className} mx-1">${match}</span>`;
            });
        });
    });

    return {
        html: safeText,
        counts
    };
}
        </script>

    @endpush

