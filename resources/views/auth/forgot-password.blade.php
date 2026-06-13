<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="{{ route('frontend.index') }}">
                <x-application-logo class="w-20 h-20" />
            </a>
        </x-slot>

        <div class="my-4">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="mt-1"
                         type="email"
                         name="email"
                         :value="old('email')"
                         required
                         autofocus
                         pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                         title="Please enter a valid email address." />

                <!-- Custom error message for invalid email format -->
                <span id="emailError" class="text-danger" style="display: none;">Invalid email format</span>
            </div>

            <div class="d-flex align-items-center justify-content-center mt-4">
                <x-button class="w-100">
                    {{ __('Send Email') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>

    <!-- JavaScript for custom email validation -->
    <script type="text/javascript">
        document.getElementById('email').addEventListener('input', function() {
            var emailField = document.getElementById('email');
            var emailError = document.getElementById('emailError');

            // Email validation pattern
            var emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;

            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.style.display = 'block'; // Show error message
                emailField.classList.add('is-invalid'); // Add error styling if needed
            } else {
                emailError.style.display = 'none'; // Hide error message
                emailField.classList.remove('is-invalid'); // Remove error styling if fixed
            }
        });
    </script>

</x-guest-layout>
