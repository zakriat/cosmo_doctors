<template>
  <div class="forms">
    <div class="non-printable">
      <div class="d-flex justify-content-between align-items-center">
        <h4>{{ title }}</h4>
      </div>
    </div>
    <hr class="non-printable" />
    <component :is="dynamicComponent" @tab-change="nextTabChange" @on-reset="resetApp" :wizardNext="wizardNext" :wizardPrev="wizardPrev" :user_id="user_id"></component>
  </div>
</template>
<script setup>
// Library
import { computed, ref } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import {useQuickBooking} from '../store/quick-booking'

// Select Options List Request
 import { STORE_URL } from '@/vue/constants/quick_booking'

const {  storeRequest } = useRequest()


// Components
import NotFound from './NotFound.vue'
import SelectBranch from './BookingComponent/SelectBranch.vue'
import SelectDateTime from './BookingComponent/SelectDateTime.vue'
import SelectService from './BookingComponent/SelectService.vue'
import SelectSystemService from './BookingComponent/SelectSystemService.vue'
import SelectStaff from './BookingComponent/SelectStaff.vue'
import Confirmation from './BookingComponent/Confirmation.vue'
import CustomerDetail from './BookingComponent/CustomerDetail.vue'
import ConfirmationDetail  from './BookingComponent/ConfirmationDetail.vue'
import SelectsinglevendorService  from './BookingComponent/SelectsinglevendorService.vue'
import EmailVerification  from './BookingComponent/EmailVerification.vue'





const IS_RESETTING = ref(false)

const props = defineProps({
  wizardNext: {
    default: '',
    type: [String, Number]
  },
  wizardPrev: {
    default: '',
    type: [String, Number]
  },
  type: {
    type: String
  },
  title: {
    type: String
  },
  user_id: [String, Number]
})

const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}


  const dynamicComponent = computed(() => {
  switch (props.type) {
    case 'select-branch':
      return SelectBranch
      break

    case 'select-date-time':
      return SelectDateTime
      break

     case 'select-system-service':
      return SelectSystemService
      break

      case 'select-singal_vendor-service':
      return SelectsinglevendorService
      break  

      case 'select-service':
      return SelectService
      break

       case 'verify_email':
      return EmailVerification
      break

    case 'select-employee':
      return SelectStaff
      break

    case 'select-confirm':
      return Confirmation
      break

    case 'customer-details':
      return CustomerDetail
      break

    case 'confirmation-detail':
      return ConfirmationDetail
      break

    default:
      return NotFound
      break
  }
})


const emit = defineEmits(['onClick'])

const store = useQuickBooking()
const booking = computed(() => store.booking)
const user = computed(() => store.user)
const nextTabChange = (value) => {

 if ((enable_multi_vendor() == 1 && value == 7) || (enable_multi_vendor() != 1 && value == 6)) {
    const body = {
      user: user.value,
      booking: booking.value
    };
    storeRequest({ url: STORE_URL, body: body }).then((res) => {
      store.updateBookingResponse(res.data);
      emit('onClick', value);
    });
  } else {
    emit('onClick', value);
  }


}





const resetApp = () => {
  store.resetState()
  emit('onClick', 1)
}
</script>
