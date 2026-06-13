<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<form
    action="{{ isset($vendor) ? route('backend.multivendors.update', $vendor->id) : route('backend.multivendors.store') }}"
    method="POST" enctype="multipart/form-data" class="vendor-form" id="vendorForm">
    @csrf
    @if (isset($vendor))
        @method('PUT')
    @endif

    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas"
        data-vendor-exists="{{ isset($vendor) ? '1' : '0' }}" data-vendor-id="{{ $vendor->id ?? '' }}"
        data-country-id="{{ $vendor->country ?? '' }}" data-state-id="{{ $vendor->state ?? '' }}"
        data-city-id="{{ $vendor->city ?? '' }}" data-mobile="{{ $vendor->mobile ?? '' }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                {{ isset($vendor) ? __('messages.edit') . ' ' . __('clinic.clinic_admin') : __('messages.create') . ' ' . __('clinic.clinic_admin') }}
            </h5>
        </div>

        <div class="offcanvas-body">
            <div class="row">
                {{-- Profile Image --}}
                <div class="col-md-6 create-service-image mb-3">
                    <label class="form-label">{{ __('clinic.image') }}</label>
                    <div class="image-upload-container text-center">
                        <div
                            class="vendor-image-preview d-flex justify-content-center align-items-center mb-2 w-50 mx-auto">
                            <img id="vendorImagePreview" alt="Vendor Image Preview"
                                src="{{ isset($vendor) && $vendor->profile_image ? asset($vendor->profile_image) : asset('img/avatar/avatar.webp') }}"
                                class="img-fluid object-fit-cover avatar-170 rounded-circle" />
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-3 mb-1">
                            <button type="button" class="btn btn-light" onclick="triggerVendorFile(this)">
                                {{ __('clinic.upload') }}
                            </button>
                            <!-- <button type="button"
                                class="btn btn-danger"
                                id="removeVendorImageBtn"
                                {{ isset($vendor) && $vendor->profile_image ? '' : 'disabled' }}
                                onclick="removeVendorImage();">
                                {{ __('messages.remove') }}
                            </button> -->
                        </div>
                        <input type="file" name="profile_image" id="vendorFileInput" style="display: none;"
                            accept=".jpeg,.jpg,.png" onchange="previewVendorImage(this);" />
                        <input type="hidden" name="remove_profile_image" id="remove_profile_image" value="0" />
                        <div class="text-muted small" id="image-error"></div>
                    </div>
                    @error('profile_image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- First Name --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_first_name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control"
                            value="{{ old('first_name', $vendor->first_name ?? '') }}"
                            placeholder="{{ __('clinic.enter_first_name') }}">
                        @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_last_name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ old('last_name', $vendor->last_name ?? '') }}"
                            placeholder="{{ __('clinic.enter_last_name') }}">
                        @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_Email') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="email" class="form-control" id="email"
                            value="{{ old('email', $vendor->email ?? '') }}"
                            placeholder="{{ __('clinic.enter_email') }}">
                        <span class="text-danger" id="email-error">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_gender') }} <span
                                class="text-danger">*</span></label>
                        <select name="gender" class="form-select" id="genderSelect">
                            <option value="">{{ __('clinic.select_gender') }}</option>
                            <option value="male"
                                {{ old('gender', $vendor->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female"
                                {{ old('gender', $vendor->gender ?? '') == 'female' ? 'selected' : '' }}>Female
                            </option>
                            <option value="intersex"
                                {{ old('gender', $vendor->gender ?? '') == 'intersex' ? 'selected' : '' }}>Intersex
                            </option>
                        </select>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('employee.lbl_phone_number') }}<span
                                class="text-danger">*</span></label>
                        <input type="tel" id="mobileInput" class="form-control"
                            value="{{ old('mobile', $vendor->mobile ?? '') }}"
                            placeholder="{{ __('clinic.enter_phone_number') }}">
                        <span class="text-danger" id="mobile-error">
                            @error('mobile')
                                {{ $message }}
                            @enderror
                        </span>
                        <input type="hidden" name="mobile" id="hiddenMobile">
                    </div>
                </div>

                {{-- Password & Confirm Password --}}
                @if (!isset($vendor))
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('clinic.lbl_password') }} <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="{{ __('clinic.enter_password') }}">
                            <span class="text-danger" id="password-error">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('clinic.lbl_confirm_password') }} <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control"
                                id="confirm_password" placeholder="{{ __('clinic.enter_confirm_password') }}">
                            <span class="text-danger" id="confirm-password-error">
                                @error('confirm_password')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Date of Birth --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('customer.lbl_date_of_birth') }}</label>
                        <div class="input-group">
                            <input type="text" name="date_of_birth" id="datepicker" class="form-control"
                                value="{{ old('date_of_birth', $vendor->date_of_birth ?? '') }}"
                                placeholder="dd-mm-yyyy" readonly>
                            <span class="input-group-text" role="button" tabindex="0"
                                onclick="(function(){var el=document.getElementById('datepicker'); if(!el) return; if(el._flatpickr){ el._flatpickr.open(); } else { el.focus(); el.click(); } })()">
                                <i class="ph ph-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label d-block">{{ __('clinic.lbl_status') }}</label>
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <span>{{ __('messages.active') }}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" id="clinic-status" name="status" type="checkbox"
                                value="1"
                                {{ old('status', isset($vendor) ? $vendor->status ?? 1 : 1) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                {{-- Other Details --}}
                <div class="col-12 mt-4">
                    <h5 class="mb-3">{{ __('customer.other_details') }}</h5>
                </div>

                {{-- Address --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_address') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $vendor->address ?? '') }}"
                            placeholder="{{ __('clinic.enter_address') }}">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Country --}}
                <div class="col-md-6">
                    <label for="country" class="form-label">{{ __('clinic.lbl_country') }} <span
                            class="text-danger">*</span></label>
                    <select id="country" name="country" class="form-select" onchange="getState(this.value)">
                        <option value="" selected disabled>{{ __('clinic.select_country') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ old('country', $vendor->country ?? '') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="country-error">
                        @error('country')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <!-- State -->
                <div class="col-md-4">
                    <label for="state" class="form-label">{{ __('clinic.lbl_state') }} <span
                            class="text-danger">*</span></label>
                    <select id="state" name="state" class="form-select" onchange="getCity(this.value)">
                        <option value="" selected disabled>{{ __('clinic.select_state') }}</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}"
                                {{ old('state', $vendor->state ?? '') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="state-error">
                        @error('state')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <!-- City -->
                <div class="col-md-4">
                    <label for="city" class="form-label">{{ __('clinic.lbl_city') }} <span
                            class="text-danger">*</span></label>
                    <select id="city" name="city" class="form-select">
                        <option value="" selected disabled>{{ __('clinic.select_city') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ old('city', $vendor->city ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="city-error">
                        @error('city')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                {{-- Pincode --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('clinic.lbl_postal_code') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="pincode" class="form-control"
                            value="{{ old('pincode', $vendor->pincode ?? '') }}"
                            placeholder="{{ __('clinic.enter_postal_code') }}" pattern="[0-9]*" inputmode="numeric"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                        @error('pincode')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <div class="offcanvas-footer p-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-white"
                data-bs-dismiss="offcanvas">{{ __('messages.cancel') }}</button>
            <button type="submit" class="btn btn-secondary">{{ __('messages.save') }}</button>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
    // Preset values for edit mode, sourced from data-* attributes to avoid inline Blade in JS
    function getVendorPresetFromDOM() {
        var el = document.getElementById('form-offcanvas');
        if (!el) return {
            exists: false
        };
        return {
            exists: el.getAttribute('data-vendor-exists') === '1',
            id: el.getAttribute('data-vendor-id') || null,
            country_id: el.getAttribute('data-country-id') || null,
            state_id: el.getAttribute('data-state-id') || null,
            city_id: el.getAttribute('data-city-id') || null,
            mobile: el.getAttribute('data-mobile') || null,
        };
    }
    var vendorPreset = getVendorPresetFromDOM();
    window.getState = function(countryId, selectedStateId = null) {
        if (!countryId || countryId === '') {
            var root = window.currentVendorOffcanvas || document;
            var stateEl = root.querySelector('#state');
            var cityEl = root.querySelector('#city');
            if (stateEl) stateEl.innerHTML =
                '<option value="" selected disabled>{{ __('clinic.select_state') }}</option>';
            if (cityEl) cityEl.innerHTML =
                '<option value="" selected disabled>{{ __('clinic.select_city') }}</option>';
            return;
        }

        fetch(`{{ route('backend.state.index_list') }}?country_id=${countryId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="" selected disabled>{{ __('clinic.select_state') }}</option>';
                data.forEach(state => {
                    // Convert both to strings for comparison
                    const isSelected = selectedStateId && String(selectedStateId) === String(state.id);
                    options += '<option value="' + state.id + '"' + (isSelected ? ' selected' : '') +
                        '>' + state.name + '</option>';
                });
                var root = window.currentVendorOffcanvas || document;
                var stateEl = root.querySelector('#state');
                var cityEl = root.querySelector('#city');
                if (stateEl) stateEl.innerHTML = options;
                if (cityEl) cityEl.innerHTML =
                    '<option value="" selected disabled>{{ __('clinic.select_city') }}</option>';

                // Reinitialize Select2 after DOM update
                setTimeout(() => {
                    if (typeof window.initSelect2Offcanvas === 'function') {
                        window.initSelect2Offcanvas(window.currentVendorOffcanvas || null);
                    }
                }, 100);
            })
            .catch(error => console.error('Error fetching states:', error));
    }

    window.getCity = function(stateId, selectedCityId = null) {
        if (!stateId || stateId === '') {
            var root = window.currentVendorOffcanvas || document;
            var cityEl = root.querySelector('#city');
            if (cityEl) cityEl.innerHTML =
                '<option value="" selected disabled>{{ __('clinic.select_city') }}</option>';
            return;
        }

        fetch(`{{ route('backend.city.index_list') }}?state_id=${stateId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="" selected disabled>{{ __('clinic.select_city') }}</option>';
                data.forEach(city => {
                    // Convert both to strings for comparison
                    const isSelected = selectedCityId && String(selectedCityId) === String(city.id);
                    options += '<option value="' + city.id + '"' + (isSelected ? ' selected' : '') +
                        '>' + city.name + '</option>';
                });
                var root = window.currentVendorOffcanvas || document;
                var cityEl = root.querySelector('#city');
                if (cityEl) cityEl.innerHTML = options;

                // Reinitialize Select2 after DOM update
                setTimeout(() => {
                    if (typeof window.initSelect2Offcanvas === 'function') {
                        window.initSelect2Offcanvas(window.currentVendorOffcanvas || null);
                    }
                }, 100);
            })
            .catch(error => console.error('Error fetching cities:', error));
    }

    // When the form is loaded (edit), call getState/getCity with selected values
    document.addEventListener('DOMContentLoaded', function() {
        if (vendorPreset && vendorPreset.exists) {
            setTimeout(() => {
                if (vendorPreset.country_id) {
                    getState(vendorPreset.country_id, vendorPreset.state_id);
                    if (vendorPreset.state_id) {
                        getCity(vendorPreset.state_id, vendorPreset.city_id);
                    }
                }
            }, 500);
        }
    });

    // Global function to initialize vendor form after AJAX load
    window.vendorFormInit = function(root) {
        if (root) {
            window.currentVendorOffcanvas = root;
        }
        // Recompute preset each time we init
        vendorPreset = getVendorPresetFromDOM();
        if (vendorPreset && vendorPreset.exists) {
            setTimeout(() => {
                if (vendorPreset.country_id) {
                    getState(vendorPreset.country_id, vendorPreset.state_id);
                    if (vendorPreset.state_id) {
                        getCity(vendorPreset.state_id, vendorPreset.city_id);
                    }
                }
            }, 300);
        }
    };

    function triggerVendorFile(btn) {
        var container = btn && btn.closest ? btn.closest('.image-upload-container') : null;
        var input = container ? container.querySelector('#vendorFileInput') : document.getElementById('vendorFileInput');
        if (input && typeof input.click === 'function') input.click();
    }

    function previewVendorImage(input) {
        const file = input.files && input.files[0] ? input.files[0] : null;
        const container = input.closest('.image-upload-container') || document;
        const preview = container.querySelector('#vendorImagePreview');
        const removeBtn = container.querySelector('#removeVendorImageBtn');
        const removeFlag = container.querySelector('#remove_profile_image');
        const imageError = container.querySelector('#image-error');

        if (!file || !preview) return;

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            if (imageError) {
                imageError.textContent = '{{ __('messages.only_jpeg_jpg_and_png_files_are_allowed') }}';
                imageError.style.display = 'block';
                imageError.className = 'text-danger';
            }
            input.value = ''; // Clear the input
            return;
        }

        // Clear any previous error
        if (imageError) imageError.textContent = '';

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            if (removeBtn) removeBtn.disabled = false;
            if (removeFlag) removeFlag.value = '0';
        };
        reader.readAsDataURL(file);
    }

    function removeVendorImage(btn) {
        const container = (btn && btn.closest && btn.closest('.image-upload-container')) || document;
        const preview = container.querySelector('#vendorImagePreview');
        const fileInput = container.querySelector('#vendorFileInput');
        const removeFlag = container.querySelector('#remove_profile_image');
        const removeBtn = container.querySelector('#removeVendorImageBtn');
        if (preview) preview.src = "{{ asset('img/avatar/avatar.webp') }}";
        if (fileInput) fileInput.value = '';
        if (removeFlag) removeFlag.value = '1';
        if (removeBtn) removeBtn.disabled = true;
    }
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!(form && form.classList && form.classList.contains('vendor-form'))) return;
        e.preventDefault();

        const offcanvas = form.querySelector('#form-offcanvas') || form.querySelector('.offcanvas');

        // Client-side validation before AJAX submission
        let isValid = true;

        // Get form elements for validation
        const firstNameInput = form.querySelector('input[name="first_name"]');
        const lastNameInput = form.querySelector('input[name="last_name"]');
        const genderSelect = form.querySelector('select[name="gender"]');
        const addressInput = form.querySelector('input[name="address"]');
        const countrySelect = form.querySelector('select[name="country"]');
        const stateSelect = form.querySelector('select[name="state"]');
        const citySelect = form.querySelector('select[name="city"]');
        const pincodeInput = form.querySelector('input[name="pincode"]');
        const passwordInput = form.querySelector('input[name="password"]');
        const confirmInput = form.querySelector('input[name="confirm_password"]');
        const emailInput = form.querySelector('input[name="email"]');
        const mobileInput = form.querySelector('#mobileInput');
        const hiddenMobile = form.querySelector('#hiddenMobile');

        // Validation helper functions
        function showFieldError(field, message) {
            // Create/find error element
            let errorElement = field.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('text-danger')) {
                errorElement = document.createElement('span');
                errorElement.className = 'text-danger';
            }

            errorElement.textContent = message;

            // If field is enhanced by Select2, append error after the rendered container
            const select2Container = field.parentNode.querySelector('.select2');
            if (field.tagName === 'SELECT' && select2Container) {
                select2Container.parentNode.insertBefore(errorElement, select2Container.nextSibling);
            } else {
                field.parentNode.insertBefore(errorElement, field.nextSibling);
            }
        }

        function validateRequired(field, fieldName) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
                return false;
            }
            return true;
        }

        function validateName(field, fieldName) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
                return false;
            }
            if (value.length < 2) {
                showFieldError(field, `${fieldName} {{ __('messages.must_be_at_least_2_characters_long') }}`);
                return false;
            }
            // if (!/^[a-zA-Z\s]+$/.test(value)) {
            //     showFieldError(field, `${fieldName} can only contain letters and spaces`);
            //     return false;
            // }
            return true;
        }

        function validateAddress(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.address_is_required') }}');
                return false;
            }
            return true;
        }

        function validatePincode(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.pincode_is_required') }}');
                return false;
            }
            if (!/^\d{4,8}$/.test(value)) {
                showFieldError(field, '{{ __('messages.pincode_must_be_4_8_digits') }}');
                return false;
            }
            return true;
        }

        function validateEmail(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.email_is_required') }}');
                return false;
            }
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                showFieldError(field, '{{ __('messages.invalid_email_format') }}');
                return false;
            }
            return true;
        }

        function validateMobile(field) {
            const value = field.value.trim();
            if (value === '') {
                showMobileFieldError(field, '{{ __('messages.mobile_number_is_required') }}');
                return false;
            }
            return true;
        }

        function showMobileFieldError(field, message) {
            // For mobile input, we need to find the intlTelInput container and place error after it
            const mobileContainer = field.closest('.form-group');
            if (!mobileContainer) return;

            // Remove existing mobile error
            const existingError = mobileContainer.querySelector('#mobile-error');
            if (existingError) {
                existingError.textContent = message;
            } else {
                // Create error span after the hidden mobile input
                const hiddenMobile = mobileContainer.querySelector('#hiddenMobile');
                if (hiddenMobile) {
                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'text-danger';
                    errorSpan.id = 'mobile-error';
                    errorSpan.textContent = message;
                    hiddenMobile.parentNode.insertBefore(errorSpan, hiddenMobile.nextSibling);
                }
            }
        }

        function validatePassword(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.password_is_required') }}');
                return false;
            }
            const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z0-9@!%*#?&]{8,14}$/;
            if (!regex.test(value)) {
                showFieldError(field,
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}'
                );
                return false;
            }
            return true;
        }

        function validateConfirmPassword(field, password) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.confirm_password_is_required') }}');
                return false;
            }
            if (value !== password) {
                showFieldError(field, '{{ __('messages.passwords_do_not_match') }}');
                return false;
            }
            return true;
        }

        function clearFieldError(field) {
            const errorElement = field.nextElementSibling;
            if (errorElement && errorElement.classList.contains('text-danger')) {
                errorElement.remove();
            }
        }

        // Live validation for password and confirm password fields
        if (passwordInput) {
            const runPasswordValidation = () => {
                if (validatePassword(passwordInput)) {
                    clearFieldError(passwordInput);
                }
                if (confirmInput && confirmInput.value.trim() !== '') {
                    if (validateConfirmPassword(confirmInput, passwordInput.value.trim())) {
                        clearFieldError(confirmInput);
                    }
                }
            };
            passwordInput.addEventListener('input', runPasswordValidation);
            passwordInput.addEventListener('blur', runPasswordValidation);
        }

        if (confirmInput) {
            const runConfirmValidation = () => {
                if (validateConfirmPassword(confirmInput, (passwordInput ? passwordInput.value.trim() :
                        ''))) {
                    clearFieldError(confirmInput);
                }
            };
            confirmInput.addEventListener('input', runConfirmValidation);
            confirmInput.addEventListener('blur', runConfirmValidation);
        }

        // Clear previous errors
        form.querySelectorAll('.text-danger').forEach(el => {
            if (el.id === 'email-error' || el.id === 'mobile-error') {
                el.textContent = '';
            } else if (el.closest('label')) {
                // Keep asterisks inside labels
                return;
            } else {
                el.remove();
            }
        });

        // Validate all required fields
        if (firstNameInput && !validateName(firstNameInput, '{{ __('messages.first_name_is') }}')) {
            isValid = false;
        }
        if (lastNameInput && !validateName(lastNameInput, '{{ __('messages.last_name_is') }}')) {
            isValid = false;
        }
        if (emailInput && !validateEmail(emailInput)) {
            isValid = false;
        }
        if (genderSelect && !validateRequired(genderSelect, '{{ __('messages.gender_is') }}')) {
            isValid = false;
        }
        if (mobileInput && !validateMobile(mobileInput)) {
            isValid = false;
        }
        if (addressInput && !validateAddress(addressInput)) {
            isValid = false;
        }
        if (countrySelect && !validateRequired(countrySelect, '{{ __('messages.country_is') }}')) {
            isValid = false;
        }
        if (stateSelect && !validateRequired(stateSelect, '{{ __('messages.state_is') }}')) {
            isValid = false;
        }
        if (citySelect && !validateRequired(citySelect, '{{ __('messages.city_is') }}')) {
            isValid = false;
        }
        if (pincodeInput && !validatePincode(pincodeInput)) {
            isValid = false;
        }

        // Validate password fields only for new vendors
        if (passwordInput) {
            if (!validatePassword(passwordInput)) {
                isValid = false;
            }
            if (confirmInput && !validateConfirmPassword(confirmInput, passwordInput.value.trim())) {
                isValid = false;
            }
        }

        if (!isValid) {
            // Scroll to first error
            const firstError = form.querySelector('.text-danger');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            return;
        }

        // Update hidden mobile field before submission
        if (mobileInput && hiddenMobile) {
            const iti = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(mobileInput);
            if (iti) {
                let dialCode = iti.getSelectedCountryData().dialCode;
                let nationalNumber = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\D/g, '');

                // Remove leading zero for India
                if (nationalNumber.charAt(0) === '0') {
                    nationalNumber = nationalNumber.substring(1);
                }

                // Remove country code from national number if present (for any country)
                if (nationalNumber.startsWith(dialCode)) {
                    nationalNumber = nationalNumber.substring(dialCode.length);
                }

                // Ensure only one country code, always starts with '+'
                dialCode = dialCode.replace(/^\+/, '');
                const formattedDialCode = `+${dialCode}`;

                hiddenMobile.value = `${formattedDialCode} ${nationalNumber}`;
            }
        }

        const formData = new FormData(form);
        const action = form.action;
        const method = form.method.toUpperCase();

        // Set spinner only after validation passes
        const submitBtn = offcanvas ? offcanvas.querySelector('.offcanvas-footer button[type="submit"]') : null;
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            var currentText = submitBtn && submitBtn.textContent ? submitBtn.textContent.trim() : '';
            var fallbackText = '{{ __('messages.save') }}';
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>' + (currentText || fallbackText);
        }

        fetch(action, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(async response => {
                if (response.ok) {
                    if (offcanvas) {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas) || new bootstrap
                            .Offcanvas(offcanvas);
                        if (bsOffcanvas) bsOffcanvas.hide();
                    }
                    // Success snackbar
                    try {
                        var msg = (vendorPreset && vendorPreset.exists) ?
                            '{{ __('multivendor.update_vendor') }}' :
                            '{{ __('multivendor.new_vendor') }}';
                        if (typeof window.successSnackbar === 'function') {
                            window.successSnackbar(msg);
                        } else {
                            alert(msg);
                        }
                    } catch (_) {}
                    if (typeof window.reloadDatatable === 'function') window.reloadDatatable();
                } else if (response.status === 422) {
                    const data = await response.json();
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                let next = input.nextElementSibling;
                                if (next && next.classList.contains('text-danger')) next
                                    .remove();
                                const errorSpan = document.createElement('span');
                                errorSpan.className = 'text-danger';
                                errorSpan.textContent = messages[0];
                                const select2Container = input.parentNode.querySelector(
                                    '.select2');
                                if (input.tagName === 'SELECT' && select2Container) {
                                    select2Container.parentNode.insertBefore(errorSpan,
                                        select2Container.nextSibling);
                                } else {
                                    input.parentNode.insertBefore(errorSpan, input.nextSibling);
                                }
                            }
                        });
                    }
                } else {
                    alert('An error occurred. Please try again.');
                }
            })
            .catch(() => {
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });
    });

    // Remove server-side error instantly on user input/change (delegated)
    document.addEventListener('input', function(ev) {
        const el = ev.target;
        if (!el) return;

        // Handle mobile input specifically
        if (el.id === 'mobileInput') {
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }
            return;
        }

        if (!el.name) return;
        let next = el.nextElementSibling;
        if (next && next.classList && next.classList.contains('text-danger')) {
            if (next.id === 'email-error' || next.id === 'mobile-error') {
                next.textContent = '';
            } else if (next.closest('label')) {
                // Keep asterisks inside labels
                return;
            } else {
                next.remove();
            }
        }
    });
    document.addEventListener('change', function(ev) {
        const el = ev.target;
        if (!el) return;

        // Handle mobile input specifically
        if (el.id === 'mobileInput') {
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }
            return;
        }

        if (!el.name) return;
        let next = el.nextElementSibling;
        if (next && next.classList && next.classList.contains('text-danger')) {
            if (next.id === 'email-error' || next.id === 'mobile-error') {
                next.textContent = '';
            } else if (next.closest('label')) {
                // Keep asterisks inside labels
                return;
            } else {
                next.remove();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var root = document.querySelector('#form-offcanvas');
        if (root) {
            window.currentVendorOffcanvas = root;
        }
        // Initialize Flatpickr date picker
        var dateEl = root ? root.querySelector('#datepicker') : null;
        if (dateEl && window.flatpickr) {
            window.flatpickr(dateEl, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                animate: true,
                allowInput: true,
                monthSelectorType: "static",
                yearSelectorType: "static",
                showMonths: 1,
                disableMobile: false,
                appendTo: root
            });
        }

        const emailError = document.getElementById('email-error');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const passwordError = document.getElementById('password-error');
        const confirmError = document.getElementById('confirm-password-error');

        // Get form elements for validation
        const firstNameInput = document.querySelector('input[name="first_name"]');
        const lastNameInput = document.querySelector('input[name="last_name"]');
        const genderSelect = document.getElementById('genderSelect');
        const addressInput = document.querySelector('input[name="address"]');
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');
        const pincodeInput = document.querySelector('input[name="pincode"]');

        // Validation helper functions
        function showFieldError(field, message) {
            let errorElement = field.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('text-danger')) {
                errorElement = document.createElement('span');
                errorElement.className = 'text-danger';
                field.parentNode.insertBefore(errorElement, field.nextSibling);
            }
            errorElement.textContent = message;
        }

        function clearFieldError(field) {
            let errorElement = field.nextElementSibling;
            if (errorElement && errorElement.classList.contains('text-danger')) {
                errorElement.textContent = '';
            }
        }

        function validateRequired(field, fieldName) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
                return false;
            }
            clearFieldError(field);
            return true;
        }

        function validateName(field, fieldName) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
                return false;
            }
            if (value.length < 2) {
                showFieldError(field, `${fieldName} {{ __('messages.must_be_at_least_2_characters_long') }}`);
                return false;
            }
            // if (!/^[a-zA-Z\s]+$/.test(value)) {
            //     showFieldError(field, `${fieldName} can only contain letters and spaces`);
            //     return false;
            // }
            clearFieldError(field);
            return true;
        }

        function validateAddress(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.address_is_required') }}');
                return false;
            }
            // if (value.length < 10) {
            //     showFieldError(field, 'Address must be at least 10 characters long');
            //     return false;
            // }
            clearFieldError(field);
            return true;
        }

        function validatePincode(field) {
            const value = field.value.trim();
            if (value === '') {
                showFieldError(field, '{{ __('messages.pincode_is_required') }}');
                return false;
            }
            if (!/^\d{4,8}$/.test(value)) {
                showFieldError(field, '{{ __('messages.pincode_must_be_4_8_digits') }}');
                return false;
            }
            clearFieldError(field);
            return true;
        }


        // Mobile validation
        const mobileError = document.getElementById('mobile-error');
        document.addEventListener('focusout', function(ev) {
            const target = ev.target;
            if (!target || target.id !== 'mobileInput') return;

            // Get the complete mobile number with country code from hidden field
            const hiddenMobile = document.getElementById('hiddenMobile');
            const mobile = (hiddenMobile.value || '').trim();

            if (mobile === '') {
                if (mobileError) mobileError.textContent = '';
                return;
            }
            if (mobileError) mobileError.textContent = '';
            const params = new URLSearchParams();
            params.append('mobile', mobile);
            if (vendorPreset && vendorPreset.id) {
                params.append('exclude_id', vendorPreset.id);
            }
            fetch(`{{ route('backend.multivendors.check_mobile') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    if (data && data.available === false) {
                        if (mobileError) mobileError.textContent = data.message ||
                            'Mobile number already exists.';
                    } else {
                        if (mobileError) mobileError.textContent = '';
                    }
                })
                .catch(() => {
                    /* ignore network errors for UX */
                });
        });

        // First Name validation
        if (firstNameInput) {
            firstNameInput.addEventListener('blur', function() {
                validateName(this, 'First Name');
            });
            firstNameInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    validateName(this, 'First Name');
                }
            });
        }

        // Last Name validation
        if (lastNameInput) {
            lastNameInput.addEventListener('blur', function() {
                validateName(this, 'Last Name');
            });
            lastNameInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    validateName(this, 'Last Name');
                }
            });
        }

        // Address validation
        if (addressInput) {
            addressInput.addEventListener('blur', function() {
                validateAddress(this);
            });
            addressInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    validateAddress(this);
                }
            });
        }

        // Gender validation
        if (genderSelect) {
            genderSelect.addEventListener('change', function() {
                validateRequired(this, 'Gender');
            });
        }

        // Country validation
        if (countrySelect) {
            countrySelect.addEventListener('change', function() {
                validateRequired(this, 'Country');
            });
        }

        // State validation
        if (stateSelect) {
            stateSelect.addEventListener('change', function() {
                validateRequired(this, 'State');
            });
        }

        // City validation
        if (citySelect) {
            citySelect.addEventListener('change', function() {
                validateRequired(this, 'City');
            });
        }

        // Pincode validation
        if (pincodeInput) {
            pincodeInput.addEventListener('blur', function() {
                validatePincode(this);
            });
            pincodeInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    validatePincode(this);
                }
            });
        }

        // Enhanced password validation
        passwordInput.addEventListener('blur', function() {
            const password = passwordInput.value.trim();
            let errorMessage = '';

            if (password.length < 8) {
                errorMessage = '{{ __('messages.password_must_be_at_least_8_characters_long') }}';
            } else if (!/[A-Z]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (!/[a-z]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (!/\d/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (!/[@$!%*#?&]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_at_least_1_special_character_at_dollar_bang_percent_star_hash_question_and_ampersand') }}';
            }

            passwordError.textContent = errorMessage;

            // Confirm password check
            if (confirmInput.value.trim() !== '' && confirmInput.value !== password) {
                confirmError.textContent = '{{ __('messages.passwords_do_not_match') }}';
            } else {
                confirmError.textContent = '';
            }
        });

        // Real-time password validation on input
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value.trim();
            let errorMessage = '';

            if (password.length > 0 && password.length < 8) {
                errorMessage = '{{ __('messages.password_must_be_at_least_8_characters_long') }}';
            } else if (password.length >= 8 && !/[A-Z]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (password.length >= 8 && /[A-Z]/.test(password) && !/[a-z]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (password.length >= 8 && /[A-Z]/.test(password) && /[a-z]/.test(password) && !/\d/
                .test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            } else if (password.length >= 8 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/
                .test(password) && !/[@$!%*#?&]/.test(password)) {
                errorMessage =
                    '{{ __('messages.password_must_contain_1_capital_letter_1_small_letter_1_special_character_and_1_number') }}';
            }

            passwordError.textContent = errorMessage;

            // Real-time confirm password check
            if (confirmInput.value.trim() !== '' && confirmInput.value !== password) {
                confirmError.textContent = '{{ __('messages.passwords_do_not_match') }}';
            } else {
                confirmError.textContent = '';
            }
        });

        confirmInput.addEventListener('blur', function() {
            const password = passwordInput.value.trim();
            const confirmPassword = confirmInput.value.trim();
            if (confirmPassword !== password) {
                confirmError.textContent = '{{ __('messages.passwords_do_not_match') }}';
            } else {
                confirmError.textContent = '';
            }
        });
        var mobileInput = root ? root.querySelector("#mobileInput") : null;
        var hiddenMobile = root ? root.querySelector("#hiddenMobile") : null;
        var iti = null;
        if (mobileInput && window.intlTelInput) {
            var existingIti = (window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance) ? window
                .intlTelInputGlobals.getInstance(mobileInput) : null;
            if (!existingIti && !mobileInput.dataset.itiInited) {
                iti = window.intlTelInput(mobileInput, {
                    initialCountry: "gb",
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
                });
                mobileInput.dataset.itiInited = '1';
                if (vendorPreset && vendorPreset.mobile) {
                    iti.setNumber(vendorPreset.mobile);
                }
            }
        }

        function updateHiddenMobile() {
            if (!hiddenMobile || !iti) return;
            hiddenMobile.value = iti.getNumber();
        }
        if (mobileInput) mobileInput.addEventListener("input", function() {
            const cursorPos = mobileInput.selectionStart;
            mobileInput.value = mobileInput.value.replace(/\D/g, '');
            mobileInput.setSelectionRange(cursorPos, cursorPos);
            updateHiddenMobile();
            // Immediately clear mobile validation error if present
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }
        });

        // Form submission validation - this will be handled by the main AJAX handler
        if (mobileInput) mobileInput.addEventListener("countrychange", function() {
            // Clear raw input so old dial code doesn't overlap with new one
            mobileInput.value = '';
            updateHiddenMobile();
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }

            // Validate mobile number with new country code
            const hidden = root ? root.querySelector('#hiddenMobile') : null;
            if (mobileError && hidden && hidden.value.trim() !== '') {
                const params = new URLSearchParams();
                params.append('mobile', hidden.value.trim());
                if (vendorPreset && vendorPreset.id) {
                    params.append('exclude_id', vendorPreset.id);
                }
                fetch(`{{ route('backend.multivendors.check_mobile') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.ok ? r.json() : Promise.reject())
                    .then(data => {
                        if (data && data.available === false) {
                            mobileError.textContent = data.message ||
                                '{{ __('messages.mobile_number_already_exists') }}';
                        } else {
                            mobileError.textContent = '';
                        }
                    })
                    .catch(() => {
                        /* ignore network errors for UX */
                    });
            }
        });
        updateHiddenMobile();
        var formEl = root ? root.closest('form') : null;
        if (formEl) {
            formEl.addEventListener("submit", function() {
                updateHiddenMobile();
            });
        }
    });


    function initVendorFormPlugins(root) {
        if (!root) return;
        // Flatpickr init
        var dateEl = root.querySelector('#datepicker');
        if (dateEl && window.flatpickr) {
            window.flatpickr(dateEl, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                animate: true,
                allowInput: true,
                monthSelectorType: "static",
                yearSelectorType: "static",
                showMonths: 1,
                disableMobile: false
            });
        }

        // intlTelInput init with language support
        var mobileInput = root.querySelector("#mobileInput");
        var hiddenMobile = root.querySelector("#hiddenMobile");

        function initTelInput() {
            if (mobileInput && window.intlTelInput) {
                // Destroy existing instance if it exists
                if (mobileInput.dataset.itiInited) {
                    var existingIti = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(mobileInput);
                    if (existingIti) {
                        existingIti.destroy();
                    }
                }

                // Detect current language and set appropriate country
                var currentLang = document.documentElement.lang || 'en';
                var initialCountry = 'gb'; // Default to UK for all users

                var iti = window.intlTelInput(mobileInput, {
                    initialCountry: initialCountry,
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
                    preferredCountries: currentLang === 'ar' ? ['sa', 'ae', 'eg', 'jo', 'kw', 'qa', 'bh',
                        'om'
                    ] : ['in', 'us', 'gb', 'au', 'ca'],
                    autoPlaceholder: 'aggressive',
                    nationalMode: false,
                    formatOnDisplay: true
                });

                mobileInput.dataset.itiInited = '1';

                if (vendorPreset && vendorPreset.mobile) {
                    iti.setNumber(vendorPreset.mobile);
                }

                function updateHiddenMobileInit() {
                    if (!hiddenMobile) return;
                    hiddenMobile.value = iti.getNumber();
                }

                mobileInput.addEventListener("input", function() {
                    const cursorPos = mobileInput.selectionStart;
                    mobileInput.value = mobileInput.value.replace(/\D/g, '');
                    mobileInput.setSelectionRange(cursorPos, cursorPos);
                    updateHiddenMobileInit();
                    // Clear mobile validation error
                    const mobileError = document.getElementById('mobile-error');
                    if (mobileError) {
                        mobileError.textContent = '';
                    }
                });

                mobileInput.addEventListener("countrychange", function() {
                    updateHiddenMobileInit();
                    // Clear mobile validation error
                    const mobileError = document.getElementById('mobile-error');
                    if (mobileError) {
                        mobileError.textContent = '';
                    }
                });
                updateHiddenMobileInit();

                // Add RTL support for Arabic
                if (currentLang === 'ar') {
                    setTimeout(function() {
                        var flagContainer = mobileInput.parentElement.querySelector('.iti__flag-container');
                        if (flagContainer) {
                            flagContainer.style.direction = 'ltr';
                        }
                        var countryList = mobileInput.parentElement.querySelector('.iti__country-list');
                        if (countryList) {
                            countryList.style.direction = 'ltr';
                        }
                    }, 100);
                }
            }
        }

        // Initialize tel input
        initTelInput();
    }

    // Client-side validation functions (work with dynamically loaded content)
    function showFieldError(field, message) {
        let errorElement = field.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('text-danger')) {
            errorElement = document.createElement('span');
            errorElement.className = 'text-danger';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
        errorElement.textContent = message;
    }

    function clearFieldError(field) {
        let errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('text-danger')) {
            errorElement.textContent = '';
        }
    }

    function validateRequired(field, fieldName) {
        const value = field.value.trim();
        if (value === '') {
            showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validateName(field, fieldName) {
        const value = field.value.trim();
        if (value === '') {
            showFieldError(field, `${fieldName} {{ __('messages.is_required') }}`);
            return false;
        }
        if (value.length < 2) {
            showFieldError(field, `${fieldName} {{ __('messages.must_be_at_least_2_characters_long') }}`);
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validateAddress(field) {
        const value = field.value.trim();
        if (value === '') {
            showFieldError(field, '{{ __('messages.address_is_required') }}');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validatePincode(field) {
        const value = field.value.trim();
        if (value === '') {
            showFieldError(field, '{{ __('messages.pincode_is_required') }}');
            return false;
        }
        if (!/^\d{4,8}$/.test(value)) {
            showFieldError(field, '{{ __('messages.pincode_must_be_4_8_digits') }}');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validateEmail(field) {
        const value = field.value.trim();
        if (value === '') {
            showFieldError(field, '{{ __('messages.email_is_required') }}');
            return false;
        }
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(value)) {
            showFieldError(field, '{{ __('messages.invalid_email_format') }}');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function validateMobile(field) {
        const value = field.value.trim();
        if (value === '') {
            showMobileFieldError(field, '{{ __('messages.mobile_number_is_required') }}');
            return false;
        }
        return true;
    }

    function showMobileFieldError(field, message) {
        const mobileContainer = field.closest('.form-group');
        if (!mobileContainer) return;

        const existingError = mobileContainer.querySelector('#mobile-error');
        if (existingError) {
            existingError.textContent = message;
        } else {
            const hiddenMobile = mobileContainer.querySelector('#hiddenMobile');
            if (hiddenMobile) {
                const errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger';
                errorSpan.id = 'mobile-error';
                errorSpan.textContent = message;
                hiddenMobile.parentNode.insertBefore(errorSpan, hiddenMobile.nextSibling);
            }
        }
    }

    // Email validation - Real-time validation on input
    document.addEventListener('input', function(ev) {
        const target = ev.target;
        if (!target || target.id !== 'email') return;

        const emailErrorElement = document.getElementById('email-error');
        if (!emailErrorElement) return;

        const email = (target.value || '').trim();
        if (email === '') {
            emailErrorElement.textContent = '';
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            emailErrorElement.textContent = '{{ __('messages.invalid_email_format') }}';
            return;
        }

        emailErrorElement.textContent = '';
    });

    // Email validation - Check uniqueness on focusout
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.id !== 'email') return;

        const emailErrorElement = document.getElementById('email-error');
        if (!emailErrorElement) return;

        const email = (target.value || '').trim();
        if (email === '') {
            emailErrorElement.textContent = '';
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            emailErrorElement.textContent = '{{ __('messages.invalid_email_format') }}';
            return;
        }

        emailErrorElement.textContent = '';

        const currentVendorPreset = getVendorPresetFromDOM();
        const params = new URLSearchParams();
        params.append('email', email);
        if (currentVendorPreset && currentVendorPreset.id) {
            params.append('exclude_id', currentVendorPreset.id);
        }

        fetch(`{{ route('backend.multivendors.check_email') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                if (data && data.available === false) {
                    emailErrorElement.textContent = data.message ||
                        '{{ __('messages.this_email_is_already_taken') }}';
                } else {
                    emailErrorElement.textContent = '';
                }
            })
            .catch(() => {
                console.log('Email validation check failed - network error');
            });
    });

    // First Name validation
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'first_name') return;
        validateName(target, 'First Name');
    });

    document.addEventListener('input', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'first_name') return;
        if (target.value.trim() !== '') {
            validateName(target, 'First Name');
        }
    });

    // Last Name validation
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'last_name') return;
        validateName(target, 'Last Name');
    });

    document.addEventListener('input', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'last_name') return;
        if (target.value.trim() !== '') {
            validateName(target, 'Last Name');
        }
    });

    // Address validation
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'address') return;
        validateAddress(target);
    });

    document.addEventListener('input', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'address') return;
        if (target.value.trim() !== '') {
            validateAddress(target);
        }
    });

    // Pincode validation
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'pincode') return;
        validatePincode(target);
    });

    document.addEventListener('input', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'pincode') return;
        if (target.value.trim() !== '') {
            validatePincode(target);
        }
    });

    // Gender validation
    document.addEventListener('change', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'gender') return;
        validateRequired(target, 'Gender');
    });

    // Country validation
    document.addEventListener('change', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'country') return;
        validateRequired(target, 'Country');
    });

    // State validation
    document.addEventListener('change', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'state') return;
        validateRequired(target, 'State');
    });

    // City validation
    document.addEventListener('change', function(ev) {
        const target = ev.target;
        if (!target || target.name !== 'city') return;
        validateRequired(target, 'City');
    });

    // Mobile validation
    document.addEventListener('focusout', function(ev) {
        const target = ev.target;
        if (!target || target.id !== 'mobileInput') return;

        const mobileError = document.getElementById('mobile-error');
        if (!mobileError) return;

        // Get the full phone number from the intl-tel-input
        const iti = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(target);
        let mobile = '';

        if (iti) {
            mobile = iti.getNumber();
        } else {
            mobile = target.value.trim();
        }

        console.log('Validating mobile:', mobile);

        if (mobile === '' || mobile === '+') {
            if (mobileError) mobileError.textContent = '';
            return;
        }

        if (mobileError) mobileError.textContent = '';

        const params = new URLSearchParams();
        params.append('mobile', mobile);
        const currentVendorPreset = getVendorPresetFromDOM();
        if (currentVendorPreset && currentVendorPreset.id) {
            params.append('exclude_id', currentVendorPreset.id);
        }

        console.log('Checking mobile:', mobile, 'Exclude ID:', currentVendorPreset?.id);

        fetch(`{{ route('backend.multivendors.check_mobile') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                console.log('Phone validation response:', data);
                if (data && data.available === false) {
                    if (mobileError) {
                        mobileError.textContent = data.message || 'Phone Number Exists';
                        mobileError.style.color = 'red';
                        mobileError.style.display = 'block';
                    }
                } else {
                    if (mobileError) {
                        mobileError.textContent = '';
                        mobileError.style.display = 'none';
                    }
                }
            })
            .catch((error) => {
                console.log('Phone validation error:', error);
            });
    });

    // jQuery version for better compatibility
    document.addEventListener('DOMContentLoaded', function() {
        const mobileInput = document.getElementById('mobileInput');
        if (!mobileInput) return;

        mobileInput.addEventListener('focusout', function() {
            const mobileError = document.getElementById('mobile-error');
            if (!mobileError) return;

            const target = this;

            // Get the full phone number from intl-tel-input
            let mobile = '';
            const iti = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(target);
            if (iti) {
                mobile = iti.getNumber();
            } else {
                mobile = target.value.trim();
            }

            console.log('Validating mobile:', mobile);

            if (mobile === '' || mobile === '+') {
                mobileError.textContent = '';
                return;
            }

            mobileError.textContent = '';

            const params = new URLSearchParams();
            params.append('mobile', mobile);

            const currentVendorPreset = getVendorPresetFromDOM();
            if (currentVendorPreset && currentVendorPreset.id) {
                params.append('exclude_id', currentVendorPreset.id);
            }

            console.log('Checking mobile:', mobile, 'Exclude ID:', currentVendorPreset?.id);

            // Fetch all existing numbers (for debugging)
            fetch('{{ route('backend.multivendors.get_all_mobiles') }}')
                .then(res => res.json())
                .then(data => {
                    console.log('All existing phone numbers:', data.mobiles);
                });

            // Check if number exists
            fetch(`{{ route('backend.multivendors.check_mobile') }}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Phone validation response:', data);

                    if (data && data.available === false) {
                        console.log('Phone number EXISTS - showing error');
                        mobileError.textContent = data.message || 'Phone Number Exists';
                        mobileError.style.color = 'red';
                        mobileError.style.display = 'block';
                    } else {
                        console.log('Phone number AVAILABLE - clearing error');
                        mobileError.textContent = '';
                        mobileError.style.display = 'none';
                    }
                })
                .catch(err => console.error('Phone validation error:', err));
        });
    });

    // Clear errors on input/change
    document.addEventListener('input', function(ev) {
        const el = ev.target;
        if (!el) return;

        if (el.id === 'mobileInput') {
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }
            return;
        }
    });

    // jQuery version for clearing errors
    document.addEventListener('DOMContentLoaded', function() {
        const mobileInput = document.getElementById('mobileInput');
        const mobileError = document.getElementById('mobile-error');

        if (!mobileInput || !mobileError) return;

        mobileInput.addEventListener('input', function() {
            mobileError.textContent = '';
            mobileError.style.display = 'none';
        });
    });

    document.addEventListener('input', function(ev) {
        const el = ev.target;
        if (!el) return;

        if (!el.name) return;
        let next = el.nextElementSibling;
        if (next && next.classList && next.classList.contains('text-danger')) {
            if (next.id === 'email-error' || next.id === 'mobile-error') {
                next.textContent = '';
            } else if (next.closest('label')) {
                return;
            } else {
                next.remove();
            }
        }
    });

    document.addEventListener('change', function(ev) {
        const el = ev.target;
        if (!el) return;

        if (el.id === 'mobileInput') {
            const mobileError = document.getElementById('mobile-error');
            if (mobileError) {
                mobileError.textContent = '';
            }
            return;
        }

        if (!el.name) return;
        let next = el.nextElementSibling;
        if (next && next.classList && next.classList.contains('text-danger')) {
            if (next.id === 'email-error' || next.id === 'mobile-error') {
                next.textContent = '';
            } else if (next.closest('label')) {
                return;
            } else {
                next.remove();
            }
        }
    });

    // Call when DOM is ready (for initial load)
    document.addEventListener("DOMContentLoaded", function() {
        var root = document.querySelector('#form-offcanvas');
        if (root) initVendorFormPlugins(root);
    });

    // Re-initialize tel input when language changes
    document.addEventListener('languageChanged', function() {
        var root = document.querySelector('#form-offcanvas');
        if (root) {
            var mobileInput = root.querySelector("#mobileInput");
            if (mobileInput && window.intlTelInput) {
                // Destroy existing instance
                if (mobileInput.dataset.itiInited) {
                    var existingIti = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(
                        mobileInput);
                    if (existingIti) {
                        existingIti.destroy();
                    }
                    mobileInput.dataset.itiInited = '';
                }
                // Re-initialize
                initVendorFormPlugins(root);
            }
        }
    });

    // Also listen for offcanvas show to re-initialize tel input
    document.addEventListener('shown.bs.offcanvas', function(event) {
        if (event.target && event.target.id === 'form-offcanvas') {
            setTimeout(function() {
                var root = event.target;
                if (root) {
                    var mobileInput = root.querySelector("#mobileInput");
                    if (mobileInput && window.intlTelInput && !mobileInput.dataset.itiInited) {
                        initVendorFormPlugins(root);
                    }
                }
            }, 100);
        }
    });

    // Add CSS for RTL support
    var style = document.createElement('style');
    style.textContent = `
    /* RTL support for intlTelInput */
    [dir="rtl"] .iti__flag-container {
        direction: ltr !important;
    }
    [dir="rtl"] .iti__country-list {
        direction: ltr !important;
        text-align: left !important;
    }
    [dir="rtl"] .iti__country-name {
        text-align: left !important;
    }
    [dir="rtl"] .iti__dial-code {
        text-align: left !important;
    }
    /* Ensure tel input works properly in RTL */
    [dir="rtl"] .iti {
        direction: ltr !important;
    }
    [dir="rtl"] .iti__selected-flag {
        direction: ltr !important;
    }
`;
    document.head.appendChild(style);

    // Function to reset submit button state
    function resetSubmitButton(offcanvas) {
        if (!offcanvas) return;
        var submitBtn = offcanvas.querySelector('.offcanvas-footer button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            var fallbackText = '{{ __('messages.save') }}';
            submitBtn.innerHTML = fallbackText;
        }
    }

    // Call again when form is opened dynamically
    document.addEventListener("shown.bs.offcanvas", function(event) {
        if (event.target && event.target.id === "form-offcanvas") {
            window.currentVendorOffcanvas = event.target;
            // Reset button state when form is shown
            resetSubmitButton(event.target);
            // Recompute preset on show
            vendorPreset = getVendorPresetFromDOM();
            initVendorFormPlugins(event.target);
            // Ensure Select2 is initialized for new and edit
            if (typeof window.initSelect2Offcanvas === 'function') {
                window.initSelect2Offcanvas(event.target);
            }
        }
    });

    // Reset form when opening/closing create offcanvas so stale data doesn't persist
    function resetVendorForm(root) {
        if (!root) return;
        var form = root.closest('form') || document.querySelector('form.vendor-form');
        if (!form) return;

        // Reset submit button state first
        var submitBtn = root.querySelector('.offcanvas-footer button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            var fallbackText = '{{ __('messages.save') }}';
            submitBtn.innerHTML = fallbackText;
        }

        // Clear text, email, password, tel, hidden (except CSRF/ method)
        var inputs = form.querySelectorAll('input');
        inputs.forEach(function(input) {
            var keep = (input.type === 'hidden' && (input.name === '_token' || input.name === '_method'));
            if (keep) return;
            if (input.id === 'hiddenMobile') {
                input.value = '';
                return;
            }
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
                return;
            }
            input.value = '';
        });
        // Clear selects
        var selects = form.querySelectorAll('select');
        selects.forEach(function(sel) {
            sel.selectedIndex = 0;
            if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(sel).data('select2')) {
                window.jQuery(sel).val(null).trigger('change');
            }
        });
        // Ensure status is checked by default on create
        var statusToggle = form.querySelector('#clinic-status');
        var isEdit = root.getAttribute('data-vendor-exists') === '1';
        if (statusToggle && !isEdit) {
            statusToggle.checked = true;
        }
        // Clear error messages but keep required asterisks inside labels
        form.querySelectorAll('.text-danger').forEach(function(el) {
            if (el.id === 'email-error' || el.id === 'mobile-error') {
                el.textContent = '';
                return;
            }
            if (el.closest('label')) {
                return;
            }
            // Only remove error spans that are adjacent to form controls
            var prev = el.previousElementSibling;
            if (prev && (prev.tagName === 'INPUT' || prev.tagName === 'SELECT' || prev.tagName ===
                    'TEXTAREA')) {
                el.remove();
            }
        });
        // Reset image preview
        var preview = form.querySelector('#vendorImagePreview');
        var fileInput = form.querySelector('#vendorFileInput');
        var removeFlag = form.querySelector('#remove_profile_image');
        if (preview) preview.src = "{{ asset('img/avatar/avatar.webp') }}";
        if (fileInput) fileInput.value = '';
        if (removeFlag) removeFlag.value = '0';
        // Reset mobile input visual
        var mobile = form.querySelector('#mobileInput');
        if (mobile) {
            mobile.value = '';
        }
    }

    document.addEventListener('show.bs.offcanvas', function(event) {
        if (event.target && event.target.id === 'form-offcanvas') {
            // Always reset button state when form is opened
            resetSubmitButton(event.target);

            var isEdit = event.target.getAttribute('data-vendor-exists') === '1';
            if (!isEdit) {
                // Clear on open for create form
                resetVendorForm(event.target);
            }
        }
    });

    document.addEventListener('hidden.bs.offcanvas', function(event) {
        if (event.target && event.target.id === 'form-offcanvas') {
            var isEdit = event.target.getAttribute('data-vendor-exists') === '1';
            if (!isEdit) {
                // Clear on close so next open is clean
                resetVendorForm(event.target);
            }
        }
    });

    // Safe Select2 initializer scoped to this offcanvas
    window.initSelect2Offcanvas = function(root) {
        if (!(window.jQuery && window.jQuery.fn.select2)) {
            return;
        }
        var $parent = null;
        if (root) {
            $parent = window.jQuery(root);
        } else if (window.currentVendorOffcanvas) {
            $parent = window.jQuery(window.currentVendorOffcanvas);
        } else {
            $parent = window.jQuery('#form-offcanvas');
        }
        if (!$parent || !$parent.length) {
            return;
        }

        $parent.find('select').each(function() {
            var $el = window.jQuery(this);
            var selectId = $el.attr('id');

            // Special handling for gender select with placeholder
            if (selectId === 'genderSelect') {
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $parent,
                        placeholder: '{{ __('clinic.select_gender') }}',
                        minimumResultsForSearch: Infinity // Disable search for gender
                    });
                } else {
                    $el.select2('destroy');
                    $el.select2({
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $parent,
                        placeholder: '{{ __('clinic.select_gender') }}',
                        minimumResultsForSearch: Infinity
                    });
                }
            } else {
                // Default Select2 initialization for other selects
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $parent,
                        placeholder: function() {
                            return $el.find('option[disabled]').text() || 'Select an option';
                        }
                    });
                } else {
                    $el.select2('destroy');
                    $el.select2({
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $parent,
                        placeholder: function() {
                            return $el.find('option[disabled]').text() || 'Select an option';
                        }
                    });
                }
            }
        });
    };

    // Wire Select2 initializer on ready and when offcanvas is shown
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initSelect2Offcanvas === 'function') {
            window.initSelect2Offcanvas(document.querySelector('#form-offcanvas'));
        }
    });
    document.addEventListener('shown.bs.offcanvas', function(event) {
        if (event.target && event.target.id === 'form-offcanvas' && typeof window.initSelect2Offcanvas ===
            'function') {
            window.initSelect2Offcanvas(event.target);
        }
    });

    // Track current offcanvas for scoping
    window.currentVendorOffcanvas = window.currentVendorOffcanvas || null;
</script>
