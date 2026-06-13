@extends('frontend::layouts.auth_layout')
@section('title',  __('frontend.reset_password'))

<div class="auth-container" id="login" style="background-image: url('{{ asset('img/frontend/auth-bg.png')}}'); background-position: center center; background-repeat: no-repeat;background-size: cover;">
    <div class="container h-100 min-vh-100">
        <div class="row h-100 min-vh-100 align-items-center">
            <div class="col-xl-4 col-lg-5 col-md-6 my-5">
                <div class="auth-card">
                    <div class="text-center mb-3">
                        @include('frontend::components.partials.logo')
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div>
                            <x-label for="email" :value="__('Email')" />

                            <x-input id="email" class="" type="email" name="email" :value="old('email', $request->email)" required autofocus />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-label for="password" :value="__('Password')" />

                            <x-input id="password" class="" type="password" name="password" required />
                            <div class="invalid-feedback text-danger" id="password_error" style="color: #dc3545; font-size: 1.1rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;"></div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-label for="password_confirmation" :value="__('Confirm Password')" />

                            <x-input id="password_confirmation" class="" type="password" name="password_confirmation" required />
                            <div class="invalid-feedback text-danger" id="confirm_password_error" style="color: #dc3545; font-size: 1.1rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;"></div>
                            <div class="invalid-feedback" id="password_mismatch_error"
                                style="color: #dc3545; font-size: 1.1rem; font-weight: 500; display: none; text-align: left; width: 100%; margin-top: 4px;">
                                Password and Confirm Password do not match
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end mt-4">
                            <x-button>
                                {{ __('Reset Password') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.validationMessages = {
        password_length: "{{ __('messages.password_length') }}",
        password_complexity: "{{ __('messages.validation_new_password_regex') }}"
    };

    function validatePassword(password) {
        const minLength = 8;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/;
        if (password.length < minLength) {
            return window.validationMessages.password_length || "Password must be at least 8 characters.";
        }
        if (!regex.test(password)) {
            return window.validationMessages.password_complexity || "Password must contain uppercase, lowercase, number, and special character.";
        }
        return "";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const passwordError = document.getElementById('password_error');
        const confirmPasswordError = document.getElementById('confirm_password_error');
        const mismatchError = document.getElementById('password_mismatch_error');
        const form = document.querySelector('form[action="{{ route('password.update') }}"]');

        function checkPassword() {
            const password = passwordInput.value;
            const error = validatePassword(password);
            if (error) {
                passwordInput.classList.add('is-invalid');
                passwordError.textContent = error;
                passwordError.style.display = 'block';
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordError.textContent = '';
                passwordError.style.display = 'none';
            }
        }

        function checkConfirmPassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.classList.add('is-invalid');
                mismatchError.style.display = 'block';
            } else {
                confirmPasswordInput.classList.remove('is-invalid');
                mismatchError.style.display = 'none';
            }
        }

        passwordInput.addEventListener('input', checkPassword);
        confirmPasswordInput.addEventListener('input', checkConfirmPassword);

        form.addEventListener('submit', function(e) {
            checkPassword();
            checkConfirmPassword();

            if (
                passwordInput.classList.contains('is-invalid') ||
                confirmPasswordInput.classList.contains('is-invalid')
            ) {
                e.preventDefault();
            }
        });
    });
</script>