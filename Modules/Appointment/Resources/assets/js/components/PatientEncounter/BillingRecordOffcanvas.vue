<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group" v-if="!selectedEncounter && currentId === 0">
              <label class="form-label">{{ $t('clinic.lbl_encounter_select') }} <span class="text-danger">*</span></label>
              <Multiselect id="encounter_id" v-model="encounter_id" :value="encounter_id" :placeholder="$t('clinic.lbl_encounter_select')" v-bind="singleSelectOption" :options="encounterlist.options" class="form-group"></Multiselect>
              <span v-if="errorMessages['encounter_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['encounter_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors['encounter_id'] }}</span>
            </div>

            <div class="row" v-if="selectedEncounter || currentId > 0">
              <div class="d-flex align-items-start gap-4 mb-4">
                <div class="flex-grow-1">
                  <div class="gap-2">
                    <h5 v-if="selectedEncounter">{{ selectedEncounter?.text }}</h5>
                  </div>
                </div>
                <button type="button" @click="removeEncounter()" class="text-danger bg-transparent border-0"><i class="ph ph-trash"></i></button>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label col-md-6">{{ $t('clinic.lbl_select_clinic') }} <span class="text-danger">*</span></label>
                <Multiselect id="clinic_id" v-model="clinic_id" :value="clinic_id" :placeholder="$t('clinic.lbl_select_clinic')" v-bind="singleSelectOption" :options="cliniclist.options" class="form-group" :disabled="selectedEncounter"></Multiselect>
                <span v-if="errorMessages['clinic_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['clinic_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['clinic_id'] }}</span>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label">{{ $t('clinic.lbl_select_doctor') }} <span class="text-danger">*</span></label>
                <Multiselect id="doctor_id" v-model="doctor_id" :value="doctor_id" :placeholder="$t('clinic.lbl_select_doctor')" v-bind="singleSelectOption" :options="doctorlist.options" @select="getServiceList" class="form-group" :disabled="selectedEncounter"></Multiselect>
                <span v-if="errorMessages['doctor_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['doctor_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['doctor_id'] }}</span>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label">{{ $t('clinic.lbl_select_patient') }} <span class="text-danger">*</span></label>
                <Multiselect id="user_id" v-model="user_id" :value="user_id" :placeholder="$t('clinic.lbl_select_patient')" v-bind="singleSelectOption" :options="patientlist.options" class="form-group" :disabled="selectedEncounter"></Multiselect>
                <span v-if="errorMessages['user_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['user_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['user_id'] }}</span>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label" for="date">{{ $t('clinic.lbl_date') }} <span class="text-danger">*</span></label>

                <flat-pickr :placeholder="$t('clinic.lbl_date')" id="date" class="form-control" v-model="date" :value="date" :config="config" :disabled="selectedEncounter"></flat-pickr>
                <span v-if="errorMessages['date']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['date']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.date }}</span>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label">{{ $t('clinic.lbl_select_service') }} <span class="text-danger">*</span></label>
                <Multiselect id="service_id" v-model="service_id" :value="service_id" @select="getServiceDetails" :placeholder="$t('clinic.lbl_select_service')" v-bind="singleSelectOption" :options="servicelist.options" class="form-group"></Multiselect>
                <span v-if="errorMessages['service_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['service_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['service_id'] }}</span>
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ $t('clinic.lbl_payment_status') }}</label>
                <Multiselect id="payment_status" v-model="payment_status" :value="payment_status" v-bind="payment_status_data" class="form-group"></Multiselect>
                <span class="text-danger">{{ errors.payment_status }}</span>
              </div>

              <div class="row">
                <div class="col-md-12 table-responsive">
                  <table class="table table-sm text-center table-bordered custom-table">
                    <thead class="thead-light">
                      <tr>
                        <th>{{ $t('clinic.services') }}</th>
                        <th>{{ $t('clinic.price') }}</th>
                        <th>{{ $t('clinic.total') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="ServiceDetails != null">
                        <td>{{ ServiceDetails.name }}</td>
                        <td v-if="ServiceDetails.service_price_data">{{ formatCurrencyVue(ServiceDetails.service_price_data.service_price) }}</td>
                        <td v-else>{{ formatCurrencyVue(0) }}</td>

                        <td v-if="ServiceDetails.service_price_data">{{ formatCurrencyVue(ServiceDetails.service_price_data.total_amount) }}</td>
                        <td v-else>{{ formatCurrencyVue(0) }}</td>
                      </tr>
                      <tr v-else>
                        <td colspan="3">
                          <span class="text-primary mb-0">{{ $t('appointment.data_not_found') }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 table-responsive">
                  <table class="table table-sm text-center table-bordered custom-table">
                    <thead class="thead-light">
                      <tr>
                        <th>{{ $t('appointment.sr_no') }}</th>
                        <th>{{ $t('appointment.tax_name') }}</th>
                        <th>{{ $t('appointment.charges') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(tax, index) in taxData" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td v-if="tax.type == 'fixed'">{{ tax.title }} ( {{ formatCurrencyVue(tax.value) }} )</td>
                        <td v-else>{{ tax.title }} ( {{ tax.value }} % )</td>
                        <td>{{ formatCurrencyVue(tax.amount) }}</td>
                      </tr>
                      <tr v-if="taxData.length == 0">
                        <td colspan="3">
                          <span class="text-primary mb-0">{{ $t('appointment.data_not_found') }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 table-responsive">
                  <div class="d-flex justify-content-end">
                    <h6>{{ $t('appointment.service_price') }} :</h6>
                    <h6 v-if="ServiceDetails != null">{{ formatCurrencyVue(ServiceDetails.service_price_data.service_price) }}</h6>
                    <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                  </div>                  
                  <div class="d-flex justify-content-end">
                    <h6>{{ $t('appointment.discount_amount') }} :</h6>
                    <h6 v-if="ServiceDetails != null">{{ formatCurrencyVue(ServiceDetails.service_price_data.discount_amount) }}</h6>
                    <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                  </div>
                  <div class="d-flex justify-content-end">
                    <h6>{{ $t('appointment.total_tax') }} :</h6>
                    <h6 v-if="ServiceDetails != null">{{ formatCurrencyVue(ServiceDetails.service_price_data.total_tax) }}</h6>
                    <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                  </div>
                  <div class="d-flex justify-content-end">
                    <h6>{{ $t('appointment.total_payable_amount') }}</h6>
                    <h6 v-if="ServiceDetails != null">{{ formatCurrencyVue(ServiceDetails.service_price_data.total_amount) }}</h6>
                    <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                  </div>
                </div>
              </div>

              <div v-for="field in customefield" :key="field.id">
                <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3 p-3">
          <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.close') }}
          </button>
          <button :disabled="isDisabled" class="btn btn-secondary" name="submit">
            <template v-if="IS_SUBMITED">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ $t('appointment.loading') }}
            </template>
            <template v-else> {{ $t('messages.save') }}</template>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import moment from 'moment'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { EDIT_URL, STORE_URL, UPDATE_URL, CLINIC_LIST, DOCTOR_LIST, SERVICE_LIST, PATIENT_LIST, ENCOUNTER_LIST, APPOINTMENT_SERVICE_LIST, GET_SERVICE_DETAILS, SAVE_BILLING_DETAILS } from '../../constant/billing-record'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FlatPickr from 'vue-flatpickr-component'

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

// flatepicker

const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  minDate: 'today'
})

const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

// Validations
const validationSchema = yup.object({
  clinic_id: yup.string().required('Clinic name is required'),
  service_id: yup.string().required('Service is required'),
  doctor_id: yup.string().required('Doctor is required'),
  user_id: yup.string().required('Patient is required'),
  encounter_id: yup.string().required('Encounter is required'),
  date: yup.string().required('Encounter Date is required'),
  payment_status: yup.string().required('Payment status is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: encounter_id } = useField('encounter_id')
const { value: clinic_id } = useField('clinic_id')
const { value: service_id } = useField('service_id')
const { value: doctor_id } = useField('doctor_id')
const { value: user_id } = useField('user_id')
const { value: date } = useField('date')
const { value: payment_status } = useField('payment_status')

const errorMessages = ref({})

// Default FORM DATA
const defaultData = () => {
  const currentDate = new Date().toISOString().substr(0, 10)
  errorMessages.value = {}
  return {
    encounter_id: '',
    clinic_id: '',
    service_id: '',
    doctor_id: '',
    user_id: '',
    date: currentDate,
    payment_status: 0
  }
}

const payment_status_data = ref({
  searchable: true,
  options: [
    { label: 'Pending', value: 0 },
    { label: 'Paid', value: 1 }
  ]
})

const patientlist = ref({ options: [], list: [] })

const getPatient = () => {
  useSelect({ url: PATIENT_LIST }, { value: 'id', label: 'name' }).then((data) => (patientlist.value = data))
}

const encounterlist = ref({ options: [], list: [] })

const getEncounter = () => {
  useSelect({ url: ENCOUNTER_LIST }, { value: 'id', label: 'text' }).then((data) => (encounterlist.value = data))
}
const cliniclist = ref({ options: [], list: [] })

const getClinic = () => {
  useSelect({ url: CLINIC_LIST }, { value: 'id', label: 'clinic_name' }).then((data) => (cliniclist.value = data))
}

const doctorlist = ref({ options: [], list: [] })

const getDoctorList = () => {
  useSelect({ url: DOCTOR_LIST }, { value: 'doctor_id', label: 'doctor_name' }).then((data) => (doctorlist.value = data))
}

const appointment_id = ref()
const selectedEncounter = computed(() => encounterlist.value.list.find((encounter) => encounter.id == encounter_id.value) ?? null)

watch(selectedEncounter, (newValue, oldValue) => {
  if (newValue !== null) {
    ;(clinic_id.value = newValue.clinic_id), (doctor_id.value = newValue.doctor_id), (user_id.value = newValue.user_id), (date.value = newValue.date)
    service_id.value = newValue.service_id || service_id.value || null
    appointment_id.value = newValue.appointment_id

    if (appointment_id.value !== null) {
      useSelect({ url: APPOINTMENT_SERVICE_LIST, data: { id: service_id.value } }, { value: 'id', label: 'name' }).then((data) => (servicelist.value = data))
      getServiceDetails()
    } else {
      getServiceList(clinic_id.value, doctor_id.value)
    }
  }
})

const ServiceDetails = ref(null)
const taxData = ref([])

const getServiceDetails = () => {
  listingRequest({ url: GET_SERVICE_DETAILS, data: { encounter_id: encounter_id.value, service_id: service_id.value } }).then((res) => {
    ServiceDetails.value = res.data

    if (res.data) {
      taxData.value = res.data.tax_data
    }
  })
}

const removeEncounter = () => {
  encounter_id.value = null
}
const IS_SUBMITED = ref(false)

const servicelist = ref({ options: [], list: [] })

const getServiceList = () => {
  ;(service_id.value = ''), useSelect({ url: SERVICE_LIST, data: { doctor_id: doctor_id.value, clinic_id: clinic_id.value } }, { value: 'id', label: 'name' }).then((data) => (servicelist.value = data))
}

onMounted(() => {
  getEncounter()
  getClinic()
  getPatient()
  getDoctorList()
  setFormData(defaultData())
})

const setFormData = (data) => {
  // encounter_id.value = data.encounter_id
  clinic_id.value = data.clinic_id
  service_id.value = data.service_id
  doctor_id.value = data.doctor_id
  user_id.value = data.user_id
  date.value = data.date
  payment_status.value = data.payment_status

  resetForm({
    values: {
      encounter_id: data.encounter_id,
      payment_status: data.payment_status
    }
  })
}

const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true

  if (props.customefield > 0) {
    values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  }

  values.service_details = ServiceDetails.value

  storeRequest({ url: SAVE_BILLING_DETAILS, body: values }).then((res) => {
    if (res.status) {
      reset_datatable_close_offcanvas(res)
    }
  })
})
</script>
