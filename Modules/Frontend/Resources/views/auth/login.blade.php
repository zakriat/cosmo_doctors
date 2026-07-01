@extends('frontend::layouts.auth_layout')
@section('title', __('frontend.login'))

@section('content')
<div class="auth-container" id="login"
    style="background-image: url('{{ asset('img/frontend/auth1.jpg') }}'); background-position: center center; background-repeat: no-repeat;background-size: cover;">
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
                        <div class="auth-card-content mt-3">
                            <p class="text-danger mb-1" id="login_error_message"></p>
                            <form action="post" id="login-form" class="requires-validation" data-toggle="validator"
                                novalidate>
                                @csrf
                                <input type="hidden" name="user_type" id="user_type" value="user">
                                <input type="hidden" name="action_type" value="login">
                                <input type="hidden" name="redirect_to" value="{{ $redirect_to ?? null }}">

                                <div class="input-group custom-input-group mb-3" style="flex-direction: column; align-items: flex-start;">
                                    <div style="width: 100%; display: flex;">
                                        <input type="email" name="email" class="form-control" placeholder="Enter Email"
                                            value="{{ setting('is_dummy_credentials') ? 'john@gmail.com' : '' }}"
                                            required autofocus>
                                        <span class="input-group-text"><i class="ph ph-envelope-simple"></i></span>
                                    </div>
                                    <div class="invalid-feedback" id="email-error" style="color: #dc3545; font-size: 1.1rem; font-weight: 500; display: none; text-align: left; width: 100%;">
                                        Invalid email format
                                    </div>
                                </div>

                                <div class="input-group custom-input-group mb-3">
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="Enter Password" value="{{ setting('is_dummy_credentials') ? '12345678' : '' }}" required>
                                    <span class="input-group-text"><i class="ph ph-eye-slash"
                                            id="togglePassword"></i></span>
                                </div>
                                <div class="invalid-feedback" id="password-error">Password field is required.</div>
                                <div class="d-flex justify-content-between flex-wrap gap-3">
                                    <div>
                                        <label for="remember_me" class="d-inline-flex" style="cursor: pointer;">
                                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                            <span class="ms-2">{{ __("messages.remember_me") }}</span>
                                        </label>
                                    </div>
                                    <a href="{{ route('forgot-password') }}"
                                        class="fw-semibold font-size-14 fst-italic">{{ __("messages.forgot_password") }}</a>
                                </div>
                                <div class="d-flex justify-content-between gap-3 mt-5 auth-btn">
                                    <button type="submit" id="login-button" class="btn btn-secondary sign-in-btn">{{ __("messages.sign_in") }}</button>
                                    @if (setting('is_google_login'))
                                    <a href="{{ route('auth.google') }}" id="google-login" class="btn px-3 google-btn">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.87845 8.99871C3.87845 8.41412 3.97554 7.85367 4.14883 7.32799L1.11563 5.01172C0.524469 6.21199 0.191406 7.56444 0.191406 8.99871C0.191406 10.4318 0.52406 11.7834 1.1144 12.9828L4.14597 10.6621C3.97431 10.1389 3.87845 9.58044 3.87845 8.99871Z"
                                                fill="#FBBC05" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.20477 3.68181C10.4747 3.68181 11.6218 4.13181 12.5231 4.86818L15.145 2.25C13.5473 0.85909 11.4989 0 9.20477 0C5.64308 0 2.58202 2.03686 1.11621 5.01299L4.14942 7.32927C4.84832 5.20772 6.84055 3.68181 9.20477 3.68181Z"
                                                fill="#EB4335" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.20477 14.3174C6.84055 14.3174 4.84832 12.7915 4.14942 10.6699L1.11621 12.9858C2.58202 15.9623 5.64308 17.9992 9.20477 17.9992C11.4031 17.9992 13.5018 17.2186 15.077 15.7561L12.1978 13.5303C11.3854 14.0421 10.3625 14.3174 9.20477 14.3174Z"
                                                fill="#34A853" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M17.8082 9.0016C17.8082 8.46978 17.7262 7.89705 17.6033 7.36523H9.20508V10.8425H14.0392C13.7975 12.028 13.1396 12.9395 12.1981 13.5327L15.0773 15.7585C16.732 14.2228 17.8082 11.9352 17.8082 9.0016Z"
                                                fill="#4285F4" />
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                                {{-- OTP REMOVED: Secure login with Google2FA link hidden
                                <div class="d-flex justify-content-center flex-wrap gap-1 mt-5 mb-3">
                                 <a href="{{ route('login-page', ['google2fa' => 1]) }}"
                                    class=" font-size-14 fw-bold">{{ __("messages.secure_login_google2fa") }}</a>
                                </div>
                                --}}
                                <div class="d-flex justify-content-center flex-wrap gap-1 mt-5 mb-3">
                                    <span class="font-size-14 text-body">{{ __("messages.not_a_member") }}</span>
                                    <a href="{{ route('register-page') }}"
                                        class="text-secondary font-size-14 fw-bold">{{ __("messages.register_now") }}</a>
                                </div>

                                <!-- <a href="{{ route('login') }}"
                                    class=" font-size-14 fw-bold">{{ __("messages.admin_login") }}</a> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<script>
    const loginUrl = "{{ route('user-login') }}";
    const homeUrl = "{{ route('frontend.index') }}";
    const rgitsterUrl = "{{ route('api.register') }}";

    const redirectInput = document.querySelector('input[name="redirect_to"]');
    const redirectTo = redirectInput ? redirectInput.value : null;

    // Email validation on blur
    document.querySelector('input[name="email"]').addEventListener('blur', function() {
        const email = this.value;
        const emailError = document.getElementById('email-error');
        const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;

        if (email && !emailPattern.test(email)) {
            this.classList.add('is-invalid');
            emailError.style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            emailError.style.display = 'none';
        }
    });
</script>
<script src="{{ asset('js/auth.min.js') }}" defer></script>
@endsection
