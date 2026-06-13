@extends('frontend::layouts.auth_layout')
@section('title',  __('frontend.login'))

@section('content')
    <div class="auth-container" id="login"
        style="background-image: url('{{ asset('img/frontend/auth-bg.png') }}'); background-position: center center; background-repeat: no-repeat;background-size: cover;">
        <div class="container h-100 min-vh-100">
            <div class="row h-100 min-vh-100 align-items-center">
                <div class="col-xl-4 col-lg-5 col-md-6 my-5">
                    <div class="auth-card">
                        <div class="text-center">
                            @include('frontend::components.partials.logo')
                            <div class="auth-card-content mt-3">
                                <p class="text-danger mb-1" id="multi_auth_message"></p>
                                <!-- <form action="post" id="login-form" class="requires-validation" data-toggle="validator" novalidate> -->

                                    @if($AlreadyExist != 1)
                                    <div class="card-body" style="text-align: center;">
                                        <p>{{__('messages.setup_two_fector_line')}} <strong>{{ $secret }}</strong></p>

                                        <div>
                                            {!! $QR_Image !!}
                                        </div>

                                        <p>{{ __('messages.pls_scan_qr_after_clcik_here') }}
                                            <a href="{{ route('multi-factor-auth', ['id' => $user_id, 'qr_scan' => 1]) }}">{{__('messages.then_click_here')}}</a>
                                        </p>


                                    </div>
                                    @endif

                                    @if($AlreadyExist == 1)
                                    <div class="panel-body">
                                        @if(isset($google_authentication_type) && $google_authentication_type == 'email' )
                                        <p>{{ __('messages.line_for_email_page')}}</p>
                                        @else
                                        <p>{{ __('messages.line_for_otp_page')}}</p>
                                        @endif
                                        @php
                                         $one_time_password='';
                                            if(isset($is_demo) && $is_demo==1){
                                                $one_time_password=123456;
                                             }    
                                        @endphp

                                        <form method="POST" action="{{ route('2fa') }}" id="multi-auth-form" class="requires-validation" data-toggle="validator" novalidate>
                                            @csrf
                                            <input id="user_id" type="hidden" class="form-control" name="user_id" value="{{ $user_id }}">
                                            <input type="hidden" name="redirect_to" value="{{ $redirect_to ?? null }}">
                                            <input type="hidden" name="google_authentication_type" id="google_authentication_type" value="{{$google_authentication_type ?? null}}">

                                           <input id="one_time_password"
                                                     type="text"
                                                     class="form-control"
                                                     placeholder="OTP"
                                                     value="{{ old('one_time_password', $one_time_password ?? '') }}"
                                                     name="one_time_password"
                                                     pattern="\d{6}"
                                                     title="Please enter exactly 6 digits"
                                                     required
                                                     autofocus>

                                            <div id="otpError" class="invalid-feedback">OTP is required</div>

                                            @if(isset($google_authentication_type) && $google_authentication_type == 'email')

                                            <small class="mt-1">{{ __('messages.line_for_otp_message')}}</small>

                                            @else

                                                <small class="mt-1">{{ __('messages.line_for_otp_message_for_qr_login')}}</small>

                                            @endif

                                            <div class="d-flex justify-content-between gap-3 mt-3 auth-btn">
                                                <button type="submit" id="multi-auth-button" class="btn btn-secondary sign-in-btn">Submit</button>
                                            </div>
                                        </form>

                                    </div>
                                    @endif
                                <!-- </form> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>



    <style>
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    .is-invalid {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    </style>

@if (session('error'))
    <div class="snackbar" id="snackbar">
        <div class="d-flex justify-content-around align-items-center">
            <p class="mb-0">{{ session('error') }}</p>
            <a href="#" class="dismiss-link text-decoration-none text-white ms-3" onclick="dismissSnackbar(event)">Dismiss</a>
        </div>
    </div>
@endif
    <script>
        const loginUrl = "{{ route('2fa') }}";
        const homeUrl = "{{ route('frontend.index') }}";
        // const rgitsterUrl = "{{ route('api.register') }}";

        const redirectTo = document.querySelector('input[name="redirect_to"]').value;

      document.addEventListener("DOMContentLoaded", function () {
        var snackbar = document.getElementById("snackbar");
        if (snackbar) {
            snackbar.classList.add("show");
            setTimeout(function () {
                snackbar.classList.remove("show");
            }, 3000);
        }
    });

    function dismissSnackbar(event) {
        event.preventDefault();
        var snackbar = document.getElementById("snackbar");
        if (snackbar) {
            snackbar.style.display = "none";
        }
    }
    </script>
    <script src="{{ asset('js/auth.min.js') }}" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('one_time_password');
        const otpForm = document.getElementById('multi-auth-form');
        const otpError = document.getElementById('otpError');

        // Prevent non-numeric input
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Form validation
        otpForm.addEventListener('submit', function(e) {
            const otpValue = otpInput.value.trim();

            if (otpValue.length === 0) {
                e.preventDefault();
                otpInput.classList.add('is-invalid');
                otpError.textContent = 'OTP is required';
                otpError.style.display = 'block';
                return false;
            } else if (otpValue.length !== 6) {
                e.preventDefault();
                otpInput.classList.add('is-invalid');
                otpError.textContent = 'Please enter 6 digits OTP';
                otpError.style.display = 'block';
                return false;
            }

            otpInput.classList.remove('is-invalid');
            otpError.style.display = 'none';
            return true;
        });
    });
    </script>

    <style>
.snackbar {
    display: none;
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f44336;
    color: #fff;
    padding: 16px 24px;
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    z-index: 9999;
}
.snackbar.show {
    display: block;
    animation: fadein 0.5s, fadeout 0.5s 4.5s;
}
@keyframes fadein {
    from { bottom: 20px; opacity: 0; }
    to { bottom: 30px; opacity: 1; }
}
@keyframes fadeout {
    from { bottom: 30px; opacity: 1; }
    to { bottom: 20px; opacity: 0; display: none; }
}
/* Chrome, Safari, Edge, Opera */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>

@endsection
