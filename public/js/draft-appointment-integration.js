/**
 * Integration hooks for draft appointment auto-save
 * This file bridges appointment.js with draft-appointment.js
 */

// Wait for both appointment.js and draft-appointment.js to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔗 Initializing draft appointment integration');
    
    // Wait a bit for appointment.js to fully initialize
    setTimeout(() => {
        setupDraftIntegration();
    }, 500);
});

function setupDraftIntegration() {
    if (!window.state || !window.draftAppointment) {
        console.warn('⚠️ State or draft module not available yet');
        return;
    }
    
    console.log('✅ Draft integration ready');
    
    // Create a state watcher using Proxy (if supported)
    if (typeof Proxy !== 'undefined') {
        const originalState = window.state;
        window.state = new Proxy(originalState, {
            set: function(target, property, value) {
                const oldValue = target[property];
                target[property] = value;
                
                // Only save if value actually changed and it's a selection property
                if (oldValue !== value && ['selectedClinic', 'selectedDoctor', 'selectedCategory', 'selectedDate', 'selectedTime'].includes(property)) {
                    console.log(`📝 State changed: ${property} = ${value}`);
                    setTimeout(() => window.draftAppointment.save(true), 100);
                }
                
                return true;
            }
        });
        console.log('✅ State proxy watcher installed');
    }
    
    // Also track currentStep changes
    let lastStep = window.currentStep;
    setInterval(() => {
        if (window.currentStep !== lastStep) {
            console.log(`📝 Step changed: ${lastStep} → ${window.currentStep}`);
            lastStep = window.currentStep;
            setTimeout(() => window.draftAppointment.save(true), 100);
        }
    }, 500);
    
    // Hook into updateState function if it exists
    if (typeof window.updateState === 'function') {
        const originalUpdateState = window.updateState;
        window.updateState = function(newState) {
            originalUpdateState(newState);
            // Auto-save after state update
            setTimeout(() => window.draftAppointment.save(true), 100);
        };
        console.log('✅ Hooked into updateState');
    }
    
    // Hook into updateClinicSelection
    if (typeof window.updateClinicSelection === 'function') {
        const originalUpdateClinicSelection = window.updateClinicSelection;
        window.updateClinicSelection = function(card) {
            originalUpdateClinicSelection(card);
            // Auto-save after clinic selection
            setTimeout(() => window.draftAppointment.save(true), 100);
        };
        console.log('✅ Hooked into updateClinicSelection');
    }
    
    // Hook into updateDoctorSelection
    if (typeof window.updateDoctorSelection === 'function') {
        const originalUpdateDoctorSelection = window.updateDoctorSelection;
        window.updateDoctorSelection = function(card) {
            originalUpdateDoctorSelection(card);
            // Auto-save after doctor selection
            setTimeout(() => window.draftAppointment.save(true), 100);
        };
        console.log('✅ Hooked into updateDoctorSelection');
    }
    
    // Hook into enhanced-booking.js selectDoctor if it exists
    if (window.enhancedBooking && typeof window.enhancedBooking.selectDoctor === 'function') {
        const originalSelectDoctor = window.enhancedBooking.selectDoctor;
        window.enhancedBooking.selectDoctor = function(doctorId, doctorName, clinicName) {
            originalSelectDoctor(doctorId, doctorName, clinicName);
            // Auto-save after doctor selection in enhanced flow
            setTimeout(() => window.draftAppointment.save(true), 100);
        };
        console.log('✅ Hooked into enhanced booking selectDoctor');
    }
    
    // Listen for category selection events
    document.addEventListener('categorySelected', function(event) {
        console.log('📢 Category selected event detected');
        setTimeout(() => window.draftAppointment.save(true), 100);
    });
    
    // Hook into date selection
    const dateInput = document.getElementById('appointment_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            console.log('📅 Date changed');
            window.state.selectedDate = this.value;
            window.draftAppointment.save(true);
        });
        console.log('✅ Hooked into date input');
    }
    
    // Hook into time slot selection - use event delegation
    document.addEventListener('click', function(e) {
        const timeSlot = e.target.closest('.time-slot');
        if (timeSlot) {
            console.log('⏰ Time slot clicked');
            setTimeout(() => window.draftAppointment.save(true), 500);
        }
    });
    
    // Monitor for clinic checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('clinic-checkbox')) {
            console.log('🏥 Clinic checkbox changed');
            setTimeout(() => window.draftAppointment.save(true), 200);
        }
        if (e.target.classList.contains('doctor-checkbox')) {
            console.log('👨‍⚕️ Doctor checkbox changed');
            setTimeout(() => window.draftAppointment.save(true), 200);
        }
    });
    
    // Hook into successful appointment submission
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const url = args[0];
        
        // If this is the save appointment call
        if (typeof url === 'string' && url.includes('save-appointment')) {
            return originalFetch.apply(this, args).then(response => {
                // Clone response to read it
                const clonedResponse = response.clone();
                
                clonedResponse.json().then(data => {
                    if (data.success || data.status) {
                        console.log('✅ Appointment saved successfully, cleaning up draft');
                        window.draftAppointment.delete();
                    }
                }).catch(() => {
                    // Ignore JSON parse errors
                });
                
                return response;
            });
        }
        
        return originalFetch.apply(this, args);
    };
    console.log('✅ Hooked into fetch for appointment submission');
}

console.log('✅ Draft appointment integration module loaded');
