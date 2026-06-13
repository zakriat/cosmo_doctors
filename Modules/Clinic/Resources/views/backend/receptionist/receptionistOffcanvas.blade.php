<form id="receptionist-form" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="form-offcanvasLabel">
                <span id="create-title">{{ __('receptionist.singular_title') }}</span>
                <span id="edit-title" class="d-none">{{ __('receptionist.singular_title') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body">
            <div class="row">
                <!-- Profile Image Section (Redesigned, both buttons always visible) -->
                <div class="col-md-6 create-service-image">
                    <label for="profile_image" class="form-label fw-medium">{{ __('receptionist.lbl_profile_image') }}</label>
                    <div class="d-flex flex-column align-items-center justify-content-center mb-2 gap-2">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 140px; height: 140px;">
                            <img id="profile-preview" src="{{ asset('img/avatar/avatar.webp') }}" alt="{{ __('clinic.profile_image') }}" class="w-100 h-100 object-fit-cover rounded-circle" />
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <label for="profile_image" class="btn btn-light border px-4 fw-semibold text-lowercase mb-0">
                                {{ __('messages.upload') ?: 'upload' }}
                                <input type="file" class="d-none" id="profile_image" name="profile_image" accept=".jpeg, .jpg, .png, .gif" onchange="previewImage(this)" />
                            </label>
                            <!-- <button type="button" class="btn btn-danger px-4" onclick="removeImage()" id="remove-btn" style="font-weight: 600; margin-bottom: 0;">
                                {{ __('messages.remove') ?: 'Remove' }}
                            </button> -->
                        </div>
                        <span class="text-danger">Only .jpeg, .jpg, .png files are allowed.</span>
                    </div>
                    <span class="text-danger" id="profile_image_error"></span>
                </div>

                <!-- First Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name" class="form-label">{{ __('receptionist.lbl_first_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="{{ __('receptionist.lbl_first_name') }}">
                        <span class="text-danger" id="first_name_error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">{{ __('receptionist.lbl_last_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="{{ __('receptionist.lbl_last_name') }}">
                        <span class="text-danger" id="last_name_error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">{{ __('receptionist.lbl_Email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('receptionist.lbl_Email') }}">
                        <span class="text-danger" id="email_error"></span>
                    </div>
                </div>

                <!-- Second Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="gender" class="form-label">{{ __('clinic.lbl_gender') }}</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="intersex">Intersex</option>
                        </select>
                        <span class="text-danger" id="gender_error"></span>
                    </div>
                </div>

                <!-- Third Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobile" class="form-label">{{ __('employee.lbl_phone_number') }}<span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="{{ __('employee.lbl_phone_number') }}">
                        <span class="text-danger" id="mobile_error"></span>
                    </div>
                </div>

                <!-- Password Fields (only for create) -->
                <div class="col-md-6" id="password-fields">
                    <div class="form-group">
                        <label for="password" class="form-label">{{ __('receptionist.lbl_password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('receptionist.lbl_password') }}">
                        <span class="text-danger" id="password_error"></span>
                    </div>
                </div>
                
                <div class="col-md-6" id="confirm-password-fields">
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">{{ __('receptionist.lbl_confirm_password') }} <span class="text-danger">*</span></label>
                        <input type="password" 
                        class="form-control" 
                        id="confirm_password" 
                        name="password_confirmation" 
                        placeholder="{{ __('receptionist.lbl_confirm_password') }}">

                        <span class="text-danger" id="confirm_password_error"></span>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">{{ __('customer.lbl_date_of_birth') }}</label>
                        <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="{{ __('customer.lbl_date_of_birth') }}" readonly>
                        <span class="text-danger" id="date_of_birth_error"></span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status" class="form-label">{{ __('receptionist.lbl_status') }}</label>
                        <div class="d-flex justify-content-between align-items-center form-control">
                            <label class="form-label m-0" for="status">{{ __('employee.lbl_status') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="1" name="status" id="status" type="checkbox" checked />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Details Section -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3">{{ __('receptionist.other_details') }}</h5>
                </div>

                <!-- Vendor Selection (if multi-vendor enabled) -->
                @if(multiVendor() && auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                 <div class="col-md-6">
                    <div class="form-group">
                        <label for="vendor_id" class="form-label">{{ __('receptionist.select_vendors') }}</label>
                        <select class="form-control" id="vendor_id" name="vendor_id" onchange="getClinic(this.value)">
                            <option value="">{{ __('receptionist.select_vendor_placeholder') ?: 'Select Vendor' }}</option>
                            @foreach($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="vendor_id_error"></span>
                    </div>
                </div>
                @endif

                <!-- Clinic Center -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="clinic_id" class="form-label">{{ __('receptionist.select_clinic_centre') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="clinic_id" name="clinic_id">
                            <option value="">{{ __('receptionist.select_clinic_center_placeholder') ?: 'Select Clinic Center' }}</option>
                            @if(isset($clinicCenters) && count($clinicCenters) > 0)
                                @foreach($clinicCenters as $clinic)
                                    <option value="{{ $clinic->id }}">{{ $clinic->name ?? __('clinic.lbl_name_not_available') }}</option>
                                @endforeach
                            @else
                                <option value="">{{ __('clinic.lbl_no_clinic_available') }}</option>
                            @endif
                        </select>
                        <span class="text-danger" id="clinic_id_error"></span>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address" class="form-label">{{ __('clinic.lbl_address') }}</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('clinic.lbl_address') }}">
                        <span class="text-danger" id="address_error"></span>
                    </div>
                </div>

                <!-- Country -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country" class="form-label">{{ __('clinic.lbl_country') }}</label>
                        <select class="form-control" id="country" name="country" onchange="getState(this.value, null, null)">
                            <option value="">Country</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="country_error"></span>
                    </div>
                </div>

                <!-- State -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="state" class="form-label">{{ __('clinic.lbl_state') }}</label>
                        <select class="form-control" id="state" name="state" onchange="getCity(this.value, null)">
                            <option value="">State</option>
                        </select>
                        <span class="text-danger" id="state_error"></span>
                    </div>
                </div>

                <!-- City -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="city" class="form-label">{{ __('clinic.lbl_city') }}</label>
                        <select class="form-control" id="city" name="city">
                            <option value="">City</option>
                        </select>
                        <span class="text-danger" id="city_error"></span>
                    </div>
                </div>

                <!-- Postal Code -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pincode" class="form-label">{{ __('receptionist.postal_code') }}</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="{{ __('clinic.lbl_postal_code') }}">
                        <span class="text-danger" id="pincode_error"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Footer -->
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</form>


<script>
let currentId = 0;
let isEditMode = false;

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing receptionist form');
    setupForm();
    setupEventListeners();
    initializeFlatpickr();
    @if(multiVendor() && auth()->user()->hasAnyRole(['admin', 'demo_admin']))
        getVendors();
    @endif
    console.log('Receptionist form initialization complete');
});

// Handle page visibility change to refresh data when user returns
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page is now visible, refresh datatable
        refreshDataTable();
    }
});

// Handle beforeunload to clear any pending operations
window.addEventListener('beforeunload', function() {
    clearAllErrors();
});

// Listen for centralized offcanvas edit/create event
document.addEventListener('crud_change_id', function(e) {
    const id = e.detail && e.detail.form_id ? Number(e.detail.form_id) : 0;
    const offcanvasEl = document.getElementById('form-offcanvas');
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    if (id && id > 0) {
        offcanvas.show();
        setEditMode(id);
    } else {
        setCreateMode();
    }
});

// Also listen for offcanvas show event to ensure form is ready
document.addEventListener('shown.bs.offcanvas', function(e) {
    if (e.target.id === 'form-offcanvas') {
        console.log('Offcanvas shown - ensuring form is ready');
        // Clear any previous errors and notifications
        clearAllErrors();
        // Re-setup event listeners in case they were lost
        setupEventListeners();
    }
});

function setupForm() {
    // Set default values
    document.getElementById('gender').value = 'male';
    document.getElementById('status').checked = true;
    
    // Show password fields for create mode by default
    togglePasswordFields(true);
}

function initializeFlatpickr() {
    // Initialize flatpickr for date of birth field
    flatpickr("#date_of_birth", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: true,
        clickOpens: true,
        placeholder: "{{ __('customer.lbl_date_of_birth') }}",
        locale: {
            firstDayOfWeek: 1 // Start week on Monday
        },
        onChange: function(selectedDates, dateStr, instance) {
            // Validate date when changed
            if (selectedDates.length > 0) {
                const selectedDate = selectedDates[0];
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate > today) {
                    showFieldError('date_of_birth', 'Date of birth cannot be in the future.');
                    instance.clear();
                } else {
                    // Clear any existing error
                    const errorElement = document.getElementById('date_of_birth_error');
                    if (errorElement) {
                        errorElement.textContent = '';
                    }
                    document.getElementById('date_of_birth').classList.remove('is-invalid');
                }
            }
        }
    });
}


function setupEventListeners() {
    const form = document.getElementById('receptionist-form');
    if (form) {
        console.log('Setting up form event listener');
        form.addEventListener('submit', handleFormSubmit);
        
        // Prevent HTML validation from showing
        form.addEventListener('invalid', function(e) {
            e.preventDefault();
        });
    } else {
        console.error('Form element not found!');
    }
    
    // Handle offcanvas hide
    const offcanvas = document.getElementById('form-offcanvas');
    if (offcanvas) {
        offcanvas.addEventListener('hidden.bs.offcanvas', function() {
            resetForm();
            clearAllErrors();  
        });
        
        // Also handle when offcanvas is being hidden (before it's completely hidden)
        offcanvas.addEventListener('hide.bs.offcanvas', function() {
            // Clear any ongoing operations
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = false;
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) {
                    spinner.classList.add('d-none');
                }
            }
        });
    }
    
    // Add real-time validation
    setupRealTimeValidation();
    
    // Disable HTML validation for all form inputs
    disableHTMLValidation();
}

function setupRealTimeValidation() {
    // Required fields validation on blur
    const requiredFields = ['first_name', 'last_name', 'email', 'mobile', 'clinic_id'];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                validateSingleField(fieldId);
            });
            
            field.addEventListener('input', function() {
                // Clear error styling when user starts typing
                if (field.classList.contains('is-invalid')) {
                    field.classList.remove('is-invalid');
                    const errorElement = document.getElementById(fieldId + '_error');
                    if (errorElement) {
                        errorElement.textContent = '';
                    }
                }
            });
        }
    });
    
    // Email validation on blur
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            validateEmailField();
        });
    }
    
    // Mobile validation on blur
    const mobileField = document.getElementById('mobile');
    if (mobileField) {
        mobileField.addEventListener('blur', function() {
            validateMobileField();
        });
    }
    
    // Password validation for create mode
    if (!isEditMode) {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        
        if (passwordField) {
            passwordField.addEventListener('blur', function() {
                validatePasswordField();
            });
        }
        
        if (confirmPasswordField) {
            confirmPasswordField.addEventListener('blur', function() {
                validateConfirmPasswordField();
            });
        }
    }
}

function disableHTMLValidation() {
    // Disable HTML validation for all form inputs
    const formInputs = document.querySelectorAll('#receptionist-form input, #receptionist-form select, #receptionist-form textarea');
    
    formInputs.forEach(input => {
        // Remove required attribute if it exists
        input.removeAttribute('required');
        
        // Add event listener to prevent HTML validation
        input.addEventListener('invalid', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        
        // Override the checkValidity method
        input.checkValidity = function() {
            return true; // Always return true to bypass HTML validation
        };
    });
}

function togglePasswordFields(show) {
    const passwordFields = document.getElementById('password-fields');
    const confirmPasswordFields = document.getElementById('confirm-password-fields');
    
    if (show) {
        passwordFields.style.display = 'block';
        confirmPasswordFields.style.display = 'block';
        document.getElementById('password').required = true;
        document.getElementById('confirm_password').required = true;
    } else {
        passwordFields.style.display = 'none';
        confirmPasswordFields.style.display = 'none';
        document.getElementById('password').required = false;
        document.getElementById('confirm_password').required = false;
    }
}

function setEditMode(id) {
    currentId = id;
    isEditMode = true;
    
    // Update UI
    document.getElementById('create-title').style.display = 'none';
    document.getElementById('edit-title').style.display = 'inline';
    
    // Hide password fields for edit mode
    togglePasswordFields(false);
    
    // Fetch data for editing
    fetchEditData(id);
}

function setCreateMode() {
    currentId = 0;
    isEditMode = false;
    
    // Update UI
    document.getElementById('create-title').style.display = 'inline';
    document.getElementById('edit-title').style.display = 'none';
    
    // Show password fields for create mode
    togglePasswordFields(true);
    
    // Reset form
    resetForm();
}

function fetchEditData(id) {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;

    // Use the correct parameter name: 'receptionist'
    const url = `{{ route('backend.receptionist.edit', ['receptionist' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', id);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                populateForm(data.data);
            } else {
                console.error('Error fetching data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
        });
}

function populateForm(data) {
    // Populate form fields
    document.getElementById('first_name').value = data.first_name || '';
    document.getElementById('last_name').value = data.last_name || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('mobile').value = data.mobile || '';
    document.getElementById('gender').value = data.gender || 'male';
    document.getElementById('date_of_birth').value = data.date_of_birth || '';
    document.getElementById('address').value = data.address || '';
    document.getElementById('pincode').value = data.pincode || '';
    document.getElementById('status').checked = data.status == 1;
    
    if (data.vendor_id) {
        document.getElementById('vendor_id').value = data.vendor_id;
        getClinic(data.vendor_id, data.clinic_id); // pass clinic_id for selection
    } else {
        getClinic('', data.clinic_id); // fallback for no vendor
    }
    
    if (data.clinic_id) {
        document.getElementById('clinic_id').value = data.clinic_id;
    }
    
    if (data.country) {
        console.log('Setting country:', data.country, 'state:', data.state, 'city:', data.city);
        document.getElementById('country').value = data.country;
        getState(data.country, data.state, data.city);
    }
    
    // Set date of birth in flatpickr
    if (data.date_of_birth) {
        const dateOfBirthInput = document.getElementById('date_of_birth');
        if (dateOfBirthInput && dateOfBirthInput._flatpickr) {
            dateOfBirthInput._flatpickr.setDate(data.date_of_birth);
        } else {
            dateOfBirthInput.value = data.date_of_birth;
        }
    }
    
    // Set profile image preview
    if (data.profile_image) {
        document.getElementById('profile-preview').src = data.profile_image;
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    console.log('Form submission started...');
    
    // Prevent double submission
    const submitBtn = document.getElementById('submit-btn');
    if (submitBtn.disabled) {
        console.log('Form already submitting, ignoring...');
        return;
    }
    
    // Clear previous errors
    clearErrors();
    
    // Validate form using our custom validation only
    if (!validateForm()) {
        console.log('Form validation failed');
        // Focus on first invalid field
        const firstInvalidField = document.querySelector('.is-invalid');
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
        return;
    }
    
    console.log('Form validation passed');
    
    // Show loading state
    const spinner = submitBtn.querySelector('.spinner-border');
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    // Prepare form data
    const formData = new FormData(e.target);
    
    // Ensure form doesn't use HTML validation
    e.target.setAttribute('novalidate', 'true');
    
    // For edit mode, handle image preservation
    if (isEditMode) {
        const profileImageInput = document.getElementById('profile_image');
        const removeImageInput = document.getElementById('remove_image');
        
        if (profileImageInput.files.length === 0) {
            // No new image selected, remove the profile_image field to preserve existing image
            formData.delete('profile_image');
        }
        
        // If user explicitly wants to remove image, keep the remove_image flag
        if (removeImageInput && removeImageInput.value === '1') {
            // Keep the remove_image flag in the form data
        } else {
            // Remove the remove_image flag if it exists
            formData.delete('remove_image');
        }
    }
    // formData.append('_token', '{{ csrf_token() }}');
    
    // Determine URL and method
    const url = isEditMode
        ? `{{ route('backend.receptionist.update', ['receptionist' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', currentId)
        : `{{ route('backend.receptionist.store') }}`;
    const method = 'POST';
    
    // Add method override for PUT
    if (isEditMode) {
        formData.append('_method', 'PUT');
    }
    
    // Submit form
    console.log('Submitting to URL:', url);
    console.log('Method:', method);
    console.log('Is Edit Mode:', isEditMode);
    
    // Add timeout to prevent infinite loading
    const timeoutId = setTimeout(() => {
        console.error('Request timeout - resetting button state');
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
        showErrorMessage('Request timed out. Please try again.');
    }, 30000); // 30 second timeout
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })

    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const contentType = response.headers.get('content-type');
        if (!response.ok) {
            if (response.status === 422 && contentType && contentType.includes('application/json')) {
                const errorData = await response.json();
                console.log('Validation errors:', errorData.errors);
                showErrors(errorData.errors || {});
                throw new Error('Validation error');
            }
            // Try to read as text for debugging
            const errorText = await response.text();
            console.error('Server error response:', errorText);
            throw new Error('Network response was not ok');
        }
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            const errorText = await response.text();
            console.error('Expected JSON, got:', errorText);
            // Try to parse as JSON anyway in case content-type is wrong
            try {
                return JSON.parse(errorText);
            } catch (e) {
                throw new Error('Invalid JSON response: ' + errorText.substring(0, 200));
            }
        }
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.status) {
            console.log('Success:', data.message);
            showSuccessMessage(data.message);
            closeOffcanvas();
            // Refresh datatable (assume DataTable instance is available globally or via id)
            if (window.renderedDataTable && typeof window.renderedDataTable.ajax === 'function') {
                window.renderedDataTable.ajax.reload(null, false);
            } else if ($.fn.DataTable && $('#datatable').length) {
                $('#datatable').DataTable().ajax.reload(null, false);
            }
        } else {
            console.log('Server returned error:', data.all_message);
            showErrors(data.all_message || {});
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        // showErrorMessage('An error occurred while saving the data.');
    })
    .finally(() => {
        clearTimeout(timeoutId);
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
    });
}

function validateForm() {
    let isValid = true;
    
    // Clear previous errors
    clearErrors();
    
    // Required field validation with specific messages
    const requiredFields = [
        { id: 'first_name', message: '{{ __("receptionist.lbl_first_name") }} is required.' },
        { id: 'last_name', message: '{{ __("receptionist.lbl_last_name") }} is required.' },
        { id: 'email', message: '{{ __("receptionist.lbl_Email") }} is required.' },
        { id: 'mobile', message: '{{ __("employee.lbl_phone_number") }} is required.' },
        { id: 'clinic_id', message: '{{ __("receptionist.select_clinic_centre") }} is required.' }
    ];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            showFieldError(field.id, field.message);
            isValid = false;
        }
    });
    
    // Email format validation
    const emailField = document.getElementById('email');
    if (emailField && emailField.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value.trim())) {
            showFieldError('email', '{{ __("clinic.please_enter_valid_email_address") }}');
            isValid = false;
        }
    }
    
    // Mobile number validation
    const mobileField = document.getElementById('mobile');
    if (mobileField && mobileField.value.trim()) {
        const mobileRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!mobileRegex.test(mobileField.value.trim())) {
            showFieldError('mobile', '{{ __("clinic.please_enter_valid_phone_number") }}');
            isValid = false;
        }
    }
    
    // Password validation for create mode
    if (!isEditMode) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password && password.value.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters long.');
            isValid = false;
        }
        
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            showFieldError('confirm_password', 'Passwords must match.');
            isValid = false;
        }
    }
    
    // Date of birth validation (if provided)
    const dateOfBirth = document.getElementById('date_of_birth');
    if (dateOfBirth && dateOfBirth.value) {
        const selectedDate = new Date(dateOfBirth.value);
        const today = new Date();
        if (selectedDate > today) {
            showFieldError('date_of_birth', 'Date of birth cannot be in the future.');
            isValid = false;
        }
    }
    
    return isValid;
}

// Individual field validation functions for real-time validation
function validateSingleField(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field || !field.value.trim()) {
        const fieldMessages = {
            'first_name': '{{ __("receptionist.lbl_first_name") }} is required.',
            'last_name': '{{ __("receptionist.lbl_last_name") }} is required.',
            'email': '{{ __("receptionist.lbl_Email") }} is required.',
            'mobile': '{{ __("employee.lbl_phone_number") }} is required.',
            'clinic_id': '{{ __("receptionist.select_clinic_centre") }} is required.'
        };
        
        if (fieldMessages[fieldId]) {
            showFieldError(fieldId, fieldMessages[fieldId]);
        }
    } else {
        // Clear error if field has value
        const errorElement = document.getElementById(fieldId + '_error');
        if (errorElement) {
            errorElement.textContent = '';
        }
        field.classList.remove('is-invalid');
        
    }
}

function validateEmailField() {
    const emailField = document.getElementById('email');
    if (emailField && emailField.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value.trim())) {
            showFieldError('email', '{{ __("clinic.please_enter_valid_email_address") }}');
        } else {
            emailField.classList.remove('is-invalid');
           
            const errorElement = document.getElementById('email_error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }
}

function validateMobileField() {
    const mobileField = document.getElementById('mobile');
    if (mobileField && mobileField.value.trim()) {
        const mobileRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!mobileRegex.test(mobileField.value.trim())) {
            showFieldError('mobile', '{{ __("clinic.please_enter_valid_phone_number") }}');
        } else {
            mobileField.classList.remove('is-invalid');
          
            const errorElement = document.getElementById('mobile_error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }
}

function validatePasswordField() {
    const passwordField = document.getElementById('password');
    if (passwordField && passwordField.value.length > 0) {
        if (passwordField.value.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters long.');
        } else {
            passwordField.classList.remove('is-invalid');
            
            const errorElement = document.getElementById('password_error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }
}

function validateConfirmPasswordField() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (confirmPasswordField && confirmPasswordField.value.length > 0) {
        if (passwordField && passwordField.value !== confirmPasswordField.value) {
            showFieldError('confirm_password', 'Passwords must match.');
        } else {
            confirmPasswordField.classList.remove('is-invalid');
          
            const errorElement = document.getElementById('confirm_password_error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('[id$="_error"]');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.color = '';
        element.style.fontSize = '';
        element.style.marginTop = '';
    });
    
    // Clear field styling
    const formFields = document.querySelectorAll('.form-control');
    formFields.forEach(field => {
        field.classList.remove('is-invalid', 'is-valid');
    });
}

function clearAllErrors() {
    // Clear all error messages
    clearErrors();
    
    // Clear any success/error notifications
    const notifications = document.querySelectorAll('.alert, .toast, .notification');
    notifications.forEach(notification => {
        notification.remove();
    });
    
    // Clear any global error messages
    const globalErrors = document.querySelectorAll('.error-message, .validation-error');
    globalErrors.forEach(error => {
        error.remove();
    });
    
    console.log('All errors and notifications cleared');
}

function refreshDataTable() {
    // Refresh the datatable to show updated data
    if (typeof window.reloadDataTable === 'function') {
        window.reloadDataTable();
    } else if (window.renderedDataTable) {
        window.renderedDataTable.ajax.reload(null, false);
    } else if ($.fn.DataTable && $('#datatable').length) {
        $('#datatable').DataTable().ajax.reload(null, false);
    }
    
    console.log('DataTable refreshed');
}

function showFieldError(fieldName, message) {
    const errorElement = document.getElementById(fieldName + '_error');
    const fieldElement = document.getElementById(fieldName);
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.color = '#dc3545';
        errorElement.style.fontSize = '0.875rem';
        errorElement.style.marginTop = '0.25rem';
    }
    
    if (fieldElement) {
        // Add error styling to the field
        fieldElement.classList.add('is-invalid');
        fieldElement.classList.remove('is-valid');
        
        // Add focus to first invalid field
        if (!document.querySelector('.is-invalid:focus')) {
            fieldElement.focus();
        }
    }
}

function showErrors(errors) {
    Object.keys(errors).forEach(field => {
        let message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
        showFieldError(field, message);
    });
}


function showSuccessMessage(message) {
    // You can implement your own success message display
    if (typeof window.successSnackbar === 'function') {
        window.successSnackbar(message);
    } else {
        alert(message);
    }
}

function showErrorMessage(message) {
    // You can implement your own error message display
    if (typeof window.errorSnackbar === 'function') {
        window.errorSnackbar(message);
    } else {
        alert(message);
    }
}

function closeOffcanvas() {
    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('form-offcanvas'));
    if (offcanvas) {
        offcanvas.hide();
    }
}

function resetForm() {
    // Reset the form
    document.getElementById('receptionist-form').reset();
    
    // Reset profile image
    document.getElementById('profile-preview').src = '{{ asset("images/default-user.png") }}';
    
    // Clear all errors and styling
    clearAllErrors();
    
    // Reset location selects
    document.getElementById('state').innerHTML = '<option value="">{{ __("clinic.state") }}</option>';
    document.getElementById('city').innerHTML = '<option value="">{{ __("clinic.city") }}</option>';
    
    // Reset flatpickr
    const dateOfBirthInput = document.getElementById('date_of_birth');
    if (dateOfBirthInput && dateOfBirthInput._flatpickr) {
        dateOfBirthInput._flatpickr.clear();
    }
    
    // Set defaults
    document.getElementById('gender').value = 'male';
    document.getElementById('status').checked = true;
    
    
    // Reset form mode
    currentId = 0;
    isEditMode = false;
    
    // Update UI for create mode
    document.getElementById('create-title').style.display = 'inline';
    document.getElementById('edit-title').style.display = 'none';
    
    // Show password fields for create mode
    togglePasswordFields(true);
    
    console.log('Form reset to create mode');
}

// Image handling functions
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        
        // Clear remove_image flag when new image is selected
        const removeImageInput = document.getElementById('remove_image');
        if (removeImageInput) {
            removeImageInput.value = '0';
        }
    }
}

function removeImage() {
    document.getElementById('profile_image').value = '';
    document.getElementById('profile-preview').src = '{{ asset('img/avatar/avatar.webp') }}';
    
    // Add hidden input to indicate image should be removed
    let removeImageInput = document.getElementById('remove_image');
    if (!removeImageInput) {
        removeImageInput = document.createElement('input');
        removeImageInput.type = 'hidden';
        removeImageInput.name = 'remove_image';
        removeImageInput.id = 'remove_image';
        document.getElementById('receptionist-form').appendChild(removeImageInput);
    }
    removeImageInput.value = '1';
}

// Location functions
function getClinic(vendorId, selectedClinicId = null) {
    let url = `{{ route('backend.clinics.index_list') }}`;
    if (vendorId) {
        url += `?vendor_id=${vendorId}`;
    }
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const clinicSelect = document.getElementById('clinic_id');
            if (!clinicSelect) return; // Prevent JS error if element not found
            clinicSelect.innerHTML = '<option value="">{{ __("clinic.select_clinic_center_placeholder") }}</option>';
            data.forEach(clinic => {
                const option = document.createElement('option');
                option.value = clinic.id;
                option.textContent = clinic.clinic_name || clinic.name;
                if (selectedClinicId && clinic.id == selectedClinicId) {
                    option.selected = true;
                }
                clinicSelect.appendChild(option);
            });
            
        })
        .catch(error => console.error('Error fetching clinics:', error));
}

function getState(countryId, selectedStateId = null, selectedCityId = null) {
    if (!countryId) return;
    
    console.log('Loading states for country:', countryId, 'selectedState:', selectedStateId, 'selectedCity:', selectedCityId);
    
    fetch(`{{ route('backend.state.index_list') }}?country_id=${countryId}`)
        .then(response => response.json())
        .then(data => {
            const stateSelect = document.getElementById('state');
            stateSelect.innerHTML = '<option value="">{{ __("clinic.state") }}</option>';
            
            data.forEach(state => {
                const option = document.createElement('option');
                option.value = state.id;
                option.textContent = state.name;
                if (selectedStateId && state.id == selectedStateId) {
                    option.selected = true;
                    console.log('Selected state:', state.name);
                }
                stateSelect.appendChild(option);
            });
            
            
            // If we have a selected state, load cities for that state
            if (selectedStateId) {
                getCity(selectedStateId, selectedCityId);
            } else {
                // Reset city when no state is selected
                document.getElementById('city').innerHTML = '<option value="">{{ __("clinic.city") }}</option>';
            }
        })
        .catch(error => console.error('Error fetching states:', error));
}

function getCity(stateId, selectedCityId = null) {
    if (!stateId) return;
    
    console.log('Loading cities for state:', stateId, 'selectedCity:', selectedCityId);
    
    fetch(`{{ route('backend.city.index_list') }}?state_id=${stateId}`)
        .then(response => response.json())
        .then(data => {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">{{ __("clinic.city") }}</option>';
            
            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                if (selectedCityId && city.id == selectedCityId) {
                    option.selected = true;
                    console.log('Selected city:', city.name);
                }
                citySelect.appendChild(option);
            });
            
        })
        .catch(error => console.error('Error fetching cities:', error));
}

function getVendors(systemService = '') {
    fetch(`{{ route('backend.multivendors.index_list') }}?system_service=${systemService}`)
        .then(response => response.json())
        .then(data => {
            const vendors = Array.isArray(data) ? data : data.data;
            const vendorSelect = document.getElementById('vendor_id');
            vendorSelect.innerHTML = '<option value="">{{ __("clinic.select_vendor_placeholder") }}</option>';

            vendors.forEach(vendor => {
                const option = document.createElement('option');
                option.value = vendor.id;
                option.textContent = vendor.name;
                vendorSelect.appendChild(option);
            });
            
        })
        .catch(error => console.error('Error fetching vendors:', error));
}

// Make functions globally available
window.setEditMode = setEditMode;
window.setCreateMode = setCreateMode;
</script>
