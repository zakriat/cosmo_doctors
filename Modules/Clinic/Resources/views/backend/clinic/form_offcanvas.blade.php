
 

{{-- Offcanvas Form --}}
<form enctype="multipart/form-data" id="clinic-form-offcanvas">
    @csrf
    <input type="hidden" name="_method" id="form_method" value="POST">
    <input type="hidden" name="id" id="clinic_id">

    <div class="offcanvas offcanvas-end offcanvas-w-40"
        tabindex="-1"
        id="form-offcanvas"
        aria-labelledby="form-offcanvasLabel">
        {{-- Form Header --}}
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="form-offcanvasLabel">
                {{ __('clinic.create_clinic') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        {{-- Form Body --}}
        <div class="offcanvas-body position-relative">

            <div class="row">
                {{-- Clinic Image --}}
                <div class="row align-items-start">
                    <div class="col-md-6">
                        <div class="image-upload-container text-center">
                            <div class="clinic-image-preview d-flex justify-content-center align-items-center mb-2 mx-auto">
                                <img id="clinicImagePreview"
                                    alt="{{ __('clinic.profile_image') }}"
                                    src="{{ default_file_url() }}"
                                    class="img-fluid object-fit-cover avatar-170 rounded-circle" />
                            </div>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-light"
                                    onclick="document.getElementById('fileInput').click();">
                                    {{ __('clinic.upload') }}
                                </button>
                            </div>
                            <input type="file" 
                                name="file_url" 
                                id="fileInput" 
                                class="d-none"
                                accept=".jpg,.jpeg,.png"
                                onchange="previewClinicImage(this);" />
                            <input type="hidden" name="remove_file" id="remove_file" value="0" />
                            <div id="file-format-error" class="text-danger mt-1 d-none"></div>
                            <span class="text-muted small">{{ __('clinic.only_jpeg_jpg_png_files_allowed') }}</span>
                        </div>
                        <span class="text-danger">@error('file_url'){{ $message }}@enderror</span>
                    </div>

                    {{-- Right Column: Name + Description --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('clinic.lbl_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="{{ __('clinic.lbl_name') }}">
                            <span class="validation-error text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">{{ __('clinic.lbl_description') }}</label>
                            <textarea class="form-control" name="description" id="description"
                                      maxlength="250"
                                      placeholder="{{ __('clinic.lbl_description') }}"></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted" id="clinic-description-counter">0/250</small>
                            </div>
                            <span class="validation-error text-danger"></span>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="clinic-email" class="form-control"
                        placeholder="{{ __('clinic.lbl_Email') }}">
                    <span id="email-error" class="text-danger"></span>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Contact Number --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_contact_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="contact_number" id="clinic-contact-number" class="form-control"
                           placeholder="{{ __('clinic.lbl_contact_number') }}"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    <input type="hidden" id="clinic-dial-code" name="dial_code" value="">
                    <span id="contact-number-error" class="text-danger"></span>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- clinic admin --}}
                @if(multivendor())
                @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vendor_id" class="form-label">{{ __('clinic.clinic_admin') }}</label>
                        <select class="form-select select2 @error('vendor_id') is-invalid @enderror"
                            id="vendor_id" name="vendor_id"
                            data-placeholder="{{ __('clinic.select_clinic_admin') }}">
                            <option value="">{{ __('clinic.select_vendor') }}</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->first_name }} {{ $vendor->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif
                @endif
                {{-- Speciality --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_speciality') }} <span class="text-danger">*</span></label>
                    <select name="speciality" id="speciality" class="form-select select2" data-placeholder="{{ __('clinic.lbl_speciality') }}">
                        <option value="">{{ __('clinic.lbl_speciality') }}</option>
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Time Slot --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_time_slot') }} <span class="text-danger">*</span></label>
                    <select name="time_slot" id="time_slot" class="form-select select2" data-placeholder="{{ __('clinic.lbl_time_slot') }}">
                        <option value="">{{ __('clinic.lbl_time_slot') }}</option>
                        @foreach([5,10,15,20,25,30,35,40,45,55,60] as $slot)
                        <option value="{{ $slot }}">{{ $slot }}</option>
                        @endforeach
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label d-block">{{ __('clinic.lbl_status') }}</label>
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <span>{{ __('messages.active') }}</span>
                         <div class="form-check form-switch m-0">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" id="clinic-status" name="status" type="checkbox" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3"></div>
                <div class="col-md-12">
                    <label class="form-label h3 fw-bold">{{ __('clinic.other_detail') }}</label>
                </div>

                {{-- Address --}}
                <div class="col-md-12">
                    <label class="form-label">{{ __('clinic.lbl_address') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="address" placeholder="{{ __('clinic.lbl_address') }}"></textarea>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Country --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_country') }} <span class="text-danger">*</span></label>
                    <select name="country" class="form-select select2" id="form_country" data-placeholder="{{ __('clinic.lbl_country') }}">
                        <option value="">{{ __('clinic.lbl_country') }}</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- State --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_state') }} <span class="text-danger">*</span></label>
                    <select name="state" id="form_state" class="form-select select2" data-placeholder="{{ __('clinic.lbl_state') }}">
                        <option value="">{{ __('clinic.lbl_state') }}</option>
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- City --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_city') }} <span class="text-danger">*</span></label>
                    <select name="city" id="form_city" class="form-select select2" data-placeholder="{{ __('clinic.lbl_city') }}">
                        <option value="">{{ __('clinic.lbl_city') }}</option>
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Postal Code --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_postal_code') }}</label>
                    <input type="text" name="pincode" class="form-control" placeholder="{{ __('clinic.lbl_postal_code') }}">
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Latitude / Longitude --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_lat') }}</label>
                    <input type="text" name="latitude" class="form-control" placeholder="{{ __('clinic.lbl_lat') }}">
                    <span class="validation-error text-danger"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_long') }}</label>
                    <input type="text" name="longitude" class="form-control" placeholder="{{ __('clinic.lbl_long') }}">
                    <span class="validation-error text-danger"></span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="offcanvas-footer p-3 d-flex justify-content-end">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-white" id="clinic-form-cancel-btn">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-secondary" id="clinic-form-save-btn">
                    <span class="btn-text">{{ __('messages.save') }}</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="loading-text d-none">Loading...</span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('clinic-form-offcanvas');
        const saveBtn = document.getElementById('clinic-form-save-btn');
        const cancelBtn = document.getElementById('clinic-form-cancel-btn');
        const offcanvasEl = document.getElementById('form-offcanvas');
        const previewImgEl = document.getElementById('clinicImagePreview');
        
        // Get button child elements for loading state
        const btnText = saveBtn?.querySelector('.btn-text');
        const spinner = saveBtn?.querySelector('.spinner-border');
        const loadingText = saveBtn?.querySelector('.loading-text');
        const removeFlagEl = document.getElementById('remove_file');
        const removeBtn = document.getElementById('remove-image-btn') || form.querySelector('button[data-action="remove-image"]') || null;
        const emailInput = document.getElementById('clinic-email');
        const emailError = document.getElementById('email-error');
        const contactNumberInput = document.getElementById('clinic-contact-number');
        const contactNumberError = document.getElementById('contact-number-error');
        const defaultImageUrl = "{{ default_file_url() }}";
        const baseUrl = "{{ url('/') }}";
        const formTitle = document.getElementById('form-offcanvasLabel');
        const dialCodeHidden = document.getElementById('clinic-dial-code');

        const CREATE_URL = "{{ route('backend.clinics.store') }}";
        const UPDATE_URL_TMPL = "{{ route('backend.clinics.update', ':id') }}";
        const EDIT_URL_TMPL = "{{ route('backend.clinics.edit', ':id') }}";
        const STATES_URL = baseUrl + "/app/state/index_list?country_id=";
        const CITIES_URL = baseUrl + "/app/city/index_list?state_id=";
        const CHECK_EMAIL_URL = baseUrl + "/app/clinics/check-email";
        const SPECIALITY_URL = baseUrl + "/app/clinics/speciality";

        // Track if we are in edit mode
        let isEditMode = false;

        // Helper functions for loading state
        function showLoading() {
            if (btnText) btnText.classList.add('d-none');
            if (spinner) spinner.classList.remove('d-none');
            if (loadingText) loadingText.classList.remove('d-none');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.style.opacity = '0.7';
                saveBtn.style.cursor = 'not-allowed';
            }
        }

        function hideLoading() {
            if (btnText) btnText.classList.remove('d-none');
            if (spinner) spinner.classList.add('d-none');
            if (loadingText) loadingText.classList.add('d-none');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
                saveBtn.style.cursor = 'pointer';
            }
        }

        // ====== Load Specialities ======
        function loadSpecialities() {
            return fetch(SPECIALITY_URL, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    const $speciality = $('#speciality');
                    if ($speciality.length && data.status && data.data) {
                        // Clear existing options
                        $speciality.empty();
                        $speciality.append('<option value="">{{ __("clinic.lbl_speciality") }}</option>');
                        
                        if (Array.isArray(data.data)) {
                            data.data.forEach(speciality => {
                                $speciality.append($('<option>', {
                                    value: speciality.id,
                                    text: speciality.name
                                }));
                            });
                        }
                        
                        // Trigger change to update Select2
                        $speciality.trigger('change');
                    }
                    return data;
                })
                .catch(error => {
                    console.error('Error loading specialities:', error);
                    return null;
                });
        }

        // ====== Select2 init ======
        let select2Initialized = false;
        
        function initializeSelect2WithSearch() {
            if (window.jQuery && !select2Initialized) {
                // TARGETED: Only initialize Select2 elements within this offcanvas
                $('#form-offcanvas .select2').select2({
                    width: '100%',
                    allowClear: false,
                    minimumResultsForSearch: 0, // Always show search box
                    dropdownParent: $('#form-offcanvas'), // Important for offcanvas
                    placeholder: function() {
                        return $(this).data('placeholder') || '{{ __("clinic.select_option") }}';
                    },
                    language: {
                        noResults: function() {
                            return "{{ __('clinic.no_results_found') }}";
                        },
                        searching: function() {
                            return "{{ __('clinic.searching') }}";
                        },
                        inputTooShort: function() {
                            return "{{ __('clinic.please_enter_more_characters') }}";
                        }
                    }
                });
                
                select2Initialized = true;
            }
        }

        // Initialize Select2 only once when document is ready
        $(document).ready(function() {
            initializeSelect2WithSearch();
            loadSpecialities();
        });

        // Ensure Select2 is initialized when offcanvas is first shown
        if (offcanvasEl) {
            offcanvasEl.addEventListener('shown.bs.offcanvas', function() {
                if (!select2Initialized) {
                    initializeSelect2WithSearch();
                }
            });
        }


        // Description character counter (max 250)
        (function initDescriptionCounter(){
            var descEl = document.getElementById('description');
            var counterEl = document.getElementById('clinic-description-counter');
            if (!descEl || !counterEl) return;
            var max = 250;
            var update = function(){
                if (descEl.value.length > max) {
                    descEl.value = descEl.value.slice(0, max);
                }
                counterEl.textContent = descEl.value.length + '/' + max;
            };
            // initialize
            update();
            descEl.addEventListener('input', update);
            descEl.addEventListener('change', update);
        })();


        // ====== Utility ======
        function setSelectValue(selector, value) {
            const el = form.querySelector(selector);
            if (!el) return;
            if (window.jQuery && $(el).hasClass('select2')) {
                $(el).val(value).trigger('change');
            } else {
                el.value = value ?? '';
            }
        }

        // ====== Email validation ======
        function isValidEmailFormat(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                // Only validate email on create mode
                if (isEditMode) return;
                const email = this.value.trim();
                if (!email) {
                    emailError.textContent = '';
                    this.classList.remove('is-invalid');
                    return;
                }
                if (!isValidEmailFormat(email)) {
                    emailError.textContent = "{{ __('clinic.please_enter_valid_email_address') }}";
                    this.classList.add('is-invalid');
                    return;
                }
                fetch(CHECK_EMAIL_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            email
                        })
                    })
                    .then(res => res.ok ? res.json() : Promise.resolve({
                        exists: false
                    }))
                    .then(data => {
                        if (data.exists) {
                            emailError.textContent = "{{ __('clinic.email_has_already_been_taken') }}";
                            emailInput.classList.add('is-invalid');
                        } else {
                            emailError.textContent = '';
                            emailInput.classList.remove('is-invalid');
                        }
                    })
                    .catch(() => {
                        emailError.textContent = '';
                        emailInput.classList.remove('is-invalid');
                    });
            });
            emailInput.addEventListener('input', function() {
                // Clear validation error immediately when user starts typing
                const errorSpan = this.parentElement.querySelector('.validation-error');
                if (errorSpan) errorSpan.textContent = '';
                this.classList.remove('is-invalid');

                // Only validate email on create mode
                if (isEditMode) return;
                const email = this.value.trim();
                if (!email) {
                    emailError.textContent = '';
                    this.classList.remove('is-invalid');
                    return;
                }
                if (!isValidEmailFormat(email)) {
                    emailError.textContent = "{{ __('clinic.please_enter_valid_email_address') }}";
                    this.classList.add('is-invalid');
                } else {
                    emailError.textContent = '';
                    emailInput.classList.remove('is-invalid');
                }
            });
        }

        // ====== Phone number (intl-tel-input) ======
        let usingIntlTelInput = false;
        let itiInstance = null;
        function isValidPhoneNumber(phone) {
            // Fallback validation when plugin is not available
            return /^[0-9]{5,20}$/.test(phone);
        }
        if (contactNumberInput && window.intlTelInput) {
            try {
                itiInstance = window.intlTelInput(contactNumberInput, {
                    initialCountry: 'auto',
                    separateDialCode: true,
                    utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js',
                    geoIpLookup: function(callback) {
                        fetch('https://ipapi.co/json')
                            .then(function(res){ return res.ok ? res.json() : Promise.reject(); })
                            .then(function(data){ callback(data && data.country ? data.country : 'US'); })
                            .catch(function(){ callback('US'); });
                    }
                });
                usingIntlTelInput = true;
                // Set initial dial code
                if (dialCodeHidden) {
                    var c = itiInstance.getSelectedCountryData();
                    dialCodeHidden.value = c && c.dialCode ? c.dialCode : '';
                }
                contactNumberInput.addEventListener('countrychange', function(){
                    if (dialCodeHidden) {
                        var d = itiInstance.getSelectedCountryData();
                        dialCodeHidden.value = d && d.dialCode ? d.dialCode : '';
                    }
                    // Clear previous errors on country change
                    if (contactNumberError) contactNumberError.textContent = '';
                    contactNumberInput.classList.remove('is-invalid');
                });
                // Plugin-based validation
                var handlePhoneValidation = function(){
                    const val = contactNumberInput.value.trim();
                    if (!val) { if (contactNumberError) contactNumberError.textContent = ''; contactNumberInput.classList.remove('is-invalid'); return; }
                    if (!itiInstance.isValidNumber()) {
                        if (contactNumberError) contactNumberError.textContent = '{{ __("clinic.please_enter_valid_phone_number") }}';
                        contactNumberInput.classList.add('is-invalid');
                    } else {
                        if (contactNumberError) contactNumberError.textContent = '';
                        contactNumberInput.classList.remove('is-invalid');
                    }
                };
                contactNumberInput.addEventListener('input', handlePhoneValidation);
                contactNumberInput.addEventListener('blur', handlePhoneValidation);
            } catch(e) {
                usingIntlTelInput = false;
            }
        }
        // Fallback simple numeric validation if plugin not active
        if (contactNumberInput && !usingIntlTelInput) {
            contactNumberInput.addEventListener('input', function() {
                const errorSpan = this.parentElement.querySelector('.validation-error');
                if (errorSpan) errorSpan.textContent = '';
                this.classList.remove('is-invalid');
                this.value = this.value.replace(/[^0-9]/g, '');
                const phone = this.value.trim();
                if (!phone) { contactNumberError.textContent = ''; this.classList.remove('is-invalid'); return; }
                if (!isValidPhoneNumber(phone)) { contactNumberError.textContent = '{{ __("clinic.please_enter_valid_phone_number_digits_only") }}'; this.classList.add('is-invalid'); }
                else { contactNumberError.textContent = ''; this.classList.remove('is-invalid'); }
            });
            contactNumberInput.addEventListener('blur', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                const phone = this.value.trim();
                if (!phone) { contactNumberError.textContent = ''; this.classList.remove('is-invalid'); return; }
                if (!isValidPhoneNumber(phone)) { contactNumberError.textContent = '{{ __("clinic.please_enter_valid_phone_number_digits_only") }}'; this.classList.add('is-invalid'); }
                else { contactNumberError.textContent = ''; this.classList.remove('is-invalid'); }
            });
        }

	
        // ====== Client-side validation ======
        function validateClinicForm(isEdit) {
            let isValid = true;
            const requiredFields = [{
                    name: 'name',
                    message: '{{ __("clinic.name_is_required") }}'
                },
                {
                    name: 'email',
                    message: '{{ __("clinic.email_is_required") }}'
                },
                {
                    name: 'contact_number',
                    message: '{{ __("clinic.contact_number_is_required") }}'
                },
                {
                    name: 'address',
                    message: '{{ __("clinic.address_is_required") }}'
                },
                {
                    name: 'country',
                    message: '{{ __("clinic.country_is_required") }}'
                },
                {
                    name: 'state',
                    message: '{{ __("clinic.state_is_required") }}'
                },
                {
                    name: 'city',
                    message: '{{ __("clinic.city_is_required") }}'
                },
                {
                    name: 'time_slot',
                    message: '{{ __("clinic.time_slot_is_required") }}'
                },
                {
                    name: 'speciality',
                    message: '{{ __("clinic.speciality_is_required") }}'
                }
            ];
            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                const errorSpan = input?.parentElement.querySelector('.validation-error');
                let value = input ? (window.jQuery && $(input).hasClass('select2') ? $(input).val() : input.value) : '';
                if (input && (!value || !value.toString().trim())) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    if (errorSpan) errorSpan.textContent = field.message;
                } else {
                    input?.classList.remove('is-invalid');
                    if (errorSpan) errorSpan.textContent = '';
                }
            });
            const emailInputField = form.querySelector('[name="email"]');
            if (emailInputField) {
                const emailVal = emailInputField.value.trim();
                // Only validate email format on create
                if (!isEdit && emailVal && !isValidEmailFormat(emailVal)) {
                    isValid = false;
                    emailInputField.classList.add('is-invalid');
                    if (emailError) emailError.textContent = "{{ __('clinic.please_enter_valid_email_address') }}";
                }
            }
            const contactNumberField = form.querySelector('[name="contact_number"]');
            if (contactNumberField) {
                // Remove all non-digit characters before validation
                contactNumberField.value = contactNumberField.value.replace(/[^0-9]/g, '');
                const phoneVal = contactNumberField.value.trim();
                if (phoneVal && !isValidPhoneNumber(phoneVal)) {
                    isValid = false;
                    contactNumberField.classList.add('is-invalid');
                    if (contactNumberError) contactNumberError.textContent = "{{ __('clinic.please_enter_valid_phone_number_digits_only') }}";
                }
            }
            return isValid;
        }

        // ====== Dependent dropdowns ======
        function loadStates(countryId, selectedState = null, selectedCity = null) {
            return fetch(STATES_URL + encodeURIComponent(countryId), {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(states => {
                const $state = $('#form_state');
                if ($state.length) {
                    $state.html('<option value="">{{ __("clinic.lbl_state") }}</option>');
                    if (Array.isArray(states)) {
                        states.forEach(s => $state.append($('<option>', { value: s.id, text: s.name })));
                    }
                    setSelectValue('#form_state', selectedState);
                    if (selectedState && selectedCity) {
                        return loadCities(selectedState, selectedCity);
                    }
                }
            });
        }

        function loadCities(stateId, selectedCity = null) {
            return fetch(CITIES_URL + encodeURIComponent(stateId), {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(cities => {
                const $city = $('#form_city');
                if ($city.length) {
                    $city.html('<option value="">{{ __("clinic.lbl_city") }}</option>');
                    if (Array.isArray(cities)) {
                        cities.forEach(c => $city.append($('<option>', { value: c.id, text: c.name })));
                    }
                    setSelectValue('#form_city', selectedCity);
                }
            });
        }

        if (window.jQuery) {
            $(document).off('change.formCountry').on('change.formCountry', 'select[name="country"]', function() {
                const cid = $(this).val();
                $('#form_state').html('<option value="">{{ __("clinic.lbl_state") }}</option>').val('').trigger('change');
                $('#form_city').html('<option value="">{{ __("clinic.lbl_city") }}</option>').val('').trigger('change');
                if (cid) loadStates(cid);
            });
            $(document).off('change.formState').on('change.formState', '#form_state', function() {
                const sid = $(this).val();
                $('#form_city').html('<option value="">{{ __("clinic.lbl_city") }}</option>').val('').trigger('change');
                if (sid) loadCities(sid);
            });
        }

        // ====== Image preview/remove ======
        window.previewClinicImage = function(input) {
            const file = input?.files?.[0];
            if (!file) return;  

            const fileFormatError = document.getElementById('file-format-error');
            fileFormatError.style.display = 'none';
            fileFormatError.textContent = '';

            // Allowed types/extensions
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            const allowedExtensions = ['jpeg', 'jpg', 'png'];
            const fileExtension = file.name.toLowerCase().split('.').pop();

            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                // ❌ Invalid file format
                fileFormatError.textContent = '{{ __("clinic.unsupported_format_use_jpg_jpeg_png") }}';
                fileFormatError.style.display = 'block';

                // Reset input & preview
                input.value = '';
                previewImgEl.src = defaultImageUrl;
                removeFlagEl.value = '0';

                return;
            }

            // ✅ Valid image, show preview
            const reader = new FileReader();
            reader.onload = e => {
                previewImgEl.src = e.target.result;
                removeFlagEl.value = '0';
            };
            reader.readAsDataURL(file);
        };


        // ====== Cancel button functionality ======
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                (bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl)).hide();
            });
        }

        // ====== Real-time validation ======
        function clearFieldError(input) {
            const errorSpan = input.parentElement.querySelector('.validation-error');
            if (errorSpan) errorSpan.textContent = '';
            input.classList.remove('is-invalid');
        }

        // Add event listeners to all form fields
        const allFormFields = form.querySelectorAll('input, select, textarea');
        allFormFields.forEach(field => {
            field.addEventListener('input', function() {
                clearFieldError(this);
            });
            field.addEventListener('change', function() {
                clearFieldError(this);
            });
        });

        // For select2 dropdowns
        if (window.jQuery) {
            $('#form_country, #form_state, #form_city, select[name="time_slot"], select[name="vendor_id"], select[name="speciality"]').on('change', function() {
                clearFieldError(this);
            });
        }

        // ====== Offcanvas close ======
        if (offcanvasEl) {
            offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
                form.reset();
                isEditMode = false;
                form.querySelector('#form_method').value = 'POST';
                
                // Clear the ID field completely
                let idHidden = form.querySelector('[name="id"]');
                if (idHidden) {
                    idHidden.value = '';
                    idHidden.removeAttribute('value');
                }
                
                if (window.jQuery) {
                    $(form).find('.select2').val(null).trigger('change');
                }
                previewImgEl.src = defaultImageUrl;
                if (removeFlagEl) removeFlagEl.value = '0';
                if (removeBtn) removeBtn.disabled = true;
                form.querySelectorAll('.validation-error').forEach(span => span.textContent = '');
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Clear file format error
                const fileFormatError = document.getElementById('file-format-error');
                if (fileFormatError) {
                    fileFormatError.style.display = 'none';
                    fileFormatError.textContent = '';
                }
                if (formTitle) formTitle.textContent = '{{ __("clinic.create_clinic") }}';
                // Reset button to normal state
                hideLoading();
                if (window.jQuery && $.fn.DataTable && $('#datatable').length) {
                    $('#datatable').DataTable().ajax.reload(null, false);
                } else if (typeof window.reloadClinicTable === 'function') {
                    window.reloadClinicTable();
                }
                // Clear phone error
                if (contactNumberError) contactNumberError.textContent = '';
                if (emailError) emailError.textContent = '';
            });
        }

        // ====== Create mode ======
        window.addClinic = function() {
            form.reset();
            isEditMode = false;
            form.querySelector('#form_method').value = 'POST';
            let idHidden = form.querySelector('[name="id"]');
            if (idHidden) {
                idHidden.value = ''; // Clear the ID field completely
                idHidden.removeAttribute('value'); // Remove the value attribute completely
            }
            
            // Clear all validation errors
            form.querySelectorAll('.validation-error').forEach(span => span.textContent = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            if (emailError) emailError.textContent = '';
            
            if (window.jQuery) {
                $(form).find('.select2').val(null).trigger('change');
            }
            previewImgEl.src = defaultImageUrl;
            if (removeFlagEl) removeFlagEl.value = '0';
            if (formTitle) formTitle.textContent = '{{ __("clinic.create_clinic") }}';
            // Reset button to normal state
            hideLoading();
            
            // Clear file format error
            const fileFormatError = document.getElementById('file-format-error');
            if (fileFormatError) {
                fileFormatError.style.display = 'none';
                fileFormatError.textContent = '';
            }
            (bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl)).show();
            // Clear phone error
            if (contactNumberError) contactNumberError.textContent = '';
            // Always enable save button
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
                saveBtn.style.cursor = 'pointer';
            }
        };

        

        window.editClinic = async function(id) {
            const EDIT_URL = EDIT_URL_TMPL.replace(':id', id);
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);

            form.reset();
            isEditMode = true;

            if (window.jQuery) {
                $(form).find('.select2').val(null).trigger('change');
            }

            if (removeFlagEl) removeFlagEl.value = '0';

            form.querySelector('#form_method').value = 'PUT';
            form.querySelector('[name="id"]').value = id;

            if (formTitle) formTitle.textContent = '{{ __("clinic.update_clinic") }}';
            // Reset button to normal state
            hideLoading();

            if (contactNumberError) contactNumberError.textContent = '';

            bsOffcanvas.show();

            try {
                const res = await fetch(EDIT_URL, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const clinic = data.clinic;


                // Fill text fields
                form.querySelector('[name="name"]').value = clinic.name ?? '';
                form.querySelector('[name="email"]').value = clinic.email ?? '';
                form.querySelector('[name="description"]').value = clinic.description ?? '';
                form.querySelector('[name="address"]').value = clinic.address ?? '';
                form.querySelector('[name="pincode"]').value = clinic.pincode ?? '';
                form.querySelector('[name="latitude"]').value = clinic.latitude ?? '';
                form.querySelector('[name="longitude"]').value = clinic.longitude ?? '';
                form.querySelector('[name="id"]').value = clinic.id ?? id;

                // ✅ Set phone number correctly
                if (itiInstance && clinic.contact_number) {
                    let storedNumber = clinic.contact_number.trim();
                    
                    // Handle different phone number formats
                    if (storedNumber.includes(' ')) {
                        // Format: "+91 9313039898" - extract country code and number
                        const parts = storedNumber.split(' ');
                        if (parts.length >= 2) {
                            const countryCode = parts[0].replace('+', '');
                            const phoneNumber = parts[1];
                            
                            // Set the country and number
                            itiInstance.setNumber('+' + countryCode + phoneNumber);
                            const countryData = itiInstance.getSelectedCountryData();
                            if (countryData && dialCodeHidden) {
                                dialCodeHidden.value = countryData.dialCode || '';
                            }
                            contactNumberInput.value = phoneNumber;
                        }
                    } else if (storedNumber.startsWith('+')) {
                        // Format: "+919313039898" - full international number
                        itiInstance.setNumber(storedNumber);
                        const countryData = itiInstance.getSelectedCountryData();
                        const dialCode = countryData?.dialCode || '';
                        let localPart = storedNumber.replace('+' + dialCode, '').trim();
                        contactNumberInput.value = localPart;
                        if (dialCodeHidden) {
                            dialCodeHidden.value = dialCode;
                        }
                    } else {
                        // Format: "9313039898" - local number only
                        contactNumberInput.value = storedNumber;
                    }
                }

                // ✅ Image
                document.getElementById('clinicImagePreview').src = clinic.file_url ?? "{{ default_file_url() }}";
                if (clinic.file_url) {
                    removeFlagEl.value = '0';
                }

                // ✅ Wait and set async dropdowns in correct order
                if (window.jQuery) {
                    // Set country first and wait for states to load
                    if (clinic.country) {
                        $(form).find('select[name="country"]').val(clinic.country).trigger('change');
                        // Load states and cities in sequence
                        loadStates(clinic.country, clinic.state, clinic.city);
                    } else {
                        // If no country, set state and city directly
                        $(form).find('select[name="state"]').val(clinic.state).trigger('change');
                        if (clinic.state) {
                            loadCities(clinic.state, clinic.city);
                        } else {
                            $(form).find('select[name="city"]').val(clinic.city).trigger('change');
                        }
                    }

                    // Set other dropdowns
                    $(form).find('select[name="time_slot"]').val(clinic.time_slot).trigger('change');
                    $(form).find('select[name="vendor_id"]').val(clinic.vendor_id).trigger('change');

                    // Load specialities and then select the clinic's one
                    loadSpecialities().then(() => {
                        if (clinic.system_service_category) {
                            $(form).find('select[name="speciality"]').val(clinic.system_service_category).trigger('change');
                        }
                    });
                }


                // ✅ Status toggle
                form.querySelector('#clinic-status').checked = Number(clinic.status) === 1;

            } catch (error) {
                console.error('Error loading clinic data:', error);
                // Show error toast message
                if (typeof window.errorSnackbar === 'function') {
                    window.errorSnackbar('{{ __("clinic.unable_to_load_clinic") }}');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ __("clinic.unable_to_load_clinic") }}'
                    });
                }
            }
        };

        // ====== Submit ======
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            form.querySelectorAll('.validation-error').forEach(span => span.textContent = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            if (contactNumberError) contactNumberError.textContent = '';
            const formData = new FormData(form);

            // --- FIX: Always check the _method and id field to determine create/update ---
            // If _method is PUT and id is present, it's update. Otherwise, it's create.
            let isEdit = false;
            const idValue = formData.get('id');
            const methodValue = formData.get('_method');
            
            // Only consider it edit mode if both method is PUT and id has a valid value
            if (methodValue === 'PUT' && idValue && idValue.trim() !== '') {
                isEdit = true;
            } else {
                // Always force _method to POST and REMOVE id completely for create
                formData.set('_method', 'POST');
                formData.delete('id'); // Remove ID completely instead of setting to empty string
                isEdit = false;
            }

            // Always validate on both create and update
            if (!validateClinicForm(isEdit)) {
                hideLoading();
                return;
            }

            // Show loading state
            showLoading();
            // When create, call CREATE_URL; when edit, call UPDATE_URL
            let url = '';
            if (isEdit) {
                url = UPDATE_URL_TMPL.replace(':id', formData.get('id'));
            } else {
                url = CREATE_URL;
            }
            if (!form.querySelector('#clinic-status').checked) {
                formData.set('status', '0');
            }

            // Include speciality in form submission
            const specialityValue = form.querySelector('[name="speciality"]').value;
            if (specialityValue) {
                formData.set('system_service_category', specialityValue);
            }

            // Include dial_code and formatted phone when plugin is active
            if (usingIntlTelInput && itiInstance) {
                const countryData = itiInstance.getSelectedCountryData();
                const dialCode = countryData && countryData.dialCode ? countryData.dialCode : '';

                // Get the full E.164 number
                const fullNumber = itiInstance.getNumber();

                // Extract just the local number by removing +<dialcode>
                const nationalNumber = fullNumber.replace('+' + dialCode, '').trim();

                // Store formatted as "+91 9313039898"
                const formattedPhone = `+${dialCode} ${nationalNumber}`;

                formData.set('contact_number', formattedPhone);
                formData.set('dial_code', dialCode);
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    if (res.ok) {
                        // Show success toast message
                        const successMessage = isEdit ? '{{ __("clinic.clinic_updated_successfully") }}' : '{{ __("clinic.clinic_created_successfully") }}';
                        if (typeof window.successSnackbar === 'function') {
                            window.successSnackbar(successMessage);
                        }
                        // Close the offcanvas
                        (bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl)).hide();
                        if (window.jQuery && $.fn.DataTable && $('#datatable').length) {
                            $('#datatable').DataTable().ajax.reload(null, false);
                        } else if (typeof window.reloadClinicTable === 'function') {
                            window.reloadClinicTable();
                        }
                        form.reset();
                        isEditMode = false;
                        form.querySelector('#form_method').value = 'POST';
                        
                        // Clear the ID field completely
                        let idHidden = form.querySelector('[name="id"]');
                        if (idHidden) {
                            idHidden.value = '';
                            idHidden.removeAttribute('value');
                        }
                        
                        if (window.jQuery) {
                            $(form).find('.select2').val(null).trigger('change');
                        }
                        // Don't reset image here - let the offcanvas close event handle it
                        removeFlagEl && (removeFlagEl.value = '0');
                        if (formTitle) formTitle.textContent = '{{ __("clinic.create_clinic") }}';
                        // Hide loading state
                        hideLoading();
                        // Clear phone error
                        if (contactNumberError) contactNumberError.textContent = '';
                        if (emailError) emailError.textContent = '';
                    } else if (res.status === 422) {
                        const data = await res.json();
                        let generalErrorMsg = '';
                        if (data.message) {
                            generalErrorMsg = data.message;
                        }
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                let input = form.querySelector(`[name="${key}"]`);
                                let errorSpan = input?.parentElement.querySelector('.validation-error');
                                if (input) input.classList.add('is-invalid');
                                if (errorSpan) errorSpan.textContent = data.errors[key][0];
                                // Show phone error if contact_number
                                if (key === 'contact_number' && contactNumberError) {
                                    contactNumberError.textContent = data.errors[key][0];
                                }
                            });
                        }
                        // Hide loading state on validation error
                        hideLoading();
                    } else {
                        // Show error toast message
                        if (typeof window.errorSnackbar === 'function') {
                            window.errorSnackbar('{{ __("clinic.an_error_occurred_try_again") }}');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: '{{ __("clinic.an_error_occurred_try_again") }}'
                            });
                        }
                        // Hide loading state on error
                        hideLoading();
                    }
                })
                .catch(() => {
                    // Show error toast message
                    if (typeof window.errorSnackbar === 'function') {
                        window.errorSnackbar('{{ __("clinic.request_failed_try_again") }}');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '{{ __("clinic.request_failed_try_again") }}'
                        });
                    }
                    // Hide loading state on catch error
                    hideLoading();
                });
        });
    });
</script>

@push('before-styles')
<!-- Select2 CSS for clinic form only -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- intl-tel-input CSS for country code functionality -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css">
@endpush

@push('after-scripts')
<!-- Select2 JavaScript for clinic form only -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- intl-tel-input JavaScript for country code functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>

<!-- Removed duplicate Select2 initialization to prevent multiple dropdown renderings -->
@endpush
