// Other Patient functionality scripts
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addPatientForm");
    const modalElement = document.getElementById("addOtherPatientModal");
    const modal = new bootstrap.Modal(modalElement);

    // Extract ID from URL
    const urlSegments = window.location.pathname.split('/');
    const userId = urlSegments[urlSegments.length - 1];

    // Image upload functionality
    const profileImageInput = document.getElementById('add-patient-profile');
    const profileImagePreview = document.getElementById('add-patient-preview');
    const removeImageBtn = document.getElementById('add-patient-remove-image');

    if (profileImageInput && profileImagePreview) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn && profileImagePreview) {
        removeImageBtn.addEventListener('click', function() {
            profileImagePreview.src = "{{ default_file_url() }}";
            if (profileImageInput) {
                profileImageInput.value = '';
            }
        });
    }

    function formatIntlWithSpace(inputEl, itiInstance) {
        if (!inputEl || !itiInstance) return inputEl ? inputEl.value : '';
        try {
            var dial = (itiInstance.getSelectedCountryData && itiInstance.getSelectedCountryData().dialCode) || '';
            var full = (itiInstance.getNumber && typeof itiInstance.getNumber === 'function') ? itiInstance.getNumber() : '';
            if (full && typeof full === 'string') {
                var digits = full.replace(/\D/g, '');
                if (dial && digits.startsWith(dial)) {
                    var rest = digits.slice(dial.length);
                    return rest ? '+' + dial + ' ' + rest : '+' + dial;
                }
                if (dial) {
                    var remaining = digits;
                    return remaining ? '+' + dial + ' ' + remaining : '+' + dial;
                }
                return full;
            }
            var raw = (inputEl.value || '').replace(/\D/g, '');
            if (dial && raw) {
                if (raw.startsWith(dial)) raw = raw.slice(dial.length);
                return '+' + dial + (raw ? ' ' + raw : '');
            }
        } catch (e) {}
        return inputEl.value || '';
    }

    // Add event listener to handle form submission
    document.getElementById('add-patient-submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Clear previous error messages
        form.querySelectorAll(".error").forEach(el => el.textContent = '');

        const firstName = form.querySelector('[name="first_name"]');
        const lastName = form.querySelector('[name="last_name"]');
        const dob = form.querySelector('[name="dob"]');
        const contactNumber = form.querySelector('[name="contactNumber"]');
        const gender = form.querySelector('[name="gender"]:checked');
        const relation = form.querySelector('[name="relation"]:checked');

        // Validation
        if (!firstName.value.trim() || !lastName.value.trim() || !dob.value.trim() || !contactNumber.value.trim() || !gender || !relation) {
            if (!firstName.value.trim()) {
                const container = firstName.closest('.mb-3');
                container.querySelector('.error').textContent = 'First Name field is required.';
            }
            if (!lastName.value.trim()) {
                const container = lastName.closest('.mb-3');
                container.querySelector('.error').textContent = 'Last Name field is required.';
            }
            if (!dob.value.trim()) {
                const container = dob.closest('.mb-3');
                container.querySelector('.error').textContent = 'Date of Birth field is required.';
            }
            if (!contactNumber.value.trim()) {
                const container = contactNumber.closest('.mb-3');
                container.querySelector('.error').textContent = 'Phone Number field is required.';
            }
            if (!gender) {
                const genderContainer = form.querySelector('.mb-3 .gender-error').closest('.mb-3');
                const genderError = genderContainer.querySelector('.gender-error');
                genderError.textContent = 'Gender field is required.';
            }
            if (!relation) {
                const relationContainer = form.querySelector('.mb-3 .relation-error').closest('.mb-3');
                const relationError = relationContainer.querySelector('.relation-error');
                relationError.textContent = 'Relation field is required.';
            }
            return;
        }

        // Format phone number
        try {
            if (window.intlTelInputGlobals && contactNumber) {
                const itiInstance = window.intlTelInputGlobals.getInstance(contactNumber);
                if (itiInstance) {
                    contactNumber.value = formatIntlWithSpace(contactNumber, itiInstance);
                }
            } 
        } catch (err) {}

        const formData = new FormData(form);
        formData.append("user_id", userId);

        fetch("{{ route('backend.appointment.other_patient') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                $("#addOtherPatientModal").modal("hide");
                successSnackbar("Patient added successfully!");
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Reset form when modal is closed
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.querySelectorAll(".error").forEach(el => el.textContent = '');
        form.querySelectorAll(".required-star").forEach(el => el.style.display = 'none');
        form.reset();
        profileImagePreview.src = "{{ default_file_url() }}";
    });
});

// Delete patient functionality
document.querySelectorAll('.delete-patient').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const deleteUrl = this.dataset.url;
        const patientName = this.dataset.name;

        Swal.fire({
            title: '{{ __("messages.are_you_sure") }}',
            html: `{{ __("messages.delete_confirm") }} <br><strong>${patientName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            title: '{{ __("messages.deleted") }}',
                            text: '{{ __("messages.delete_success") }}',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __("messages.error") }}',
                            text: data.message ?? '{{ __("messages.delete_error") }}',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.delete_error") }}',
                        icon: 'error'
                    });
                });
            }
        });
    });
});

// Edit patient functionality
document.querySelectorAll('.edit-form').forEach(function (form) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        form.querySelectorAll(".error").forEach(el => el.remove());
        form.querySelectorAll(".required-star").forEach(el => el.style.display = "none");

        let hasError = false;
        const patientId = form.dataset.patientId;

        const firstName = form.querySelector('[name="first_name"]');
        const lastName = form.querySelector('[name="last_name"]');
        const dob = form.querySelector('[name="dob"]');
        const contactNumber = form.querySelector('[name="contactNumber"]');
        const gender = form.querySelector('[name="gender"]:checked');
        const relation = form.querySelector('[name="relation"]');

        // Validation
        if (!firstName.value.trim()) {
            firstName.insertAdjacentHTML('afterend', '<small class="text-danger error">First Name is required.</small>');
            hasError = true;
        }
        if (!lastName.value.trim()) {
            lastName.insertAdjacentHTML('afterend', '<small class="text-danger error">Last Name is required.</small>');
            hasError = true;
        }
        if (!dob.value.trim()) {
            dob.insertAdjacentHTML('afterend', '<small class="text-danger error">Date of Birth is required.</small>');
            hasError = true;
        }
        if (!contactNumber.value.trim()) {
            contactNumber.insertAdjacentHTML('afterend', '<small class="text-danger error">Phone Number is required.</small>');
            hasError = true;
        }
        if (!gender) {
            form.querySelector('.gender-error').innerText = 'Gender is required.';
            hasError = true;
        }
        if (!relation.value.trim()) {
            relation.insertAdjacentHTML('afterend', '<small class="text-danger error">Relation is required.</small>');
            hasError = true;
        }

        if (hasError) return;

        // Format phone number
        try {
            if (window.intlTelInputGlobals && contactNumber) {
                const itiInstance = window.intlTelInputGlobals.getInstance(contactNumber);
                if (itiInstance) {
                    contactNumber.value = formatIntlWithSpace(contactNumber, itiInstance);
                }
            }
        } catch (err) {}

        const formData = new FormData(form);
        const actionUrl = form.getAttribute("action");

        fetch(actionUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                successSnackbar("Patient updated successfully!");
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Reset validation when modal is closed
    const modalElement = form.closest('.modal');
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.querySelectorAll(".error").forEach(el => el.remove());
        form.querySelectorAll(".required-star").forEach(el => el.style.display = "none");
    });
});

// Initialize Flatpickr and phone inputs
document.addEventListener("DOMContentLoaded", function () {
    // Initialize Flatpickr for DOB fields
    const dobInput = document.getElementById('dob');
    if (dobInput) {
        flatpickr(dobInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            allowInput: false,
            clickOpens: true,
            placeholder: "Select date of birth"
        });
    }

    document.querySelectorAll('.flatpickr-dob').forEach(function(input) {
        if (input.id !== 'dob') {
            flatpickr(input, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                allowInput: false,
                clickOpens: true,
                placeholder: "Select date of birth"
            });
        }
    });

    // Initialize International Telephone Input
    function initializePhoneInputs() {
        const phoneInputs = document.querySelectorAll('.phone-input, .intl-tel-input');
        phoneInputs.forEach(function(input) {
            if (input.getAttribute('data-initialized') === 'true') {
                return;
            }

            const iti = intlTelInput(input, {
                initialCountry: "gb",
                preferredCountries: ["gb", "us", "in", "au", "ca"],
                separateDialCode: true,
                autoPlaceholder: "aggressive",
                nationalMode: false,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/utils.js"
            });

            input.setAttribute('data-initialized', 'true');

            try {
                const existing = (input.value || '').trim();
                if (existing) {
                    iti.setNumber(existing);
                }
            } catch (e) {}

            function updateFullNumber() {
                const countryData = iti.getSelectedCountryData();
                const dialCode = countryData.dialCode;
                let number = input.value.replace(/^\+\d+\s*/, '').trim();
                const formattedNumber = `+${dialCode} ${number}`;
                input.value = formattedNumber;
            }

            input.addEventListener("countrychange", updateFullNumber);
            input.addEventListener("blur", updateFullNumber);
        });
    }

    initializePhoneInputs();

    document.addEventListener('shown.bs.modal', function () {
        setTimeout(initializePhoneInputs, 100);
    });

    // Image preview/remove for edit modals
    document.querySelectorAll('input[type="file"][id^="profile_image_"]').forEach(function(fileInput){
        const idSuffix = fileInput.id.replace('profile_image_', '');
        const preview = document.getElementById(`edit-patient-preview-${idSuffix}`);
        const removeBtn = document.getElementById(`edit-patient-remove-image-${idSuffix}`);
        
        if (fileInput && preview) {
            fileInput.addEventListener('change', function(e){
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(ev){
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
        
        if (removeBtn && preview) {
            removeBtn.addEventListener('click', function(){
                preview.src = "{{ default_user_avatar() }}";
                fileInput.value = '';
            });
        }
    });
});