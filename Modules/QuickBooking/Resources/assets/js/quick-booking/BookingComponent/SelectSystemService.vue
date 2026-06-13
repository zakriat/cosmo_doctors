<template>
    <div class="card-list-data">
  
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3" v-if="!IS_LOADER && enable_multi_vendor() "> 
              <template  class="col" v-for="(item, index) in systemserviceList" :key="item">
                  <div class="iq-widget">
                      <input type="radio" :id="item.name + item.id" v-model="system_service_id" :value="item.id" name="radio" class="btn-check" @change="onChange"/>
                      <label :for="item.name + item.id" class="d-block w-100">
                          <div class="card iq-service-box text-center">
                              <div class="card-body">
                                  <div>
                                    <div class="branch-image">
                                      <img :src="item.file_url" class="avatar-70 rounded-circle" alt="feature-image" loading="lazy">
                                  </div>
                                      <h5 class="mb-2">{{ item.name }}</h5>
              
                                  </div>
                               
                              </div>
                          </div>
                      </label>
                  </div>
              </template>
          </div>
  
      <div v-if="systemserviceList.length == 0 && IS_LOADER">
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
      <div v-else-if="systemserviceList.length == 0 && !IS_LOADER" class="h-100 w-75 d-flex align-items-center justify-content-center">
        We apologize for any inconvenience caused. Unfortunately, the selected salon branch does not offer the service you are looking for at the moment.
      </div>
    </div>
    <div class="card-footer">
      <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">Back</button>
      <button type="button" v-if="wizardNext" class="btn btn-primary iq-text-uppercase" :disabled="service_id !== null ? false : true" @click="nextTabChange(wizardNext)">Next</button>
    </div>
  </template>
  
  <script setup>
  import { ref,onMounted,watch} from 'vue'
  import { useRequest } from '@/helpers/hooks/useCrudOpration'
  
  // Select Options List Request
  import { SYSTEM_SERVICE_LIST } from '@/vue/constants/quick_booking'
//   import { SERVICE_LIST } from '@/vue/constants/quick_booking'
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
  
  const system_service_id=ref(0);
  
  const enable_multi_vendor = () => {
    return window.multiVendorEnabled
  }
  
  const {  listingRequest } = useRequest()
  const store = useQuickBooking()
  const IS_LOADER = ref(true)
  

  const systemserviceList=ref([]);
  
  onMounted(() => {
     getSystemServiceList()
  })
  
  const getSystemServiceList = () => {
      IS_LOADER.value = true
      listingRequest({ url: SYSTEM_SERVICE_LIST, data: { user_id: props.user_id }}).then((res) => {
          IS_LOADER.value = false
          systemserviceList.value = res.data
    })
  }

// On Change Next
const onChange = () => {
    emit('tab-change', props.wizardNext)
}
// Next & Prev Function
const nextTabChange = (val) => (emit('tab-change', val))
const prevTabChange = (val) => {
    resetData()
    emit('tab-change', val)
}

// Reset Data Function
const resetData = () => {
    system_service_id.value = null
}

// Watch
watch(() => props.user_id, (value) => {
  alert(props.user_id);
  store.updateBookingValues(
    {key: 'vendor_id',value: value}
  )
}, {deep: true})

watch(() => system_service_id.value, (value) => {
  store.updateBookingValues(
    {key: 'system_service_id',value: value}
  )
}, {deep: true})


watch(() => store.bookingResponse, (value) => {
  resetData()
}, {deep: true})
  
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
  