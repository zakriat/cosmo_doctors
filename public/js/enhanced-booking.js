// Enhanced Booking Flow JavaScript - Category-Based Booking
// This file handles category selection and conditional doctor flow

// Global variables
let currentStep = 0;
let hasCategories = false;
let selectedCategoryId = null;
let categoryRequiresDoctor = true;
let selectedClinicId = null;
let selectedDoctorId = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Get values from PHP (these will be set by the blade template)
    if (typeof window.bookingConfig !== 'undefined') {
        currentStep = window.bookingConfig.currentStep || 0;
        hasCategories = window.bookingConfig.hasCategories || false;
        selectedCategoryId = window.bookingConfig.selectedCategoryId || null;
    }

    console.log('✅ Enhanced Booking Initialized:', {
        hasCategories,
        currentStep,
        selectedCategoryId
    });

    // Only coordinate if we have categories
    if (hasCategories) {
        initializeEnhancedFlow();
    }
});

function initializeEnhancedFlow() {
    console.log('🚀 Enhanced booking flow initialization');

    if (hasCategories && currentStep === 0) {
        // Let the category component handle step 0 directly
        console.log('✅ Category component handling step 0');

        // Listen for category selection
        document.addEventListener('categorySelected', function (event) {
            selectedCategoryId = event.detail.categoryId;
            categoryRequiresDoctor = event.detail.requiresDoctor;

            console.log('📢 Category selected:', {
                categoryId: selectedCategoryId,
                requiresDoctor: categoryRequiresDoctor
            });

            // Determine next step based on doctor requirement
            if (categoryRequiresDoctor) {
                // Go to doctor selection (clinic auto-selected)
                console.log('→ Category requires doctor, loading doctors...');
                currentStep = 2; // Skip clinic step (auto-selected)
                loadDoctorsForCategory(selectedCategoryId);
            } else {
                // Skip to datetime/payment
                console.log('→ Category doesn\'t require doctor, skipping to datetime...');
                currentStep = 3;
                loadDateTimeStep();
            }
        });

        return;
    }
}

/**
 * Load doctors filtered by category
 */
function loadDoctorsForCategory(categoryId) {
    console.log('🔍 Loading doctors for category:', categoryId);

    // Show loading state
    const stepContent = document.getElementById('step-content-1');
    if (stepContent) {
        stepContent.classList.remove('d-none');
        stepContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading doctors...</p>
            </div>
        `;
    }

    fetch(`/api/categories/${categoryId}/doctors`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Doctors loaded:', data.doctors.length);

                // Store clinic info (auto-selected)
                selectedClinicId = data.clinic.id;
                sessionStorage.setItem('selectedClinic', data.clinic.id);

                // Render doctors
                renderDoctorsForCategory(data.doctors, data.category, data.clinic);

                // Update step indicators
                updateStepIndicators();
            } else {
                showError('Failed to load doctors for this category');
            }
        })
        .catch(error => {
            console.error('❌ Error loading doctors:', error);
            showError('Error loading doctors. Please try again.');
        });
}

/**
 * Render doctors in the UI
 */
function renderDoctorsForCategory(doctors, category, clinic) {
    const stepContent = document.getElementById('step-content-1');

    if (!stepContent) {
        console.error('❌ Step content not found');
        return;
    }

    if (doctors.length === 0) {
        stepContent.innerHTML = `
            <div class="alert alert-warning">
                <i class="ph ph-warning me-2"></i>
                <strong>No doctors available</strong>
                <p class="mb-0 mt-2">There are no doctors available for ${category.name} at this time.</p>
            </div>
        `;
        return;
    }

    const doctorsHTML = `
        <div class="mb-4">
            <h6 class="mb-3">Select Doctor for ${category.name}</h6>
            <p class="text-muted small">Clinic: ${clinic.name} (Auto-selected)</p>
        </div>
        <div class="row g-3" id="doctors-container">
            ${doctors.map(doctor => `
                <div class="col-lg-6 col-md-6">
                    <div class="doctor-card card h-100 border-0 shadow-sm" 
                         data-doctor-id="${doctor.id}"
                         data-doctor-name="Dr. ${doctor.user.first_name} ${doctor.user.last_name}"
                         data-clinic-name="${clinic.name}"
                         style="cursor: pointer; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-semibold">
                                        Dr. ${doctor.user.first_name} ${doctor.user.last_name}
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        ${doctor.experience ? doctor.experience + ' years experience' : 'Experienced professional'}
                                    </p>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">
                                    £${doctor.category_charges || category.price}
                                </span>
                            </div>
                            <button class="btn btn-outline-primary btn-sm w-100">
                                Select Doctor
                            </button>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    stepContent.innerHTML = doctorsHTML;

    // Store clinic name for later use
    window.selectedClinicName = clinic.name;

    // Add click handlers
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.addEventListener('click', function () {
            const doctorId = this.dataset.doctorId;
            const doctorName = this.dataset.doctorName;
            const clinicName = this.dataset.clinicName;
            selectDoctor(doctorId, doctorName, clinicName);
        });
    });

    console.log('✅ Doctors rendered successfully');
}

/**
 * Handle doctor selection
 */
function selectDoctor(doctorId, doctorName, clinicName) {
    console.log('👨‍⚕️ Doctor selected:', doctorId, doctorName);

    // Highlight selected doctor
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary-subtle');
    });

    const selectedCard = document.querySelector(`[data-doctor-id="${doctorId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('border-primary', 'bg-primary-subtle');
    }

    // Store selection
    selectedDoctorId = parseInt(doctorId); // Convert to integer
    sessionStorage.setItem('selectedDoctor', doctorId);

    // Also store names for display
    window.selectedDoctorName = doctorName;
    window.selectedClinicName = clinicName;

    // Update appointment.js state immediately if available
    if (typeof state !== 'undefined') {
        state.selectedDoctor = parseInt(doctorId); // Store as integer
        state.selectedDoctorName = doctorName;
        state.selectedClinic = parseInt(selectedClinicId); // Store as integer
        state.selectedClinicName = clinicName;
        console.log('✅ State updated with doctor selection');
    }

    // Move to datetime/payment
    currentStep = 3;
    window.currentStep = 3; // Update global currentStep
    loadDateTimeStep();
}

/**
 * Load datetime step directly (for no-doctor categories or after doctor selection)
 */
function loadDateTimeStep() {
    console.log('📅 Loading datetime/payment step');

    // Hide other steps
    for (let i = 0; i <= 2; i++) {
        const step = document.getElementById(`step-content-${i}`);
        if (step) {
            step.classList.add('d-none');
        }
    }

    // Show datetime step
    const stepContent = document.getElementById('step-content-3');
    if (stepContent) {
        stepContent.classList.remove('d-none');

        // Load the datetime picker and time slots
        loadDateTimeContent();
    }

    // Update step indicators
    updateStepIndicators();
}

/**
 * Load datetime content - Initialize payment details and time slots
 * This is the CRITICAL missing function that bridges enhanced-booking.js and appointment.js
 */
function loadDateTimeContent() {
    console.log('🔄 Initializing datetime/payment content');

    // Update global currentStep
    window.currentStep = 3;
    currentStep = 3;

    // STEP 1: Synchronize state with appointment.js
    // Access the global state from appointment.js
    if (typeof state !== 'undefined') {
        console.log('✅ Found appointment.js state, updating...');

        // Update state with our selections (ensure integers)
        if (selectedDoctorId) {
            state.selectedDoctor = parseInt(selectedDoctorId);
            console.log('→ Updated state.selectedDoctor:', parseInt(selectedDoctorId));
        }

        if (selectedClinicId) {
            state.selectedClinic = parseInt(selectedClinicId);
            console.log('→ Updated state.selectedClinic:', parseInt(selectedClinicId));
        }

        if (selectedCategoryId) {
            state.selectedCategory = parseInt(selectedCategoryId);
            console.log('→ Updated state.selectedCategory:', parseInt(selectedCategoryId));
        }

        // STEP 2: Set default date to today if not already set
        if (!state.selectedDate) {
            const today = new Date().toISOString().split('T')[0];
            state.selectedDate = today;

            const dateInput = document.getElementById('appointment_date');
            if (dateInput) {
                dateInput.value = today;
                console.log('→ Set default date:', today);
            }
        }

        // STEP 3: Show payment container (remove d-none class)
        const paymentContainer = document.querySelector('.payment-container');
        if (paymentContainer) {
            paymentContainer.classList.remove('d-none');
            console.log('→ Payment container made visible');
        }

        // STEP 4: Fetch payment details (price, taxes, etc.)
        if (typeof fetchDynamicData === 'function') {
            console.log('→ Fetching payment details...');
            fetchDynamicData(state);
        } else {
            console.warn('⚠️ fetchDynamicData function not found');
        }

        // STEP 5: Fetch available time slots
        if (typeof fetchAvailableTimeSlots === 'function' && state.selectedDate) {
            console.log('→ Fetching time slots for date:', state.selectedDate);
            fetchAvailableTimeSlots(state.selectedDate);
        } else {
            console.warn('⚠️ fetchAvailableTimeSlots function not found or no date selected');
        }

        // STEP 6: Ensure date change listener is active
        if (typeof initializeDateChange === 'function') {
            // This might already be initialized, but calling it again is safe
            console.log('→ Date change listener ready');
        }

        console.log('✅ DateTime content loaded successfully');
        console.log('Current state:', {
            doctor: state.selectedDoctor,
            clinic: state.selectedClinic,
            category: state.selectedCategory,
            date: state.selectedDate,
            service: state.selectedService,
            currentStep: window.currentStep
        });

    } else {
        console.error('❌ appointment.js state not found! Cannot synchronize.');
        console.error('Make sure appointment.min.js is loaded before enhanced-booking.js');

        // Show error to user
        const stepContent = document.getElementById('step-content-3');
        if (stepContent) {
            stepContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ph ph-warning-circle me-2"></i>
                    <strong>Error loading booking form</strong>
                    <p class="mb-0 mt-2">Please refresh the page and try again.</p>
                </div>
            `;
        }
    }
}

/**
 * Update step indicators
 */
function updateStepIndicators() {
    document.querySelectorAll('.appointments-steps-item').forEach((item, index) => {
        item.classList.remove('active', 'complete');

        if (index < currentStep) {
            item.classList.add('complete');
        } else if (index === currentStep) {
            item.classList.add('active');
        }
    });
}

/**
 * Show error message
 */
function showError(message) {
    const stepContent = document.getElementById('step-content-1');
    if (stepContent) {
        stepContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="ph ph-warning-circle me-2"></i>
                ${message}
            </div>
        `;
    }
}

// Make functions globally available
window.enhancedBooking = {
    loadDoctorsForCategory,
    selectDoctor,
    loadDateTimeStep,
    hasCategories,
    currentStep,
    selectedCategoryId,
    selectedDoctorId
};
