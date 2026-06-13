<template>
  <div class="card-list-data">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3" v-if="!IS_LOADER">
        <template v-for="(serviceItem, index) in serviceList" :key="`services-${index}`">
            <div class="iq-widget">
                <input type="radio" :id="serviceItem.slug + serviceItem.id" v-model="service_id" :value="serviceItem.id" name="radio" class="btn-check" @change="onChange"/>
                <label :for="serviceItem.slug + serviceItem.id" class="d-block w-100">
                    <div class="card iq-service-box text-center">
                        <div class="card-body">
                          <div class="branch-image">
                            <img :src="serviceItem.service_image" class="avatar-70 rounded-circle" alt="feature-image" loading="lazy">
                          </div>
                            <div>
                                <h5 class="mb-2">{{ serviceItem.name }}</h5>
                                <p class="m-0 mt-3">{{ serviceItem.duration }} min</p>
                            </div>
                            <div class="service-price mt-3" v-if="serviceItem.discount_amount>0">
                                <b>{{ formatCurrencyVue(serviceItem.payable_amount) }}
                                    <span v-if="serviceItem.discount_type=='percentage'">
                                        ({{ serviceItem.discount_value }} %) off
                                    </span>
                                    <span v-else >
                                        ({{ formatCurrencyVue(serviceItem.discount_value) }}) off
                                    </span> 
                                </b>
                                <del> {{ formatCurrencyVue(serviceItem.price) }}</del> 
                            </div>
                            <div v-else class="service-price mt-3" >
                              <b>{{ formatCurrencyVue(serviceItem.payable_amount) }}</b>
                          </div>

                          <p class="m-0 mt-3" v-if="enable_multi_vendor()==1 && props.user_id ==null"> By {{ serviceItem.vendor_name }} </p>

                          
                        </div>
                    </div>
                </label>
            </div>
        </template>
    </div>

  <div v-if="serviceList.length == 0 && IS_LOADER">
      <h5 class="skeleton skeleton-title w-25 ms-0 mb-4"></h5>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3" v-if="IS_LOADER">
        <div class="col" v-for="index in 9" :key="index">
          <div class="iq-widget card card-skeleton text-center">
            <div class="card-body text-center pt-5 pb-5">
                <h5 class="skeleton skeleton-title w-100 mb-2"></h5>
                <p class="skeleton skeleton-text w-50 m-auto mt-3"></p>
                <div class="skeleton skeleton-badge mt-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="serviceList.length == 0 && !IS_LOADER" class="h-100 w-75 d-flex align-items-center justify-content-center">
      We apologize for any inconvenience caused. Unfortunately, the selected salon branch does not offer the service you are looking for at the moment.
    </div>
  </div>
  <div class="card-footer">
    <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">Back</button>
    <button type="button" v-if="wizardNext" class="btn btn-primary iq-text-uppercase" :disabled="service_id !== null ? false : true" @click="nextTabChange(wizardNext)">Next</button>
  </div>
</template>

<script setup>
import { ref,onMounted,watch } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'

// Select Options List Request
import { SERVICE_LIST } from '@/vue/constants/quick_booking'
import {useQuickBooking} from '../../store/quick-booking'
const props = defineProps({
  wizardNext: {
    default: '',
    type: [String, Number]
  },
  wizardPrev: {
    default: '',
    type: [String, Number]
  },
  user_id: [String, Number]
})
const emit = defineEmits(['tab-change', 'onReset'])
const formatCurrencyVue = (value) => {
  if(window.currencyFormat !== undefined && value) {
    return window.currencyFormat(value)
  }
  return value
}

const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}

const {  listingRequest } = useRequest()
const store = useQuickBooking()
const IS_LOADER = ref(true)

const serviceList = ref([]);


if(enable_multi_vendor()==1){

   const getService = () => {
    IS_LOADER.value = true
    if(store.booking.system_service_id !== null || props.user_id !== null) {
      listingRequest({ url: SERVICE_LIST, data: { user_id: props.user_id, system_service_id: store.booking.system_service_id}}).then((res) => {
        IS_LOADER.value = false
        serviceList.value = res.data
      })
    }
  }

  watch(() => store.booking.system_service_id, () => {
    getService()
  })
  
}


const service_id = ref(null)
watch(() => service_id.value, (value) => {

    let services = Object.values(serviceList.value)

     const foundService = services.find(service => service.id === value);
  
      if(foundService) {
        store.updateBookingValues({
          key: 'services',
          value: [{
            doctor_id: null,
            service_id: foundService.id,
            service_name:foundService.name,
            service_price: foundService.charges,
            duration_min: foundService.duration,
            discount_amount: foundService.discount_amount,
            discount_type: foundService.discount_type,
            discount_value: foundService.discount_value,
            payable_amount: foundService.payable_amount,
            vendor_name: foundService.vendor_name,
            start_date_time: store.booking.start_date_time
          }]
        });
      }
  
}, {deep: true})
watch(() => store.bookingResponse, (value) => {
  resetData()
}, {deep: true})


// On Change Next
const onChange = () => {

    emit('tab-change', props.wizardNext)
}
const nextTabChange = (val) => (emit('tab-change', val))
const prevTabChange = (val) => {
  resetData()
  emit('tab-change', val)
}
const resetData = () => {
  store.updateBookingValues({
    key: 'services',
    value: [{
      duration_min: null,
      doctor_id: store.booking.employee_id,
      service_id: null,
      service_name:null,
      discount_amount:0,
      discount_type:null,
      discount_value:null,
      payable_amount:null,
      service_price: null,
      vendor_name: null,
      start_date_time: store.booking.start_date_time
    }]
  });
  service_id.value = null
}
</script>

<style scoped>

    .card-list-data {
        position: relative;
        padding-top: 10px;
        padding-right: 10px;
    }
    .card.iq-service-box {
        cursor: pointer;
        border: 1px solid #ECECEC;
        background: var(--bs-white);
        border-radius: 10px;
        transition: all 0.5s ease-in-out;

    }
    .card.iq-service-box .card-body {
        padding: 30px 15px;
    }

    .card.iq-service-box:hover {
        border-color: var(--bs-primary);
        transform: translateY(-5px);
    }

    .iq-service-box::after {
        position: absolute;
        content: "";
        background: var(--bs-primary) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
        height: 23px;
        width: 23px;
        border: 2px solid var(--bs-white);
        top: -7px;
        left: auto;
        right: -7px;
        border-radius: 100%;
        opacity: 0;
        transition: all 0.5s ease-in-out;
    }

    .iq-widget .btn-check:checked + label .iq-service-box::after {
        opacity: 1;
    }

    .service-price {
        color: #19235A;
        background: #FCF2E3;
        border-radius: 26px;
        padding: 6px 16px;
        display: inline-block;
    }

    .iq-widget .btn-check:checked + label .iq-service-box {
        background: var(--bs-primary);
        color: var(--bs-white);
    }

    .iq-widget .btn-check:checked + label .iq-service-box h5 {
        color: var(--bs-white);
    }
    .iq-widget .btn-check:checked + label .iq-service-box .service-price {
        background: var(--bs-white);
    }
</style>
