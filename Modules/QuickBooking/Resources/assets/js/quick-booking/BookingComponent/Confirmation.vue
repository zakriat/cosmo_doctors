<template>
  <div class="card-list-data">
    <div class="text-center">
      <div class="mb-5">
        <div class="success-animation">
          <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" /><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" /></svg>
        </div>
      </div>
      <h5>Your Appointment is Booked Sucessfully!</h5>
      <p class="mt-3">Please check your email for verification</p>
      <hr />
      <!-- Quick summary right on confirmation -->
      <div v-if="booking" class="text-start mx-auto" style="max-width: 680px;">
        <div class="iq-card iq-card-border p-3 mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Clinic</h6>
            <h6 class="mb-0 text-primary">{{ booking.clinic_name ?? '-' }}</h6>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span>Date & Time</span>
            <span class="text-primary">{{ moment(booking.appointment_date).format('MMMM DD, YYYY') }} {{ booking.appointment_time }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span>Service</span>
            <span class="text-primary">{{ booking.service_name }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span>Doctor</span>
            <span class="text-primary">{{ booking.doctor_name }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
            <h6 class="mb-0">Total</h6>
            <h6 class="mb-0 text-primary">{{ formatCurrencyVue(booking.total_amount) }}</h6>
          </div>
        </div>
      </div>
      <div class="non-printable">
        <div class="d-flex flex-wrap gap-1 justify-content-center">
          <button type="button" class="btn btn-primary d-flex gap-3" @click="reset">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Book More Appointments</span>
          </button>
          <button type="button" class="btn btn-secondary d-flex gap-3" @click="nextTabChange(wizardNext)">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>View detail</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue'
import moment from 'moment'
import { useQuickBooking } from '../../store/quick-booking'
const props = defineProps({
  wizardNext: {
    default: '',
    type: [String, Number]
  },
  wizardPrev: {
    default: '',
    type: [String, Number]
  },
})
const emit = defineEmits(['tab-change','onReset'])
const reset = () => {
  emit('onReset')
}

const nextTabChange = (val) => (emit('tab-change', val))

// Store booking summary for confirmation view
const store = useQuickBooking()
const booking = computed(() => store.bookingResponse)

const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined && value !== null && value !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}
</script>
<style scoped>
  #select-confirm .card-list-data {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position:relative;
    top: 5px;
    right: 5px;
   margin: 0 auto;
}
.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4bb71b;
    fill: #fff;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;

}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }

    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0px 0px 0px 30px #4bb71b;
    }
}
</style>
