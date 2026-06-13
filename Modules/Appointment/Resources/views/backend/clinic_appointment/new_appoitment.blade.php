@php
$patientsData = [];
// Support both $customer, $customers and $patients variable names
$customerList = $customer ?? $customers ?? $patients ?? [];
foreach($customerList as $patient) {
    $patientsData[$patient->id] = [
        'name' => trim($patient->first_name . ' ' . $patient->last_name),
        'email' => $patient->email ?? '',
        'mobile' => $patient->mobile ?? '',
        'created_at' => $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('F Y') : '',
        'avatar' => $patient->profile_image ?? default_user_avatar(),
    ];
}
$authUserId = auth()->id();
$currencySymbol = Currency::defaultSymbol();
@endphp

<form id="clinic-appointment-form" enctype="multipart/form-data" class="d-flex flex-column h-100">
@csrf
<input type="hidden" name="status" value="pending">
<input type="hidden" name="appointment_id" id="appointment_id" value="">

{{-- Main content without extra scroll --}}
<div class="flex-grow-1">
    {{-- Patient Selection --}}
    <div class="mb-3">
        <label class="form-label">{{ __('appointment.lbl_select_patient') }} <span class="text-danger">*</span></label>
        <div class="position-relative">
            <select id="patient-select" name="patient_id" class="form-select select2" data-placeholder="{{ __('appointment.lbl_select_patient') }}">
                <option value=""></option>
                @foreach($patientsData as $patientId => $patientInfo)
                    <option value="{{ $patientId }}">{{ $patientInfo['name'] }}</option>
                @endforeach
            </select>
            <div id="patient-loader" class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                <i class="fas fa-spinner fa-spin text-primary"></i>
            </div>
        </div>
    </div>

    {{-- Patient Details Section (Hidden by default) --}}
    <div id="appointment-details" class="d-none">
        <div class="d-flex m-0 mb-3 p-3 align-items-center gap-lg-3 gap-2 flex-wrap border bg-gray-900 rounded">
            <!-- Avatar -->
            <img id="patient-avatar"
                 src="{{ default_user_avatar() }}"
                 class="rounded-circle border object-fit-cover"
                 width="64" height="64"
                 alt="{{ __('appointment.lbl_avatar') }}">

            <!-- Patient details -->
            <div class="flex-grow-1">
                <h6 id="patient-name" class="mb-1 fw-semibold heading-color"></h6>
                <small id="patient-since" class="d-block mb-2 text-muted"></small>
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">
                        <b class="heading-color">{{ __('appointment.lbl_phone') }}:</b>
                        <span id="patient-phone" class="text-dark"></span>
                    </small>
                    <small class="text-muted">
                        <b class="heading-color">{{ __('appointment.lbl_email') }}:</b>
                        <span id="patient-email" class="text-dark"></span>
                    </small>
                </div>
            </div>
        </div>

        {{-- Booking For Section --}}
        <div class="booking-for-section mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label mb-0">{{ __('appointment.lbl_booking_for') }}</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="booking-for-toggle" name="booking_for_others">
                    <label class="form-check-label" for="booking-for-toggle"></label>
                </div>
            </div>

            <div id="other-patients-section" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="#" class="btn btn-link p-0 text-primary" id="add-other-patient-btn">{{ __('appointment.lbl_add_other_patient') }}</a>
                </div>
                <div id="other-patients-list" class="d-flex align-items-center flex-wrap column-gap-4 row-gap-3 mt-2">
                    <div class="text-muted small">{{ __('appointment.lbl_no_other_patients_found') }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- Clinic Selection --}}
            <div class="col-md-6">
                <label class="form-label">{{ __('appointment.lbl_select_clinic') }} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <select id="clinic-select" class="form-select select2" name="clinic_id" data-placeholder="{{ __('appointment.lbl_select_clinic') }}">
                        <option value=""></option>
                    </select>
                    <div id="clinic-loader" class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                    </div>
                </div>
            </div>

            {{-- Doctor Selection --}}
            <div class="col-md-6">
                <label class="form-label">{{ __('appointment.lbl_select_doctor') }} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <select id="doctor-select" class="form-select select2" name="doctor_id" data-placeholder="{{ __('appointment.lbl_select_doctor') }}">
                        <option value=""></option>
                    </select>
                    <div id="doctor-loader" class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                    </div>
                </div>
            </div>

            {{-- Service Selection --}}
            <div class="col-md-6">
                <label class="form-label">{{ __('appointment.lbl_select_service') }} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <select id="service-select" class="form-select select2" name="service_id" data-placeholder="{{ __('appointment.lbl_select_service') }}">
                        <option value=""></option>
                    </select>
                    <div id="service-loader" class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                    </div>
                </div>
            </div>

            {{-- Appointment Date --}}
            <div class="col-md-6">
                <label class="form-label">{{ __('appointment.lbl_appointment_date') }} <span class="text-danger">*</span></label>
                <input type="text" id="appointment-date" name="appointment_date" class="form-control" placeholder="{{ __('appointment.lbl_appointment_date') }}">
            </div>

            {{-- Available Slots --}}
            <div class="col-12">
                <label class="form-label">{{ __('appointment.lbl_availble_slots') }} <span class="text-danger">*</span></label>
                <div id="available-slots" class="slots-container">
                    <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
                </div>
            </div>

            {{-- Medical Report Upload --}}
            <div class="col-12 mb-3">
                <label class="form-label">{{ __('appointment.lbl_medical_report') }}</label>
                <input type="file" id="medical-report" class="form-control" name="file_url[]" multiple accept=".jpeg, .jpg, .png, .gif, .pdf">
            </div>

            {{-- Medical History --}}
            <div class="col-12 mb-3">
                <label class="form-label">{{ __('appointment.lbl_medical_history') }}</label>
                <textarea id="medical-history" class="form-control" name="appointment_extra_info" rows="4" placeholder="{{ __('appointment.lbl_medical_history_placeholder') }}"></textarea>
            </div>
        </div>
    </div>
</div>

{{-- Fixed bottom section - Pricing --}}
<div class="mt-auto">
    <div class="custom-pricing-box mt-4 bg-gray-900 p-3 rounded border">
        <div class="custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
            <span class="custom-label" id="service-price-label">{{ __('appointment.lbl_service_price') }}:</span>
            <span class="custom-value text-end text-primary fw-bold" id="service-price">{{ $currencySymbol }}0.00</span>
        </div>

        <div class="custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2 d-none" id="discount-row">
            <span class="custom-label" id="discount-label">{{ __('appointment.lbl_discount') }}:</span>
            <span class="custom-value text-success" id="discount-amount">-{{ $currencySymbol }}0.00</span>
        </div>

        <div class="custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2 d-none" id="subtotal-row">
            <span class="custom-label" id="subtotal-label">{{ __('appointment.lbl_subtotal') }}:</span>
            <span class="custom-value" id="subtotal-amount">{{ $currencySymbol }}0.00</span>
        </div>

        <!-- Inline tax (dynamic) -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="font-size-14">{{ __('appointment.lbl_tax') }}</span>
            <div class="cursor-pointer applied-tax" data-bs-toggle="collapse" data-bs-target="#applied-tax" aria-expanded="false">
                <i class="ph ph-caret-down fw-semibold" id="tax-caret-icon"></i>
                <span class="text-danger h6 m-0" id="tax-inline-amount">{{ $currencySymbol }}0.00</span>
            </div>
        </div>
        <div id="applied-tax" class="mt-2 p-3 card m-0 rounded collapse">
            <h6 class="font-size-14">{{ __('appointment.lbl_applied_tax') }}</h6>
            <div id="applied-tax-inline">
                <div class="text-center bg-body py-3 rounded">
                    <i class="ph ph-receipt mb-2 fs-2"></i>
                    <!-- <i class="fas fa-receipt fa-2x mb-2"></i> -->
                    <p class="mb-0">{{ __('appointment.lbl_no_taxes_applied') }}</p>
                </div>
            </div>
        </div>

        <div class="custom-pricing-divider"></div>
        <div class="custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
            <span class="custom-label fw-bold">{{ __('appointment.lbl_total_amount') }}:</span>
            <span class="custom-value text-success fw-bold" id="total-amount">{{ $currencySymbol }}0.00</span>
        </div>
    </div>

    {{-- Add Other Patient Modal --}}
    <div class="modal fade" id="addOtherPatientModal" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('appointment.lbl_add_other_patient') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column - Patient Photo --}}
                        <div class="col-md-4">
                            <div class="patient-photo-section">
                                <label class="form-label">{{ __('appointment.lbl_patient_photo') }}</label>
                                <div class="patient-photo-upload">
                                    <div class="photo-preview-container">
                                        <img id="patient-photo-preview" src="{{ asset('img/avatar/avatar.webp') }}"
                                            class="photo-preview" alt="{{ __('appointment.lbl_patient_photo_alt') }}">
                                    </div>
                                    <div class="photo-actions mt-3 d-flex justify-content-center">
                                        <button type="button" class="btn btn-sm btn-info" id="upload-photo-btn">{{ __('appointment.lbl_upload') }}</button>
                                    </div>
                                    <input type="file" id="patient-photo-input" accept="image/*" class="d-none">
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - Form Fields --}}
                        <div class="col-md-8">
                            <div class="patient-details-section">
                                {{-- First Name --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_first_name') }}</label>
                                    <input type="text" id="other-patient-first-name" class="form-control" placeholder="{{ __('appointment.lbl_first_name') }}">
                                </div>

                                {{-- Last Name --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_last_name') }}</label>
                                    <input type="text" id="other-patient-last-name" class="form-control" placeholder="{{ __('appointment.lbl_last_name') }}">
                                </div>

                                {{-- Date of Birth --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_date_of_birth') }}</label>
                                    <input type="text" id="other-patient-dob" class="form-control" placeholder="{{ __('appointment.lbl_date_of_birth') }}" readonly>
                                </div>

                                {{-- Contact Number --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_contact_number') }}</label>
                                    <input type="tel" id="other-patient-contact" class="form-control" placeholder="{{ __('appointment.lbl_enter_phone_number') }}">
                                </div>


                                {{-- Gender --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_gender') }}</label>
                                    <div class="gender-selection">
                                        <input type="radio" class="btn-check" name="other-patient-gender" id="gender-male" value="male">
                                        <label class="btn btn-outline-primary" for="gender-male">{{ __('appointment.lbl_male') }}</label>

                                        <input type="radio" class="btn-check" name="other-patient-gender" id="gender-female" value="female">
                                        <label class="btn btn-outline-secondary" for="gender-female">{{ __('appointment.lbl_female') }}</label>

                                        <input type="radio" class="btn-check" name="other-patient-gender" id="gender-other" value="other">
                                        <label class="btn btn-outline-secondary" for="gender-other">{{ __('appointment.lbl_other') }}</label>
                                    </div>
                                </div>

                                {{-- Relation --}}
                                <div class="mb-3">
                                    <label class="form-label">{{ __('appointment.lbl_relation') }}</label>
                                    <div class="relation-selection">
                                        <input type="radio" class="btn-check" name="other-patient-relation" id="relation-parents" value="parents">
                                        <label class="btn btn-outline-primary" for="relation-parents">{{ __('appointment.lbl_parents') }}</label>

                                        <input type="radio" class="btn-check" name="other-patient-relation" id="relation-sibling" value="sibling">
                                        <label class="btn btn-outline-secondary" for="relation-sibling">{{ __('appointment.lbl_sibling') }}</label>

                                        <input type="radio" class="btn-check" name="other-patient-relation" id="relation-spouse" value="spouse">
                                        <label class="btn btn-outline-secondary" for="relation-spouse">{{ __('appointment.lbl_spouse') }}</label>

                                        <input type="radio" class="btn-check" name="other-patient-relation" id="relation-other" value="other">
                                        <label class="btn btn-outline-secondary" for="relation-other">{{ __('appointment.lbl_other') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('appointment.lbl_close') }}</button>
                    <button type="button" class="btn btn-primary" id="confirm-add-patient">{{ __('appointment.lbl_save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="d-flex justify-content-end align-items-center mt-3 gap-2">
        <button type="button" class="btn btn-white" data-bs-dismiss="offcanvas" id="close-appointment-btn">{{ __('appointment.lbl_close') }}</button>
        <button class="btn btn-secondary" type="submit" id="save-appointment-btn">
            <span class="save-btn-text">{{ __('appointment.lbl_save') }}</span>
            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="save-btn-spinner"></span>
            <span class="loading-text d-none">{{ __('appointment.lbl_loading') }}...</span>
        </button>
    </div>
</div>
</form>

{{-- Appointment Forms CSS --}}
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- International Telephone Input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>

<script>
    $(function () {
        var patients = @json($patientsData),
            csrfToken = '{{ csrf_token() }}',
            currentAppointmentId = null;

        // Define routes object for API endpoints
        const baseUrl = "{{ url('/') }}";
        const routes = {
            doctorList: "{{ route('backend.appointments.doctor.index_list') }}",
            serviceList: "{{ route('backend.appointments.services.index_list') }}",
            clinicList: "{{ route('backend.appointments.clinics.index_list') }}",
            customerList: "{{ route('backend.appointments.customers.index_list') }}",
            taxList: "{{ route('backend.appointments.tax.index_list') }}",
            appointmentList: "{{ route('backend.appointments.other_patientlist') }}",
            availableSlots: "{{ route('backend.appointments.doctor.availableSlot') }}",
            servicePrice: "{{ route('backend.appointments.services.service_price') }}",
            appointmentStore: "{{ route('backend.appointment.store') }}",
            appointmentSavePayment: "{{ route('backend.appointment.save_payment') }}",
            appointmentOtherPatient: "{{ route('backend.appointment.other_patient') }}"
        };

        // Format currency using dynamic currency symbol
        const currencySymbol = '{{ $currencySymbol }}';
        function fmt(n) {
            if (typeof window.currencyFormat === 'function') {
                return window.currencyFormat(parseFloat(n || 0));
            }
            return currencySymbol + parseFloat(n || 0).toFixed(2);
        }

        // Function to safely destroy Select2 instances
        function safeDestroySelect2($element) {
            if ($element.hasClass('select2-hidden-accessible')) {
                try {
                    $element.select2('destroy');
                } catch (e) {
                    $element.removeClass('select2-hidden-accessible');
                    $element.next('.select2-container').remove();
                }
            }
        }

        // Initialize Select2
        function initClinicAppointmentSelect2() {
            $('.select2').each(function () {
                safeDestroySelect2($(this));
            });

            $('.select2').each(function () {
                let $t = $(this);
                if (!$t.hasClass('select2-hidden-accessible') && !$t.next('.select2-container').length) {
                    $t.select2({
                        width: '100%',
                        allowClear: false,
                        placeholder: $t.data('placeholder') || "{{ __('appointment.lbl_select_option') }}",
                        dropdownParent: $t.closest('.modal,.offcanvas').length ? $t.closest('.modal,.offcanvas') : $(document.body),
                        minimumResultsForSearch: 0,
                        language: {
                            noResults: function () {
                                return "{{ __('appointment.lbl_no_results_found') }}";
                            },
                            searching: function () {
                                return "{{ __('appointment.lbl_searching') }}";
                            }
                        }
                    });
                }
            });
        }

        // Refresh Select2 dropdown
        function refreshSelect($el, data) {
            safeDestroySelect2($el);

            $el.empty().append('<option value=""></option>');
            data.forEach(function (d) {
                $el.append(new Option(d.text, d.id, false, false));
            });
            $el.val(null).trigger('change');

            if (!$el.hasClass('select2-hidden-accessible') && !$el.next('.select2-container').length) {
                $el.select2({
                    width: '100%',
                    allowClear: false,
                    placeholder: $el.data('placeholder') || "{{ __('appointment.lbl_select_option') }}",
                    dropdownParent: $el.closest('.modal,.offcanvas').length ? $el.closest('.modal,.offcanvas') : $(document.body),
                    minimumResultsForSearch: 0
                });
            }
        }

        // Load Patient List - Patients are already loaded from PHP, just initialize Select2
        function loadPatientList() {
            // Patients are already in the dropdown from PHP
            // Just ensure Select2 is initialized
            if (!$('#patient-select').hasClass('select2-hidden-accessible')) {
                $('#patient-select').select2({
                    width: '100%',
                    allowClear: false,
                        placeholder: "{{ __('appointment.lbl_select_patient') }}",
                    dropdownParent: $('#patient-select').closest('.modal,.offcanvas').length ? $('#patient-select').closest('.modal,.offcanvas') : $(document.body),
                    minimumResultsForSearch: 0
                });
            }
        }

        // Load Clinic List
        function loadClinicList() {
            $('#clinic-loader').removeClass('d-none');
            $('#clinic-select').prop('disabled', true);

            $.getJSON(routes.clinicList, function (data) {
                let clinicOptions = data.map(function (clinic) {
                    return {
                        id: clinic.id,
                        text: clinic.name || clinic.clinic_name || "{{ __('appointment.lbl_unknown_clinic') }}"
                    };
                });
                refreshSelect($('#clinic-select'), clinicOptions);
            }).fail(function () {
                console.error('Failed to load clinics');
            }).always(function() {
                $('#clinic-loader').addClass('d-none');
                $('#clinic-select').prop('disabled', false);
            });
        }

        // Initialize select2 and load lists
        initClinicAppointmentSelect2();
        loadPatientList();
        loadClinicList();

        // Patient Selection Handler
        $('#patient-select').on('change', function () {
            let id = $(this).val();
            if (id && patients[id]) {
                let p = patients[id];
                $('#appointment-details').removeClass('d-none');
                $('#patient-name').text(p.name);
                $('#patient-since').text(p.created_at ? ('{{ __("appointment.lbl_patient_since") }} ' + p.created_at) : '');
                $('#patient-phone').text(p.mobile);
                $('#patient-email').text(p.email);
                $('#patient-avatar').attr('src', p.avatar);
            } else {
                $('#appointment-details').addClass('d-none');
            }
            checkAllRequiredFields();
        });

        // Clinic → Doctor
        $('#clinic-select').on('change', function () {
            let clinicId = $(this).val();
            refreshSelect($('#doctor-select'), []);
            refreshSelect($('#service-select'), []);
            $('#doctor-loader').removeClass('d-none');
            $('#doctor-select').prop('disabled', true);

            if (!clinicId) {
                $('#doctor-loader').addClass('d-none');
                $('#doctor-select').prop('disabled', false);
                checkAllRequiredFields();
                return;
            }

            $.getJSON(routes.doctorList, { clinic_id: clinicId }, function (d) {
                refreshSelect($('#doctor-select'), d.map(function (dd) {
                    return { id: dd.doctor_id || dd.id, text: dd.doctor_name || dd.name };
                }));
            }).fail(function () {
                console.error('Failed to load doctors');
            }).always(function() {
                $('#doctor-loader').addClass('d-none');
                $('#doctor-select').prop('disabled', false);
                checkAllRequiredFields();
            });
        });

        // Doctor → Service
        $('#doctor-select').on('change', function () {
            let doctorId = $(this).val(),
                clinicId = $('#clinic-select').val();
            refreshSelect($('#service-select'), []);
            $('#service-loader').removeClass('d-none');
            $('#service-select').prop('disabled', true);

            if (!doctorId || !clinicId) {
                $('#service-loader').addClass('d-none');
                $('#service-select').prop('disabled', false);
                checkAllRequiredFields();
                return;
            }

            $.getJSON(routes.serviceList, { doctor_id: doctorId, clinic_id: clinicId }, function (d) {
                console.log(d);
                refreshSelect($('#service-select'), d.map(function (s) {
                    console.log(s.id);
                    return { id: s.id, text: s.name };
                }));
            }).fail(function () {
                console.error('Failed to load services');
            }).always(function() {
                $('#service-loader').addClass('d-none');
                $('#service-select').prop('disabled', false);
                checkAllRequiredFields();
            });
        });

        // Service → Pricing
        $('#service-select').on('change', function () {
            let serviceId = $(this).val(),
                doctorId = $('#doctor-select').val(),
                clinicId = $('#clinic-select').val();

            if (!serviceId || !doctorId || !clinicId) {
                checkAllRequiredFields();
                return;
            }

            $.getJSON(routes.servicePrice, { service_id: serviceId, doctor_id: doctorId }, function (r) {
                let base = parseFloat(r.base_price || 0),
                    disc = parseFloat(r.discount || 0),
                    inclusiveTaxAmount = parseFloat(r.inclusive_tax_amount || 0),
                    isInclusiveTax = r.is_inclusive_tax || false;

                // Show service price with inclusive tax if applicable
                let displayPrice = isInclusiveTax && inclusiveTaxAmount > 0 ? base + inclusiveTaxAmount : base;
                $('#service-price').text(fmt(displayPrice));

                // Update label to indicate inclusive tax
                if (isInclusiveTax && inclusiveTaxAmount > 0) {
                    $('#service-price-label').html('{{ __("appointment.lbl_service_price") }}: <span class="text-danger">({{ __("appointment.lbl_with_inclusive_tax") }})</span>');
                } else {
                    $('#service-price-label').text('{{ __("appointment.lbl_service_price") }}:');
                }

                $('#inclusive-tax-list').html('');

                if (isInclusiveTax && r.inclusive_tax_data && r.inclusive_tax_data.length > 0) {
                    let inclusiveTaxHtml = '';
                    r.inclusive_tax_data.forEach(function (t) {
                        inclusiveTaxHtml += `
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="heading-color fw-medium">${t.title || 'Tax'} (${t.value}${t.type == 'percent' ? '%' : ''})</span>
                                <span class="heading-color fw-bold">${fmt(t.amount)}</span>
                            </div>
                        `;
                    });
                    inclusiveTaxHtml += `
                        <div class="d-flex justify-content-between align-items-center py-3 mt-2">
                            <span class="heading-color fw-bold fs-5">{{ __('appointment.lbl_total_inclusive_tax') }}</span>
                            <span class="text-primary fw-bold fs-5">${fmt(inclusiveTaxAmount)}</span>
                        </div>
                    `;
                    $('#inclusive-tax-list').html(inclusiveTaxHtml);
                    $('#inclusive-tax-row').removeClass('d-none');
                } else {
                    $('#inclusive-tax-list').html(`
                        <div class="text-center bg-body py-3 rounded">
                            <i class="ph ph-receipt mb-2 fs-2"></i>
                            <p class="mb-0">{{ __('appointment.lbl_no_inclusive_taxes_applied') }}</p>
                        </div>
                    `);
                    $('#inclusive-tax-row').addClass('d-none');
                }

                // Subtotal = displayPrice - discount
                let subtotal = displayPrice - disc;
                $('#subtotal-row').removeClass('d-none');
                $('#subtotal-amount').text(fmt(subtotal));

                if (disc > 0) {
                    $('#discount-row').removeClass('d-none');
                    $('#discount-amount').text('-' + fmt(disc));

                    if (r.discount_type === 'percentage' || r.discount_type === 'percent') {
                        let discountPercent = r.discount_value || r.discount_percentage || 0;
                        $('#discount-label').text('{{ __("appointment.lbl_discount") }} (' + discountPercent + '%):');
                    } else {
                        $('#discount-label').text('{{ __("appointment.lbl_discount") }}:');
                    }
                } else {
                    $('#discount-row').addClass('d-none');
                }

                // Load taxes
                    $.getJSON(routes.taxList, {
                    service_id: serviceId,
                    doctor_id: doctorId,
                    clinic_id: clinicId,
                    subtotal: subtotal,
                    tax_type: r.tax_type || ''
                }, function (taxRes) {
                    let totalExclusiveTax = 0;

                    if (Array.isArray(taxRes) && taxRes.length > 0) {
                        let hasTaxes = false;
                        let taxHtml = '';

                        taxRes.forEach(t => {
                            if (t.tax_type === 'exclusive' && t.status == 1) {
                                let taxValue = 0;
                                if (t.type === 'percent') {
                                    taxValue = (subtotal * t.value) / 100;
                                } else {
                                    taxValue = t.value;
                                }
                                totalExclusiveTax += taxValue;
                                hasTaxes = true;
                                const taxLabel = t.type === 'percent'
                                    ? `${t.title || 'Service Tax'} (${t.value}%)`
                                    : `${t.title || 'Service Tax'}`;
                                taxHtml += `
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span class="heading-color fw-medium">${taxLabel}</span>
                                        <span class="heading-color fw-bold">${fmt(taxValue)}</span>
                                    </div>
                                `;
                            }
                        });

                        if (hasTaxes) {
                            $('#applied-tax-inline').html(taxHtml);
                        } else {
                            const noTaxHtml = `
                                <div class="text-center bg-body py-3 rounded">
                                    <i class="ph ph-receipt mb-2 fs-2"></i>
                                    <p class="mb-0">{{ __('appointment.lbl_no_taxes_applied') }}</p>
                                </div>`;
                            $('#applied-tax-inline').html(noTaxHtml);
                        }
                    } else {
                        const noTaxHtml = `
                            <div class="text-center bg-body py-3 rounded">
                                <i class="ph ph-receipt mb-2 fs-2"></i>
                                <p class="mb-0">{{ __('appointment.lbl_no_taxes_applied') }}</p>
                            </div>`;
                        $('#applied-tax-inline').html(noTaxHtml);
                    }

                    let total = subtotal + totalExclusiveTax;
                    $('#tax-inline-amount').text(fmt(totalExclusiveTax));
                    $('#total-amount').text(fmt(total));
                });
            });
            checkAllRequiredFields();
        });

        // Flatpickr initialization
        flatpickr("#appointment-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "Y-m-d",
            minDate: "today",
            onChange: function () {
                clearAllErrorMessages();
                checkAllRequiredFields();
            }
        });

        // Load Slots
        $('#appointment-date,#doctor-select,#service-select,#clinic-select').on('change', function () {
            clearAllErrorMessages();
            let date = $('#appointment-date').val(),
                doctorId = $('#doctor-select').val(),
                clinicId = $('#clinic-select').val(),
                serviceId = $('#service-select').val();

            if (!(date && doctorId && clinicId && serviceId)) {
                checkAllRequiredFields();
                return;
            }

            $('#available-slots').html('<div class="slots-loading text-center d-flex align-items-center justify-content-center p-4"><i class="fas fa-spinner fa-spin text-primary me-2"></i><span class="text-muted">{{ __('appointment.lbl_loading_available_slots') }}</span></div>');

            $.getJSON(routes.availableSlots, {
                appointment_date: date,
                doctor_id: doctorId,
                clinic_id: clinicId,
                service_id: serviceId
            }, function (r) {
                    let slots = r.availableSlot || r.data || [];
                    $('#available-slots').html('');

                    if (slots.length === 0) {
                        $('#available-slots').html(`
                            <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
                        `);
                    } else {
                        // Create slots grid
                        let slotsHtml = '<div class="slots-grid avb-slot d-flex flex-wrap align-items-center gap-3 form-chceck">';
                        slots.forEach(function (s, i) {
                            slotsHtml += `
                                <input type="radio" class="btn-check form-check-input" id="slot${i}" name="appointment_time" value="${s}" required>
                                <label for="slot${i}" class="clickable-text form-check-label">${s}</label>
                            `;
                        });
                        slotsHtml += '</div>';
                        $('#available-slots').html(slotsHtml);
                    }
                    checkAllRequiredFields();
            }).fail(function () {
                $('#available-slots').html(`
                    <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
                `);
                checkAllRequiredFields();
            });
            $(document).on('change', 'input[name="appointment_time"]', function () {
                $('label.form-check-label').removeClass('selected_slot');
                $(this).next('label').addClass('selected_slot');
            });
        });

        // Form Submit
        $('#clinic-appointment-form').on('submit', function (e) {
            e.preventDefault();
            let fd = new FormData(this),
                slot = $('input[name="appointment_time"]:checked').val();

            $('.field-error').remove();

            // Validation
            var patientId = $('#patient-select').val();
            var clinicId = $('#clinic-select').val();
            var doctorId = $('#doctor-select').val();
            var serviceId = $('#service-select').val();
            var appointmentDate = $('#appointment-date').val();
            var hasErrors = false;

            if (!patientId) {
                $('#patient-select').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_patient_required') }}</div>');
                hasErrors = true;
            }
            if (!clinicId) {
                $('#clinic-select').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_clinic_required') }}</div>');
                hasErrors = true;
            }
            if (!doctorId) {
                $('#doctor-select').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_doctor_required') }}</div>');
                hasErrors = true;
            }
            if (!serviceId) {
                $('#service-select').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_service_required') }}</div>');
                hasErrors = true;
            }
            if (!appointmentDate) {
                $('#appointment-date').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_appointment_date_required') }}</div>');
                hasErrors = true;
            }
            if (!slot) {
                $('#available-slots').parent().append('<div class="field-error text-danger small mt-1">{{ __('appointment.lbl_time_slot_required') }}</div>');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            fd.set('appointment_time', slot);
            fd.set('user_id', patientId);

            // Add other patients if any
            if (otherPatients.length > 0) {
                fd.set('other_patients', JSON.stringify(otherPatients));
            }
            
            // Add selected other patient if any
            if (selectedOtherPatient) {
                var otherId = (typeof selectedOtherPatient === 'object') ? selectedOtherPatient.id : selectedOtherPatient;
                fd.set('otherpatient_id', otherId);
            }

            $('#save-appointment-btn .save-btn-text').addClass('d-none');
            $('#save-btn-spinner').removeClass('d-none');
            $('#save-appointment-btn .loading-text').removeClass('d-none');
            $('#save-appointment-btn').prop('disabled', true);

            // Determine URL based on create or update
            let url = currentAppointmentId
                ? routes.appointmentUpdate.replace(':id', currentAppointmentId)
                : routes.appointmentStore;

            if (currentAppointmentId) {
                fd.append('_method', 'PUT');
            }

                $.ajax({
                url: url,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(function (res) {
                if (res.status) {
                    $.ajax({
                        url: routes.appointmentSavePayment,
                        method: 'POST',
                        data: res.data,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    }).done(function () {
                        resetAppointmentForm();
                        let $offcanvas = $('#clinic-appointment-form').closest('.offcanvas');
                        let $modal = $('#clinic-appointment-form').closest('.modal');
                        if ($offcanvas.length) {
                            let offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance($offcanvas[0]);
                            offcanvasInstance.hide();
                        }
                        if ($modal.length) {
                            let modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
                            modalInstance.hide();
                        }

                        // Reload datatable if exists
                        if (window.renderedDataTable && typeof window.renderedDataTable.ajax === 'function') {
                            window.renderedDataTable.ajax.reload(null, false);
                        } else if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables['datatable']) {
                            window.LaravelDataTables['datatable'].ajax.reload(null, false);
                        } else if (typeof window.$ !== 'undefined' && $('#datatable').length) {
                            $('#datatable').DataTable().ajax.reload(null, false);
                        }
                    }).always(function () {
                        $('#save-appointment-btn .save-btn-text').removeClass('d-none');
                        $('#save-btn-spinner').addClass('d-none');
                        $('#save-appointment-btn .loading-text').addClass('d-none');
                        $('#save-appointment-btn').prop('disabled', false);
                    });
                } else {
                    $('#save-appointment-btn .save-btn-text').removeClass('d-none');
                    $('#save-btn-spinner').addClass('d-none');
                    $('#save-appointment-btn .loading-text').addClass('d-none');
                    clearAllErrorMessages();
                    $('#save-appointment-btn').prop('disabled', false);
                }
            }).fail(function (xhr) {
                showErrorMessage('{{ __("appointment.lbl_server_error") }}');
                    }).always(function () {
                        $('#save-appointment-btn .save-btn-text').removeClass('d-none');
                        $('#save-btn-spinner').addClass('d-none');
                        $('#save-appointment-btn .loading-text').addClass('d-none');
                        $('#save-appointment-btn').prop('disabled', false);
                    });
        });

        // Reset appointment form
        function resetAppointmentForm() {
            $('#clinic-appointment-form')[0].reset();
            $('#appointment_id').val('');
            currentAppointmentId = null;
            $('#patient-select').val(null).trigger('change');
            $('#clinic-select').val(null).trigger('change');
            $('#doctor-select').val(null).trigger('change');
            $('#service-select').val(null).trigger('change');
            $('#appointment-date').val('');
            $('#appointment-details').addClass('d-none');
            $('#available-slots').html(`
                <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
            `);
            $('#service-price').text(fmt(0));
            $('#service-price-label').text('{{ __("appointment.lbl_service_price") }}:');
            $('#inclusive-tax-row').addClass('d-none');
            $('#discount-row').addClass('d-none');
            $('#subtotal-row').addClass('d-none');
            $('#tax-inline-amount').text(fmt(0));
            $('#applied-tax-inline').html(`
                <div class="text-center bg-body py-3 rounded">
                    <i class="ph ph-receipt mb-2 fs-2"></i>
                    <p class="mb-0">{{ __('appointment.lbl_no_taxes_applied') }}</p>
                </div>
            `);
            $('#total-amount').text(fmt(0));
                    clearAllErrorMessages();
            $('#medical-report').val('');
            $('#medical-history').val('');
            $('input[name="appointment_time"]').prop('checked', false);
            $('#save-appointment-btn').removeClass('all-fields-filled');
        }

        // Load appointment for editing
        window.loadClinicAppointment = function(appointmentId) {
            currentAppointmentId = appointmentId;

            $.getJSON(routes.appointmentEdit.replace(':id', appointmentId), function(res) {
                if (res.status && res.data) {
                    let data = res.data;
                    $('#appointment_id').val(data.id);
                    $('#patient-select').val(data.patient_id).trigger('change');
                    $('#clinic-select').val(data.clinic_id).trigger('change');

                    setTimeout(function() {
                        $('#doctor-select').val(data.doctor_id).trigger('change');
                        setTimeout(function() {
                            $('#service-select').val(data.service_id).trigger('change');
                            $('#appointment-date').val(data.appointment_date);
                        }, 500);
                    }, 500);
                }
            });
        };

        // Helper functions
        function clearAllErrorMessages() {
            $('.field-error').remove();
        }

        function checkAllRequiredFields() {
            var patientId = $('#patient-select').val();
            var clinicId = $('#clinic-select').val();
            var doctorId = $('#doctor-select').val();
            var serviceId = $('#service-select').val();
            var appointmentDate = $('#appointment-date').val();
            var selectedSlot = $('input[name="appointment_time"]:checked').val();

            var allFieldsFilled = patientId && clinicId && doctorId && serviceId && appointmentDate && selectedSlot;

            if (allFieldsFilled) {
                $('#save-appointment-btn').addClass('all-fields-filled');
            } else {
                $('#save-appointment-btn').removeClass('all-fields-filled');
            }
        }

        function showSuccessMessage(message) {
            var notification = $('<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;">' +
                '<i class="fas fa-check-circle me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('body').append(notification);
            setTimeout(function () {
                notification.alert('close');
            }, 5000);
        }

        function showErrorMessage(message) {
            var notification = $('<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;">' +
                '<i class="fas fa-exclamation-circle me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('body').append(notification);
            setTimeout(function () {
                notification.alert('close');
            }, 7000);
        }

        // Event listeners
        $('#close-appointment-btn').on('click', function () {
            resetAppointmentForm();
        });

        $(document).on('hidden.bs.offcanvas', function (e) {
            if ($(e.target).find('#clinic-appointment-form').length > 0) {
                resetAppointmentForm();
            }
        });

        $(document).on('change', 'input[name="appointment_time"]', function () {
            clearAllErrorMessages();
            checkAllRequiredFields();
        });

        $('#patient-select, #clinic-select, #doctor-select, #service-select, #appointment-date').on('change', function () {
            clearAllErrorMessages();
        });

        // ========== OTHER PATIENTS FUNCTIONALITY ==========
        var otherPatients = [];

        // Booking For Toggle
        $('#booking-for-toggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('#other-patients-section').removeClass('d-none');
                updateOtherPatientsList();
                otherPatientsListAndDisplay();
            } else {
                $('#other-patients-section').addClass('d-none');
                otherPatients = [];

            }
        });

        // Add Other Patient Button
        $('#add-other-patient-btn').on('click', function (e) {
            e.preventDefault();
            $('#addOtherPatientModal').modal('show');
            $('.modal-backdrop').remove();
        });

        // Upload Photo Button
        $('#upload-photo-btn').on('click', function () {
            $('#patient-photo-input').click();
        });

        // Photo Input Change
        $('#patient-photo-input').on('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#patient-photo-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove Photo Button
        $('#remove-photo-btn').on('click', function () {
            $('#patient-photo-preview').attr('src', '{{ asset("img/avatar/avatar.webp") }}');
            $('#patient-photo-input').val('');
        });

        // Initialize flatpickr for DOB when modal opens
        $('#addOtherPatientModal').on('show.bs.modal', function () {
            if (!$("#other-patient-dob").data('flatpickr')) {
                flatpickr("#other-patient-dob", {
                    dateFormat: "Y-m-d",
                    maxDate: "today"
                });
            }
        });

        // Confirm Add Patient
        $('#confirm-add-patient').on('click', function () {
            var firstName = $('#other-patient-first-name').val().trim();
            var lastName = $('#other-patient-last-name').val().trim();
            var dob = $('#other-patient-dob').val().trim();
            var contact = $('#other-patient-contact').val().trim();

            // Get full contact with country code using intl-tel-input
            var fullContact = itiOtherPatient ? itiOtherPatient.getNumber() : contact;

            var gender = $('input[name="other-patient-gender"]:checked').val();
            var relation = $('input[name="other-patient-relation"]:checked').val();

            var formData = new FormData();
            formData.append('user_id', $('#patient-select').val() || '');
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('dob', dob);
            formData.append('contactNumber', fullContact);
            formData.append('gender', gender || '');
            formData.append('relation', relation || '');

            var profileImage = $('#patient-photo-input')[0].files[0];
            if (profileImage) {
                formData.append('profile_image', profileImage);
            }

            var $btn = $(this);
            var originalText = $btn.text();
            $btn.prop('disabled', true).text("{{ __('appointment.saving') }}");

                $.ajax({
                url: routes.appointmentOtherPatient,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        var patientId = response.data.id;
                        var fullName = firstName + ' ' + lastName;
                        var avatar = response.data.profile_image || $('#patient-photo-preview').attr('src');
                        var patientData = {
                            id: patientId,
                            name: fullName,
                            first_name: firstName,
                            last_name: lastName,
                            email: '',
                            mobile: fullContact,
                            avatar: avatar,
                            created_at: "{{ __('appointment.new_patient') }}",
                            dob: dob,
                            gender: gender,
                            relation: relation,
                            is_new: true
                        };
                        otherPatients.push(patientData);
                        updateOtherPatientsList();
                        resetOtherPatientModal();
                        otherPatientsListAndDisplay();
                        $('#addOtherPatientModal').modal('hide');
                        showSuccessMessage('{{ __("appointment.other_patient_added") }}');
                    }
                },
                error: function(xhr) {
                    showErrorMessage('{{ __("appointment.error_adding_patient") }}');
                },
                complete: function () {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });

        function otherPatientsListAndDisplay() {
            const userId = $('#patient-select').val();
            otherPatients = []; // Clear array before populating

            $.ajax({
                url: "{{ route('backend.appointment.other_patientlist') }}",
                type: 'GET',
                data: {
                    patient_id: userId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var $list = $('#other-patients-list');
                    if (!response || response.length === 0) {
                        $list.html(
                            '<div class="text-muted small">{{ __('appointment.no_other_patients_found') }}</div>'
                        );
                        return;
                    }
                    var html = '';
                    response.forEach(function(patient, index) {
                        var isSelected = selectedOtherPatient && selectedOtherPatient.id === patient.id;
                        var cardClass = isSelected ? 'bg-primary text-white' : 'bg-white text-dark';
                        var iconClass = isSelected ? 'text-white' : 'text-dark';
                        otherPatients.push(patient);
                        html +=
                            '<div class="book-for-appointments ' + cardClass + '" data-patient-id="' +
                            patient.id + '">';
                        html += '<img src="' + (patient.avatar || patient.profile_image ||
                                "{{ asset('img/avatar/avatar.webp') }}") +
                            '" class="img-fluid rounded-circle avatar-35 object-fit-cover" alt="{{ __('appointment.avatar') }}" >';
                        html += '<h6 class="appointments-title mb-0 ' + iconClass + '">' + (patient.name ||
                            patient.first_name || '') + '</h6>';
                        html += '</div>';
                    });
                    $list.html(html);
                },
                error: function(xhr) {
                    $('#other-patients-list').html(
                        '<div class="text-danger small">Error fetching other patients.</div>');
                }
            });
        }

        // Reset Other Patient Modal
        function resetOtherPatientModal() {
            $('#other-patient-first-name').val('');
            $('#other-patient-last-name').val('');
            $('#other-patient-dob').val('');
            $('#other-patient-contact').val('');
            $('input[name="other-patient-gender"]').prop('checked', false);
            $('input[name="other-patient-relation"]').prop('checked', false);
            $('#patient-photo-preview').attr('src', '{{ asset("img/avatar/avatar.webp") }}');
            $('#patient-photo-input').val('');

            // Clear flatpickr
            if ($("#other-patient-dob").data('flatpickr')) {
                $("#other-patient-dob").data('flatpickr').clear();
            }

            // Reset contact input
            $('#other-patient-contact').val('');
            if (itiOtherPatient) {
                itiOtherPatient.setCountry('in');
            }
        }

        // Selected other patient
        var selectedOtherPatient = null;

        // Update Other Patients List
        function updateOtherPatientsList() {
            var $list = $('#other-patients-list');
            if (otherPatients.length === 0) {
                $list.html('<div class="text-muted small">{{ __('appointment.no_other_patients_found') }}</div>');
                return;
            }
            var html = '';
            otherPatients.forEach(function (patient, index) {
                var isSelected = selectedOtherPatient && selectedOtherPatient.id === patient.id;
                var cardClass = isSelected ? 'bg-primary text-white' : 'bg-white text-dark';
                var iconClass = isSelected ? 'text-white' : 'text-dark';

                html += '<div class="book-for-appointments cursor-pointer ' + cardClass + '" data-patient-id="' + patient.id + '" data-index="' + index + '" style="max-width: 200px; width: fit-content; min-height: 45px;">';
                html += '<img src="' + (patient.avatar || patient.profile_image ||
                                "{{ asset('img/avatar/avatar.webp') }}") +
                            '" class="img-fluid rounded-circle avatar-35 object-fit-cover" alt="{{ __('appointment.avatar') }}" >';
                html += '<h6 class="appointments-title mb-0 ' + iconClass + '">' + (patient.full_name ||
                            patient.first_name || '') + '</h6>';
                html += '</div>';
            });
            html += '</div>';
            $list.html(html);
        }

        $(document).on('click', '.book-for-appointments', function (e) {
            // Prevent selecting when clicking remove button (if any) inside card
            if ($(e.target).hasClass('remove-patient') || $(e.target).closest('.remove-patient').length) {
                return;
            }

            var patientId = $(this).data('patient-id');
            // Toggle selection: if already selected, deselect
            if (selectedOtherPatient && selectedOtherPatient.id === patientId) {
                selectedOtherPatient = null;
            } else {
                // find patient in otherPatients array (if populated) otherwise set id-only
                var idx = otherPatients.findIndex(function(p) { return p && (p.id === patientId || p.id == patientId); });
                if (idx !== -1) {
                    selectedOtherPatient = otherPatients[idx];
                } else {
                    selectedOtherPatient = { id: patientId };
                }
            }

            // Refresh both displays so classes update
            // updateOtherPatientsList();
            otherPatientsListAndDisplay();
        });

        // Select Other Patient
        $(document).on('click', '.other-patient-card', function (e) {
            if ($(e.target).hasClass('remove-patient') || $(e.target).closest('.remove-patient').length) {
                return; // Don't select if clicking remove button
            }
            
            var patientId = $(this).data('patient-id');
            var index = $(this).data('index');
            selectedOtherPatient = otherPatients[index];
            updateOtherPatientsList();
            otherPatientsListAndDisplay();
        });

        // Remove Other Patient
        $(document).on('click', '.remove-patient', function (e) {
            e.stopPropagation(); // Prevent card selection
            var index = $(this).data('index');
            
            // If removing selected patient, clear selection
            if (selectedOtherPatient && otherPatients[index] && selectedOtherPatient.id === otherPatients[index].id) {
                selectedOtherPatient = null;
            }
            
            otherPatients.splice(index, 1);
            updateOtherPatientsList();
        });

        // Reset Modal on Close
        $('#addOtherPatientModal').on('hidden.bs.modal', function () {
            clearAllErrorMessages();
            resetOtherPatientModal();
        });

        // Update resetAppointmentForm to include other patients
        var originalResetAppointmentForm = resetAppointmentForm;
        resetAppointmentForm = function () {
            originalResetAppointmentForm();
            $('#booking-for-toggle').prop('checked', false);
            $('#other-patients-section').addClass('d-none');
            otherPatients = [];
            selectedOtherPatient = null;
            updateOtherPatientsList();
            resetOtherPatientModal();
        };
        // ========== END OTHER PATIENTS FUNCTIONALITY ==========

        // Initialize International Telephone Input
        const otherPatientContactInput = document.querySelector("#other-patient-contact");
        const itiOtherPatient = window.intlTelInput(otherPatientContactInput, {
            initialCountry: "gb",
            separateDialCode: true,
            preferredCountries: ["gb", "us", "in", "de", "fr", "cn", "jp", "kr", "au", "br"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"
        });

        // Tax Section Toggle Functionality
        $('#applied-tax').on('show.bs.collapse', function () {
            $('#tax-caret-icon').removeClass('ph-caret-down').addClass('ph-caret-up');
        });

        $('#applied-tax').on('hide.bs.collapse', function () {
            $('#tax-caret-icon').removeClass('ph-caret-up').addClass('ph-caret-down');
        });

        // Initialize
        $(document).ready(function () {
            clearAllErrorMessages();
            checkAllRequiredFields();
            loadClinicList();
            loadPatientList();
        });
    });
</script>

