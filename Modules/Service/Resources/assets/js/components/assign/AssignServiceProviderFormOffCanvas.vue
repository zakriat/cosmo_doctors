<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="service-service-provider-assign-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom" v-if="service">
        <h6 class="m-0 h5">
          {{ $t('service.singular_title') }} : <span>{{ service.name }}</span>
        </h6>
      </div>

      <div class="offcanvas-body">
        <div class="form-group">
          <div class="d-grid">
            <div class="d-flex flex-column">
              <div class="form-group">
                <Multiselect v-model="assign_ids" placeholder="Select Service Provider" :canClear="false" :value="assign_ids" v-bind="service_providers" @select="serviceProviderSelect" @deselect="removeServiceProvider" id="service_providers_ids">
                  <template v-slot:multiplelabel="{ values }">
                    <div class="multiselect-multiple-label">{{ $t('service_providers.select_service_provider') }}</div>
                  </template>
                </Multiselect>
              </div>
            </div>
            <div class="list-group list-group-flush">
              <div v-for="(item, index) in selectedServiceProvider" :key="item" class="list-group-item">
                <div class="d-flex justify-between align-items-center flex-grow-1 gap-2 mt-2">
                  <span>{{ ++index }} - </span>
                  <div class="flex-grow-1">{{ item.name }}</div>
                  <button type="button" @click="removeServiceProvider(item.service_provider_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                </div>
                <div class="row mb-2">
                  <div class="d-flex justify-content-end align-items-center gap-2 col-6"><i class="fa-regular fa-clock"></i><input type="number" v-model="item.duration_min" class="form-control" /></div>
                  <div class="d-flex justify-content-end align-items-center gap-2 col-6">{{ CURRENCY_SYMBOL }}<input type="number" v-model="item.service_price" class="form-control" /></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer">
        <p class="text-center mb-0">
          <small> {{ $t('service_providers.assign_service_provider_to_service') }}</small>
        </p>
        <div class="d-grid gap-3 p-3">
          <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas">
            <i class="fa-solid fa-angles-left"></i>
            {{ $t('messages.close') }}
          </button>
          <button class="btn btn-primary d-block">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $t('messages.update') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { POST_SERVICE_PROVIDER_ASSIGN_URL, GET_SERVICE_PROVIDER_ASSIGN_URL, EDIT_URL } from '../../constant/service'
import { SERVICE_PROVIDER_LIST } from '@/vue/constants/service_provider'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'

// Request
const { listingRequest, getRequest, updateRequest } = useRequest()

// Vue Form Select START
// Select Option
const service_providers = ref({
  mode: 'multiple',
  searchable: true,
  options: []
})
const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
const selected_service_providers = ref([])
// Vue Form Select END

// Form Values
const assign_ids = ref([])
const service = ref(null)
const serviceId = useModuleId(() => {
  getRequest({ url: GET_SERVICE_PROVIDER_ASSIGN_URL, id: serviceId.value }).then((res) => {
    if (res.status && res.data) {
      selected_service_providers.value = res.data
      assign_ids.value = res.data.map((item) => item.service_provider_id)
    }
  })
  getRequest({ url: EDIT_URL, id: serviceId.value }).then((res) => res.status && (service.value = res.data))
}, 'service_provider_assign')

const serviceProvidersList = ref([])
onMounted(() => {
  listingRequest({ url: SERVICE_PROVIDER_LIST }).then((res) => {
    serviceProvidersList.value = res
    service_providers.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })
  })
})

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const errorMessages = ref([])
const reset_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Offcanvas.getInstance('#service-service-provider-assign-form').hide()
    renderedDataTable.ajax.reload(null, false)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formSubmit = () => {
  const data = { service_providers: [] }
  for (let index = 0; index < selected_service_providers.value.length; index++) {
    const element = selected_service_providers.value[index]
    data.service -
      providers.push({
        service_provider_id: element.service_provider_id,
        service_id: element.service_id,
        service_price: element.service_price,
        duration_min: element.duration_min
      })
  }
  updateRequest({ url: POST_SERVICE_PROVIDER_ASSIGN_URL, id: serviceId.value, body: data }).then((res) => reset_close_offcanvas(res))
}

const serviceProviderSelect = (value) => {
  const service_providers = serviceProvidersList.value.find((service_provider) => service_providers.id === value)
  const newServiceprovider = {
    name: service_providers.name,
    service_provider_id: service_providers.id,
    service_id: service.value.id,
    service_price: service.value.default_price,
    duration_min: service.value.duration_min
  }
  selected_service_providers.value = [...selected_service_providers.value, newServiceprovider]
}

const removeServiceProvider = (value) => {
  selected_service_providers.value = [...selected_service_providers.value.filter((service_providers) => service_providers.service_provider_id !== value)]
  assign_ids.value = [...assign_ids.value.filter((id) => id !== value)]
}
</script>
