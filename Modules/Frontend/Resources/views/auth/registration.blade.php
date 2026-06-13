@extends('frontend::layouts.auth_layout')
@section('title',  __('frontend.register'))

@section('content')
    <div class="auth-container" id="login"
        style="background-image: url('{{ asset('img/frontend/auth-bg.png') }}'); background-position: center center; background-repeat: no-repeat; background-size: cover;">
        <div class="container h-100 min-vh-100">
            <div class="row h-100 min-vh-100 align-items-center">
                <div class="col-xl-4 col-lg-5 col-md-6 my-5">
                    <div class="auth-card">
                        <div class="text-center">
                          <!--   <div class="mb-5">
                            <div class="logo-default">
                                  <a class="navbar-brand text-primary" href="{{ route('frontend.index') }}">
                                      <div class="logo-main">

                                          <div class="logo-normal">
                                              <img src="{{ asset(setting('logo')) }}" height="50" alt="{{ app_name() }}">

                                          </div>
                                          <div class="logo-dark d-none">
                                              <img src="{{ asset(setting('dark_logo')) }}" height="50" alt="{{ app_name() }}">
                                          </div>
                                      </div>
                                  </a>
                              </div>
                            </div> -->
                            @include('frontend::components.partials.logo')
                            <style>
                                .auth-card-content,
                                .auth-card-content p,
                                .auth-card-content span,
                                .auth-card-content a,
                                .auth-card-content label,
                                .auth-card-content .form-control,
                                .auth-card-content .input-group-text {
                                    font-size: 0.9rem;
                                }
                                .auth-card .btn {
                                    font-size: 0.9rem;
                                }
                                .auth-card-content .invalid-feedback {
                                    font-size: 0.9rem;
                                }
                            </style>
                            <div class="auth-card-content mt-3">
                                <p class="text-danger mb-1" id="error_message"></p>
                                <form id="registerForm" action="post" class="requires-validation" data-toggle="validator" novalidate>
                                    @csrf
                                    <input type="hidden" name="action_type" value="register">
                                    <input type="hidden" name="user_type" id="user_type" value="user">
                                    <div class="input-group custom-input-group mb-3">
                                        <input type="text" class="form-control" placeholder="First Name"
                                            name="first_name" id="first_name" autofocus>
                                        <span class="input-group-text"><i class="ph ph-user"></i></span>
                                    </div>
                                    <div class="invalid-feedback text-danger" id="first_name_error">First Name field is
                                        required</div>
                                    <div class="input-group custom-input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Last Name" name="last_name"
                                            id="last_name" >
                                        <span class="input-group-text"><i class="ph ph-user"></i></span>
                                    </div>
                                    <div class="invalid-feedback text-danger" id="last_name_error">Last Name field is
                                        required</div>
                                    <div class="input-group custom-input-group mb-3">
                                        <input type="tel" class="form-control" placeholder="Contact Number"
                                            name="mobile" id="contact_number">
                                        <span class="input-group-text"><i class="ph ph-phone-call"></i></span>
                                    </div>
                                    <div class="invalid-feedback text-danger w-100" id="contact_number_error">Contact
                                        Number field is required</div>
                                    <div class="invalid-feedback" id="contact_number_exists_error"
                                        style="color: #dc3545; font-size: 0.9rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;">
                                        This contact number is already registered
                                    </div>
                                        <div class="input-group custom-input-group mb-3 flex-column align-items-start">
                                        <div class="d-flex w-100">
                                            <input type="email" class="form-control" placeholder="E-mail ID" name="email" id="email">
                                            <span class="input-group-text"><i class="ph ph-envelope-simple"></i></span>
                                        </div>
                                        <div class="invalid-feedback" id="email-error"
                                            style="color: #dc3545; font-size: 0.9rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;">
                                            Invalid email format
                                        </div>
                                        <div class="invalid-feedback" id="email_exists_error"
                                            style="color: #dc3545; font-size: 0.9rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;">
                                            This email is already registered
                                        </div>
                                    </div>

                                    <div class="input-group custom-input-group mb-3">
                                        <input type="password" class="form-control" placeholder="Enter password"
                                            id="password" name="password">
                                        <span class="input-group-text">
                                            <i class="ph ph-eye-slash" id="togglePassword"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback text-danger" id="password_error">Password field is
                                        required</div>
                                    <div class="input-group custom-input-group">
                                        <input type="password" class="form-control" placeholder="Enter Confirm password"
                                            id="confirm_password" name="confirm_password" >
                                        <span class="input-group-text">
                                            <i class="ph ph-eye-slash" id="toggleConfirmPassword"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback text-danger" id="confirm_password_error">Confirm
                                        Password field is required</div>
                                    <div class="invalid-feedback" id="password_mismatch_error"
                                            style="color: #dc3545; font-size: 0.9rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;">
                                            Password and Confirm Password do not match
                                        </div>
                                    <div class="d-flex my-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
    </div>
    <p class="m-0 font-size-14 text-body">
        I agree to the 
        <a class="text-decoration-underline" target="_blank"
            href="{{ route('pages', ['slug' => 'terms-conditions']) }}">
            {{ __('frontend.term_condition') }}
        </a>
        and 
        <a class="text-decoration-underline" target="_blank"
            href="{{ route('pages', ['slug' => 'privacy-policy']) }}">
            {{ __('frontend.privacy_policy') }}
        </a>
    </p>
</div>
<div class="text-danger" id="terms_error" style="display:none">
    You must accept the Terms & Conditions
</div>

                                    <button type="submit" id="register-button" class="btn btn-secondary w-100"
                                        data-signup-text="{{ __('frontend.sign_up') }}">Sign Up</button>
                                    <div class="d-flex justify-content-center gap-1 mt-4">
                                        <span class="font-size-14 text-body">Already have an account?</span>
                                        <a href="{{ route('login-page') }}" class="text-secondary font-size-14 fw-bold">Sign In</a>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <script>
        const loginUrl = "{{ route('user-login') }}";
        const homeUrl = "{{ route('frontend.index') }}";
        const rgitsterUrl = "{{ route('api.register') }}";
        const login_page = "{{ route('login-page') }}";
    </script>
    <script>
        window.validationMessages = {
            password_length: "{{ __('messages.password_length') }}",
            password_complexity: "{{ __('messages.validation_new_password_regex') }}"
        };
    </script>
    <script>
        document.getElementById('contact_number').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        </script>
    <script src="{{ asset('js/auth.min.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.querySelector("#contact_number");
            var iti = window.intlTelInput(input, {
                initialCountry: "gb", // Set UK as default
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js" // To handle number formatting
            });

            input.addEventListener("countrychange", function () {
                var fullPhoneNumber = iti.getNumber();
                document.getElementById('contact_number').value = fullPhoneNumber;
            });

            input.addEventListener("blur", function () {
                var fullPhoneNumber = iti.getNumber();
                document.getElementById('contact_number').value = fullPhoneNumber;
                
                // Check if contact number exists
                if (fullPhoneNumber) {
                    checkContactExists(fullPhoneNumber);
                }
            });

            // Email validation on blur
            document.querySelector('input[name="email"]').addEventListener('blur', function() {
            const email = this.value;
            const emailError = document.getElementById('email-error');
            const emailExistsError = document.getElementById('email_exists_error');
            const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;

            if (email && !emailPattern.test(email)) {
                this.classList.add('is-invalid');
                emailError.style.display = 'block';
                emailExistsError.style.display = 'none';
            } else {
                this.classList.remove('is-invalid');
                emailError.style.display = 'none';
                
                // Check if email exists
                if (email && emailPattern.test(email)) {
                    checkEmailExists(email);
                }
            }
            });

            document.getElementById('confirm_password').addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;
                const mismatchError = document.getElementById('password_mismatch_error');

                if (confirmPassword && password !== confirmPassword) {
                    mismatchError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    mismatchError.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });

          document.getElementById('registerForm').addEventListener('submit', function(e) {
    const terms = document.getElementById('terms');
    const termsError = document.getElementById('terms_error');
    let valid = true;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const mismatchError = document.getElementById('password_mismatch_error');

    if (password !== confirmPassword) {
        mismatchError.style.display = 'block';
        document.getElementById('confirm_password').classList.add('is-invalid');
        valid = false;
    } else {
        mismatchError.style.display = 'none';
        document.getElementById('confirm_password').classList.remove('is-invalid');
    }
    if (!terms.checked) {
        termsError.style.display = 'block';
        terms.classList.add('is-invalid');
        valid = false;
    } else {
        termsError.style.display = 'none';
        terms.classList.remove('is-invalid');
    }

    if (!valid) {
        e.preventDefault(); //  form submission
    }
});

            // Function to check if email exists
           function checkEmailExists(email) {
                const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content');
                fetch(`${baseUrl}/api/check-email`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        const emailInput = document.querySelector('input[name="email"]');
        const emailExistsError = document.getElementById('email_exists_error');
        
        if (data.status === 'error') {
            emailInput.classList.add('is-invalid');
            emailExistsError.style.display = 'block';
        } else {
            emailInput.classList.remove('is-invalid');
            emailExistsError.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error checking email:', error);
    });
}

            // Function to check if contact number exists
            function checkContactExists(contact) {
                const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') ;
                fetch(`${baseUrl}/api/check-contact`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ contact: contact })
                })
                .then(response => response.json())
                .then(data => {
                    const contactInput = document.getElementById('contact_number');
                    const contactExistsError = document.getElementById('contact_number_exists_error');
                    
                    if (data.status === 'error') {
                        contactInput.classList.add('is-invalid');
                        contactExistsError.style.display = 'block';
                    } else {
                        contactInput.classList.remove('is-invalid');
                        contactExistsError.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error checking contact:', error);
                });
            }
        });
    </script>
@endsection
