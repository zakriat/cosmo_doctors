@php
    $currencySymbol = Currency::defaultSymbol();
@endphp

{{-- Global Appointment Offcanvas --}}
<div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="global-appointment-offcanvas"
    aria-labelledby="globalAppointmentLabel" data-bs-backdrop="true" data-bs-keyboard="true">
    <form id="global-appointment-form" enctype="multipart/form-data" class="d-flex flex-column h-100">
        @csrf
        <input type="hidden" name="status" value="pending">

        {{-- Offcanvas Header --}}
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="globalAppointmentLabel">{{ __('messages.appointment') }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="{{ __('appointment.close') }}"></button>
        </div>

        {{-- Main content without extra scroll --}}
        <div class="offcanvas-body d-flex flex-column">
            <div class="flex-grow-1">
                {{-- Patient --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('appointment.select_patient') }} <span
                            class="text-danger">*</span></label>
                    <div class="position-relative">
                        <select id="global-patient-select" name="patient_id" class="form-select global-select2"
                            data-placeholder="{{ __('appointment.select_patient') }}">
                            <option value=""></option>
                        </select>
                        <div id="global-patient-loader"
                            class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                            <i class="fas fa-spinner fa-spin text-primary"></i>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Patient details box --}}
                <div id="global-appointment-details" style="display:none;">
                    <div class="global-patient-card d-flex m-0 mb-3 p-3 align-items-center border gap-lg-3 gap-2 flex-wrap bg-gray-900 rounded "
                        style="font-size: 1.05rem;">
                        <!-- Avatar -->
                        <img id="global-patient-avatar" src="{{ default_user_avatar() }}"
                            class="rounded-circle border object-fit-cover" width="64" height="64"
                            alt="{{ __('appointment.avatar') }}">

                        <!-- Patient details -->
                        <div class="flex-grow-1">
                            <h6 id="global-patient-name" class="mb-1 fw-semibold heading-color"></h6>
                            <small id="global-patient-since" class="d-block mb-2"></small>
                            <div class="d-flex flex-column gap-2">
                                <small class="text-body">
                                    <b class="heading-color">{{ __('appointment.phone') }}:</b>
                                    <span id="global-patient-phone"></span>
                                </small>
                                <small class="text-body">
                                    <b class="heading-color">{{ __('appointment.email') }}:</b>
                                    <span id="global-patient-email"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Booking For Section --}}
                    <div class="global-booking-for-section mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">{{ __('appointment.booking_for') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="global-booking-for-toggle"
                                    name="booking_for_others">
                                <label class="form-check-label" for="global-booking-for-toggle"></label>
                            </div>
                        </div>

                        <div id="global-other-patients-section" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="#" class="btn btn-link p-0 text-primary"
                                    id="global-add-other-patient-btn">{{ __('appointment.add_other_patient') }}</a>
                            </div>
                            <div id="global-other-patients-list" class="d-flex align-items-center flex-wrap column-gap-4 row-gap-3 mt-2">
                                <div class="text-muted small">{{ __('appointment.no_other_patients_found') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('appointment.select_clinic') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select id="global-clinic-select" class="form-select global-select2" name="clinic_id"
                                    data-placeholder="{{ __('appointment.select_clinic') }}">
                                    <option value=""></option>
                                </select>
                                <div id="global-clinic-loader"
                                    class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                                    <i class="fas fa-spinner fa-spin text-primary"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('appointment.select_doctor') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select id="global-doctor-select" class="form-select global-select2" name="doctor_id"
                                    data-placeholder="{{ __('appointment.select_doctor') }}">
                                    <option value=""></option>
                                </select>
                                <div id="global-doctor-loader"
                                    class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                                    <i class="fas fa-spinner fa-spin text-primary"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('appointment.select_service') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select id="global-service-select" class="form-select global-select2"
                                    name="service_id" data-placeholder="{{ __('appointment.select_service') }}">
                                    <option value=""></option>
                                </select>
                                <div id="global-service-loader"
                                    class="position-absolute top-50 start-50 translate-middle d-none" role="status">
                                    <i class="fas fa-spinner fa-spin text-primary"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('appointment.appointment_date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="global-appointment-date" name="appointment_date"
                                class="form-control" placeholder="{{ __('appointment.appointment_date') }}">
                        </div>

                        {{-- Slots --}}
                        <div class="col-12">
                            <label class="form-label">{{ __('appointment.lbl_availble_slots') }} <span
                                    class="text-danger">*</span></label>
                            <div id="global-available-slots" class="global-slots-container position-relative">
                                <p class="text-muted text-center bg-gray-900 p-3 rounded">
                                    {{ __('appointment.lbl_slot_not_found') }}</p>
                            </div>
                        </div>

                        <div class="col-12 mb-6">
                            <label class="form-label">{{ __('appointment.add_medical_report') }}</label>
                            <input type="file" id="global-medical-report" class="form-control" name="file_url[]"
                                multiple>
                        </div>

                        <div class="col-12 mb-6">
                            <label class="form-label">{{ __('appointment.medical_history') }}</label>
                            <textarea id="global-medical-history" class="form-control" name="appointment_extra_info" rows="4"
                                placeholder="{{ __('appointment.medical_history_placeholder') }}"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fixed bottom section with pricing --}}
            <div class="mt-auto">
                <div class="global-custom-pricing-box mt-4 bg-gray-900 p-3 rounded border mt-4">
                    <div
                        class="global-custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                        <span class="global-custom-label"
                            id="global-service-price-label">{{ __('appointment.service_price') }}:</span>
                        <span class="global-custom-value text-end text-primary fw-bold"
                            id="global-service-price">{{ $currencySymbol }}0.00</span>
                    </div>
                    <div class="global-custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2 d-none"
                        id="global-inclusive-tax-row">
                        <span class="global-custom-label">
                            {{ __('appointment.inclusive_tax') }}
                            <a href="#" data-bs-toggle="modal" data-bs-target="#globalInclusiveTaxModal"
                                class="global-custom-info-icon" title="{{ __('appointment.view_inclusive_taxes') }}">
                                <i class="fa-solid fa-arrow-up fa-xs" style="margin-right:2px;"></i>
                            </a>
                            :
                        </span>
                        <i class="fa-solid fa-arrow-down fa-xs"></i>
                        <span class="global-custom-value text-info" id="global-inclusive-tax-amount">$0.00</span>
                    </div>
                    <div class="global-custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2 d-none"
                        id="global-discount-row">
                        <span class="global-custom-label"
                            id="global-discount-label">{{ __('appointment.discount') }}:</span>
                        <span class="global-custom-value text-success"
                            id="global-discount-amount">-{{ $currencySymbol }}0.00</span>
                    </div>
                    <div class="global-custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2 d-none"
                        id="global-subtotal-row">
                        <span class="global-custom-label"
                            id="global-subtotal-label">{{ __('appointment.subtotal') }}:</span>
                        <span class="global-custom-value"
                            id="global-subtotal-amount">{{ $currencySymbol }}0.00</span>
                    </div>
                    <!-- Inline tax (dynamic) -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="font-size-14">{{ __('appointment.lbl_tax') }}</span>
                        <div class="cursor-pointer applied-tax" data-bs-toggle="collapse"
                            data-bs-target="#global-applied-tax" aria-expanded="false">
                            <i class="ph ph-caret-down fw-semibold" id="global-tax-caret-icon"></i>
                            <span class="text-danger h6 m-0" id="global-tax-amount">{{ $currencySymbol }}0.00</span>
                        </div>
                    </div>
                    <div id="global-applied-tax" class="mt-2 p-3 card m-0 bg-body rounded collapse">
                        <h6 class="font-size-14">{{ __('appointment.lbl_applied_tax') }}</h6>
                        <div id="global-applied-tax-inline" class="mb-2">
                            <div class="text-center bg-body py-3 rounded">
                                <i class="ph ph-receipt mb-2 fs-2"></i>
                                <p class="mb-0">{{ __('appointment.lbl_no_taxes_applied') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="global-custom-pricing-divider"></div>
                    <div
                        class="global-custom-pricing-row d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                        <span class="global-custom-label fw-bold">{{ __('appointment.total_amount') }}:</span>
                        <span class="global-custom-value text-success fw-bold"
                            id="global-total-amount">{{ $currencySymbol }}0.00</span>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-3 gap-2">
                    <button type="button" class="btn btn-white" data-bs-dismiss="offcanvas"
                        id="global-close-appointment-btn">{{ __('appointment.close') }}</button>
                    <button class="btn btn-secondary" type="submit" id="global-save-appointment-btn">
                        <span class="global-save-btn-text">{{ __('appointment.save') }}</span>
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"
                            id="global-save-btn-spinner"></span>
                        <span class="global-loading-text d-none">{{ __('appointment.lbl_loading') }}...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Inclusive Tax Modal --}}
<div class="modal fade" id="globalInclusiveTaxModal" data-bs-backdrop="true" data-bs-keyboard="true" tabindex="-1"
    aria-labelledby="globalInclusiveTaxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold heading-color" id="globalInclusiveTaxModalLabel">
                    {{ __('appointment.inclusive_tax_breakdown') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('appointment.close') }}"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="global-inclusive-tax-list">
                    <div class="text-center bg-body py-3 rounded">
                        <i class="ph ph-receipt mb-2 fs-2"></i>
                        <p class="mb-0">{{ __('appointment.no_inclusive_taxes_applied') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Other Patient Modal --}}
<div class="modal fade" id="globalAddOtherPatientModal" tabindex="-1" data-bs-backdrop="true"
    data-bs-keyboard="true" aria-labelledby="globalAddOtherPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalAddOtherPatientModalLabel">
                    {{ __('appointment.lbl_add_other_patient') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('appointment.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Left Column - Patient Photo --}}
                    <div class="col-md-4">
                        <div class="global-patient-photo-section">
                            <label class="form-label">{{ __('appointment.lbl_patient_photo') }}</label>
                            <div class="global-patient-photo-upload">
                                <div class="global-photo-preview-container">
                                    <img id="global-patient-photo-preview"
                                        src="{{ asset('img/avatar/avatar.webp') }}" class="global-photo-preview"
                                        alt="{{ __('appointment.lbl_patient_photo_alt') }}">
                                </div>
                                <div class="global-photo-actions mt-3 d-flex justify-content-center">
                                    <button type="button" class="btn btn-sm btn-info"
                                        id="global-upload-photo-btn">{{ __('appointment.lbl_upload') }}</button>
                                </div>
                                <input type="file" id="global-patient-photo-input" accept="image/*"
                                    style="display: none;">
                            </div>
                        </div>
                    </div>

                    {{-- Right Column - Form Fields --}}
                    <div class="col-md-8">
                        <div class="global-patient-details-section">
                            {{-- First Name --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.first_name') }}</label>
                                <input type="text" id="global-other-patient-first-name" class="form-control"
                                    placeholder="{{ __('appointment.first_name') }}">
                            </div>

                            {{-- Last Name --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.last_name') }}</label>
                                <input type="text" id="global-other-patient-last-name" class="form-control"
                                    placeholder="{{ __('appointment.last_name') }}">
                            </div>

                            {{-- Date of Birth --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.date_of_birth') }}</label>
                                <input type="text" id="global-other-patient-dob" class="form-control"
                                    placeholder="{{ __('appointment.date_of_birth') }}" readonly>
                            </div>

                            {{-- Contact Number --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.contact_number') }}</label>
                                <input type="tel" id="global-other-patient-contact" class="form-control"
                                    placeholder="{{ __('appointment.enter_phone_number') }}">
                            </div>


                            {{-- Gender --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.gender') }}</label>
                                <div class="global-gender-selection">
                                    <input type="radio" class="btn-check" name="global-other-patient-gender"
                                        id="global-gender-male" value="male">
                                    <label class="btn btn-outline-primary"
                                        for="global-gender-male">{{ __('appointment.male') }}</label>

                                    <input type="radio" class="btn-check" name="global-other-patient-gender"
                                        id="global-gender-female" value="female">
                                    <label class="btn btn-outline-secondary"
                                        for="global-gender-female">{{ __('appointment.female') }}</label>

                                    <input type="radio" class="btn-check" name="global-other-patient-gender"
                                        id="global-gender-other" value="other">
                                    <label class="btn btn-outline-secondary"
                                        for="global-gender-other">{{ __('appointment.other') }}</label>
                                </div>
                            </div>

                            {{-- Relation --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('appointment.relation') }}</label>
                                <div class="global-relation-selection">
                                    <input type="radio" class="btn-check" name="global-other-patient-relation"
                                        id="global-relation-parents" value="parents">
                                    <label class="btn btn-outline-primary"
                                        for="global-relation-parents">{{ __('appointment.parents') }}</label>

                                    <input type="radio" class="btn-check" name="global-other-patient-relation"
                                        id="global-relation-sibling" value="sibling">
                                    <label class="btn btn-outline-secondary"
                                        for="global-relation-sibling">{{ __('appointment.sibling') }}</label>

                                    <input type="radio" class="btn-check" name="global-other-patient-relation"
                                        id="global-relation-spouse" value="spouse">
                                    <label class="btn btn-outline-secondary"
                                        for="global-relation-spouse">{{ __('appointment.spouse') }}</label>

                                    <input type="radio" class="btn-check" name="global-other-patient-relation"
                                        id="global-relation-other" value="other">
                                    <label class="btn btn-outline-secondary"
                                        for="global-relation-other">{{ __('appointment.other') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"
                    data-bs-dismiss="modal">{{ __('appointment.close') }}</button>
                <button type="button" class="btn btn-secondary"
                    id="global-confirm-add-patient">{{ __('appointment.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Appointment Forms CSS --}}
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>
<script src="{{ asset('js/appointment-tax-modal.js') }}"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(function() {
        var patients = {},
            csrfToken = '{{ csrf_token() }}';

        // Define routes object for API endpoints using existing routes from web.php
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

        // Fallback URLs in case route() fails
        const fallbackRoutes = {
            doctorList: baseUrl + "/app/appointments/doctor/index_list",
            serviceList: baseUrl + "/app/appointments/services/index_list",
            clinicList: baseUrl + "/app/appointments/clinics/index_list",
            customerList: baseUrl + "/app/appointments/customers/index_list",
            taxList: baseUrl + "/app/appointments/tax/index_list",
            availableSlots: baseUrl + "/app/appointments/doctor/get-available-slot",
            servicePrice: baseUrl + "/app/appointments/services/service-price",
            appointmentStore: baseUrl + "/app/appointment",
            appointmentSavePayment: baseUrl + "/app/appointment/save-payment",
            appointmentOtherPatient: baseUrl + "/app/appointment/other-patient"
        };

        // Helper function to get route with fallback
        function getRoute(routeName) {
            return routes[routeName] || fallbackRoutes[routeName] || '';
        }

        // Format currency using dynamic currency symbol
        const currencySymbol = '{{ $currencySymbol }}';

        function fmt(n) {
            if (typeof window.currencyFormat === 'function') {
                return window.currencyFormat(parseFloat(n || 0));
            }
            return currencySymbol + parseFloat(n || 0).toFixed(2);
        }

        function initAppointmentSelect2() {
            // Only initialize Select2 for elements that don't already have it
            $('.global-select2').each(function() {
                let $t = $(this);
                // Check if Select2 is already initialized
                if (!$t.hasClass('select2-hidden-accessible')) {
                    $t.select2({
                        width: '100%',
                        allowClear: false,
                        placeholder: $t.data('placeholder') ||
                            "{{ __('appointment.select_option') }}",
                        dropdownParent: $t.closest('.modal,.offcanvas').length ? $t.closest(
                            '.modal,.offcanvas') : $(document.body),
                        minimumResultsForSearch: 0,
                        language: {
                            noResults: function() {
                                return "{{ __('appointment.no_results_found') }}";
                            },
                            searching: function() {
                                return "{{ __('appointment.searching') }}";
                            }
                        }
                    });
                }
            });
        }

        function refreshSelect($el, data) {
            // Destroy existing Select2 if it exists
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            // Clear and populate options
            $el.empty().append('<option value=""></option>');
            data.forEach(function(d) {
                $el.append(new Option(d.text, d.id, false, false));
            });
            $el.val(null).trigger('change');

            // Re-initialize Select2 after data is loaded
            $el.select2({
                width: '100%',
                allowClear: false,
                placeholder: $el.data('placeholder') || "{{ __('appointment.select_option') }}",
                dropdownParent: $el.closest('.modal,.offcanvas').length ? $el.closest(
                    '.modal,.offcanvas') : $(document.body),
                minimumResultsForSearch: 0,
                language: {
                    noResults: function() {
                        return "{{ __('appointment.no_results_found') }}";
                    },
                    searching: function() {
                        return "{{ __('appointment.searching') }}";
                    }
                }
            });
        }

        function loadPatientListFallback() {
            let patientOptions = [];
            refreshSelect($('#global-patient-select'), patientOptions);
            $('#global-patient-loader').addClass('d-none');
            $('#global-patient-select').prop('disabled', false);
        }

        function loadClinicListFallback() {
            let clinicOptions = [];
            refreshSelect($('#global-clinic-select'), clinicOptions);
            $('#global-clinic-loader').addClass('d-none');
            $('#global-clinic-select').prop('disabled', false);
        }

        function loadPatientList() {
            $('#global-patient-loader').removeClass('d-none');
            $('#global-patient-select').prop('disabled', true);
            setTimeout(() => {
                $.getJSON(getRoute('customerList'), function(data) {
                    let patientOptions = data.map(function(patient) {
                        let firstName = patient.first_name || patient.firstName ||
                            patient.firstname || '';
                        let lastName = patient.last_name || patient.lastName || patient
                            .lastname || '';
                        let fullName = (firstName + ' ' + lastName).trim();
                        if (!fullName && patient.name) {
                            fullName = patient.name;
                        }
                        // Store patient data for later use
                        // API returns 'avatar' key, so check both avatar and profile_image
                        let patientAvatar = patient.avatar || patient.profile_image ||
                            '';
                        patients[patient.id] = {
                            name: fullName ||
                                "{{ __('appointment.unknown_patient') }}",
                            email: patient.email || '',
                            mobile: patient.mobile || '',
                            created_at: patient.created_at ? new Date(patient
                                .created_at).toLocaleDateString('en-US', {
                                month: 'long',
                                year: 'numeric'
                            }) : '',
                            avatar: (patientAvatar && patientAvatar.trim() !== '') ?
                                patientAvatar : "{{ default_user_avatar() }}"
                        };
                        return {
                            id: patient.id,
                            text: fullName || "{{ __('appointment.unknown_patient') }}"
                        };
                    });
                    refreshSelect($('#global-patient-select'), patientOptions);
                    $('#global-patient-loader').addClass('d-none');
                    $('#global-patient-select').prop('disabled', false);
                }).fail(function() {
                    loadPatientListFallback();
                });
            }, 3000);
        }

        function loadClinicList() {
            $('#global-clinic-loader').removeClass('d-none');
            $('#global-clinic-select').prop('disabled', true);
            $.getJSON(getRoute('clinicList'), function(data) {
                let clinicOptions = data.map(function(clinic) {
                    return {
                        id: clinic.id,
                        text: clinic.name || clinic.clinic_name ||
                            "{{ __('appointment.unknown_clinic') }}"
                    };
                });
                refreshSelect($('#global-clinic-select'), clinicOptions);
                $('#global-clinic-loader').addClass('d-none');
                $('#global-clinic-select').prop('disabled', false);
            }).fail(function() {
                loadClinicListFallback();
            });
        }

        // Initialize select2 and load lists
        // Don't call initAppointmentSelect2() here since refreshSelect will handle initialization
        loadPatientList();
        setTimeout(() => {
            loadClinicList();
        }, 3000);

        // Re-initialize select2 after global select2:open
        $(document).on('select2:open', function() {
            setTimeout(function() {
                const searchField = $('.select2-dropdown .select2-search__field');
                if (searchField.length) {
                    searchField.prop('disabled', false);
                    searchField.attr('placeholder', "{{ __('appointment.type_to_search') }}");
                }
            }, 10);
        });

        // Global form initialization handled in main initialization block

        // Reset appointment form
        function resetAppointmentForm() {
            $('#global-patient-select').val(null).trigger('change');
            $('#global-clinic-select').val(null).trigger('change');
            $('#global-doctor-select').val(null).trigger('change');
            $('#global-service-select').val(null).trigger('change');
            // Reload data without reinitializing Select2
            setTimeout(function() {
                loadPatientList();
                loadClinicList();
            }, 1000);
            $('#global-appointment-date').val('');
            $('#global-appointment-details').hide();
            $('#global-available-slots').html(
                '<p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>'
            );
            $('#global-service-price').text(fmt(0));
            $('#global-service-price-label').text('{{ __('appointment.service_price') }}:');
            $('#global-inclusive-tax-row').addClass('d-none');
            $('#global-discount-row').addClass('d-none');
            $('#global-discount-label').text('{{ __('appointment.discount') }}:');
            $('#global-subtotal-row').addClass('d-none');
            $('#global-subtotal-label').text('{{ __('appointment.subtotal') }}:');
            $('#global-tax-amount').text(fmt(0));
            $('#global-total-amount').text(fmt(0));
            $('#global-tax-value-i').hide();
            clearAllErrorMessages();
            $('#global-medical-report').val('');
            $('#global-medical-history').val('');
            $('input[name="appointment_time"]').prop('checked', false);
            $('#global-save-appointment-btn').removeClass('all-fields-filled');
        }

        // Patient info
        $('#global-patient-select').on('change', function() {
            let id = $(this).val();
            if (id && patients[id]) {
                let p = patients[id];
                $('#global-appointment-details').show();
                $('#global-patient-name').text(p.name);
                $('#global-patient-since').text(p.created_at ? (
                    '{{ __('appointment.patient_since') }} ' + p.created_at) : '');
                $('#global-patient-phone').text(p.mobile);
                $('#global-patient-email').text(p.email);
                $('#global-patient-avatar').attr('src', p.avatar);
            } else {
                $('#global-appointment-details').hide();
            }
            checkAllRequiredFields();
        });

        // Clinic → Doctor
        $('#global-clinic-select').on('change', function() {
            let clinicId = $(this).val();
            refreshSelect($('#global-doctor-select'), []);
            refreshSelect($('#global-service-select'), []);
            $('#global-doctor-loader').removeClass('d-none');
            $('#global-doctor-select').prop('disabled', true);
            if (!clinicId) {
                $('#global-doctor-loader').addClass('d-none');
                $('#global-doctor-select').prop('disabled', false);
                checkAllRequiredFields();
                return;
            }
            $.getJSON(getRoute('doctorList'), {
                clinic_id: clinicId
            }, function(d) {
                refreshSelect($('#global-doctor-select'), d.map(function(dd) {
                    return {
                        id: dd.doctor_id,
                        text: dd.doctor_name
                    };
                }));
                $('#global-doctor-loader').addClass('d-none');
                $('#global-doctor-select').prop('disabled', false);
                checkAllRequiredFields();
            }).fail(function() {
                $('#global-doctor-loader').addClass('d-none');
                $('#global-doctor-select').prop('disabled', false);
                checkAllRequiredFields();
            });
        });

        // Doctor → Service
        $('#global-doctor-select').on('change', function() {
            let doctorId = $(this).val(),
                clinicId = $('#global-clinic-select').val();
            refreshSelect($('#global-service-select'), []);
            $('#global-service-loader').removeClass('d-none');
            $('#global-service-select').prop('disabled', true);
            if (!doctorId || !clinicId) {
                $('#global-service-loader').addClass('d-none');
                $('#global-service-select').prop('disabled', false);
                checkAllRequiredFields();
                return;
            }
            $.getJSON(getRoute('serviceList'), {
                doctor_id: doctorId,
                clinic_id: clinicId
            }, function(d) {
                refreshSelect($('#global-service-select'), d.map(function(s) {
                    return {
                        id: s.id,
                        text: s.name
                    };
                }));
                $('#global-service-loader').addClass('d-none');
                $('#global-service-select').prop('disabled', false);
                checkAllRequiredFields();
            }).fail(function() {
                $('#global-service-loader').addClass('d-none');
                $('#global-service-select').prop('disabled', false);
                checkAllRequiredFields();
            });
        });

        // Service → pricing
        $('#global-service-select').on('change', function() {
            let serviceId = $(this).val(),
                doctorId = $('#global-doctor-select').val(),
                clinicId = $('#global-clinic-select').val();
            if (!serviceId || !doctorId || !clinicId) {
                checkAllRequiredFields();
                return;
            }
            $.getJSON(getRoute('servicePrice'), {
                service_id: serviceId,
                doctor_id: doctorId
            }, function(r) {
                let base = parseFloat(r.base_price || 0),
                    disc = parseFloat(r.discount || 0),
                    inclusiveTaxAmount = parseFloat(r.inclusive_tax_amount || 0),
                    isInclusiveTax = r.is_inclusive_tax || false;

                // Show service price with inclusive tax if applicable
                let displayPrice = isInclusiveTax && inclusiveTaxAmount > 0 ? base +
                    inclusiveTaxAmount : base;
                $('#global-service-price').text(fmt(displayPrice));

                // Update label to indicate inclusive tax in red color
                if (isInclusiveTax && inclusiveTaxAmount > 0) {
                    $('#global-service-price-label').html(
                        '{{ __('appointment.service_price') }}: <span style="color: #dc3545;">({{ __('appointment.with_inclusive_tax') }})</span>'
                    );
                } else {
                    $('#global-service-price-label').text(
                        '{{ __('appointment.service_price') }}:');
                }

                $('#global-service-original-price-row').addClass('d-none');
                $('#global-inclusive-tax-list').html('');

                if (isInclusiveTax && r.inclusive_tax_data && r.inclusive_tax_data.length > 0) {
                    let inclusiveTaxHtml = '';
                    r.inclusive_tax_data.forEach(function(t) {
                        let label = t.title || '{{ __('appointment.tax') }}';
                        if (t.type === 'percent') {
                            label += ` (${t.value}%)`;
                        }
                        // For fixed taxes, don't add any additional text
                        inclusiveTaxHtml += `
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="heading-color fw-medium">${label}</span>
                                <span class="heading-color fw-bold">${fmt(t.amount)}</span>
                            </div>
                        `;
                    });
                    inclusiveTaxHtml += `
                        <div class="d-flex justify-content-between align-items-center py-3 mt-2">
                            <span class="heading-color fw-bold fs-5">Total Inclusive Tax</span>
                            <span class="text-primary fw-bold fs-5">${fmt(inclusiveTaxAmount)}</span>
                        </div>
                    `;
                    $('#global-inclusive-tax-list').html(inclusiveTaxHtml);
                } else {
                    $('#global-inclusive-tax-list').html(`
                        <div class="text-center bg-body py-3 rounded">
                            <i class="ph ph-receipt mb-2 fs-2"></i>
                            <p class="mb-0">{{ __('appointment.no_inclusive_taxes_applied') }}</p>
                        </div>
                    `);
                }

                // Subtotal = displayPrice (which already includes inclusive tax) - discount
                let subtotal = displayPrice - disc;
                $('#global-subtotal-row').removeClass('d-none');
                $('#global-subtotal-label').text('{{ __('appointment.subtotal') }}:');
                $('#global-subtotal-amount').text(fmt(subtotal));

                if (disc > 0) {
                    $('#global-discount-row').removeClass('d-none');
                    $('#global-discount-amount').text('-' + fmt(disc));

                    // Check if discount type is percentage and display it
                    if (r.discount_type === 'percentage' || r.discount_type === 'percent') {
                        let discountPercent = r.discount_value || r.discount_percentage || 0;
                        $('#global-discount-label').text(
                            '{{ __('appointment.discount') }} (' + discountPercent + '%):'
                        );
                    } else {
                        $('#global-discount-label').text('{{ __('appointment.discount') }}:');
                    }
                } else {
                    $('#global-discount-row').addClass('d-none');
                    $('#global-discount-label').text('{{ __('appointment.discount') }}:');
                }
                $('#global-inclusive-tax-row').addClass('d-none');

                setTimeout(() => {
                    $.getJSON(getRoute('taxList'), {
                        service_id: serviceId,
                        doctor_id: doctorId,
                        clinic_id: clinicId,
                        subtotal: subtotal,
                        tax_type: r.tax_type || ''
                    }, function(taxRes) {
                        let totalExclusiveTax = 0;
                        $('#global-applied-tax-inline').html('');
                        if (Array.isArray(taxRes) && taxRes.length > 0) {
                            let hasTaxes = false;
                            let taxItems = [];
                            taxRes.forEach(t => {
                                if (
                                    t.tax_type === 'exclusive' &&
                                    t.status == 1 &&
                                    (t.title && (
                                        t.title.toLowerCase()
                                        .includes('gst') ||
                                        t.title.toLowerCase()
                                        .includes('service tax') ||
                                        t.title.toLowerCase()
                                        .includes('tax')
                                    ))
                                ) {
                                    let taxValue = 0;
                                    let label = t.title ||
                                        '{{ __('appointment.service_tax') }}';
                                    if (t.type === 'percent') {
                                        taxValue = (subtotal * t
                                            .value) / 100;
                                        label += ` (${t.value}%)`;
                                    } else {
                                        taxValue = t.value;
                                        // For fixed taxes, don't add any additional text
                                    }
                                    totalExclusiveTax += taxValue;
                                    hasTaxes = true;
                                    taxItems.push({
                                        title: label,
                                        value: taxValue
                                    });
                                }
                            });

                            if (hasTaxes) {
                                let taxHtml = '';
                                taxItems.forEach((item, index) => {
                                    const isLast = index === taxItems
                                        .length - 1;
                                    const borderStyle = isLast ? '' :
                                        'border-bottom';
                                    taxHtml += `
                                        <div class="d-flex justify-content-between align-items-center py-2 font-size-14 ${borderStyle}">
                                            <span class="heading-color" style="font-weight: normal;">${item.title}</span>
                                            <span class="heading-color fw-bold">${fmt(item.value)}</span>
                                        </div>
                                    `;
                                });
                                $('#global-applied-tax-inline').html(taxHtml);
                            } else {
                                $('#global-applied-tax-inline').html(`
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-receipt fa-lg mb-2 text-body"></i>
                                        <p class="mb-0 font-size-14">{{ __('appointment.no_service_tax_applied') }}</p>
                                    </div>
                                `);
                            }
                        } else {
                            $('#global-applied-tax-inline').html(`
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-receipt fa-lg mb-2 text-body"></i>
                                    <p class="mb-0 font-size-14">{{ __('appointment.no_service_tax_applied') }}</p>
                                </div>
                            `);
                        }
                        let total = subtotal + totalExclusiveTax;
                        $('#global-tax-amount').text(fmt(totalExclusiveTax));
                        $('#global-total-amount').text(fmt(total));
                        if (totalExclusiveTax > 0 || inclusiveTaxAmount > 0) {
                            $('#global-tax-value-i').show();
                        } else {
                            $('#global-tax-value-i').hide();
                        }
                    });
                }, 3000);
            });
            checkAllRequiredFields();
        });

        // Flatpickr initialization for calendar date picker
        flatpickr("#global-appointment-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "Y-m-d",
            minDate: "today",
            onChange: function() {
                clearAllErrorMessages();
                checkAllRequiredFields();
            }
        });

        // Slots
        $('#global-appointment-date,#global-doctor-select,#global-service-select,#global-clinic-select').on(
            'change',
            function() {
                clearAllErrorMessages();
                let date = $('#global-appointment-date').val(),
                    doctorId = $('#global-doctor-select').val(),
                    clinicId = $('#global-clinic-select').val(),
                    serviceId = $('#global-service-select').val();
                if (!(date && doctorId && clinicId && serviceId)) {
                    checkAllRequiredFields();
                    return;
                }
                $('#global-available-slots').html(
                    '<div class="global-slots-loading text-center d-flex align-items-center justify-content-center p-4"><i class="fas fa-spinner fa-spin text-primary me-2"></i><span class="text-muted">{{ __('appointment.lbl_loading_available_slots') }}</span></div>'
                );
                setTimeout(() => {
                    $.getJSON(getRoute('availableSlots'), {
                            appointment_date: date,
                            doctor_id: doctorId,
                            clinic_id: clinicId,
                            service_id: serviceId
                        },
                        function(r) {
                            let slots = r.availableSlot || r.data || [];
                            $('#global-available-slots').html('');
                            if (slots.length === 0) {
                                $('#global-available-slots').html(`
                            <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
                        `);
                            } else {
                                // Create slots grid
                                let slotsHtml =
                                    '<div class="slots-grid avb-slot d-flex flex-wrap align-items-center gap-3 form-chceck">';
                                slots.forEach(function(s, i) {
                                    slotsHtml += `
                                <input type="radio" class="btn-check form-check-input" id="slot${i}" name="appointment_time" value="${s}" required>
                                <label for="slot${i}" class="clickable-text form-check-label">${s}</label>
                            `;
                                });
                                slotsHtml += '</div>';
                                $('#global-available-slots').html(slotsHtml);
                            }
                            checkAllRequiredFields();
                        }
                    ).fail(function() {
                        $('#global-available-slots').html(`
                    <p class="text-muted text-center bg-gray-900 p-3 rounded">{{ __('appointment.lbl_slot_not_found') }}</p>
                `);
                        checkAllRequiredFields();
                    });
                }, 1000);
                $(document).on('change', 'input[name="appointment_time"]', function() {
                    $('label.form-check-label').removeClass('selected_slot');
                    $(this).next('label').addClass('selected_slot');
                });
            });

        // Submit
        $('#global-appointment-form').on('submit', function(e) {
            e.preventDefault();
            let fd = new FormData(this),
                slot = $('input[name="appointment_time"]:checked').val();
            $('.field-error').remove();
            var patientId = $('#global-patient-select').val();
            var clinicId = $('#global-clinic-select').val();
            var doctorId = $('#global-doctor-select').val();
            var serviceId = $('#global-service-select').val();
            var appointmentDate = $('#global-appointment-date').val();
            var hasErrors = false;
            if (!patientId) {
                $('#global-patient-select').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.patient_required') }}</div>'
                );
                hasErrors = true;
            }
            if (!clinicId) {
                $('#global-clinic-select').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.clinic_required') }}</div>'
                );
                hasErrors = true;
            }
            if (!doctorId) {
                $('#global-doctor-select').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.doctor_required') }}</div>'
                );
                hasErrors = true;
            }
            if (!serviceId) {
                $('#global-service-select').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.service_required') }}</div>'
                );
                hasErrors = true;
            }
            if (!appointmentDate) {
                $('#global-appointment-date').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.appointment_date_required') }}</div>'
                );
                hasErrors = true;
            }
            if (!slot) {
                $('#global-available-slots').parent().append(
                    '<div class="field-error text-danger small mt-1">{{ __('appointment.time_slot_required') }}</div>'
                );
                hasErrors = true;
            }
            if (hasErrors) {
                return;
            }
            fd.set('appointment_time', slot);
            var selectedPatientId = $('#global-patient-select').val();
            if (selectedPatientId) {
                fd.set('user_id', selectedPatientId);
            }
            if (otherPatients.length > 0) {
                fd.set('other_patients', JSON.stringify(otherPatients));
            }

            // Add selected other patient if any
            if (selectedOtherPatient) {
                var otherId = (typeof selectedOtherPatient === 'object') ? selectedOtherPatient.id : selectedOtherPatient;
                fd.set('otherpatient_id', otherId);
            }
            $('#global-save-appointment-btn .global-save-btn-text').addClass('d-none');
            $('#global-save-btn-spinner').removeClass('d-none');
            $('#global-save-appointment-btn .global-loading-text').removeClass('d-none');
            $('#global-save-appointment-btn').prop('disabled', true);


            $.ajax({
                url: getRoute('appointmentStore'),
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(res) {
                if (res.status) {
                    $.ajax({
                        url: getRoute('appointmentSavePayment'),
                        method: 'POST',
                        data: res.data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done(function() {
                        resetAppointmentForm();
                        let $offcanvas = $('#global-appointment-form').closest(
                            '.offcanvas');
                        let $modal = $('#global-appointment-form').closest('.modal');
                        if ($offcanvas.length) {
                            let offcanvasInstance = bootstrap.Offcanvas
                                .getOrCreateInstance($offcanvas[0]);
                            offcanvasInstance.hide();
                        }
                        if ($modal.length) {
                            let modalInstance = bootstrap.Modal.getOrCreateInstance(
                                $modal[0]);
                            modalInstance.hide();
                        }
                        // Reload datatable if exists
                        if (window.renderedDataTable && typeof window.renderedDataTable
                            .ajax === 'function') {
                            window.renderedDataTable.ajax.reload(null, false);
                        } else if (typeof window.LaravelDataTables !== 'undefined' &&
                            window.LaravelDataTables['datatable']) {
                            window.LaravelDataTables['datatable'].ajax.reload(null,
                                false);
                        } else if (window.appointmentTable && typeof window
                            .appointmentTable.ajax === 'function') {
                            window.appointmentTable.ajax.reload(null, false);
                        } else if ($.fn.DataTable && $('#datatable').length) {
                            $('#datatable').DataTable().ajax.reload(null, false);
                        }
                    }).always(function() {
                        $('#global-save-appointment-btn .global-save-btn-text')
                            .removeClass('d-none');
                        $('#global-save-btn-spinner').addClass('d-none');
                        $('#global-save-appointment-btn .global-loading-text').addClass(
                            'd-none');
                        $('#global-save-appointment-btn').prop('disabled', false);
                    });
                } else {
                    $('#global-save-appointment-btn .global-save-btn-text').removeClass(
                        'd-none');
                    $('#global-save-btn-spinner').addClass('d-none');
                    $('#global-save-appointment-btn .global-loading-text').addClass('d-none');
                    clearAllErrorMessages();
                    $('#global-save-appointment-btn').prop('disabled', false);
                }
            }).fail(function() {
                $('#global-save-appointment-btn .global-save-btn-text').removeClass('d-none');
                $('#global-save-btn-spinner').addClass('d-none');
                $('#global-save-appointment-btn .global-loading-text').addClass('d-none');
                $('#global-save-appointment-btn').prop('disabled', false);
            });
        });

        // Reset form when close button is clicked
        $('#global-close-appointment-btn').on('click', function() {
            resetAppointmentForm();
        });

        // Reset form when modal/offcanvas is hidden
        $(document).on('hidden.bs.modal hidden.bs.offcanvas', function(e) {
            if ($(e.target).find('#global-appointment-form').length > 0) {
                resetAppointmentForm();
            }
        });

        // Booking For functionality
        var otherPatients = [];

        $('#global-booking-for-toggle').on('change', function() {
            if ($(this).is(':checked')) {
                $('#global-other-patients-section').show();
                otherPatientsListAndDisplay();
                updateOtherPatientsList();
            } else {
                $('#global-other-patients-section').hide();
            }
        });

        $('#global-add-other-patient-btn').on('click', function(e) {
            e.preventDefault();
            $('#globalAddOtherPatientModal').modal('show');
        });

        $('#global-upload-photo-btn').on('click', function() {
            $('#global-patient-photo-input').click();
        });

        $('#global-patient-photo-input').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#global-patient-photo-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#global-remove-photo-btn').on('click', function() {
            $('#global-patient-photo-preview').attr('src', '{{ asset('img/avatar/avatar.webp') }}');
            $('#global-patient-photo-input').val('');
        });

        function showSuccessMessage(message) {
            var notification = $(
                '<div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="fas fa-check-circle me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('body').append(notification);
            setTimeout(function() {
                notification.alert('close');
            }, 5000);
        }

        function showErrorMessage(message) {
            var notification = $(
                '<div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="fas fa-exclamation-circle me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('body').append(notification);
            setTimeout(function() {
                notification.alert('close');
            }, 7000);
        }

        function clearAllErrorMessages() {
            $('.field-error').remove();
        }

        function checkAllRequiredFields() {
            var patientId = $('#global-patient-select').val();
            var clinicId = $('#global-clinic-select').val();
            var doctorId = $('#global-doctor-select').val();
            var serviceId = $('#global-service-select').val();
            var appointmentDate = $('#global-appointment-date').val();
            var selectedSlot = $('input[name="appointment_time"]:checked').val();
            var allFieldsFilled = patientId && clinicId && doctorId && serviceId && appointmentDate &&
                selectedSlot;
            if (allFieldsFilled) {
                $('#global-save-appointment-btn').addClass('all-fields-filled');
            } else {
                $('#global-save-appointment-btn').removeClass('all-fields-filled');
            }
        }

        $('#globalAddOtherPatientModal').on('hidden.bs.modal', function() {
            clearAllErrorMessages();
        });
        $('#globalAddOtherPatientModal').on('show.bs.modal', function() {
            clearAllErrorMessages();
        });

        $(document).ready(function() {
            clearAllErrorMessages();
            checkAllRequiredFields();
            setTimeout(function() {
                checkAllRequiredFields();
            }, 500);
        });

        $('#global-patient-select, #global-clinic-select, #global-doctor-select, #global-service-select, #global-appointment-date')
            .on('change', function() {
                clearAllErrorMessages();
            });

        $(document).on('change', 'input[name="appointment_time"]', function() {
            clearAllErrorMessages();
            checkAllRequiredFields();
        });

        // Initialize country dropdown when modal opens
        $('#globalAddOtherPatientModal').on('show.bs.modal', function() {
            if (!$("#global-other-patient-dob").data('flatpickr')) {
                flatpickr("#global-other-patient-dob", {});
            }
        });

        const otherPatientContactInput = document.querySelector("#global-other-patient-contact");
        const itiOtherPatient = window.intlTelInput(otherPatientContactInput, {
            initialCountry: "gb",
            separateDialCode: true,
            preferredCountries: ["gb", "us", "in"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"
        });

        $(document).on('click', '.dropdown-item', function(e) {
            e.preventDefault();
            if (itiOtherPatient) {
                itiOtherPatient.setCountry("in");
                otherPatientContactInput.value = '';
            }
        });


        $('#global-confirm-add-patient').on('click', function() {
            var firstName = $('#global-other-patient-first-name').val().trim();
            var lastName = $('#global-other-patient-last-name').val().trim();
            var dob = $('#global-other-patient-dob').val().trim();
            var contact = $('#global-other-patient-contact').val().trim();
            var fullContact = '+' + itiOtherPatient.getSelectedCountryData().dialCode + ' ' + $(
                '#global-other-patient-contact').val().trim();
            var gender = $('input[name="global-other-patient-gender"]:checked').val();
            var relation = $('input[name="global-other-patient-relation"]:checked').val();

            var formData = new FormData();
            formData.append('user_id', $('#global-patient-select').val() || '');
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('dob', dob);
            formData.append('contactNumber', fullContact);
            formData.append('gender', gender || '');
            formData.append('relation', relation || '');

            var profileImage = $('#global-patient-photo-input')[0].files[0];
            if (profileImage) {
                formData.append('profile_image', profileImage);
            }

            var $btn = $(this);
            var originalText = $btn.text();
            $btn.prop('disabled', true).text("{{ __('appointment.saving') }}");

            setTimeout(() => {
                $.ajax({
                    url: getRoute('appointmentOtherPatient'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            updateOtherPatientsList();
                            resetOtherPatientModal();
                            otherPatientsListAndDisplay();
                            $('#globalAddOtherPatientModal').modal('hide');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                }, 3000);
            });
        });

        function otherPatientsListAndDisplay() {
            const userId = $('#global-patient-select').val();
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
                    var $list = $('#global-other-patients-list');
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
                    $('#global-other-patients-list').html(
                        '<div class="text-danger small">Error fetching other patients.</div>');
                }
            });
        }

        function resetOtherPatientModal() {
            $('#global-other-patient-first-name').val('');
            $('#global-other-patient-last-name').val('');
            $('#global-other-patient-dob').val('');
            $('#global-other-patient-contact').val('');
            $('input[name="global-other-patient-gender"]').prop('checked', false);
            $('input[name="global-other-patient-relation"]').prop('checked', false);
            $('#global-patient-photo-preview').attr('src', '{{ asset('img/avatar/avatar.webp') }}');
            $('#global-patient-photo-input').val('');
            $('#countryCodeDropdown').html(
                '<span id="selected-country-code">IN</span><span id="selected-phone-code">+91</span> <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px; color: #666;"></i>'
            );
            if ($("#global-other-patient-dob").data('flatpickr')) {
                $("#global-other-patient-dob").data('flatpickr').clear();
            }
        }

        // Selected other patient
        var selectedOtherPatient = null;

        function updateOtherPatientsList() {
            // otherPatientsList();
            var $list = $('#global-other-patients-list');
            if (otherPatients.length === 0) {
                $list.html(
                    '<div class="text-muted small">{{ __('appointment.no_other_patients_found') }}</div>');
                return;
            }
            var html = '';
            otherPatients.forEach(function(patient, index) {
                var isSelected = selectedOtherPatient && selectedOtherPatient.id === patient.id;
                var cardClass = isSelected ? 'bg-primary text-white' : 'bg-white text-dark';
                var iconClass = isSelected ? 'text-white' : 'text-dark';

                html += '<div class="book-for-appointments cursor-pointer ' + cardClass + '" data-patient-id="' + patient.id + '" data-index="' + index + '" >';
                html += '<img src="' + (patient.avatar || patient.profile_image ||
                                "{{ asset('img/avatar/avatar.webp') }}") +
                            '" class="img-fluid rounded-circle avatar-35 object-fit-cover" alt="{{ __('appointment.avatar') }}" >';
                html += '<h6 class="appointments-title mb-0 ' + iconClass + '">' + (patient.full_name ||
                            patient.first_name || '') + '</h6>';
                // html += '<button type="button" class="global-remove-patient btn btn-sm border-0 p-1 ms-1" data-index="' + index + '" style="width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.2);">';
                // html += '<i class="fas fa-times text-white" style="font-size: 8px;"></i>';
                // html += '</button>';
                html += '</div>';
            });
            // html += '</div>';
            $list.html(html);
        }

        // Add click handler for .book-for-appointments (list rendered by otherPatientsListAndDisplay)
        $(document).on('click', '.book-for-appointments', function (e) {
            // Prevent selecting when clicking remove button (if any) inside card
            if ($(e.target).hasClass('global-remove-patient') || $(e.target).closest('.global-remove-patient').length) {
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
        $(document).on('click', '.global-other-patient-card', function (e) {
            if ($(e.target).hasClass('global-remove-patient') || $(e.target).closest('.global-remove-patient').length) {
                return; // Don't select if clicking remove button
            }

            var patientId = $(this).data('patient-id');
            var index = $(this).data('index');
            selectedOtherPatient = otherPatients[index];
            updateOtherPatientsList();
        });

        $(document).on('click', '.global-remove-patient', function (e) {
            e.stopPropagation(); // Prevent card selection
            var index = $(this).data('index');

            // If removing selected patient, clear selection
            if (selectedOtherPatient && otherPatients[index] && selectedOtherPatient.id === otherPatients[index].id) {
                selectedOtherPatient = null;
            }

            otherPatients.splice(index, 1);
            updateOtherPatientsList();
        });

        // Tax Section Toggle Functionality
        $('#global-applied-tax').on('show.bs.collapse', function() {
            $('#global-tax-caret-icon').removeClass('ph-caret-down').addClass('ph-caret-up');
        });

        $('#global-applied-tax').on('hide.bs.collapse', function() {
            $('#global-tax-caret-icon').removeClass('ph-caret-up').addClass('ph-caret-down');
        });

        var originalResetAppointmentForm = resetAppointmentForm;
        resetAppointmentForm = function() {
            originalResetAppointmentForm();
            $('#global-booking-for-toggle').prop('checked', false);
            $('#global-other-patients-section').hide();
            otherPatients = [];
            selectedOtherPatient = null;
            updateOtherPatientsList();
            resetOtherPatientModal();
            // Collapse tax section
            $('#global-applied-tax').collapse('hide');
        };

        // Language change handler - ensure offcanvas content is refreshed when language changes
        $(document).ready(function() {
            if (sessionStorage.getItem('reopenOffcanvas') === 'true') {
                sessionStorage.removeItem('reopenOffcanvas');
                setTimeout(function() {
                    $('#form-offcanvas').offcanvas('show');
                }, 500);
            }
            $(document).on('click', 'a[href*="language/"]', function() {
                if ($('#form-offcanvas').hasClass('show')) {
                    sessionStorage.setItem('reopenOffcanvas', 'true');
                }
                return true;
            });
        });

        // Global offcanvas initialization and trigger
        window.initGlobalAppointmentOffcanvas = function() {
            // Initialize offcanvas with proper configuration - target Blade template
            var offcanvasElement = document.getElementById('global-appointment-offcanvas');
            var offcanvasInstance = null;

            if (offcanvasElement) {
                // Create offcanvas instance with proper configuration
                offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement, {
                    backdrop: true,
                    keyboard: true
                });
            }

            // Custom click handler for the trigger button
            $(document).off('click', '#global-appointment-trigger').on('click',
                '#global-appointment-trigger',
                function(e) {
                    e.preventDefault();

                    if (offcanvasInstance) {
                        // Use the instance to show the offcanvas
                        offcanvasInstance.show();
                    } else {
                        // Fallback: try to get or create instance
                        try {
                            var fallbackInstance = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasElement, {
                                    backdrop: true,
                                    keyboard: true
                                });
                            fallbackInstance.show();
                        } catch (error) {
                            console.error('Error opening offcanvas:', error);
                            // Last resort: show using jQuery
                            $(offcanvasElement).addClass('show');
                            $('body').addClass('offcanvas-open');
                        }
                    }
                });

            // Handle close button clicks
            $(document).off('click',
                '#appointment-form-offcanvas .btn-close, #global-close-appointment-btn').on('click',
                '#appointment-form-offcanvas .btn-close, #global-close-appointment-btn',
                function(e) {
                    e.preventDefault();
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    } else {
                        $(offcanvasElement).removeClass('show');
                        $('body').removeClass('offcanvas-open');
                    }
                });
        };

        // Initialize when document is ready
        $(document).ready(function() {
            window.initGlobalAppointmentOffcanvas();
        });
    });
</script>
