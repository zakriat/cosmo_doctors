/**
 * Draft Appointment Auto-Save Functionality
 * Automatically saves booking progress as user navigates through steps
 */

let draftId = null;
let saveDraftTimeout = null;

/**
 * Initialize draft functionality
 */
function initializeDraftSaving() {
    console.log('🔄 Draft auto-save initialized');
    
    // Check if there's a draft to resume from URL
    const urlParams = new URLSearchParams(window.location.search);
    const resumeDraftId = urlParams.get('resume');
    
    if (resumeDraftId) {
        resumeDraft(resumeDraftId);
    }
}

/**
 * Save draft appointment
 * Debounced to avoid too many API calls
 */
function saveDraft(immediate = false) {
    // Clear existing timeout
    if (saveDraftTimeout) {
        clearTimeout(saveDraftTimeout);
    }
    
    // If immediate, save right away, otherwise debounce
    const delay = immediate ? 0 : 1000;
    
    saveDraftTimeout = setTimeout(() => {
        performDraftSave();
    }, delay);
}

/**
 * Perform the actual draft save
 */
function performDraftSave() {
    // Don't save if user is not authenticated
    if (!window.state || !window.state.user_id) {
        console.log('⚠️ User not authenticated, skipping draft save');
        return;
    }
    
    // Don't save if no service selected
    if (!window.state.selectedService) {
        console.log('⚠️ No service selected, skipping draft save');
        return;
    }
    
    // Get current step - check both window.currentStep and state
    let currentStepValue = 0;
    if (typeof window.currentStep !== 'undefined') {
        currentStepValue = window.currentStep;
    } else if (window.state && window.state.currentStep) {
        currentStepValue = window.state.currentStep;
    }
    
    const draftData = {
        service_id: parseInt(window.state.selectedService) || null,
        category_id: parseInt(window.state.selectedCategory) || null,
        clinic_id: parseInt(window.state.selectedClinic) || null,
        doctor_id: parseInt(window.state.selectedDoctor) || null,
        appointment_date: window.state.selectedDate || null,
        appointment_time: window.state.selectedTime || null,
        current_step: currentStepValue,
        booking_data: {
            selectedService: parseInt(window.state.selectedService) || null,
            selectedCategory: parseInt(window.state.selectedCategory) || null,
            selectedCategoryName: window.state.selectedCategoryName,
            selectedClinic: parseInt(window.state.selectedClinic) || null,
            selectedClinicName: window.state.selectedClinicName,
            selectedDoctor: parseInt(window.state.selectedDoctor) || null,
            selectedDoctorName: window.state.selectedDoctorName,
            selectedDate: window.state.selectedDate,
            selectedTime: window.state.selectedTime,
            selectedPaymentMethod: window.state.selectedPaymentMethod,
            totalAmount: window.state.totalAmount || 0,
            currentStep: currentStepValue
        }
    };
    
    console.log('💾 Saving draft...', draftData);
    
    fetch('/api/draft-appointments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(draftData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            draftId = data.draft_id;
            console.log('✅ Draft saved successfully', { draft_id: draftId });
            
            // Show subtle notification (optional)
            showDraftSavedIndicator();
        } else {
            console.error('❌ Failed to save draft:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ Error saving draft:', error);
    });
}

/**
 * Resume draft appointment
 */
function resumeDraft(id) {
    console.log('🔄 Resuming draft:', id);
    
    fetch(`/api/draft-appointments/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const draft = data.data;
            draftId = draft.id;
            
            console.log('✅ Draft loaded:', draft);
            
            // Restore state from draft
            if (draft.booking_data) {
                window.state = {
                    ...window.state,
                    ...draft.booking_data
                };
            }
            
            // Set current step
            if (draft.current_step !== undefined) {
                window.currentStep = draft.current_step;
            }
            
            // Hide all previous step content divs
            for (let i = 0; i < draft.current_step; i++) {
                const stepContent = document.getElementById(`step-content-${i}`);
                if (stepContent) {
                    stepContent.classList.add('d-none');
                    stepContent.innerHTML = ''; // Clear content
                    console.log(`→ Hidden step-content-${i}`);
                }
            }
            
            // Show only the current step
            const currentStepContent = document.getElementById(`step-content-${draft.current_step}`);
            if (currentStepContent) {
                currentStepContent.classList.remove('d-none');
                console.log(`→ Showing step-content-${draft.current_step}`);
            }
            
            // Show notification using available method
            if (typeof Snackbar !== 'undefined') {
                Snackbar.show({
                    text: 'Resuming your previous booking...',
                    pos: 'bottom-left',
                    duration: 3000,
                    showAction: false,
                    backgroundColor: '#28a745',
                    actionTextColor: '#fff',
                    textColor: '#fff'
                });
            } else if (typeof window.successSnackbar === 'function') {
                window.successSnackbar('Resuming your previous booking...');
            } else {
                console.log('✅ Resuming your previous booking...');
            }
            
            // Update UI to reflect restored state
            if (typeof updateActiveStep === 'function') {
                updateActiveStep();
            }
            
            // Load the appropriate step content
            if (typeof loadStepContent === 'function') {
                loadStepContent(window.currentStep);
            }
            
            // If on datetime step (step 3), ensure payment container is visible
            if (draft.current_step === 3) {
                const paymentContainer = document.querySelector('.payment-container');
                if (paymentContainer) {
                    paymentContainer.classList.remove('d-none');
                    console.log('→ Payment container made visible');
                }
                
                // Fetch payment data and time slots
                if (typeof fetchDynamicData === 'function' && window.state) {
                    console.log('→ Fetching payment details...');
                    fetchDynamicData(window.state);
                }
                
                if (typeof fetchAvailableTimeSlots === 'function' && window.state.selectedDate) {
                    console.log('→ Fetching time slots...');
                    fetchAvailableTimeSlots(window.state.selectedDate);
                }
            }
            
        } else {
            console.error('❌ Failed to load draft:', data.message);
            
            // Show error using available method
            if (typeof Snackbar !== 'undefined') {
                Snackbar.show({
                    text: 'Could not resume booking. Starting fresh.',
                    pos: 'bottom-left',
                    duration: 3000,
                    showAction: false,
                    backgroundColor: '#dc3545',
                    actionTextColor: '#fff',
                    textColor: '#fff'
                });
            } else if (typeof window.errorSnackbar === 'function') {
                window.errorSnackbar('Could not resume booking. Starting fresh.');
            }
        }
    })
    .catch(error => {
        console.error('❌ Error loading draft:', error);
    });
}

/**
 * Delete draft after successful booking
 */
function deleteDraftAfterBooking() {
    if (!window.state.selectedService) {
        return;
    }
    
    console.log('🗑️ Cleaning up draft after successful booking');
    
    fetch('/api/draft-appointments/cleanup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            service_id: window.state.selectedService
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Draft cleaned up successfully');
            draftId = null;
        }
    })
    .catch(error => {
        console.error('❌ Error cleaning up draft:', error);
    });
}

/**
 * Show subtle indicator that draft was saved
 */
function showDraftSavedIndicator() {
    // Create or update indicator
    let indicator = document.getElementById('draft-saved-indicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'draft-saved-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s;
        `;
        indicator.innerHTML = '<i class="ph ph-check-circle"></i> Progress saved';
        document.body.appendChild(indicator);
    }
    
    // Show indicator
    indicator.style.opacity = '1';
    
    // Hide after 2 seconds
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

/**
 * Hook into state changes to auto-save
 */
function watchStateChanges() {
    // Save draft when category is selected
    if (window.state && window.state.selectedCategory) {
        saveDraft();
    }
    
    // Save draft when clinic is selected
    if (window.state && window.state.selectedClinic) {
        saveDraft();
    }
    
    // Save draft when doctor is selected
    if (window.state && window.state.selectedDoctor) {
        saveDraft();
    }
    
    // Save draft when date/time is selected
    if (window.state && (window.state.selectedDate || window.state.selectedTime)) {
        saveDraft();
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDraftSaving);
} else {
    initializeDraftSaving();
}

// Export functions for use in other scripts
window.draftAppointment = {
    save: saveDraft,
    resume: resumeDraft,
    delete: deleteDraftAfterBooking,
    watch: watchStateChanges
};

console.log('✅ Draft appointment module loaded');
