@extends('frontend::layouts.patient_layout')

@section('content')

@section('title', __('frontend.settings'))

@include('frontend::components.section.breadcrumb')

    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="nav flex-column nav-pills account-setting-card" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active px-5 mb-3 account-setting-btn" id="v-pills-password-tab"
                                data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab"
                                aria-controls="v-pills-password" aria-selected="true"><span
                                    class="d-flex align-items-center gap-2"><i
                                        class="ph ph-password align-middle"></i>{{ __('frontend.change_password') }}
                                    </span></button>
                            <button class="nav-link px-5 account-setting-btn" id="v-pills-delete-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-delete" type="button" role="tab"
                                aria-controls="v-pills-delete" aria-selected="false"><span
                                    class="d-flex align-items-center gap-2"><i class="ph ph-user-circle"></i>{{ __('frontend.delete_account') }}
                                </span></button>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-7 ps-md-0 ps-3 mt-md-0 mt-5">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-password" role="tabpanel"
                                aria-labelledby="v-pills-password-tab">
                                <div class="section-bg p-4 rounded">
                                    <h6 class="mb-2 pb-1">
                                        {{ __('frontend.update_password') }}
                                    </h6>
                                    <form id="update-password-form" method="POST" novalidate>
                                        @csrf
                                        <div class="mb-3">
                                            <div class="input-group custom-input-group">
                                                <input type="password" name="old_password" class="form-control" id="old_password" placeholder="{{ __('frontend.old_password') }}" required>
                                                <span class="input-group-text"><i class="ph ph-eye-slash" id="togglePassword"></i></span>
                                            </div>
                                            <div id="error-old-password" class="text-danger mt-1" style="display: block; font-size: 0.875em;"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="input-group custom-input-group">
                                                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="{{ __('frontend.new_password') }}" required>
                                                <span class="input-group-text"><i class="ph ph-eye-slash" id="togglePassword"></i></span>
                                            </div>
                                            <div id="error-new-password" class="text-danger mt-1" style="display: block; font-size: 0.875em;"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="input-group custom-input-group">
                                                <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" placeholder="{{ __('frontend.confirm_password') }}" required>
                                                <span class="input-group-text"><i class="ph ph-eye-slash" id="togglePassword"></i></span>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-md-end mt-5">
                                            <button type="submit" class="btn btn-secondary">{{ __('frontend.update') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-delete" role="tabpanel"
                                aria-labelledby="v-pills-delete-tab">
                                <div
                                    class="d-flex align-items-center justify-content-center flex-column section-bg p-4 rounded setting-delete-content">
                                    <svg width="95" height="100" viewBox="0 0 95 100" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M83.6007 17.6309V93.8436C83.6007 94.9074 83.1781 95.9277 82.4258 96.6799C81.6736 97.4321 80.6533 97.8547 79.5895 97.8547H15.4104C14.3465 97.8547 13.3263 97.4321 12.574 96.6799C11.8218 95.9277 11.3992 94.9074 11.3992 93.8436V17.6309"
                                            fill="#FEF1F1" />
                                        <path
                                            d="M83.6007 17.6309V93.8435C83.6007 94.9074 83.1781 95.9277 82.4258 96.6799C81.6736 97.4321 80.6533 97.8547 79.5895 97.8547H15.4104C14.3465 97.8547 13.3263 97.4321 12.574 96.6799C11.8218 95.9277 11.3992 94.9074 11.3992 93.8435V17.6309"
                                            stroke="#F54438" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M91.6232 17.6309H3.37695" stroke="#F54438" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M35.4666 41.6973V73.7868" stroke="#F54438" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M59.5337 41.6973V73.7868" stroke="#F54438" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M67.556 17.6307V9.60833C67.556 7.48066 66.7108 5.44013 65.2063 3.93564C63.7018 2.43115 61.6613 1.58594 59.5336 1.58594H35.4665C33.3388 1.58594 31.2983 2.43115 29.7938 3.93564C28.2893 5.44013 27.4441 7.48066 27.4441 9.60833V17.6307"
                                            stroke="#F54438" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-5 pt-2 text-center">
                                        <h6>{{ __('frontend.delete_account_title') }}</h6>
                                        <p class="mb-0 font-size-14">{{ __('frontend.delete_account_msg') }}</p>
                                        <button class="btn btn-secondary mt-4" data-bs-toggle="modal"
                                            data-bs-target="#delete-account">{{ __('frontend.delete_account') }}</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete account modal -->
    <div class="modal fade" id="delete-account">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content section-bg">
                <div class="modal-body modal-body-inner">
                    <div class="close-modal-btn" data-bs-dismiss="modal">
                        <i class="ph ph-x align-middle"></i>
                    </div>
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <svg width="95" height="100" viewBox="0 0 95 100" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                            d="M83.6007 17.6309V93.8436C83.6007 94.9074 83.1781 95.9277 82.4258 96.6799C81.6736 97.4321 80.6533 97.8547 79.5895 97.8547H15.4104C14.3465 97.8547 13.3263 97.4321 12.574 96.6799C11.8218 95.9277 11.3992 94.9074 11.3992 93.8436V17.6309"
                            fill="#FEF1F1" />
                            <path
                                d="M83.6007 17.6309V93.8435C83.6007 94.9074 83.1781 95.9277 82.4258 96.6799C81.6736 97.4321 80.6533 97.8547 79.5895 97.8547H15.4104C14.3465 97.8547 13.3263 97.4321 12.574 96.6799C11.8218 95.9277 11.3992 94.9074 11.3992 93.8435V17.6309"
                                stroke="#F54438" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M91.6232 17.6309H3.37695" stroke="#F54438" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M35.4666 41.6973V73.7868" stroke="#F54438" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M59.5337 41.6973V73.7868" stroke="#F54438" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M67.556 17.6307V9.60833C67.556 7.48066 66.7108 5.44013 65.2063 3.93564C63.7018 2.43115 61.6613 1.58594 59.5336 1.58594H35.4665C33.3388 1.58594 31.2983 2.43115 29.7938 3.93564C28.2893 5.44013 27.4441 7.48066 27.4441 9.60833V17.6307"
                                stroke="#F54438" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>

                        <div class="popup-content text-center mt-5 pt-2">
                            <h6 class="font-size-18">{{ __('frontend.delete_confirm_title') }}</h6>
                            <p class="mb-0 font-size-14">
                                {{ __('frontend.delete_account_msg') }}
                            </p>
                            <form id="deleteAccountForm" action="{{ route('account.delete') }}" method="POST"
                                class="mt-4">
                                @csrf
                                @method('DELETE')
                                <div class="form-group">
                                    <label for="password" class="font-size-14 mb-2">{{ __('frontend.password_lbl') }}</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="{{ __('frontend.password') }}" required>
                                    <div id="passwordError" class="alert alert-danger mt-2 d-none"></div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap justify-content-center gap-4 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('frontend.cancel') }}</button>
                                    <button type="submit" class="btn btn-secondary">{{ __('frontend.delete_account') }}</button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('update-password-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            document.getElementById('error-old-password').classList.add('d-none');
            document.getElementById('error-new-password').classList.add('d-none');

            fetch("{{ route('account.password.update') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Updated',
                            text: 'Your password has been successfully updated.',
                        });
                        form.reset(); // Clear form on success
                    } else if (data.errors) {
                        if (data.errors.old_password) {
                            const oldPasswordError = document.getElementById('error-old-password');
                            oldPasswordError.innerText = data.errors.old_password;
                            oldPasswordError.classList.remove('d-none');
                        }
                        if (data.errors.new_password) {
                            const newPasswordError = document.getElementById('error-new-password');
                            newPasswordError.innerText = data.errors.new_password.join(', '); // Handle array errors
                            newPasswordError.classList.remove('d-none');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });


        // Toggle password visibility
        document.querySelectorAll('.input-group-text i').forEach(toggleIcon => {
            toggleIcon.addEventListener('click', function() {
                const inputField = this.closest('.input-group').querySelector('input');
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    this.classList.remove('ph-eye-slash');
                    this.classList.add('ph-eye');
                } else {
                    inputField.type = 'password';
                    this.classList.remove('ph-eye');
                    this.classList.add('ph-eye-slash');
                }
            });
        });


        document.getElementById('deleteAccountForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const password = form.querySelector('#password').value;
            const passwordErrorDiv = document.getElementById('passwordError');
            const submitButton = form.querySelector('button[type="submit"]');
            const modalElement = document.getElementById('delete-account');

            passwordErrorDiv.classList.add('d-none');
            passwordErrorDiv.textContent = '';
            submitButton.disabled = true;

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        password: password,
                        _method: 'DELETE',
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        passwordErrorDiv.textContent = data.error;
                        passwordErrorDiv.classList.remove('d-none');
                        submitButton.disabled = false;
                    } else if (data.success) {
                        modalElement.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        document.querySelector('.modal-backdrop').remove();

                        Swal.fire({
                            icon: 'success',
                            title: 'Account Deleted',
                            text: 'Your account has been successfully deleted.',
                        }).then(() => {
                            window.location.href = "{{ route('frontend.index') }}";
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.disabled = false;
                });
        });
    </script>
@endsection
