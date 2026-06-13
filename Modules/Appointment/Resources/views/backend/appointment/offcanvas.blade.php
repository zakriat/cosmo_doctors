<form id="appointment-form">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="global-appointment" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h4 class="offcanvas-title" id="form-offcanvasLabel">
                <span>{{ __('messages.create') }} {{ __('messages.appointments') }}</span>
            </h4>
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="btn-close-offcanvas">
                <i class="ph ph-x-circle"></i>
            </button>
        </div>

        <div class="offcanvas-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group" id="select-patient-block">
                        <label class="form-label">{{ __('clinic.lbl_select_patient') }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="patient_id" id="patient_id"></select>
                        <span class="text-danger" data-error-for="patient_id"></span>
                    </div>

                    <div class="user-block card rounded d-none" id="selected-patient-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-4 mb-4">
                                <img id="selected-patient-avatar" alt="avatar" class="img-fluid avatar avatar-60 rounded-pill" />
                                <div class="flex-grow-1">
                                    <div class="gap-2">
                                        <h5 id="selected-patient-name"></h5>
                                        <p class="m-0">
                                            {{ __('appointment.lbl_since') }} <span id="selected-patient-since"></span>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" id="remove-selected-patient" class="text-danger bg-transparent border-0">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                            <div class="row d-none" id="selected-patient-mobile-row">
                                <label class="col-3 col-xl-2 fw-500"><strong class="fst-normal heading-color">{{ __('booking.lbl_phone') }}</strong></label>
                                <span class="col-7 col-xl-8" id="selected-patient-mobile"></span>
                            </div>
                            <div class="row d-none" id="selected-patient-email-row">
                                <label class="col-3 col-xl-2 fw-500"><strong><span class="fst-normal heading-color">{{ __('booking.lbl_e-mail') }}</span></strong></label>
                                <span class="col-7 col-xl-8" id="selected-patient-email"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Book for other patient section -->
                    <div class="row d-none" id="other-patient-wrapper">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">{{ __('clinic.book_for_other_patient') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle-other-patient" />
                                </div>
                            </div>
                            <div id="other-patient-section" class="mt-2 d-none">
                                <div>
                                    <p class="text-primary cursor-pointer" id="open-add-other-patient" data-bs-toggle="modal" data-bs-target="#exampleModal1">{{ __('clinic.add_other_patient') }}</p>
                                </div>
                                <div id="other-patient-list" class="d-flex align-items-center flex-wrap column-gap-4 row-gap-3 mt-2"></div>
                                <div id="other-patient-empty" class="d-none">
                                    <p>{{ __('clinic.no_other_patient') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="appointment-fields" class="row d-none">
                        <div class="col-md-6 form-group">
                            <label class="form-label col-md-6">{{ __('clinic.lbl_select_clinic') }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="clinic_id" id="offcanvas-clinic_id"></select>
                            <span class="text-danger" data-error-for="clinic_id"></span>
                        </div>

                        <div class="col-md-6 form-group" id="doctor-wrapper">
                            <label class="form-label">{{ __('clinic.lbl_select_doctor') }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="doctor_id" id="doctor_id"></select>
                            <span class="text-danger" data-error-for="doctor_id"></span>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('clinic.lbl_select_service') }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="service_id" id="service_id"></select>
                            <span class="text-danger" data-error-for="service_id"></span>
                        </div>

                        <div class="col-md-6 form-group appointment_date">
                            <label class="form-label" for="appointment_date">{{ __('clinic.lbl_appointment_date') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="appointment_date" id="appointment_date" placeholder="{{ __('clinic.lbl_appointment_date') }}" />
                            <span class="text-danger" data-error-for="appointment_date"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label" for="availble_slot">{{ __('clinic.lbl_availble_slots') }} <span class="text-danger">*</span></label>
                            </div>
                            <div id="available-slots" class="d-flex flex-wrap align-items-center gap-3"></div>
                            <div id="no-slots" class="d-none">
                                <h4 class="text-danger text-center form-control">{{ __('clinic.lbl_Slot_not_Found') }}</h4>
                            </div>
                            <span class="text-danger" id="avaible_slot_error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas-footer">
            <div class="p-3 bg-primary-subtle border border-primary rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-normal">{{ __('appointment.service_price') }}:</span>
                    <span class="fw-bold" id="base-price">0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="fw-normal">{{ __('appointment.total_tax') }}</span>
                        <i class="ph ph-info btn btn-link ms-2 p-0" data-bs-toggle="modal" data-bs-target="#globalTaxModal"></i>
                        :
                    </div>
                    <span class="fw-bold text-danger" id="total-tax">0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">{{ __('appointment.total') }}:</span>
                    <span class="fw-bold text-success" id="total-amount">0</span>
                </div>
            </div>
            <div class="d-grid d-sm-flex justify-content-sm-end gap-3 p-3">
                <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">{{ __('messages.close') }}</button>
                <button class="btn btn-secondary" name="submit" id="appointment-submit">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const baseMeta = document.querySelector('meta[name="baseUrl"]');
    const baseUrl = baseMeta?.getAttribute('content') || window.location.origin;
    const isDoctor = Array.isArray(window.auth_role) && window.auth_role[0] === 'doctor';
    const userId = window.auth_user_id || '';

    const form = document.getElementById('appointment-form');
    const patientSelect = document.getElementById('patient_id');
    const clinicSelect = document.getElementById('offcanvas-clinic_id');
    const doctorSelect = document.getElementById('doctor_id');
    const serviceSelect = document.getElementById('service_id');
    const appointmentDate = document.getElementById('appointment_date');
    const slotsWrap = document.getElementById('available-slots');
    const noSlots = document.getElementById('no-slots');
    const submitBtn = document.getElementById('appointment-submit');
    const basePriceEl = document.getElementById('base-price');
    const totalAmountEl = document.getElementById('total-amount');
    const totalTaxEl = document.getElementById('total-tax');
    const selectedPatientCard = document.getElementById('selected-patient-card');
    const selectPatientBlock = document.getElementById('select-patient-block');
    const selectedPatientMobileRow = document.getElementById('selected-patient-mobile-row');
    const selectedPatientEmailRow = document.getElementById('selected-patient-email-row');
    const appointmentFields = document.getElementById('appointment-fields');
    const otherPatientWrapper = document.getElementById('other-patient-wrapper');
    const toggleOtherPatient = document.getElementById('toggle-other-patient');
    const otherPatientSection = document.getElementById('other-patient-section');
    const otherPatientList = document.getElementById('other-patient-list');
    const otherPatientEmpty = document.getElementById('other-patient-empty');
    let selectedSlot = '';
    let taxData = [];
    let appliedTaxes = [];
    let selectedOtherPatientId = null;

    flatpickr(appointmentDate, { dateFormat: 'Y-m-d', minDate: 'today', static: true });

    function setError(name, message) {
        const el = form.querySelector(`[data-error-for="${name}"]`);
        if (el) el.textContent = message || '';
    }

    async function fetchJson(path) {
        try {
            const res = await fetch(path, { headers: { 'Accept': 'application/json' } });
            return await res.json();
        } catch (e) {
            console.error('Fetch error:', e);
            return null;
        }
    }

    async function loadTaxData() {
        const url = `${baseUrl}/app/tax/index_list?module_type=services&tax_type=exclusive`;
        const data = await fetchJson(url);
        taxData = Array.isArray(data) ? data : (data?.results || data?.data || data?.list || []);
    }

    function calculateExclusiveTax(amount) {
        let total = 0;
        appliedTaxes = [];
        (taxData || []).forEach(item => {
            const title = item.title || item.name || 'Tax';
            const type = item.type; // 'fixed' or 'percent'
            const value = parseFloat(item.value ?? 0);
            let add = 0;
            if (type === 'fixed') {
                add = value;
            } else if (type === 'percent') {
                add = amount * (value / 100);
            }
            if (add > 0) {
                total += add;
                appliedTaxes.push({ name: title, amount: add, type, value });
            }
        });
        totalTaxEl.textContent = total.toFixed(2);
        const listEl = document.getElementById('tax-breakdown-list');
        if (listEl) {
            listEl.innerHTML = '';
            appliedTaxes.forEach(t => {
                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center heading-color';
                const taxLabel = t.type === 'percent' 
                    ? `${t.name} (${t.value}%):` 
                    : `${t.name}:`;
                row.innerHTML = `${taxLabel} <span>${t.amount.toFixed(2)}</span>`;
                listEl.appendChild(row);
            });
        }
        return total;
    }

    async function loadPatients() {
        const url = `${baseUrl}/app/customers/index_list?filter=all`;
        const data = await fetchJson(url);
        patientSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_patient') }}</option>`;
        if (!data) return;
        const items = Array.isArray(data) ? data : (data.results || data.data || data.list || []);
        items.forEach(p => {
            const id = p.id ?? p.value ?? p.user_id;
            const label = p.name ?? p.label ?? [p.first_name, p.last_name].filter(Boolean).join(' ');
            const img = p.image ?? p.profile_image ?? p.avatar;
            const created_at = p.created_at ?? p.date_of_birth;
            const mobile = p.mobile ?? p.phone_number;
            const email = p.email ?? p.email_address;
            if (!id || !label) return;
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = label;
            opt.setAttribute('data-img', img);
            opt.setAttribute('data-created-at', created_at);
            opt.setAttribute('data-mobile', mobile);
            opt.setAttribute('data-email', email);
            patientSelect.appendChild(opt);
        });
    }

    async function loadClinics() {
        const url = `${baseUrl}/app/clinics/index_list`;
        const data = await fetchJson(url);
        clinicSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_clinic') }}</option>`;
        const items = Array.isArray(data) ? data : (data?.results || data?.data || data?.list || []);
        items.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id; 
            opt.textContent = c.clinic_name; 
            clinicSelect.appendChild(opt);
        });
        if (Array.isArray(window.auth_role) && window.auth_role[0] === 'receptionist' && items.length) {
            clinicSelect.value = items[0].id;
            await loadDoctors();
        }
    }

    async function loadDoctors() {
        const clinicId = clinicSelect.value;
        if (!clinicId) { doctorSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_doctor') }}</option>`; return; }
        const url = `${baseUrl}/app/doctor/index_list?clinic_id=${encodeURIComponent(clinicId)}`;
        const data = await fetchJson(url);
        doctorSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_doctor') }}</option>`;
        const items = Array.isArray(data) ? data : (data?.results || data?.data || data?.list || []);
        items.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.doctor_id; opt.textContent = d.doctor_name; doctorSelect.appendChild(opt);
        });
        if (isDoctor && userId) {
            doctorSelect.value = userId;
            await loadServices();
        }
    }

    async function loadServices() {
        const doctorId = doctorSelect.value; const clinicId = clinicSelect.value;
        if (!doctorId || !clinicId) { serviceSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_service') }}</option>`; return; }
        const url = `${baseUrl}/app/services/index_list?doctorId=${encodeURIComponent(doctorId)}&clinicId=${encodeURIComponent(clinicId)}`;
        const data = await fetchJson(url);
        serviceSelect.innerHTML = `<option value="">{{ __('clinic.lbl_select_service') }}</option>`;
        const items = Array.isArray(data) ? data : (data?.results || data?.data || data?.list || []);
        items.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id; opt.textContent = s.name; serviceSelect.appendChild(opt);
        });
    }

    async function loadAvailableSlots() {
        selectedSlot = '';
        slotsWrap.innerHTML = '';
        noSlots.classList.add('d-none');
        const date = appointmentDate.value; const doctorId = doctorSelect.value; const clinicId = clinicSelect.value; const serviceId = serviceSelect.value;
        if (!date || !doctorId || !clinicId || !serviceId) return;
        const url = `${baseUrl}/app/doctor/get-available-slot?appointment_date=${encodeURIComponent(date)}&doctor_id=${encodeURIComponent(doctorId)}&clinic_id=${encodeURIComponent(clinicId)}&service_id=${encodeURIComponent(serviceId)}`;
        const data = await fetchJson(url);
        const slots = data?.availableSlot || [];
        if (!slots.length) { noSlots.classList.remove('d-none'); return; }
        slots.forEach(slot => {
            const label = document.createElement('label');
            label.className = 'clickable-text';
            label.textContent = slot;
            label.addEventListener('click', () => {
                selectedSlot = slot;
                Array.from(slotsWrap.children).forEach(c => c.classList.remove('selected_slot'));
                label.classList.add('selected_slot');
                document.getElementById('avaible_slot_error').textContent = '';
            });
            const wrap = document.createElement('div');
            wrap.className = 'avb-slot clickable-text';
            wrap.appendChild(label);
            slotsWrap.appendChild(wrap);
        });
    }

    async function fetchServicePrice() {
        const doctorId = doctorSelect.value; const serviceId = serviceSelect.value;
        if (!doctorId || !serviceId) return;
        const url = `${baseUrl}/app/services/service-price?service_id=${encodeURIComponent(serviceId)}&doctor_id=${encodeURIComponent(doctorId)}`;
        const res = await fetchJson(url);
        const base = res?.base_price || 0; const total = (res?.service_charge || 0) + (res?.inclusive_tax_data_total || 0);
        basePriceEl.textContent = base;
        const exclusiveTax = calculateExclusiveTax(total);
        totalAmountEl.textContent = (total + exclusiveTax).toFixed(2);
    }

    // Load other patients for selected user
    async function loadOtherPatients(userId) {
        otherPatientList.innerHTML = '';
        otherPatientEmpty.classList.add('d-none');
        selectedOtherPatientId = null;
        // Adjust this endpoint if your route differs
        const url = `${baseUrl}/app/appointment/other-patientlist?patient_id=${encodeURIComponent(userId)}`;
        const data = await fetchJson(url);
        const items = Array.isArray(data) ? data : (data?.results || data?.data || data?.list || []);
        if (!items.length) {
            otherPatientEmpty.classList.remove('d-none');
            return;
        }
        items.forEach(p => {
            const wrap = document.createElement('div');
            wrap.className = 'book-for-appointments';
            wrap.addEventListener('click', () => {
                selectedOtherPatientId = p.id || p.user_id;
                Array.from(otherPatientList.children).forEach(c => c.classList.remove('bg-primary', 'border-primary', 'active'));
                wrap.classList.add('bg-primary', 'border-primary', 'active');
            });
            if (p.profile_image) {
                const img = document.createElement('img');
                img.src = p.profile_image;
                img.className = 'img-fluid rounded-circle avatar-35 object-fit-cover';
                wrap.appendChild(img);
            }
            const title = document.createElement('h6');
            title.className = 'appointments-title mb-0';
            title.textContent = p.first_name || p.name || '';
            wrap.appendChild(title);
            otherPatientList.appendChild(wrap);
        });
    }
    // Expose helpers for modal script
    window.loadOtherPatients = loadOtherPatients;
    window.setSelectedOtherPatientId = function(val){ selectedOtherPatientId = val; };

    async function submitForm(e) {
        e?.preventDefault();
        setError('patient_id'); setError('clinic_id'); setError('doctor_id'); setError('service_id'); setError('appointment_date');
        if (!selectedSlot) { document.getElementById('avaible_slot_error').textContent = '{{ __('appointment.lbl_slot_required', [], 'en') }}' || 'Appointment Slot Is Required'; return; }
        const payload = {
            status: 'confirmed',
            user_id: patientSelect.value,
            otherpatient_id: selectedOtherPatientId,
            clinic_id: clinicSelect.value,
            doctor_id: doctorSelect.value,
            service_id: serviceSelect.value,
            appointment_date: appointmentDate.value,
            appointment_time: selectedSlot
        };
        const res = await fetch(`${baseUrl}/app/appointment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data?.status) {
            window.successSnackbar && window.successSnackbar(data.message || 'Saved');
            const oc = bootstrap.Offcanvas.getInstance('#global-appointment');
            oc && oc.hide();
        } else {
            window.errorSnackbar && window.errorSnackbar(data?.message || 'Error');
            const all = data?.all_message || {};
            Object.keys(all).forEach(k => setError(k, (all[k] && all[k][0]) || ''));
        }
    }

    patientSelect.addEventListener('change', async function () {
        const id = this.value;
        if (!id) {
            selectedPatientCard.classList.add('d-none');
            selectPatientBlock.classList.remove('d-none');
            appointmentFields.classList.add('d-none');
            otherPatientWrapper.classList.add('d-none');
            otherPatientSection.classList.add('d-none');
            toggleOtherPatient.checked = false;
            clinicSelect.value = '';
            doctorSelect.value = '';
            serviceSelect.value = '';
            appointmentDate.value = '';
            slotsWrap.innerHTML = '';
            noSlots.classList.add('d-none');
            selectedSlot = '';
            basePriceEl.textContent = '0';
            totalAmountEl.textContent = '0';
            return;
        }
        selectPatientBlock.classList.add('d-none');
        selectedPatientCard.classList.remove('d-none');
        const opt = this.options[this.selectedIndex];
        if (opt.getAttribute('data-mobile') || opt.getAttribute('data-email')) {
            selectedPatientMobileRow.classList.remove('d-none');
            selectedPatientEmailRow.classList.remove('d-none');
        }
        document.getElementById('selected-patient-name').textContent = opt.textContent;
        document.getElementById('selected-patient-since').textContent = opt.getAttribute('data-created-at');
        document.getElementById('selected-patient-avatar').src = opt.getAttribute('data-img') || '{{ default_file_url() }}';
        document.getElementById('selected-patient-mobile').textContent = opt.getAttribute('data-mobile') || '';
        document.getElementById('selected-patient-email').textContent = opt.getAttribute('data-email') || '';
        appointmentFields.classList.remove('d-none');
        otherPatientWrapper.classList.remove('d-none');
        if (toggleOtherPatient.checked) {
            otherPatientSection.classList.remove('d-none');
            await loadOtherPatients(id);
        }
    });
    document.getElementById('remove-selected-patient').addEventListener('click', function () {
        patientSelect.value = '';
        selectedPatientCard.classList.add('d-none');
        selectPatientBlock.classList.remove('d-none');
        appointmentFields.classList.add('d-none');
        otherPatientWrapper.classList.add('d-none');
        otherPatientSection.classList.add('d-none');
        toggleOtherPatient.checked = false;
        clinicSelect.value = '';
        doctorSelect.value = '';
        serviceSelect.value = '';
        appointmentDate.value = '';
        slotsWrap.innerHTML = '';
        noSlots.classList.add('d-none');
        selectedSlot = '';
        basePriceEl.textContent = '0';
        totalAmountEl.textContent = '0';
    });

    // Toggle show/hide for other patient section and load list
    toggleOtherPatient.addEventListener('change', async function() {
        if (!patientSelect.value) {
            this.checked = false;
            return;
        }
        if (this.checked) {
            otherPatientSection.classList.remove('d-none');
            await loadOtherPatients(patientSelect.value);
        } else {
            otherPatientSection.classList.add('d-none');
            otherPatientList.innerHTML = '';
            otherPatientEmpty.classList.add('d-none');
            selectedOtherPatientId = null;
        }
    });

    clinicSelect.addEventListener('change', loadDoctors);
    doctorSelect.addEventListener('change', function(){ loadServices(); loadAvailableSlots(); fetchServicePrice(); });
    serviceSelect.addEventListener('change', function(){ loadAvailableSlots(); fetchServicePrice(); });
    appointmentDate.addEventListener('change', loadAvailableSlots);
    form.addEventListener('submit', submitForm);
    document.getElementById('appointment-submit').addEventListener('click', submitForm);

    document.addEventListener('shown.bs.offcanvas', function (e) {
        if (e.target.id === 'global-appointment') {
            loadPatients();
            loadClinics();
            loadTaxData();
            // init dob flatpickr for other patient form
            if (window.flatpickr) {
                flatpickr('#op_dob', { dateFormat: 'Y-m-d', maxDate: 'today', static: true });
            }
        }
    });
});
</script>

<!-- Tax Breakdown Modal -->
<div class="modal fade" id="globalTaxModal" tabindex="-1" aria-labelledby="globalTaxModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{ __('appointment.total_tax') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="tax-breakdown-list"></div>
            </div>
        </div>
    </div>
    </div>

<!-- Add Other Patient Modal (full form) -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('clinic.add_other_patient') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="other-patient-form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <img id="other-patient-preview" src="{{ default_file_url() }}" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <input type="file" class="form-control d-none" id="other-patient-profile" name="profile_image" accept=".jpeg, .jpg, .png, .gif" />
                                    <label class="btn btn-info" for="other-patient-profile">{{ __('messages.upload') }}</label>
                                    <input type="button" class="btn btn-danger" id="other-patient-remove-image" value="{{ __('settings.remove') }}" />
                                </div>
                                <span class="text-danger" id="op_profile_image_error"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="op_first_name" class="form-label">{{ __('clinic.lbl_first_name') }}</label>
                                <input type="text" class="form-control" id="op_first_name" placeholder="{{ __('clinic.lbl_first_name') }}" />
                                <span id="op_first_name_error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="op_last_name" class="form-label">{{ __('clinic.lbl_last_name') }}</label>
                                <input type="text" class="form-control" id="op_last_name" placeholder="{{ __('clinic.lbl_last_name') }}" />
                                <span id="op_last_name_error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="op_dob" class="form-label">{{ __('clinic.date_of_birth') }}</label>
                                <input type="text" class="form-control" id="op_dob" placeholder="{{ __('clinic.date_of_birth') }}" />
                                <span id="op_dob_error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="op_phone" class="form-label">{{ __('clinic.lbl_contact_number') }}</label>
                                <input type="tel" class="form-control" id="op_phone" placeholder="{{ __('employee.lbl_phone_number_placeholder') }}" />
                                <span id="op_phone_error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('clinic.lbl_gender') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="op_gender" id="op_male" value="Male" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_male">{{ __('clinic.lbl_male') }}</label>
                                    <input type="radio" class="btn-check" name="op_gender" id="op_female" value="Female" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_female">{{ __('clinic.lbl_female') }}</label>
                                    <input type="radio" class="btn-check" name="op_gender" id="op_other" value="Other" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_other">{{ __('clinic.other') }}</label>
                                </div>
                                <span id="op_gender_error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('clinic.relation') }}</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <input type="radio" class="btn-check" name="op_relation" id="op_parents" value="Parents" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_parents">{{ __('clinic.parents') }}</label>
                                    <input type="radio" class="btn-check" name="op_relation" id="op_siblings" value="Siblings" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_siblings">{{ __('clinic.sibling') }}</label>
                                    <input type="radio" class="btn-check" name="op_relation" id="op_spouse" value="Spouse" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_spouse">{{ __('clinic.spouse') }}</label>
                                    <input type="radio" class="btn-check" name="op_relation" id="op_others" value="Others" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="op_others">{{ __('clinic.other') }}</label>
                                </div>
                                <span id="op_relation_error" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('clinic.close') }}</button>
                <button type="button" class="btn btn-primary" id="op_submit_btn">{{ __('clinic.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add script handlers for Other Patient modal
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        const baseMeta = document.querySelector('meta[name="baseUrl"]');
        const baseUrl = baseMeta?.getAttribute('content') || window.location.origin;
        const profileInput = document.getElementById('other-patient-profile');
        const previewImg = document.getElementById('other-patient-preview');
        const removeBtn = document.getElementById('other-patient-remove-image');
        const submitBtn = document.getElementById('op_submit_btn');
        const submitBtnFooter = document.getElementById('op_submit_btn_footer');
        const firstName = document.getElementById('op_first_name');
        const lastName = document.getElementById('op_last_name');
        const dob = document.getElementById('op_dob');
        const phone = document.getElementById('op_phone');

        function clearErrors(){
            ['op_first_name','op_last_name','op_dob','op_phone','op_gender','op_relation','op_profile_image'].forEach(id=>{
                const el = document.getElementById(id + '_error');
                if (el) el.textContent = '';
            });
        }

        function getCheckedValue(name){
            const el = document.querySelector(`input[name="${name}"]:checked`);
            return el ? el.value : '';
        }

        profileInput?.addEventListener('change', function(e){
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt){ previewImg.src = evt.target.result; };
            reader.readAsDataURL(file);
        });
        removeBtn?.addEventListener('click', function(){
            previewImg.src = '{{ default_file_url() }}';
            if (profileInput) profileInput.value = '';
        });

        async function submitOtherPatient(){
            clearErrors();
            const gender = getCheckedValue('op_gender');
            const relation = getCheckedValue('op_relation');
            let hasError = false;
            if (!firstName.value) { document.getElementById('op_first_name_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.lbl_first_name')]) }}'; hasError = true; }
            if (!lastName.value) { document.getElementById('op_last_name_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.lbl_last_name')]) }}'; hasError = true; }
            if (!dob.value) { document.getElementById('op_dob_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.date_of_birth')]) }}'; hasError = true; }
            if (!phone.value) { document.getElementById('op_phone_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.lbl_contact_number')]) }}'; hasError = true; }
            if (!gender) { document.getElementById('op_gender_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.lbl_gender')]) }}'; hasError = true; }
            if (!relation) { document.getElementById('op_relation_error').textContent = '{{ __('validation.required', ['attribute'=> __('clinic.relation')]) }}'; hasError = true; }
            const userId = document.getElementById('patient_id')?.value || '';
            if (!userId) { hasError = true; }
            if (hasError) return;

            const formData = new FormData();
            formData.append('first_name', firstName.value);
            formData.append('last_name', lastName.value);
            formData.append('dob', dob.value);
            formData.append('contactNumber', phone.value);
            formData.append('gender', gender);
            formData.append('relation', relation);
            formData.append('user_id', userId);
            if (profileInput && profileInput.files[0]) {
                formData.append('profile_image', profileInput.files[0]);
            }

            const res = await fetch(`${baseUrl}/app/appointment/other-patient`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            });
            const data = await res.json();
            if (data?.status) {
                window.successSnackbar && window.successSnackbar(data.message || 'Saved');
                const newId = data?.data?.id;
                const userId = document.getElementById('patient_id')?.value;

                // Ensure section is visible
                const toggle = document.getElementById('toggle-other-patient');
                const section = document.getElementById('other-patient-section');
                const listEl = document.getElementById('other-patient-list');
                if (toggle) toggle.checked = true;
                if (section) section.classList.remove('d-none');

                // Reload list, then mark the new one active
                if (window.loadOtherPatients) {
                    await window.loadOtherPatients(userId);
                }
                if (newId) {
                    if (window.setSelectedOtherPatientId) window.setSelectedOtherPatientId(newId);
                    if (listEl) {
                        Array.from(listEl.children).forEach(c => c.classList.remove('bg-primary', 'border-primary', 'active'));
                        const match = Array.from(listEl.children).find(el => (el.textContent || '').trim().length);
                        if (match) match.classList.add('bg-primary', 'border-primary', 'active');
                    }
                }

                // Close modal etc...
                firstName.value = '';
                lastName.value = '';
                dob.value = '';
                phone.value = '';
                document.querySelectorAll('input[name="op_gender"]').forEach(r=> r.checked=false);
                document.querySelectorAll('input[name="op_relation"]').forEach(r=> r.checked=false);
                previewImg.src = '{{ default_file_url() }}';
                if (profileInput) profileInput.value = '';
                // close modal
                const modalEl = document.getElementById('exampleModal1');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    inst.hide();
                }
            } else {
                window.errorSnackbar && window.errorSnackbar(data?.message || 'Error');
                const all = data?.all_message || {};
                Object.keys(all).forEach(k => {
                    const map = {
                        first_name: 'op_first_name_error',
                        last_name: 'op_last_name_error',
                        dob: 'op_dob_error',
                        contactNumber: 'op_phone_error',
                        gender: 'op_gender_error',
                        relation: 'op_relation_error',
                        profile_image: 'op_profile_image_error',
                    };
                    const id = map[k];
                    if (id) {
                        const el = document.getElementById(id);
                        if (el) el.textContent = (all[k] && all[k][0]) || '';
                    }
                });
            }
        }

        submitBtn?.addEventListener('click', submitOtherPatient);
        submitBtnFooter?.addEventListener('click', submitOtherPatient);
    });
})();
</script>

