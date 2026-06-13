@extends('frontend::layouts.patient_layout')
@section('title', __('frontend.edit_profile'))

@section('content')

    @include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-3">
                        <div class="section-bg profile-box rounded d-flex align-items-center justify-content-center flex-column">
                            <div style="position: relative; margin-bottom: 1.5rem;">
                                <img src="{{ $user->profile_image }}"
                                    style="border-radius: 50%; object-fit: cover; width: 150px; height: 150px;"
                                    id="profileImage">
                                <a href="#"
                                    style="position: absolute; bottom: 10px; right: 10px; background-color: #fff; border-radius: 50%; padding: 5px; cursor: pointer;"
                                    onclick="document.getElementById('fileInput').click();">
                                    <i class="ph ph-camera" style="font-size: 1.5rem;"></i>
                                </a>
                                <input type="file" id="fileInput" style="display: none;" accept="image/*"
                                    onchange="previewImage(event)">
                            </div>
                            <p class="mb-0 text-center">Change your profile image by clicking on camera icon</p>
                        </div>
                    </div>
                    <div class="col-lg-8 mt-lg-0 mt-5">
                        <div class="personal-info-container section-bg rounded">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <h4 class="mb-0 font-size-18">Personal information</h4>
                                <button type="button" class="btn btn-link p-0 edit-icon" data-bs-toggle="modal"
                                    data-bs-target="#edit-profile-modal">
                                    <i class="ph ph-pencil-simple font-size-18"></i></button>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <p class="info-label mb-0">First Name</p>
                                    <h6 class="info-value fw-semibold mb-0">{{ $user->first_name }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">Last Name</p>
                                    <h6 class="info-value fw-semibold mb-0" id="last_name_value">{{ $user->last_name }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">Email</p>
                                    <h6 class="info-value fw-semibold mb-0" id="email_value">{{ $user->email }}</h6>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <p class="info-label mb-0">Phone Number</p>
                                    <h6 class="info-value fw-semibold mb-0" id="mobile_value">{{ $user->mobile }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3 ">
                                    <p class="info-label mb-0">Date Of Birth</p>
                                    <h6 class="info-value fw-semibold mb-0" id="date_of_birth_value">{{ $user->date_of_birth }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">Address</p>
                                    <h6 class="info-value fw-semibold mb-0" id="address_value">{{ $user->address }}</h6>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <p class="info-label mb-0">City/Town</p>
                                    <h6 class="info-value fw-semibold mb-0" id="city_value">{{ $user->city_display ?? 'Not specified' }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">County</p>
                                    <h6 class="info-value fw-semibold mb-0" id="state_value">{{ $user->county_display ?? 'Not specified' }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">Postcode</p>
                                    <h6 class="info-value fw-semibold mb-0" id="pincode_value">{{ $user->postcode_display ?? 'Not specified' }}</h6>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <p class="info-label mb-0">NHS Number</p>
                                    <h6 class="info-value fw-semibold mb-0" id="nhs_number_value">{{ $user->nhs_number ?? 'Not specified' }}</h6>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-3">
                                    <p class="info-label mb-0">Gender</p>
                                    <h6 class="info-value fw-semibold mb-0" id="gender_value">{{ ucfirst(str_replace('_', ' ', $user->gender)) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- edit modal -->
    <div class="modal fade" id="edit-profile-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content section-bg">
            <div class="modal-body edit-profile-content">
                <div class="d-flex justify-content-between gap-3 flex-wrap mb-5">
                    <h6 class="modal-title mb-0 font-size-18">Edit Profile</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-profile-form" method="POST" action="{{ route('update-profile') }}">
                    @csrf
                    <div class="form">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="first_name" class="form-control" placeholder="First Name" value="{{ $user->first_name ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-user"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-first_name"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="{{ $user->last_name ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-user"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-last_name"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="email" name="email" class="form-control" placeholder="Email Id" value="{{ $user->email ?? '' }}" readonly>
                                    <span class="input-group-text"><i class="ph ph-envelope-simple"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-email"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Phone Number" value="{{ $user->mobile ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-phone"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-mobile"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="date" name="date_of_birth" class="form-control date-picker" placeholder="Birth Date" value="{{ $user->date_of_birth ?? '' }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                    <span class="input-group-text"><i class="ph ph-cake"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-date_of_birth"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="address" class="form-control" placeholder="Address" value="{{ $user->address ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-map-pin-line"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-address"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="city_or_town" class="form-control" placeholder="City/Town" value="{{ $user->city_or_town ?? $user->city ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-buildings"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-city_or_town"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="county" class="form-control" placeholder="County" value="{{ $user->county ?? $user->state ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-map-trifold"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-county"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="postcode" class="form-control" placeholder="Postcode" value="{{ $user->postcode ?? $user->pincode ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-envelope"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-postcode"></small>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="input-group custom-input-group mb-1">
                                    <input type="text" name="nhs_number" class="form-control" placeholder="NHS number" value="{{ $user->nhs_number ?? '' }}" >
                                    <span class="input-group-text"><i class="ph ph-identification-card"></i></span>
                                </div>
                                <small class="text-danger error-message" id="error-nhs_number"></small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <div id="gender" class="d-flex gap-3 align-items-center">
                                    <div class="form-check custom-radio-btn">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" required {{ empty($user->gender) || $user->gender == 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label rounded-pill" for="male">Male</label>
                                    </div>
                                    <div class="form-check custom-radio-btn">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" required {{ $user->gender == 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label rounded-pill" for="female">Female</label>
                                    </div>
                                    <div class="form-check custom-radio-btn">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="other" required {{ $user->gender == 'other' ? 'checked' : '' }}>
                                        <label class="form-check-label rounded-pill" for="other">Other</label>
                                    </div>
                                    <div class="form-check custom-radio-btn">
                                        <input class="form-check-input" type="radio" name="gender" id="prefer_not_to_say" value="prefer_not_to_say" required {{ $user->gender == 'prefer_not_to_say' ? 'checked' : '' }}>
                                        <label class="form-check-label rounded-pill" for="prefer_not_to_say">{{ __('messages.prefer_not_to_say') }}</label>
                                    </div>
                                </div>
                                <small class="text-danger error-message" id="error-gender"></small>
                            </div>
                            <div class="d-flex justify-content-md-end">
                                <button type="submit" class="btn btn-secondary">Update</button>
                            </div>
                            <div id="snackbar" class="snackbar">Profile updated successfully!</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

   <!-- Success Modal -->
<div class="modal fade" id="update-profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg rounded">
            <div class="modal-body modal-body-inner">
                <div class="book-now text-center">
                    <svg width="145" height="140" viewBox="0 0 145 140" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <circle cx="116.014" cy="26.1068" r="3.04821" fill="#EAEAFA" />
                        <circle cx="111.746" cy="53.5421" r="2.43857" fill="#EAEAFA" />
                        <circle cx="127.597" cy="98.044" r="2.43857" fill="#EAEAFA" />
                        <circle cx="52.6109" cy="2.94052" r="2.43857" fill="#EAEAFA" />
                        <circle cx="20.9093" cy="74.878" r="2.43857" fill="#EAEAFA" />
                        <circle cx="2.62021" cy="106.579" r="2.43857" fill="#EAEAFA" />
                        <circle cx="58.7071" cy="137.062" r="2.43857" fill="#EAEAFA" />
                        <circle cx="40.4181" cy="26.7159" r="2.43857" fill="#EB5757" />
                        <circle cx="98.9432" cy="112.677" r="3.04821" fill="#EB5757" />
                        <circle cx="8.41131" cy="27.6315" r="1.5241" fill="#EAEAFA" />
                        <circle cx="72.1187" cy="71.8311" r="33.5303" fill="#5670CC" />
                        <path
                            d="M86.6602 62.0827C87.0914 62.4918 87.3425 63.0554 87.3583 63.6495C87.374 64.2437 87.1532 64.8198 86.7442 65.2511L72.0621 80.7369C71.5379 81.2886 70.9071 81.7281 70.2079 82.0287C69.5088 82.3292 68.7558 82.4846 67.9948 82.4853H67.8603C67.0767 82.4652 66.306 82.2814 65.5978 81.9455C64.8896 81.6096 64.2595 81.1292 63.7482 80.5351L57.4203 73.1862C57.0322 72.7357 56.839 72.1496 56.8831 71.5566C56.9273 70.9637 57.2052 70.4126 57.6556 70.0245C58.1061 69.6365 58.6923 69.4432 59.2852 69.4874C59.8782 69.5315 60.4293 69.8094 60.8173 70.2599L67.1452 77.611C67.2472 77.7302 67.3732 77.8264 67.515 77.8934C67.6568 77.9604 67.8111 77.9967 67.9679 78C68.1252 78.0087 68.2824 77.9819 68.4279 77.9216C68.5735 77.8612 68.7035 77.7689 68.8085 77.6514L83.4906 62.1656C83.6933 61.952 83.936 61.7804 84.205 61.6607C84.4739 61.5409 84.7639 61.4753 85.0582 61.4676C85.3525 61.4599 85.6455 61.5102 85.9204 61.6158C86.1953 61.7213 86.4466 61.88 86.6602 62.0827Z"
                            fill="white" />
                    </svg>
                    <div class="mt-5 pt-4 text-center">
                        <h6 class="mb-2 pb-1 font-size-18">{{__('frontend.profile_success_message')}}</h6>

                        <div class="mt-5 pt-2">
                            <!-- Close modal and reload page -->
                            <button class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.reload();">Done</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('after-scripts')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.querySelector("#mobile");
            var iti = window.intlTelInput(input, {
                initialCountry: "gb",
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js" // To handle number formatting
            });

            // Set only the national number (without country code) when modal opens
            var editProfileModal = document.getElementById('edit-profile-modal');
            if (editProfileModal) {
                editProfileModal.addEventListener('show.bs.modal', function () {
                    var fullNumber = input.value;
                    // If the value starts with '+', set as full number, else as national
                    if (fullNumber.startsWith('+')) {
                        iti.setNumber(fullNumber);
                        // input.value = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                    } else {
                        input.value = fullNumber;
                    }
                });
            }

            // On country change or blur, set value to full international number for backend
            function updateToFullNumber() {
                var fullPhoneNumber = iti.getNumber();
                input.value = fullPhoneNumber;
            }
            input.addEventListener("countrychange", updateToFullNumber);
            input.addEventListener("blur", updateToFullNumber);
        });
    </script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.date-picker', {
                dateFormat: 'Y-m-d',
                maxDate: 'today'
            });
        });
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    let isValid = true;

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Function to set error message
    function setError(field, message) {
        document.getElementById(`error-${field}`).textContent = message;
        isValid = false;
    }

    // Required Fields Validation
    const requiredFields = ['first_name', 'last_name', 'email', 'mobile', 'date_of_birth', 'address', 'city_or_town', 'county', 'postcode', 'gender'];

requiredFields.forEach(field => {
    if (!formData.get(field) || formData.get(field).trim() === '') {
        let formattedField = field.replace(/_/g, ' ') // Replace all underscores
                                  .replace(/\b\w/g, c => c.toUpperCase()); // Capitalize each word
        formattedField = formattedField.replace('Of', 'of'); // Ensure 'of' stays lowercase
        
        // Handle special field names
        if (field === 'city_or_town') {
            formattedField = 'City/Town';
        }
        
        setError(field, `${formattedField} is required.`);
    }
});
    // Email Validation
    const email = formData.get('email');
    if (email && !/^[\w.-]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/.test(email)) {
        setError('email', "Please enter a valid email address.");
    }

    // Stop submission if there are errors
    if (!isValid) return;

    // Submit form via AJAX
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#edit-profile-modal').modal('hide');
            $('#update-profile').modal('show');

        } else {
            window.errorSnackbar("An error occurred while updating the profile.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        window.errorSnackbar("Something went wrong!");
    });
});

// Reset form and clear error messages on modal close
document.getElementById('edit-profile-modal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('edit-profile-form').reset();
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
});

        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();
            console.log(input.files[0]);
            reader.onload = function() {
                const dataURL = reader.result;
                console.log(dataURL);
                const output = document.getElementById('profileImage');
                output.src = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
            let file = input.files[0];
            const formData = new FormData();
            formData.append("profile_image", file);

            fetch("{{ route('update-profile-image') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}" // Include CSRF token for Laravel
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                    } else {
                        console.log("Error uploading image:", data.message);

                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while uploading the image.");
                });
        }
    </script>
@endpush