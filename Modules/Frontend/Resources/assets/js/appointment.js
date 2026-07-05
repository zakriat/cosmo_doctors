import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import '@uppy/core/dist/style.min.css'
import '@uppy/dashboard/dist/style.min.css'
import Swal from 'sweetalert2'
import Snackbar from 'node-snackbar'
import 'node-snackbar/dist/snackbar.min.css'
window.Uppy = Uppy
window.Dashboard = Dashboard

// Make state globally accessible for enhanced-booking.js integration
let state = { ...initialState }
window.state = state  // Expose state globally
let currentStep = initialStep
let otherpatient_id = null;
let finalColumns = [
  {
    data: 'card',
    name: 'card',
    orderable: false,
    searchable: false
  }
]

document.addEventListener('DOMContentLoaded', () => {
  frontInitDatatable({
    url: routes.clinicIndex,
    finalColumns,
    advanceFilter: () => {
      return {
        service_id: state.selectedService
      }
    }
  })

  const maxDate = new Date().toISOString().split('T')[0];
  document.querySelector('input[name="dob"]').setAttribute('max', maxDate);

  // Allow only numbers in contact number
  document.querySelector('input[name="contactNumber"]').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  if (paymentDetails) {
    console.log(paymentDetails);

    Swal.fire({
      title: 'Payment Success',
      html: `
        <p class="px-3 mx-5">Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
        <strong>${paymentDetails.clinicName}</strong> has been confirmed on
        <strong>${paymentDetails.formate_appointment_time}</strong> at
        <strong>${paymentDetails.formate_appointment_date}</strong>.</p>
        <div>
          <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
          <p><strong>Payment via:</strong> ${capitalizeFirstLetter(paymentDetails.paymentVia)}</p>
          <p><strong>Total Payment:</strong> ${paymentDetails.currency}${paymentDetails.totalAmount}</p>
          </div>
      `,


      icon: 'success',
      confirmButtonText: 'Close',
      confirmButtonColor: '#FF6F61',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        // Redirect to appointment detail page using the booking ID
        if (paymentDetails.bookingId && paymentDetails.bookingId !== 'N/A') {
          const redirectUrl = `${routes.appointmentDetails}/${paymentDetails.bookingId}`;
          console.log('Redirecting to appointment details:', redirectUrl);
          window.location.href = redirectUrl;
        } else {
          console.error('Invalid booking ID:', paymentDetails.bookingId);
          window.location.href = routes.appointmentList;
        }
      }
    })
  }

  const walletPaymentMethod = document.querySelector('#method-Wallet')
  if (walletPaymentMethod) {
    walletPaymentMethod.addEventListener('change', async function () {
      if (this.checked) {
        const isSufficient = await handlePaymentMethodChange(state.totalAmount)
        if (!isSufficient) {
          this.checked = false // Prevent checking the checkbox
          state.selectedPaymentMethod = null

          // Update the selected payment method display
          const selectedPaymentMethodDisplay = document.getElementById('selected-payment-method');
          if (selectedPaymentMethodDisplay) {
            selectedPaymentMethodDisplay.textContent = 'Select Payment Method';
          }

          Snackbar.show({
            text: 'Insufficient balance. Please add funds in wallet',
            pos: 'bottom-left',
            duration: 2500,
            showAction: false,
            backgroundColor: '#dc3545',
            actionTextColor: '#fff',
            textColor: '#fff'
          })
        }
      }
    })
  }
  updateActiveStep()

  initializeDateChange()
  moveToPreviousStep()
  setCurrentStep()
  initializeNextButton()
  initializePrevButton()
  // Set today's date as default
  const today = new Date().toISOString().split('T')[0];
  const dateInput = document.getElementById('appointment_date');
  if (dateInput) {
    dateInput.value = today;
    state.selectedDate = today;
  }

  flatpickr('.date-picker', {
    dateFormat: 'Y-m-d',
    minDate: 'today',
    defaultDate: 'today'
  })



  const bookForOthers = document.getElementById('bookForOthers');
  const otherPatientsSection = document.getElementById('otherPatientsSection');
  const addPatientForm = document.getElementById('addPatientForm');
  const savePatientBtn = document.getElementById('savePatient');


  // Toggle other patients section
  bookForOthers.addEventListener('change', function () {
    otherPatientsSection.classList.toggle('d-none', !this.checked);
    if (this.checked) {
      loadOtherPatients();
    }
  });


  function loadOtherPatients() {
    fetch(routes.otherPatientList, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })
      .then(response => response.json())
      .then(data => {
        const container = document.querySelector('.other-patients-list');
        if (!data || data.length === 0) {
          container.innerHTML = `
                <div class="text-center p-4">
                    <p class="mb-0 text-muted">No Other Patient Found</p>
                </div>`;
          return;
        }

        container.innerHTML = data.map(patient => `
            <div class="d-flex align-items-center flex-wrap column-gap-4 row-gap-3 mt-2 book-for-appointments patient-card ${otherpatient_id === patient.id ? 'bg-primary border-primary active' : 'section-bg'}"
                 data-patient-id="${patient.id}"
                 onclick="selectPatient(${patient.id})">
                <div class="d-flex align-items-center gap-2">
                    <img src="${patient.profile_image || '{{ asset("default-avatar.png") }}'}"
                         class="img-fluid rounded-circle avatar-35 object-fit-cover"
                         alt="${patient.first_name}">
                    <div class="patient-info">
                        <h6 class="appointments-title mb-0 ${otherpatient_id === patient.id ? 'text-white' : ''}">${patient.first_name}</h6>
                    </div>
                </div>
            </div>
        `).join('');
      })
      .catch(error => {
        console.error('Error loading patients:', error);
        toastr.error('{{ __("frontend.error_loading_patients") }}');
      });
  }

  savePatientBtn.addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default form submission

    document.querySelectorAll('.text-danger').forEach(function (element) {
      element.textContent = ''; // Clear old errors
    });

    const formData = new FormData(addPatientForm);
    let hasError = false;

    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const contactNumber = formData.get('contactNumber');
    const dob = formData.get('dob');
    const gender = formData.get('gender');
    const relation = formData.get('relation');

    if (!firstName) {
      showError('first_name', window.patientFormMessages.first_name_required);
      hasError = true;
    }
    if (!lastName) {
      showError('last_name', window.patientFormMessages.last_name_required);
      hasError = true;
    }
    if (!contactNumber) {
      showError('contactNumber', window.patientFormMessages.contact_number_required);
      hasError = true;
    }
    if (!dob) {
      showError('dob', window.patientFormMessages.dob_required);
      hasError = true;
    }
    if (!gender) {
      showError('gender', window.patientFormMessages.gender_required);
      hasError = true;
    }
    if (!relation) {
      showError('relation', window.patientFormMessages.relation_required);
      hasError = true;
    }
    let patientAddedMessage = "{{ __('frontend.patient_added_successfully') }}";

    if (!hasError) {
      formData.append('user_id', state.user_id);
      fetch(routes.otherPatient, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status) {
            $('#addPatientModal').modal('hide');
            addPatientForm.reset();
            loadOtherPatients();
            // Show success message
            window.successSnackbar('Patient added successfully');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          window.errorSnackbar('{{ __("frontend.error_adding_patient") }}');
        });
    }

    function showError(fieldName, message) {
      const input = document.querySelector(`[name="${fieldName}"]`);
      if (input) {
        const errorElement = document.createElement('small');
        errorElement.className = 'text-danger';
        errorElement.textContent = message;

        const inputGroup = input.closest('.input-group');
        if (inputGroup) {
          inputGroup.parentNode.insertBefore(errorElement, inputGroup.nextSibling);
        }
      }
    }
  });

  // Select patient
  window.selectPatient = function (patientId) {
    console.log(patientId);
    otherpatient_id = patientId;
    document.querySelectorAll('.patient-card').forEach(card => {
      const isSelected = card.dataset.patientId == patientId;
      card.classList.toggle('bg-primary', isSelected);
      card.classList.toggle('border-primary', isSelected);
      card.classList.toggle('section-bg', !isSelected);

      // Update text color
      const nameElement = card.querySelector('.patient-info .appointments-title');
      if (nameElement) {
        nameElement.classList.toggle('text-white', isSelected);
      }
    });
  };
})

function moveToPreviousStep() {
  const prevStepBtn = document.getElementById('prev-step-btn')
  const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');

  prevStepBtn.addEventListener('click', function () {
    if (currentStep > 0) {
      currentStep = --currentStep
      updateActiveStep()
    }
    else {
      window.location.href = `${baseUrl}/services`;
    }
  })
}
//update state
function updateState(newState) {
  state = {
    ...state,
    ...newState
  }
  if (state.selectedClinicName) {
    updateClinicCard(state.selectedClinicName)
  }
  if (state.selectedDoctorName) {
    updateDoctorCard(state.selectedDoctorName)
  }
}

//update clinic crad
function updateClinicCard(clinicName) {
  const clinicCardContainer = document.getElementById('selected-clinic-container')
  if (!clinicCardContainer) {
    console.error('Clinic card container not found!')
    return
  }
  clinicCardContainer.innerHTML = `
       <div class="bg-primary-subtle clinic-box-wizard rounded p-3 position-relative">
    <div class="position-absolute top-0 end-0 m-2">
        <a href="#" class="text-muted" id="clinic-edit-button" data-step="${tabs.find((tab) => tab.value === 'Choose Clinics').index}">
            <i class="ph ph-pencil-simple heading-color"></i>
        </a>
    </div>
    <div>
        <p class="font-size-14 heading-color mb-2">${clinicTitle}</p>
        <h6 class="font-size-14 text-heading fw-semibold mb-0">${clinicName}</h6>
    </div>
</div>
    `
  setCurrentStep()
}

//update doctor card
function updateDoctorCard(doctorName) {
  const doctorCardContainer = document.getElementById('selected-doctor-container')
  if (!doctorCardContainer) {
    console.error('Doctor card container not found!')
    return
  }
  doctorCardContainer.innerHTML = `
  <div class="bg-primary-subtle doctor-box-wizard rounded p-3 position-relative">
      <div class="position-absolute top-0 end-0 m-2">
          <a href="#" class="text-muted" id="doctor-edit-button" data-step="${tabs.find((tab) => tab.value === 'Choose Doctors').index}">
              <i class="ph ph-pencil-simple heading-color"></i>
          </a>
      </div>
      <div>
          <p class="font-size-14 heading-color mb-2">${doctorTitle}</p>
          <h6 class="font-size-14 text-heading fw-semibold mb-0">${doctorName}</h6>
      </div>
  </div>
`
  setCurrentStep()
}

//check step valid or not
function isStepValid(step) {
  const currentTab = tabs.find((tab) => tab.index === step)
  if (!currentTab) return false

  if (step < currentStep) return true

  switch (currentTab.value) {
    case 'Choose Clinics':
      return state.selectedService !== null
    case 'Choose Doctors':
      return state.selectedClinic !== null
    case 'Choose Date, Time, Payment':
      return state.selectedClinic !== null && state.selectedDoctor !== null
    default:
      return false
  }
}


const isCurrentStep = (index) => {
  return index === currentStep
}
function setCurrentStep(step) {
  const tabLinks = document.querySelectorAll('.tab-index')
  tabLinks.forEach((link) => {
    link.addEventListener('click', function (event) {
      event.preventDefault()
      const targetStep = parseInt(this.getAttribute('data-index'), 10)
      setStep(targetStep)
    })
  })
  addEditButtonListener('doctor-edit-button')
  addEditButtonListener('clinic-edit-button')
}

function addEditButtonListener(buttonId) {
  let button = document.getElementById(buttonId)
  if (button) {
    button.addEventListener('click', function (e) {
      e.preventDefault()
      let step = e.target.closest(`#${buttonId}`).getAttribute('data-step')
      step = Number(step)
      setStep(step)
    })
  }
}

function initializeNextButton() {
  const nextButton = document.getElementById('nextButton')

  if (nextButton) {
    nextButton.addEventListener('click', function (event) {
      event.preventDefault()
      const nextStep = currentStep + 1
      setStep(nextStep)
      toTop()
    })
  }
}

function toTop() {
  document.querySelector('#nextButton').addEventListener('click', (e) => {
    e.preventDefault()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  })
}

function initializePrevButton() {
  const prevButton = document.getElementById('prev-step-btn')

  if (prevButton) {
    prevButton.addEventListener('click', function (event) {
      event.preventDefault()
      moveToPreviousStep()
    })
  }
}

function setStep(step) {
  const currentTab = tabs.find((tab) => tab.index === currentStep)

  if (step < currentStep) {
    currentStep = step
    updateActiveStep()
    loadStepContent(step)
    return true
  }

  if (step > currentStep) {
    if (currentTab?.value === 'Choose Clinics' && !state.selectedClinic) {
      Snackbar.show({
        text: 'Please select a clinic first',
        pos: 'bottom-left',
        duration: 2500,
        showAction: false,
        backgroundColor: '#dc3545',
        actionTextColor: '#fff',
        textColor: '#fff'
      })
      return false
    }

    if (currentTab?.value === 'Choose Doctors' && !state.selectedDoctor) {
      Snackbar.show({
        text: 'Please select a doctor first',
        pos: 'bottom-left',
        duration: 2500,
        showAction: false,
        backgroundColor: '#dc3545',
        actionTextColor: '#fff',
        textColor: '#fff'
      })
      return false
    }
  }

  if (step === 0 || isStepValid(step)) {
    currentStep = step
    updateActiveStep()
    return true
  }
  return false
}

//set next step
function setNextStep() {
  const maxStep = 3
  const nextStep = ++currentStep
  if (nextStep <= maxStep) {
    setStep(nextStep)
  }
}

function updateActiveStep() {
  const tabsItem = document.querySelectorAll('.appointments-steps-item a')
  const paymentContainer = document.querySelector('.payment-container')
  tabsItem.forEach((tab) => {
    const index = parseInt(tab.getAttribute('data-index'))
    const parent = tab.closest('.appointments-steps-item')
    const dataCheckValue = index < currentStep ? 'true' : 'false'
    tab.setAttribute('data-check', dataCheckValue)
    loadStepContent(currentStep)
    // Toggle active class
    if (index === currentStep) {
      parent.classList.add('active')
    } else if (index < currentStep) {
      parent.setAttribute('data-check', true)
      parent.classList.remove('active')
      parent.classList.add('complete');
    } else if (index > currentStep) {
      const currentTab = tabs.find((tab) => tab.index === index)
      if (currentTab && currentTab.value === 'Choose Clinics') {
        const clinicCardContainer = document.getElementById('selected-clinic-container')
        state.selectedClinic = null
        clinicCardContainer.innerHTML = ''
        state.selectedClinicName = null
        parent.setAttribute('data-check', false)
        parent.classList.remove('active')
      } else if (currentTab && currentTab.value === 'Choose Doctors') {
        state.selectedDoctor = null
        state.selectedDoctorName = null
        const doctorCardContainer = document.getElementById('selected-doctor-container')
        doctorCardContainer.innerHTML = ''
        parent.setAttribute('data-check', false)
        parent.classList.remove('active')
      }
    } else {
      parent.setAttribute('data-check', false)
      parent.classList.remove('active')
    }

    if (index !== 2) {
      let dateTimePaymentTab = document.querySelector('.appointments-steps-item[data-label="Choose Date, Time, Payment"]')
      if (dateTimePaymentTab) {
        dateTimePaymentTab.classList.remove('active')
      }
    }

    // Toggle disabled state
    if (index > 0 && !isStepValid(index)) {
      tab.setAttribute('disabled', 'true')
    } else {
      tab.removeAttribute('disabled')
    }
  })
  const paymentTab = tabs.find((tab) => tab.value === 'Choose Date, Time, Payment')
  if (paymentTab && currentStep === paymentTab.index) {
    paymentContainer.classList.remove('d-none')
  } else {
    paymentContainer.classList.add('d-none')
  }
}

//load next step
function loadStepContent(step) {
  // Check if enhanced booking is coordinating and we should skip step 0
  if (window.skipOriginalStep0 && step === 0) {
    console.log('Skipping original step 0 - enhanced booking is handling categories');
    return;
  }
  
  const nextButton = document.getElementById('nextButton')
  for (let i = 0; i <= 3; i++) {
    const stepElement = document.getElementById(`step-content-${i}`)
    if (stepElement) {
      if (i === 3) {
        stepElement.classList.add('d-none')
      } else {
        // Don't clear step-content-0 if enhanced booking is active
        if (!(window.skipOriginalStep0 && i === 0)) {
          stepElement.innerHTML = ''
        }
      }
    }
  }
  step = Number(step)

  const currentTab = tabs.find((tab) => tab.index === step)
  const paymentContainer = document.querySelector('.payment-container')

  if (!currentTab) {
    console.error('Invalid step:', step);
    return;
  }

  // ADDITIONAL PROTECTION: Check if this is step 0 with categories before switch statement
  if (window.skipOriginalStep0 && step === 0) {
    console.log('🛡️ BLOCKED: Preventing step 0 content overwrite - categories are being handled by enhanced booking');
    return;
  }

  // EXTRA PROTECTION: Check if currentTab.value is 'Choose Clinics' but we have categories
  if (window.skipOriginalStep0 && currentTab.value === 'Choose Clinics' && step === 0) {
    console.log('🛡️ BLOCKED: Preventing "Choose Clinics" overwrite on step 0 - categories active');
    return;
  }

  switch (currentTab.value) {
    case 'Choose Clinics':

      paymentContainer.classList.add('d-none');

      const clinicshimmerContainer = document.getElementById('clinic-shimmer-loader');

      if (clinicshimmerContainer) {
        clinicshimmerContainer.classList.remove('d-none');
      }

      // Use step-content-1 for clinics when categories exist, step-content-0 otherwise
      const clinicStepContent = hasCategories ? 'step-content-1' : 'step-content-0';

      document.getElementById(clinicStepContent).innerHTML = `

        <div>
          <h6>${ChooseClinic}</h6>
        </div>
        <div class="card-style-slider">
          <table id="datatable" class="table table-responsive custom-card-table">
          </table>
        </div>
      `;

      // Initialize the data table
      frontInitDatatable({
        url: routes.clinicIndex,
        finalColumns,
        advanceFilter: () => {
          return {
            service_id: state.selectedService
          };
        },
        onLoadComplete: () => {

          addCheckboxesToClinicCards();

          if (clinicshimmerContainer) {
            clinicshimmerContainer.classList.add('d-none');
          }

          // Auto-select single clinic if only one exists and none is selected
          const clinicCards = document.querySelectorAll('.clinics-card');
          
          if (clinicCards.length === 1 && !state.selectedClinic) {
            console.log('Auto-selecting single clinic...');
            const singleCard = clinicCards[0];
            const checkbox = singleCard.querySelector('.clinic-checkbox');
            
            if (checkbox) {
              checkbox.checked = true;
              singleCard.classList.add('text-muted');
              updateClinicSelection(singleCard);
              
              // Show visual feedback
              Snackbar.show({
                text: 'Clinic automatically selected',
                pos: 'bottom-left',
                duration: 2000,
                showAction: false,
                backgroundColor: '#28a745'
              });
              
              // Auto-advance to next step after a brief delay
              setTimeout(() => {
                nextStep();
              }, 1000);
            }
          }

          // Handle pre-selected clinics (existing logic)
          if (state.selectedClinic) {
            clinicCards.forEach(card => {
              const link = card.querySelector('a[href*="/clinic-details/"]');
              if (link) {
                const clinicIdMatch = link.getAttribute('href').match(/clinic-details\/(\d+)/);
                if (clinicIdMatch && clinicIdMatch[1] === state.selectedClinic) {
                  const checkbox = card.querySelector('.clinic-checkbox');
                  if (checkbox) {
                    checkbox.checked = true;
                    card.classList.add('text-muted');
                  }
                }
              }
            });
          }
        }
      });

      attachClinicEventListeners();

      nextButton.classList.remove('d-none');
      break;


    case 'Choose Doctors':
      paymentContainer.classList.add('d-none')

      const doctorshimmerContainer = document.getElementById('doctor-shimmer-loader');

      if (doctorshimmerContainer) {
        doctorshimmerContainer.classList.remove('d-none');
      }

      // Use step-content-2 for doctors when categories exist, step-content-1 otherwise
      const doctorStepContent = hasCategories ? 'step-content-2' : 'step-content-1';

      document.getElementById(doctorStepContent).innerHTML = `
        <div>
          <h6>${ChooseDoctor}</h6>
        </div>
        <div class="card-style-slider">
          <table id="datatable" class="table table-responsive custom-card-table doctor-table">
          </table>
        </div>
      `

      frontInitDatatable({
        url: routes.doctorIndex,
        finalColumns,
        advanceFilter: () => {
          return {
            clinic_id: state.selectedClinic,
            service_id: state.selectedService,
            category_id: state.selectedCategory // Add category filter
          }
        },
        onLoadComplete: () => {

          if (doctorshimmerContainer) {
            doctorshimmerContainer.classList.add('d-none');
          }
          addCheckboxesToDoctorCards()
          if (state.selectedDoctor) {
            const doctorCards = document.querySelectorAll('.doctor-card')
            doctorCards.forEach(card => {
              const link = card.querySelector('a[href*="/doctor-details/"]')
              if (link) {
                const doctorIdMatch = link.getAttribute('href').match(/doctor-details\/(\d+)/)
                if (doctorIdMatch && doctorIdMatch[1] === state.selectedDoctor) {
                  const checkbox = card.querySelector('.doctor-checkbox')
                  if (checkbox) {
                    checkbox.checked = true
                    card.classList.add('text-muted')
                  }
                }
              }
            })
          }
        }
      })

      attachDoctorEventListeners()
      nextButton.classList.remove('d-none')
      break

    case 'Choose Date, Time, Payment':
      const stepElement = document.getElementById('step-content-3')
      stepElement.classList.remove('d-none')
      fetchDynamicData(state)

      // Set today's date as default if no date is selected
      if (!state.selectedDate) {
        const today = new Date().toISOString().split('T')[0];
        state.selectedDate = today;
        const dateInput = document.getElementById('appointment_date')
        if (dateInput) {
          dateInput.value = today;
        }
      }

      // Fetch time slots for the selected date (today by default)
      if (state.selectedDate) {
        const dateInput = document.getElementById('appointment_date')
        if (dateInput) {
          dateInput.value = state.selectedDate
          fetchAvailableTimeSlots(state.selectedDate)
        }
      }

      nextButton.classList.add('d-none')
      break
  }
}


function attachClinicEventListeners() {
  const clinicTable = document.querySelector('#datatable')

  clinicTable.addEventListener('change', (event) => {
    if (event.target.classList.contains('clinic-checkbox')) {
      document.querySelectorAll('.clinic-checkbox').forEach((checkbox) => {
        if (checkbox !== event.target) {
          checkbox.checked = false
          checkbox.closest('.clinics-card').classList.remove('text-muted')
        }
      })

      const card = event.target.closest('.clinics-card')
      if (event.target.checked) {
        card.classList.add('text-muted')
        updateClinicSelection(card)
      } else {
        card.classList.remove('text-muted')
        updateState({
          selectedClinic: null,
          selectedClinicName: null
        })
        const clinicCardContainer = document.getElementById('selected-clinic-container')
        if (clinicCardContainer) {
          clinicCardContainer.innerHTML = ''
        }
      }
    }
  })
}

function updateClinicSelection(card) {
  const link = card.querySelector('a[href*="/clinic-details/"]')
  const clinicNameElement = card.querySelector('.clinics-title a')

  if (link) {
    const url = link.getAttribute('href')
    const clinicIdMatch = url.match(/clinic-details\/(\d+)/)
    const clinicName = clinicNameElement.textContent.trim()

    if (clinicIdMatch) {
      const clinicId = clinicIdMatch[1]
      updateState({
        selectedClinic: clinicId,
        selectedClinicName: clinicName
      })
    }
  }
}
function addCheckboxesToClinicCards() {
  const rows = document.querySelectorAll('#datatable tbody tr')
  rows.forEach(row => {
    const card = row.querySelector('.clinics-card')
    if (card && !card.querySelector('.clinic-checkbox')) {
      const checkbox = document.createElement('input')
      checkbox.type = 'checkbox'
      checkbox.className = 'clinic-checkbox form-check-input position-absolute m-2'
      card.style.position = 'relative'
      card.appendChild(checkbox)
    }
  })
}

function addCheckboxesToDoctorCards() {
  const rows = document.querySelectorAll('#datatable tbody tr')
  rows.forEach(row => {
    const card = row.querySelector('.doctor-card')
    if (card && !card.querySelector('.doctor-checkbox')) {
      const checkbox = document.createElement('input')
      checkbox.type = 'checkbox'
      checkbox.className = 'doctor-checkbox form-check-input position-absolute m-2'
      card.style.position = 'relative'
      card.appendChild(checkbox)
    }
  })
}

function attachDoctorEventListeners() {
  const doctorTable = document.querySelector('.doctor-table')

  doctorTable?.addEventListener('change', (event) => {
    if (event.target.classList.contains('doctor-checkbox')) {
      document.querySelectorAll('.doctor-checkbox').forEach((checkbox) => {
        if (checkbox !== event.target) {
          checkbox.checked = false
          checkbox.closest('.doctor-card').classList.remove('text-muted')
        }
      })

      const card = event.target.closest('.doctor-card')
      if (event.target.checked) {
        card.classList.add('text-muted')
        updateDoctorSelection(card)
      } else {
        card.classList.remove('text-muted')
        updateState({
          selectedDoctor: null,
          selectedDoctorName: null
        })
        const doctorCardContainer = document.getElementById('selected-doctor-container')
        if (doctorCardContainer) {
          doctorCardContainer.innerHTML = ''
        }
      }
    }
  })
}

// Function to update state when a doctor is selected
function updateDoctorSelection(card) {
  const link = card.querySelector('a[href*="/doctor-details/"]')
  const doctorNameElement = card.querySelector('.doctor-name a')

  if (link) {
    const url = link.getAttribute('href')
    const doctorIdMatch = url.match(/doctor-details\/(\d+)/)
    const doctorName = doctorNameElement.textContent.trim()

    if (doctorIdMatch) {
      const doctorId = doctorIdMatch[1]
      updateState({
        selectedDoctor: doctorId,
        selectedDoctorName: doctorName
      })
    }
  }
}

async function fetchDynamicData(state) {
  try {
    const response = await fetch(routes.paymentData, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(state)
    })

    if (!response.ok) {
      throw new Error('Network response was not ok')
    }

    const data = await response.json()
    showTaxDetails(data.taxData, data.tax, data.currency)
    if (data.is_inclusive_tax == 1) {
      showTaxDetailsInclusive(data.inclusive_tax_data, data.total_inclusive_tax, data.currency)
    }
    updatePaymentDetails(data)
  } catch (error) {
  }
}
// Function to update payment details dynamically
function updatePaymentDetails(data) {
  const paymentContainer = document.querySelector('.payment-container')

  // Debug logging to help identify data issues
  console.log('Payment data received:', data)
  console.log('Total amount:', data.total)

  state.totalAmount = data.total
  state.payment.is_enable_advance_payment = data.is_enable_advance_payment ?? 0
  state.payment.advance_payment_amount = data.advancePayableAmount ?? 0
  state.payment.advance_payment_status = data.is_enable_advance_payment ?? 0

  const discountLabel = (data?.discountPercentage && data.discountvalue > 0)
    ? (data.discountPercentage === "percentage"
      ? `(${data.discountvalue}%)`
      : `${data.currency}${data.discountvalue.toFixed(2)}`)
    : '';


  const priceLabel = (data?.total_inclusive_tax > 0) ? `
            <span class="font-size-14">${price}<small class="text-secondary"><i>${withInclusivetax}</i></small></span>
  ` : `<span class="font-size-14">${price}</span>`;

  const inclusiveTaxSection = (data.is_inclusive_tax == 1) ? `
    <div class="d-flex justify-content-between mb-3">
        <span class="font-size-14">${InclusiveTax}</span>
        <span class="text-danger font-size-14 fw-bold">
            <i class="ph ph-info" style="cursor: pointer;" id="toggleInclusiveTaxDetails"></i>
            ${data.currency}${data.total_inclusive_tax.toFixed(2)}
        </span>
    </div>` : '';

  const discountSection = (data?.discountAmount > 0) ? `
  <div class="d-flex justify-content-between mb-3">
      <span class="font-size-14">Discount:</span>
      <span class="text-success font-size-14 fw-bold">-${data.currency}${data.discountAmount.toFixed(2)}</span>
  </div>` : '';


  const paymentDetailsHTML = `
  <h5 class="mb-4">${paymentDetail}</h5>
    <div class="section-bg p-4 mb-3 rounded">

        <!-- Service Price (Original) -->
        <div class="d-flex justify-content-between mb-3">
            <span class="font-size-14">
                Service Price${data.is_inclusive_tax == 1 ? ' (with inclusive tax)' : ''}:
            </span>
            <span class="font-size-14 text-primary fw-bold">${data.currency}${(data.price || 0).toFixed(2)}</span>
        </div>

        ${discountSection}

        <!-- Subtotal with inclusive tax -->
        <div class="d-flex justify-content-between mb-3">
            <span class="font-size-14">Subtotal:</span>
            <span class="font-size-14 fw-bold">${data.currency}${(data.subtotal || 0).toFixed(2)}</span>
        </div>

        <!-- Total Tax -->
        <div class="d-flex justify-content-between mb-3">
            <span class="font-size-14">Total Tax :</span>
            <span class="text-danger font-size-14 fw-bold">
                <i class="ph ph-caret-down" style="cursor: pointer;" id="toggleTaxDetails"></i>
                ${data.currency}${(data.tax || 0).toFixed(2)}
            </span>
        </div>

        <hr class="my-3" style="border-color: #dee2e6;">

        <!-- Total Amount -->
        <div class="d-flex justify-content-between">
            <span class="fw-bold font-size-16">Total Amount:</span>
            <span class="fw-bold font-size-16 text-success">${data.currency}${(data.total || 0).toFixed(2)}</span>
        </div>

        ${data.is_enable_advance_payment && data.advancePayableAmount > 0
      ? `<div class="d-flex justify-content-between gap-2 mb-2 mt-3 pt-3 border-top">
                      <span>${AdvancePayableAmount} (${data.advancePayableAmountPercentage}%)</span>
                      <span class="text-secondary fw-bold">
                          ${data.currency}${(parseFloat(data.advancePayableAmount) || 0).toFixed(2)}
                      </span>
                    </div>`
      : ''
    }
    </div>

    <div class="text-end">
    <button class="btn btn-secondary px-4" id="submitAppointment" disabled>${Submit}</button>
 </div>
  `

  if (paymentContainer) {
    paymentContainer.innerHTML = paymentDetailsHTML
    initializeSubmitButton()
  } else {
    console.error('Payment container not found!')
  }

  // Add toggle functionality for tax details
  initializeTaxToggle()
}

function showTaxDetails(taxData, totalTax, currency) {
  // Store tax data globally for use when details are toggled
  window.taxData = taxData;
  window.totalTax = totalTax;
  window.currency = currency;
}

function showTaxDetailsInclusive(taxData, totalTax, currency) {
  // Store inclusive tax data globally for use when details are toggled
  window.inclusiveTaxData = taxData;
  window.inclusiveTotalTax = totalTax;
  window.inclusiveCurrency = currency;
}

function formatItemValue(item) {

  return item.type === 'percent' ? `(${item.value}%)` : ``;
}

// Function to initialize tax toggle functionality
function initializeTaxToggle() {
  // Toggle regular tax details
  const toggleTaxDetails = document.getElementById('toggleTaxDetails');
  if (toggleTaxDetails) {
    toggleTaxDetails.addEventListener('click', function () {
      const taxRow = this.closest('.d-flex.justify-content-between');
      const existingDetails = taxRow.nextElementSibling;

      // Toggle icon between up and down
      if (this.classList.contains('ph-caret-down')) {
        this.classList.remove('ph-caret-down');
        this.classList.add('ph-caret-up');
      } else {
        this.classList.remove('ph-caret-up');
        this.classList.add('ph-caret-down');
      }

      // Remove existing details if they exist
      if (existingDetails && existingDetails.classList.contains('tax-details-container')) {
        existingDetails.remove();
        return;
      }

      // Create and insert tax details below the tax row
      const taxDetailsContainer = document.createElement('div');
      taxDetailsContainer.className = 'tax-details-container mt-2';
      taxDetailsContainer.innerHTML = `
        <div class="bg-body rounded p-3">
          <ul id="taxBreakdownList" class="p-0 mb-0 list-inline">
            <!-- Dynamic tax breakdown will be injected here -->
          </ul>
        </div>
      `;

      taxRow.parentNode.insertBefore(taxDetailsContainer, taxRow.nextSibling);

      // Populate tax details if data is available
      if (window.taxData && window.totalTax && window.currency) {
        populateTaxDetails(taxDetailsContainer, window.taxData, window.totalTax, window.currency);
      }
    });
  }

  // Toggle inclusive tax details
  const toggleInclusiveTaxDetails = document.getElementById('toggleInclusiveTaxDetails');
  if (toggleInclusiveTaxDetails) {
    toggleInclusiveTaxDetails.addEventListener('click', function () {
      const taxRow = this.closest('.d-flex.justify-content-between');
      const existingDetails = taxRow.nextElementSibling;

      // Toggle icon between up and down
      if (this.classList.contains('ph-caret-down')) {
        this.classList.remove('ph-caret-down');
        this.classList.add('ph-caret-up');
      } else {
        this.classList.remove('ph-caret-up');
        this.classList.add('ph-caret-down');
      }

      // Remove existing details if they exist
      if (existingDetails && existingDetails.classList.contains('tax-details-container')) {
        existingDetails.remove();
        return;
      }

      // Create and insert inclusive tax details below the tax row
      const taxDetailsContainer = document.createElement('div');
      taxDetailsContainer.className = 'tax-details-container mt-2';
      taxDetailsContainer.innerHTML = `
        <div class="bg-body rounded p-3">
          <ul id="taxBreakdownListinclusive" class="p-0 mb-0 list-inline">
            <!-- Dynamic tax breakdown will be injected here -->
          </ul>
        </div>
      `;

      taxRow.parentNode.insertBefore(taxDetailsContainer, taxRow.nextSibling);

      // Populate inclusive tax details if data is available
      if (window.inclusiveTaxData && window.inclusiveTotalTax && window.inclusiveCurrency) {
        populateInclusiveTaxDetails(taxDetailsContainer, window.inclusiveTaxData, window.inclusiveTotalTax, window.inclusiveCurrency);
      }
    });
  }
}

// Helper function to populate tax details
function populateTaxDetails(container, taxData, totalTax, currency) {
  const taxBreakdownList = container.querySelector('#taxBreakdownList');

  if (taxBreakdownList) {
    taxBreakdownList.innerHTML = '';

    taxData.forEach((item) => {
      const listItem = document.createElement('li');
      listItem.className = 'd-flex justify-content-between mb-2';
      listItem.innerHTML = `
        <span>${item.title} ${formatItemValue(item)}</span>
        <span>${currency}${item.amount.toFixed(2)}</span>
      `;
      taxBreakdownList.appendChild(listItem);
    });
  }
}

// Helper function to populate inclusive tax details
function populateInclusiveTaxDetails(container, taxData, totalTax, currency) {
  const taxBreakdownList = container.querySelector('#taxBreakdownListinclusive');

  if (taxBreakdownList) {
    taxBreakdownList.innerHTML = '';

    taxData.taxes.forEach((item) => {
      const listItem = document.createElement('li');
      listItem.className = 'd-flex justify-content-between mb-2';
      listItem.innerHTML = `
        <span>${item.title} ${formatItemValue(item)}</span>
        <span>${currency}${item.amount.toFixed(2)}</span>
      `;
      taxBreakdownList.appendChild(listItem);
    });
  }
}

//date change get slot
function initializeDateChange() {
  const dateInput = document.getElementById('appointment_date')
  const timeSlotsContainer = document.getElementById('time-slots-container')

  // Attach event listener for date input change
  dateInput.addEventListener('change', handleDateChange)
}

function handleDateChange(event) {
  const selectedDate = event.target.value
  if (selectedDate) {
    state.selectedDate = selectedDate
    fetchAvailableTimeSlots(selectedDate)
  }
}

function fetchAvailableTimeSlots(date, clinic_id, doctor_id, service_id) {
  fetch(routes.slotTimeList, {
    // Use the correct named route
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
      appointment_date: date,
      appointment_date: date,
      clinic_id: state.selectedClinic,
      doctor_id: state.selectedDoctor,
      service_id: state.selectedService
    })
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status && data.data.length > 0) {
        updateTimeSlots(data.data)
      } else {
        const timeSlotsContainer = document.getElementById('time-slots-container')
        timeSlotsContainer.innerHTML = ''
        timeSlotsContainer.innerHTML = '<p>No available slots for the selected date.</p>'
      }
    })
    .catch((error) => {
      const timeSlotsContainer = document.getElementById('time-slots-container')
      timeSlotsContainer.innerHTML = ''
      timeSlotsContainer.innerHTML = '<p>No available slots for the selected date.</p>'
    })
}

function updateTimeSlots(timeSlots) {
  const timeSlotsContainer = document.getElementById('time-slots-container')
  timeSlotsContainer.innerHTML = ''
  // Add flexbox styles to the container if not already done
  timeSlotsContainer.style.display = 'flex'
  timeSlotsContainer.style.flexWrap = 'wrap'
  timeSlotsContainer.style.gap = '10px'

  timeSlots.forEach((time) => {
    const button = document.createElement('button')
    button.classList.add('btn', 'time-slot-btn')
    button.textContent = time

    // Optionally, you can add active class to a default time slot
    // if (time === '03:00 PM') {
    //   button.classList.add('active')
    // }

    // Add click event listener for time slot selection
    button.addEventListener('click', function () {
      selectTimeSlot(time)
    })

    timeSlotsContainer.appendChild(button)
  })
}

function selectTimeSlot(time) {
  // Remove 'active' class from all time slot buttons
  const allButtons = document.querySelectorAll('.time-slot-btn')
  allButtons.forEach((button) => button.classList.remove('active'))

  // Add 'active' class to the selected button
  const selectedButton = Array.from(allButtons).find((button) => button.textContent === time)
  if (selectedButton) {
    selectedButton.classList.add('active')
  }

  state.selectedTime = time

  const submitButton = document.getElementById('submitAppointment')
  if (state.selectedTime && state.selectedPaymentMethod) {
    submitButton.disabled = false // Enable the button
  }
}

//uppy
// const uppy = new Uppy({
//   restrictions: {
//     maxFileSize: 20 * 1024 * 1024,
//     maxNumberOfFiles: 3, // Only allow 1 file
//     minNumberOfFiles: 1, // At least 1 file is required
//     allowedFileTypes: ['.pdf', '.png', '.jpg', '.jpeg']

//   },
//   autoProceed: false // Don't automatically start upload, handle manually
// }).use(window.Dashboard, {
//   inline: true,
//   target: '#uppy-dashboard',
//   replaceTargetContent: true,
//   showProgressDetails: true,
//   height: 300,
//   showLinkToFileUploadResult: false, // Hide the file upload result link
//   showSelectedFiles: true, // Optionally hide the selected files section
//   hideUploadButton: true // Hide the default upload button
// })

// // Handle the file added event (optional)
// uppy.on('file-added', (file) => {

//   state.uploadedFiles = []

//   state.uploadedFiles.push(file)
//   const fileObject = uppy.getFile(file.id) // Get the file object by ID
// })

// Uppy medical report upload
const uppy = new Uppy({
  restrictions: {
    maxFileSize: 20 * 1024 * 1024, // 20MB
    maxNumberOfFiles: 3,
    minNumberOfFiles: 0, // optional upload
    allowedFileTypes: ['.pdf', '.png', '.jpg', '.jpeg']
  },
  autoProceed: false
}).use(window.Dashboard, {
    inline: true,
    target: '#uppy-dashboard',
    replaceTargetContent: true,
    showProgressDetails: true,
    height: 300,
    hideUploadButton: true,
    showSelectedFiles: true,
    showLinkToFileUploadResult: false,
    proudlyDisplayPoweredByUppy: false,
    note: 'Allowed: PDF, PNG, JPG, JPEG • Maximum 20MB each • Maximum 3 files'
});

uppy.on('file-added', (file) => {
  if (!state.uploadedFiles) {
    state.uploadedFiles = [];
  }

  state.uploadedFiles.push(file);

  const errorBox = document.getElementById('medical-report-error');
  if (errorBox) {
    errorBox.textContent = '';
    errorBox.classList.add('d-none');
  }
});

uppy.on('file-removed', (file) => {
  state.uploadedFiles = (state.uploadedFiles || []).filter(
    uploadedFile => uploadedFile.id !== file.id
  );
});

uppy.on('restriction-failed', (file, error) => {
  const errorBox = document.getElementById('medical-report-error');

  if (errorBox) {
    errorBox.textContent = error?.message || 'Only PDF, PNG, JPG, JPEG files up to 20MB are allowed.';
    errorBox.classList.remove('d-none');
  }
});

document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
  radio.addEventListener('change', (event) => {
    state.selectedPaymentMethod = event.target.value
  })
})

function initializeSubmitButton() {
  const submitButton = document.getElementById('submitAppointment')

  if (submitButton) {
    submitButton.addEventListener('click', submitForm)
  }
}

async function handlePaymentMethodChange(totalAmount) {
  try {
    const response = await fetch(checkWalletBalanceUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken // Include CSRF token
      },
      body: JSON.stringify({ totalAmount: totalAmount })
    })

    if (!response.ok) {
      throw new Error('Failed to fetch wallet balance')
    }

    const data = await response.json()

    if (data.success) {
      if (state.payment.advance_payment_status == 1) {
        return data.balance >= state.payment.advance_payment_amount
      } else {
        return data.balance >= totalAmount
      }
    } else {
      submitButton.disabled = false

      throw new Error(data.message || 'Unable to fetch wallet balance.')
    }
  } catch (error) {
    submitButton.disabled = false

    // window.Snackbar('An error occurred while checking the wallet balance.');
    return false
  }
}

function capitalizeFirstLetter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

async function submitForm() {
  event.stopPropagation()

  const submitButton = document.getElementById('submitAppointment')

  submitButton.disabled = true

  if (state.selectedPaymentMethod === 'Wallet') {
    const amountToCheck = state.payment.advance_payment_status == 1
      ? state.payment.advance_payment_amount
      : state.totalAmount;

    const isSufficient = await handlePaymentMethodChange(amountToCheck);

    submitButton.disabled = false

    if (!isSufficient) {
      // Uncheck wallet payment method
      const walletPaymentMethod = document.querySelector('#method-Wallet');

      if (walletPaymentMethod) {
        walletPaymentMethod.checked = false;
      }

      state.selectedPaymentMethod = null;

      // Update the selected payment method display
      const selectedPaymentMethodDisplay = document.getElementById('selected-payment-method');
      if (selectedPaymentMethodDisplay) {
        selectedPaymentMethodDisplay.textContent = 'Select Payment Method';
      }

      Snackbar.show({
        text: 'Insufficient balance. Please add funds in wallet',
        pos: 'bottom-left',
        duration: 2500,
        showAction: false,
        backgroundColor: '#dc3545',
        actionTextColor: '#fff',
        textColor: '#fff'
      });
      return;
    }
  }

  // You can submit the state data using an Ajax request, or set it into a form field.
  const formData = new FormData()
  const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');

  // Add form fields from state
  formData.append('service_id', state.selectedService)
  formData.append('clinic_id', state.selectedClinic)
  formData.append('selectedDoctor', state.selectedDoctor)
  formData.append('appointment_date', state.selectedDate)
  formData.append('appointment_time', state.selectedTime)
  formData.append('selectedDoctorName', state.selectedDoctorName)
  formData.append('selectedServiceName', state.selectedServiceName)
  formData.append('transaction_type', state.selectedPaymentMethod)
  // formData.append('file_url', state.uploadedFiles[0]?.data)
  // Medical history
    formData.append(
        'appointment_extra_info',
        document.getElementById('appointment_extra_info').value
    );

    // Upload every selected file
    if (state.uploadedFiles && state.uploadedFiles.length) {
        state.uploadedFiles.forEach(file => {
            formData.append('file_url[]', file.data);
        });
    }

    // Upload all recorded audio IDs
    if (window.appointmentAudioIds && window.appointmentAudioIds.length) {
        window.appointmentAudioIds.forEach(id => {
            formData.append('audio_ids[]', id);
        });
    }
  formData.append('user_id', state.user_id)
  formData.append('status', state.status)
  formData.append('total_amount', state.totalAmount)
  formData.append('advance_payment_status', state.payment.advance_payment_status)
  formData.append('otherpatient_id', otherpatient_id)
  formData.append('appointment_extra_info', document.getElementById('appointment_extra_info').value)
  // Log formData

  // Submit the form via fetch or other methods
  fetch(routes.saveAppointment, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': csrfToken // Include CSRF token for Laravel
    }
  })
    .then((response) => response.json())
    .then((data) => {

      submitButton.disabled = false

      if (state.selectedPaymentMethod == 'cash' || state.selectedPaymentMethod == 'Wallet') {
        const paymentDetails = {
          doctorName: data.data.doctor_name || 'N/A',
          doctorExpert: data.data.doctor_expert || '',
          clinicName: data.data.clinic_name || 'N/A',
          serviceName: data.data.servicename || data.data.service_name || data.data.serviceName || state.selectedServiceName || 'Service Name Not Available',
          appointmentDate: data.data.formate_appointment_date || 'N/A',
          appointmentTime: state.selectedTime || 'N/A',
          bookingId: data.data.id || 'N/A',
          paymentVia: state.selectedPaymentMethod || 'N/A',
          currency: data.data.currency_symbol || 'N/A',
          totalAmount: data.data.total_amount || '0.00',
          advancepayment: data.data.advance_paid_amount || '0',
        }

        Swal.fire({
          // title: 'Appointment Submitted!ss',
          html: `

             <div class="container ">
                <div class="booking-sucssufully text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="228" height="150" viewBox="0 0 228 150" fill="none">
                        <circle cx="111.266" cy="62.542" r="37.0947" fill="#5670CC"/>
                        <path d="M126.111 52.2447C126.54 52.6524 126.791 53.2141 126.806 53.8062C126.822 54.3984 126.602 54.9725 126.194 55.4024L111.562 70.836C111.039 71.3859 110.411 71.8239 109.714 72.1234C109.017 72.4229 108.267 72.5778 107.508 72.5785H107.374C106.593 72.5585 105.825 72.3752 105.119 72.0405C104.413 71.7058 103.785 71.227 103.276 70.6349L96.9692 63.3108C96.5825 62.8618 96.3899 62.2776 96.4339 61.6867C96.4779 61.0957 96.7548 60.5465 97.2038 60.1597C97.6528 59.773 98.237 59.5804 98.8279 59.6244C99.4188 59.6684 99.9681 59.9453 100.355 60.3943L106.661 67.7207C106.763 67.8394 106.889 67.9353 107.03 68.0021C107.171 68.0689 107.325 68.1051 107.481 68.1083C107.638 68.117 107.795 68.0903 107.94 68.0301C108.085 67.97 108.214 67.878 108.319 67.7609L122.952 52.3274C123.154 52.1145 123.396 51.9434 123.664 51.8241C123.932 51.7047 124.221 51.6393 124.514 51.6316C124.807 51.6239 125.099 51.6741 125.373 51.7793C125.647 51.8845 125.898 52.0427 126.111 52.2447Z" fill="white"/>
                        <path d="M173.061 122.724C171.465 117.137 164.282 117.735 164.282 117.735L164.481 121.527C168.671 120.728 171.265 125.717 171.265 125.717L173.061 122.724Z" fill="#5670CC"/>
                        <path d="M32.8532 133.452C31.2569 127.865 24.0737 128.464 24.0737 128.464L24.2733 132.255C28.4635 131.457 31.0574 136.445 31.0574 136.445L32.8532 133.452Z" fill="#E0E0E0"/>
                        <path d="M55.1137 89.1406C51.0423 89.5692 50.3994 94.7121 50.3994 94.7121L53.1851 95.1406C53.1851 92.1406 57.0423 90.8549 57.0423 90.8549L55.1137 89.1406Z" fill="#5670CC"/>
                        <path d="M96.5987 141.82C91.1702 142.392 90.313 149.249 90.313 149.249L94.0273 149.82C94.0273 145.82 99.1701 144.106 99.1701 144.106L96.5987 141.82Z" fill="#5670CC"/>
                        <path d="M225.296 26.3295C226.002 21.8589 220.59 19.9766 220.59 19.9766L219.414 22.8001C222.708 23.7413 222.943 27.9766 222.943 27.9766L225.296 26.3295Z" fill="#5670CC"/>
                        <path d="M81.093 11.3549C81.7989 6.88433 76.3872 5.00195 76.3872 5.00195L75.2107 7.8255C78.5048 8.76668 78.7401 13.002 78.7401 13.002L81.093 11.3549Z" fill="#5670CC"/>
                        <path d="M0.29049 83.5883C-0.30951 89.5883 7.4905 90.9883 7.4905 90.9883L8.8905 86.9883C4.0905 86.5883 3.4905 80.9883 3.4905 80.9883L0.29049 83.5883Z" fill="#5670CC"/>
                        <path d="M197.344 77.9939C200.994 78.186 202.338 73.7683 202.338 73.7683L200.033 73C199.649 75.689 196 76.2653 196 76.2653L197.344 77.9939Z" fill="#E0E0E0"/>
                        <path d="M213.702 131.926C213.702 132.136 212.237 132.345 210.353 132.345C208.469 132.345 207.004 132.136 207.004 131.926C207.004 131.717 208.469 131.508 210.353 131.508C212.027 131.508 213.702 131.717 213.702 131.926Z" fill="#E0E0E0"/>
                        <path d="M217.278 122.715C217.488 122.715 217.697 124.18 217.697 126.064C217.697 127.948 217.488 129.413 217.278 129.413C217.069 129.413 216.86 127.948 216.86 126.064C216.86 124.18 217.069 122.715 217.278 122.715Z" fill="#E0E0E0"/>
                        <path d="M221.044 131.716C221.044 131.506 222.51 131.297 224.393 131.297C226.277 131.297 227.742 131.506 227.742 131.716C227.742 131.925 226.277 132.134 224.393 132.134C222.51 131.925 221.044 131.925 221.044 131.716Z" fill="#E0E0E0"/>
                        <path d="M217.484 140.716C217.274 140.716 217.065 139.25 217.065 137.367C217.065 135.483 217.274 134.018 217.484 134.018C217.693 134.018 217.902 135.483 217.902 137.367C217.693 139.25 217.693 140.716 217.484 140.716Z" fill="#E0E0E0"/>
                        <path d="M222.91 136.739C222.701 136.948 222.073 136.32 221.445 135.483C220.817 134.645 220.398 134.017 220.607 133.808C220.817 133.599 221.445 134.227 222.073 135.064C222.491 135.901 222.91 136.739 222.91 136.739Z" fill="#E0E0E0"/>
                        <path d="M222.503 126.48C222.712 126.69 222.084 127.318 221.456 127.946C220.828 128.574 220.2 128.992 219.991 128.992C219.781 128.783 220.409 128.155 221.037 127.527C221.665 126.899 222.293 126.48 222.503 126.48Z" fill="#E0E0E0"/>
                        <path d="M214.764 129.203C214.555 129.413 213.927 128.994 213.299 128.575C212.671 127.947 212.252 127.529 212.252 127.319C212.252 127.11 213.09 127.529 213.718 127.947C214.555 128.366 214.974 128.994 214.764 129.203Z" fill="#E0E0E0"/>
                        <path d="M214.546 133.598C214.756 133.807 214.337 134.435 213.709 135.063C213.081 135.691 212.453 136.109 212.244 136.109C212.035 135.9 212.453 135.272 213.081 134.644C213.709 134.016 214.337 133.598 214.546 133.598Z" fill="#E0E0E0"/>
                        <path d="M22.8263 29.4733C22.8263 29.6826 21.3612 29.8919 19.4775 29.8919C17.5938 29.8919 16.1287 29.6826 16.1287 29.4733C16.1287 29.264 17.5938 29.0547 19.4775 29.0547C21.3612 29.0547 22.8263 29.264 22.8263 29.4733Z" fill="#5670CC"/>
                        <path d="M26.3742 20.2637C26.5835 20.2637 26.7928 21.7288 26.7928 23.6125C26.7928 25.4962 26.5835 26.9613 26.3742 26.9613C26.1649 26.9613 25.9556 25.4962 25.9556 23.6125C26.1649 21.7288 26.3742 20.2637 26.3742 20.2637Z" fill="#5670CC"/>
                        <path d="M30.1458 29.2643C30.1458 29.055 31.6109 28.8457 33.4946 28.8457C35.3783 28.8457 36.8435 29.055 36.8435 29.2643C36.8435 29.4736 35.3783 29.6829 33.4946 29.6829C31.8202 29.4736 30.1458 29.2643 30.1458 29.2643Z" fill="#5670CC"/>
                        <path d="M26.7924 38.2641C26.5831 38.2641 26.3738 36.799 26.3738 34.9153C26.3738 33.0315 26.5831 31.5664 26.7924 31.5664C27.0017 31.5664 27.211 33.0315 27.211 34.9153C27.0017 36.799 26.7924 38.2641 26.7924 38.2641Z" fill="#5670CC"/>
                        <path d="M32.0348 34.2872C31.8255 34.4965 31.1976 33.8686 30.5697 33.0314C29.9417 32.1942 29.5231 31.5663 29.7324 31.357C29.9417 31.1477 30.5696 31.7756 31.1975 32.6128C31.8254 33.45 32.2441 34.2872 32.0348 34.2872Z" fill="#5670CC"/>
                        <path d="M31.8232 24.0313C32.0325 24.2406 31.4046 24.8685 30.7767 25.4964C30.1488 26.1243 29.5209 26.5429 29.3116 26.5429C29.1023 26.3336 29.7302 25.7057 30.3581 25.0778C30.986 24.4499 31.6139 23.822 31.8232 24.0313Z" fill="#5670CC"/>
                        <path d="M24.081 26.7519C23.8717 26.9612 23.2438 26.5426 22.6158 26.124C21.9879 25.4961 21.5693 25.0775 21.5693 24.8682C21.7786 24.6589 22.4065 25.0775 23.0344 25.4961C23.8717 25.9147 24.2903 26.5426 24.081 26.7519Z" fill="#5670CC"/>
                        <path d="M23.8709 31.1484C24.0802 31.3577 23.6616 31.9856 23.0337 32.6135C22.4058 33.2415 21.7779 33.6601 21.5686 33.6601C21.3593 33.4508 21.7779 32.8229 22.4058 32.1949C23.0337 31.567 23.6616 31.1484 23.8709 31.1484Z" fill="#5670CC"/>
                        <path d="M156.207 11.4769C165.81 14.5218 171.9 3.27911 171.9 3.27911L166.044 0C163.234 6.79248 153.396 6.08978 153.396 6.08978L156.207 11.4769Z" fill="#E0E0E0"/>
                        </svg>
                </div>
                <h5 class="my-3">Great, Appointment Successful!</h5>
                <h6 class="text-center">
                    ${(function() {
                        const isGP = paymentDetails.serviceName.toLowerCase().includes('gp');
                        const expert = (paymentDetails.doctorExpert || '').trim();
                        const doctorDisplay = 'Dr. ' + paymentDetails.doctorName + ((!isGP && expert) ? ' ' + expert : '');
                        const appointmentType = isGP ? 'private GP appointment' : 'private Specialist appointment';
                        return `<span class="text-body">Your ${appointmentType} with</span> <strong>${doctorDisplay}</strong><br>
                    <span class="text-body">at</span> <strong>Cosmo Doctors</strong><br>
                    <span class="text-body">has been confirmed for</span>
                    <strong>${paymentDetails.appointmentDate} <span class="text-body">at</span> ${paymentDetails.appointmentTime}</strong>.`;
                    })()}
                </h6>
                <!-- Booking Info -->
                    <div class="bg-primary-subtle border-none rounded-3 p-3 my-5">
                    <p class="mb-2 text-body">service name:
                         <a href="#" class="text-decoration-none fw-semibold">${paymentDetails.serviceName}</a></p>
                        <p class="mb-2 text-body">Booking ID:
                         <a href="#" class="text-decoration-none fw-semibold">#${paymentDetails.bookingId}</a></p>
                                <div class="d-flex gap-2 align-items-center justify-content-center">
                                  <p class="mb-0 text-body">Payment via: </p>
                                    <div class="d-flex gap-2 align-items-center">
                                    <h6 class="font-size-14 mb-0 fw-semibold">${capitalizeFirstLetter(paymentDetails.paymentVia)}</h6>
                                    </div>
                                </div>
                    </div>
                <span class="fw-bold text-body font-size-14">Total Payment</span>
                <h4 class="fw-semibold mb-5 pb-2 mt-2">
                  ${paymentDetails.currency}${(parseFloat(paymentDetails.advancepayment) > 0
              ? parseFloat(paymentDetails.advancepayment).toFixed(2)
              : parseFloat(paymentDetails.totalAmount).toFixed(2))}
                </h4>

            </div>
          `,
          confirmButtonText: 'Close',
          confirmButtonColor: 'var(--bs-secondary)',
          allowOutsideClick: false
        }).then((result) => {
          if (result.isConfirmed) {
            submitButton.disabled = false
            // Redirect to appointment detail page using the booking ID
            if (paymentDetails.bookingId && paymentDetails.bookingId !== 'N/A') {
              const redirectUrl = `${routes.appointmentDetails}/${paymentDetails.bookingId}`;
              console.log('Redirecting to appointment details:', redirectUrl);
              window.location.href = redirectUrl;
            } else {
              console.error('Invalid booking ID:', paymentDetails.bookingId);
              window.location.href = routes.appointmentList;
            }
          }
        })
      } else if (data.redirect) {
        submitButton.disabled = false
        window.location.href = data.redirect
      }
    })
    .catch((error) => {
      submitButton.disabled = false
      alert('There was an error submitting the form.')
    })
    .catch(error => {
      submitButton.disabled = false
      // Handle error
      console.error('Error saving appointment:', error);
      // Show error to user
      alert('Failed to save appointment: ' + error.message);
    });
}

{
  /* <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
            <strong>${paymentDetails.clinicName}</strong> has been successfully booked on
            <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
            <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime + 'Z').toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
            <div>
              <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
              <p><strong>Payment via:</strong> ${paymentDetails.paymentVia}</p>
              <p><strong>Total Payment:</strong> ${paymentDetails.currency} ${paymentDetails.totalAmount}</p>
            </div> */
}

{
  /* <p class="mb-0 text-body">Payment via: <svg
xmlns="http://www.w3.org/2000/svg" width="16"
height="16" fill="currentColor"
class="bi bi-stripe ms-2 me-1 mb-1"
viewBox="0 0 16 16">
<path
    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.226 5.385c-.584 0-.937.164-.937.593 0 .468.607.674 1.36.93 1.228.415 2.844.963 2.851 2.993C11.5 11.868 9.924 13 7.63 13a7.7 7.7 0 0 1-3.009-.626V9.758c.926.506 2.095.88 3.01.88.617 0 1.058-.165 1.058-.671 0-.518-.658-.755-1.453-1.041C6.026 8.49 4.5 7.94 4.5 6.11 4.5 4.165 5.988 3 8.226 3a7.3 7.3 0 0 1 2.734.505v2.583c-.838-.45-1.896-.703-2.734-.703" />
</svg>
<strong>${paymentDetails.paymentVia}</strong></p> */
}

// Make functions globally available for enhanced booking coordination
window.loadStepContent = loadStepContent;
window.currentStep = currentStep;
window.state = state;
window.fetchDynamicData = fetchDynamicData;
window.fetchAvailableTimeSlots = fetchAvailableTimeSlots;
window.initializeDateChange = initializeDateChange;
window.updateState = updateState;
