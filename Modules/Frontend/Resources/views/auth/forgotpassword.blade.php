@extends('frontend::layouts.auth_layout')
@section('title',  __('frontend.forgot_password'))
@section('content')
<div class="auth-container" id="login" style="background-image: url('{{ asset('img/frontend/auth-bg.png')}}'); background-position: center center; background-repeat: no-repeat;background-size: cover;">
    <div class="container h-100 min-vh-100">
        <div class="row h-100 min-vh-100 align-items-center">
            <div class="col-xl-4 col-lg-5 col-md-6 my-5">
                <div class="auth-card">
                    <div class="text-center">
                        @include('frontend::components.partials.logo')
                    </div>
                
                    <div class="my-4">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('password.emailuser') }}">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-label for="email" :value="__('Email')" />

                            <div class="input-group custom-input-group mb-3">
                                        <input type="email" name="email" class="form-control" placeholder="Enter Email"
                                            required value="{{old('email')}}">
                                        <span class="input-group-text"><i class="ph ph-envelope-simple"></i></span>
                             </div>



                            <!-- <x-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus /> -->
                        </div>

                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <x-button class="w-100 btn btn-secondary">
                                {{ __('Send Email') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>    
        </div>
    </div>
</div>
@endsection
