 <div class="offcanvas offcanvas-end offcanvas-booking" id="doctor_change_password" aria-labelledby="doctorChangePasswordLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="doctorChangePasswordLabel">
            {{ $createTitle ?? __('employee.change_password') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form id="change-password-form" method="POST" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="doctor_id" id="change-password-doctor-id">

            <!-- Old Password -->
            <div class="mb-3">
                <label for="old_password" class="form-label">
                    {{ __('employee.lbl_old_password')}} <span class="text-danger">*</span>
                </label>
                <div class="position-relative input-group">
                    <input
                        type="password"
                        id="old_password"
                        name="old_password"
                        class="form-control password-input-with-eye"
                        placeholder="{{ __('employee.lbl_old_password') ?? 'Old Password' }}"
                    >
                    <span class="password-eye-inside input-group-text">
                        <i class="ph ph-eye-slash" data-target="old_password"></i>
                    </span>
                </div>
                <div class="invalid-feedback old-password-error d-none"></div>
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    {{ __('employee.lbl_password') }} <span class="text-danger">*</span>
                </label>
                <div class="position-relative input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control password-input-with-eye"
                        placeholder="{{ __('employee.lbl_password') }}"
                    >
                    <span class="password-eye-inside input-group-text">
                        <i class="ph ph-eye-slash" data-target="password"></i>
                    </span>
                </div>
                <div class="invalid-feedback password-error d-none"></div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="confirm_password" class="form-label">
                    {{ __('employee.lbl_confirm_password') }} <span class="text-danger">*</span>
                </label>
                <div class="position-relative input-group">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control password-input-with-eye"
                        placeholder="{{ __('employee.lbl_confirm_password') }}"
                    >
                    <span class="password-eye-inside input-group-text">
                        <i class="ph ph-eye-slash" data-target="confirm_password"></i>
                    </span>
                </div>
                <div class="invalid-feedback confirm-password-error d-none"></div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="offcanvas"
                >
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span
                        class="spinner-border spinner-border-sm me-2 d-none"
                        role="status"
                    ></span>
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const offcanvasEl = document.getElementById('doctor_change_password');
      const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
      const form = document.getElementById('change-password-form');
      const submitBtn = document.getElementById('submit-btn');
      const spinner = submitBtn.querySelector('.spinner-border');
      const oldPwdInput = form.old_password;
      const pwdInput = form.password;
      const confInput = form.confirm_password;
      const oldPwdErr = form.querySelector('.old-password-error');
      const pwdErr = form.querySelector('.password-error');
      const confErr = form.querySelector('.confirm-password-error');
      const doctorIdInput = document.getElementById('change-password-doctor-id');

      // Password show/hide toggle (eye inside input at the end)
      form.querySelectorAll('.password-eye-inside').forEach(function(span) {
          span.addEventListener('click', function() {
              const icon = span.querySelector('i');
              const targetId = icon.getAttribute('data-target');
              const input = form.querySelector('#' + targetId);

              if (input.type === 'password') {
                  input.type = 'text';
                  icon.classList.remove('fa-eye-slash');
                  icon.classList.add('fa-eye');
              } else {
                  input.type = 'password';
                  icon.classList.remove('fa-eye');
                  icon.classList.add('fa-eye-slash');
              }
          });
      });

      // Always enable password fields on page load
      [oldPwdInput, pwdInput, confInput].forEach(input => input.disabled = false);

      // Reset form and errors when offcanvas opens
      offcanvasEl.addEventListener('show.bs.offcanvas', function (ev) {
          const btn = ev.relatedTarget;
          const docId = btn ? btn.getAttribute('data-doctor-id') : '';
          doctorIdInput.value = docId || '';
          form.reset();
          [oldPwdErr, pwdErr, confErr].forEach(el => {
              el.textContent = '';
              el.classList.add('d-none');
              el.classList.remove('d-block');
          });
          [oldPwdInput, pwdInput, confInput].forEach(i => {
              i.classList.remove('is-invalid');
              i.disabled = false;
              i.type = 'password';
          });
          form.querySelectorAll('.password-eye-inside i').forEach(function(icon) {
              icon.classList.remove('fa-eye');
              icon.classList.add('fa-eye-slash');
          });
      });

      // Validation functions
      function validateField(input, errorDiv) {
          let valid = true;
          errorDiv.textContent = '';
          errorDiv.classList.add('d-none');
          errorDiv.classList.remove('d-block');
          input.classList.remove('is-invalid');

          if (input === oldPwdInput && !input.value) {
              errorDiv.textContent = '{{ __("clinic.old_password_is_required") }}';
              errorDiv.classList.remove('d-none');
              errorDiv.classList.add('d-block');
              input.classList.add('is-invalid');
              valid = false;
          }
          if (input === pwdInput) {
              if (!input.value) {
                  errorDiv.textContent = '{{ __("clinic.password_is_required") }}';
                  errorDiv.classList.remove('d-none');
                  errorDiv.classList.add('d-block');
                  input.classList.add('is-invalid');
                  valid = false;
              } else if (input.value.length < 8 || input.value.length > 14) {
                  errorDiv.textContent = '{{ __("clinic.password_must_be_8_to_14_characters") }}';
                  errorDiv.classList.remove('d-none');
                  errorDiv.classList.add('d-block');
                  input.classList.add('is-invalid');
                  valid = false;
              } else {
                  // Require at least one uppercase, one lowercase, one number, one special character, and no spaces
                  const complexity = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])\S{8,14}$/;
                  if (!complexity.test(input.value)) {
                      errorDiv.textContent = 'Password must contain at least one uppercase / one lowercase / one number and one symbol.';
                      errorDiv.classList.remove('d-none');
                      errorDiv.classList.add('d-block');
                      input.classList.add('is-invalid');
                      valid = false;
                  }
              }
          }
          if (input === confInput) {
              if (!input.value) {
                  errorDiv.textContent = '{{ __("clinic.confirm_password_is_required") }}';
                  errorDiv.classList.remove('d-none');
                  errorDiv.classList.add('d-block');
                  input.classList.add('is-invalid');
                  valid = false;
              } else if (pwdInput.value && input.value !== pwdInput.value) {
                  errorDiv.textContent = '{{ __("clinic.new_password_and_confirm_password_do_not_match") }}';
                  errorDiv.classList.remove('d-none');
                  errorDiv.classList.add('d-block');
                  input.classList.add('is-invalid');
                  valid = false;
              } else if (input.value.length < 8 || input.value.length > 14) {
                  errorDiv.textContent = '{{ __("clinic.password_must_be_8_to_14_characters") }}';
                  errorDiv.classList.remove('d-none');
                  errorDiv.classList.add('d-block');
                  input.classList.add('is-invalid');
                  valid = false;
              }
          }
          if (input === pwdInput && confInput.value) {
              if (confInput.value !== pwdInput.value) {
                  confErr.textContent = '{{ __("clinic.new_password_and_confirm_password_do_not_match") }}';
                  confErr.classList.remove('d-none');
                  confErr.classList.add('d-block');
                  confInput.classList.add('is-invalid');
                  valid = false;
              } else {
                  confErr.textContent = '';
                  confErr.classList.add('d-none');
                  confErr.classList.remove('d-block');
                  confInput.classList.remove('is-invalid');
              }
          }
          return valid;
      }

      // Attach input event listeners
      oldPwdInput.addEventListener('input', () => validateField(oldPwdInput, oldPwdErr));
      pwdInput.addEventListener('input', () => validateField(pwdInput, pwdErr));
      confInput.addEventListener('input', () => validateField(confInput, confErr));

      function validateForm() {
          return validateField(oldPwdInput, oldPwdErr) &&
                 validateField(pwdInput, pwdErr) &&
                 validateField(confInput, confErr);
      }

      // Form submit handler
      form.addEventListener('submit', function (e) {
          e.preventDefault();

          if (!validateForm()) {
              if (!oldPwdInput.value) oldPwdInput.focus();
              else if (!pwdInput.value) pwdInput.focus();
              else if (!confInput.value) confInput.focus();
              return;
          }

          submitBtn.disabled = true;
          spinner.classList.remove('d-none');

          fetch('{{ route("backend.doctor.change_password") }}', {
              method: 'POST',
              headers: { 'X-Requested-With': 'XMLHttpRequest' },
              body: new FormData(form)
          })
          .then(response => response.json())
          .then(data => {
              if (data.status) {
                  window.successSnackbar(data.message || 'Password changed successfully.');
                  if (window.renderedDataTable) renderedDataTable.ajax.reload(null, false);
                  offcanvas.hide();
              } else if (data.errors) {
                  Object.entries(data.errors).forEach(([field, messages]) => {
                      const input = form.querySelector(`[name="${field}"]`);
                      let errorDiv = null;
                      if (field === 'old_password') errorDiv = oldPwdErr;
                      else if (field === 'password') errorDiv = pwdErr;
                      else if (field === 'confirm_password') errorDiv = confErr;
                      if (input && errorDiv) {
                          input.classList.add('is-invalid');
                          errorDiv.innerHTML = Array.isArray(messages) ? messages.join('<br>') : messages;
                          errorDiv.classList.remove('d-none');
                          errorDiv.classList.add('d-block');
                      }
                  });
              }
          })
          .catch(() => {
              window.errorSnackbar('An unexpected error occurred. Please try again.');
          })
          .finally(() => {
              submitBtn.disabled = false;
              spinner.classList.add('d-none');
          });
      });
  });
</script>
