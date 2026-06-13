import Snackbar from 'node-snackbar'
import 'node-snackbar/dist/snackbar.min.css'

const snackbarMessage = () => {
  const PRIMARY_COLOR = window.getComputedStyle(document.querySelector('html')).getPropertyValue('--bs-success').trim()
  const DANGER_COLOR = window.getComputedStyle(document.querySelector('html')).getPropertyValue('--bs-danger').trim()

  const successSnackbar = (message) => {
    Snackbar.show({
      text: message,
      pos: 'bottom-left',
      actionTextColor: PRIMARY_COLOR,
      duration: 2500
    })
  }
  window.successSnackbar = successSnackbar

  const errorSnackbar = (message) => {
    Snackbar.show({
      text: message,
      pos: 'bottom-left',
      actionTextColor: '#FFFFFF',
      backgroundColor: DANGER_COLOR,
      duration: 2500
    })
  }
  window.errorSnackbar = errorSnackbar
}
snackbarMessage()
const togglePassword = document.querySelector('#togglePassword')
console.log(togglePassword)
const password = document.querySelector('#password')
togglePassword.addEventListener('click', function () {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password'
  password.setAttribute('type', type)
  if (type === 'text') {
    this.classList.remove('ph-eye-slash')
    this.classList.add('ph-eye')
  } else {
    this.classList.remove('ph-eye')
    this.classList.add('ph-eye-slash')
  }
  console.log(this.classList)
})

const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword')
const confirm_password = document.querySelector('#confirm_password')
if (toggleConfirmPassword) {
  toggleConfirmPassword.addEventListener('click', function () {
    const type_confirm = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password'
    confirm_password.setAttribute('type', type_confirm)
    this.classList.toggle('ph-eye-slash')
    if (type_confirm === 'text') {
      this.classList.remove('ph-eye-slash')
      this.classList.add('ph-eye')
    } else {
      this.classList.remove('ph-eye')
      this.classList.add('ph-eye-slash')
    }
  })
}

const registerForm = document.querySelector('#registerForm')
const registerButton = document.querySelector('#register-button')
const errorMessage = document.querySelector('#error_message')
const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content')

if (registerForm) {
  const registerButton = document.querySelector('#register-button')

  // Initial check and disable button
  registerButton.disabled = true

  // Add input event listeners to specific fields
  const fields = [
    'input[name="first_name"]',
    'input[name="last_name"]',
    'input[name="email"]',
    'input[name="password"]',
    'input[name="confirm_password"]'
  ].map(selector => registerForm.querySelector(selector));

  fields.forEach(field => {
    if (field) {

      field.addEventListener('input', validateFormOnInput)
    }
  })

  function validateFormOnInput() {

    clearAllErrors();
    const firstName = registerForm.querySelector('input[name="first_name"]').value.trim()
    const lastName = registerForm.querySelector('input[name="last_name"]').value.trim()
    const email = registerForm.querySelector('input[name="email"]').value.trim()
    const password = registerForm.querySelector('input[name="password"]').value.trim()
    const confirmPassword = registerForm.querySelector('input[name="confirm_password"]').value.trim()

    if (password.length > 0) {
      if (password.length < 8 || password.length > 14) {
        showFieldError(
          registerForm.querySelector('input[name="password"]'),
          window.validationMessages.password_length
        );
        registerButton.disabled = true;
        return;
      }
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,14}$/;
      if (!passwordRegex.test(password)) {
        showFieldError(
          registerForm.querySelector('input[name="password"]'),
          window.validationMessages.password_complexity
        );
        registerButton.disabled = true;
        return;
      }
    }

    const isFormValid = firstName !== '' &&
      lastName !== '' &&
      email !== '' &&
      validateEmail(email) &&
      password !== '' &&
      password.length >= 6 &&
      confirmPassword !== '' &&
      password === confirmPassword

    registerButton.disabled = !isFormValid
  }
  registerForm.addEventListener('submit', async function (e) {
    e.preventDefault()
    clearAllErrors();
    const isValid = validateRegisterForm() // Manually validate the form
    if (!isValid) {
      return
    }
    toggleRegisterButton(true, registerButton)
    errorMessage.textContent = ''

    try {
      const formData = new FormData(this)
      const response = await fetch(`${rgitsterUrl}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      })

      const data = await response.json()

      if (!response.ok) {
        // handleValidationErrors(data.errors)
      }

      if (data.status == true) {
        try {
          const formData = new FormData(this)
          const response = await fetch(`${loginUrl}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
          })

          const data = await response.json()

          if (data.status == true) {
            window.successSnackbar(data.message);
            window.location.href = `${login_page}`
          } else {
            console.log(data)
          }
        } catch (error) {
          if (error.message !== 'Validation Error') {
          }

        }
      } else {

        if (data.errors) {
          Object.entries(data.errors).forEach(([field, messages]) => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
              showFieldError(input, messages[0]);
            }
          });
        } else if (data.message) {
          errorMessage.textContent = data.message
        }

      }
    } catch (error) {
      console.log('Registration error:', error);
      errorMessage.textContent = 'An error occurred. Please try again later.'
    } finally {
      toggleRegisterButton(false, registerButton)
    }
  })
}

function showFieldError(input, message) {
  const container = input.closest('.form-group') || input.parentElement;

  // Create error div
  const errorDiv = document.createElement('div');
  errorDiv.className = 'error-message text-danger text-start mb-3';
  errorDiv.textContent = message;

  // Remove existing error message if any
  const existingError = container.querySelector('.error-message');
  if (existingError) {
    existingError.remove();
  }

  // Add error class to input
  input.classList.add('is-invalid');

  // Insert error message
  const targetElement = input.closest('.input-group') || input;
  targetElement.insertAdjacentElement('afterend', errorDiv);
}

function clearAllErrors() {
  document.querySelectorAll('.error-message').forEach(el => el.remove());
  document.querySelectorAll('.is-invalid').forEach(input => {
    input.classList.remove('is-invalid');
  });
}


function toggleRegisterButton(isSubmitting, button) {
  const registrationText = button.getAttribute('data-login-text') || 'Sign Up'
  button.textContent = isSubmitting ? 'Sign Up...' : registrationText
  button.disabled = isSubmitting
}

function validateRegisterForm() {
  let isValid = true

  const firstName = registerForm.querySelector('input[name="first_name"]')
  const lastName = registerForm.querySelector('input[name="last_name"]')
  const email = registerForm.querySelector('input[name="email"]')
  const password = registerForm.querySelector('input[name="password"]')
  const confirmPassword = registerForm.querySelector('input[name="confirm_password"]')

  if (!firstName.value.trim()) {
    showValidationError(firstName, 'First Name field is required.')
    isValid = false
  } else {
    clearValidationError(firstName)
  }

  if (!lastName.value.trim()) {
    showValidationError(lastName, 'Last Name field is required.')
    isValid = false
  } else {
    clearValidationError(lastName)
  }

  if (email && email.required) {
    if (email.value.trim() === '') {
      showValidationError(email, 'Email field is required.')
      isValid = false
    } else if (!validateEmail(email.value)) {
      showValidationError(email, 'Enter a valid Email Address.')
      isValid = false
    } else {
      clearValidationError(email)
    }
  }

  if (!password.value.trim()) {
    showValidationError(password, 'Password field is required.')
    isValid = false
  } else if (password.value.length < 6) {
    showValidationError(password, 'Password must be at least 6 characters long.')
    isValid = false
  } else {
    clearValidationError(password)
  }

  if (password.value.length > 6 && password.value !== confirmPassword.value) {
    showValidationError(confirmPassword, 'Passwords and confirm password do not match.')
    isValid = false
  } else {
    clearValidationError(confirmPassword)
  }

  return isValid
}

const loginForm = document.querySelector('#login-form')

if (loginForm) {
  const loginButton = document.querySelector('#login-button')
  const loginError = document.querySelector('#login_error_message')

  loginForm.addEventListener('submit', async function (e) {
    e.preventDefault()
    console.log('Login form submitted!'); // DEBUG
    const isValid = validateloginForm() // Manually validate the form
    console.log('Form validation result:', isValid); // DEBUG
    if (!isValid) {
      console.log('Form validation failed!'); // DEBUG
      return
    }
    toggleLoginButton(true, loginButton)
    loginError.textContent = ''

    try {
      const formData = new FormData(this)
      console.log('Sending login request to:', loginUrl); // DEBUG
      const response = await fetch(`${loginUrl}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      })

      const data = await response.json()

      if (!response.ok) {
        handleValidationErrors(data.errors)
      }
      if (data.status == true) {
        console.log(data.status);
        // Use redirect URL from server response (supports OTP removal)
        if (data.redirect) {
          window.location.href = data.redirect;
        } else if (redirectTo && redirectTo !== 'null') {
          window.location.href = `${redirectTo}`;
        } else {
          window.location.href = `${homeUrl}`;
        }
      } else {
        loginError.textContent = data.message
      }
    } catch (error) {
      if (error.message !== 'Validation Error') {
      }
    } finally {
      toggleLoginButton(false, loginButton)
    }
  })

  function validateloginForm() {
    let isValid = true
    const emailField = loginForm.querySelector('input[name="email"]')
    const passwordField = loginForm.querySelector('input[name="password"]')

    if (emailField && emailField.required) {
      if (emailField.value.trim() === '') {
        showValidationError(emailField, 'Email field is required.')
        isValid = false
      } else if (!validateEmail(emailField.value)) {
        showValidationError(emailField, 'Enter a valid Email Address.')
        isValid = false
      } else {
        clearValidationError(emailField)
      }
    }

    if (passwordField && passwordField.value.trim() === '') {
      passwordField.classList.add('is-invalid')
      isValid = false
    } else {
      passwordField.classList.remove('is-invalid')
    }

    return isValid
  }

  function toggleLoginButton(isSubmitting, button) {
    const loginText = button.getAttribute('data-login-text') || 'Login'
    button.textContent = isSubmitting ? 'Sign In...' : loginText
    button.disabled = isSubmitting
  }
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

//   function showValidationError(input, message) {

//       const errorFeedback = input.nextElementSibling;
//       if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
//           errorFeedback.textContent = message;
//           input.classList.add('is-invalid');
//       }
//   }

//   function clearValidationError(input) {
//     const errorFeedback = input.nextElementSibling;
//     if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
//         errorFeedback.textContent = '';
//         input.classList.remove('is-invalid');
//     }
// }

function showValidationError(input, message) {
  const container = input.closest('.input-group')
  const errorFeedback = container.querySelector('.invalid-feedback')
  if (errorFeedback) {
    errorFeedback.textContent = message
    input.classList.add('is-invalid')
  }
}

function clearValidationError(input) {
  const container = input.closest('.input-group')
  const errorFeedback = container.querySelector('.invalid-feedback')

  if (errorFeedback) {
    errorFeedback.textContent = ''
    input.classList.remove('is-invalid')
  }
}
