<template>
  <form @submit="formSubmit" class="">
    <div class="modal fade" id="Billing_Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <template v-if="currentId != 0">
              <h6>{{ $t('clinic.generate_invoice') }}</h6>
            </template>
            <template v-else>
              <h6>{{ $t('clinic.generate_invoice') }}</h6>
            </template>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row" id="form-offcanvas">
              <div class="d-flex align-items-center justify-content-end gap-3 mb-4">
                <button type="button" class="btn btn-primary" @click="toggleBillingItem">
                  <i class="ph" :class="isBillingItemOpen ? 'ph-caret-double-up' : 'ph-caret-double-down'"></i>
                  {{ billingItemButtonText }}
                </button>
              </div>

              <div class="card-body border-top">
                <b-collapse id="billingitem" v-model="isBillingItemOpen">
                  <div class="card bg-body">
                    <div class="card-body border-top">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <div class="form-group">
                            <label class="form-label">{{ $t('clinic.lbl_select_service') }} <span class="text-danger">*</span></label>
                            <Multiselect id="service_id" v-model="service_id" :value="service_id" :placeholder="$t('clinic.lbl_select_service')" v-bind="multiselectOption" :options="services.options" @select="selectService" @create="createNewService" class="form-group"></Multiselect>
                            <span class="text-danger">{{ errorMessages.service_id }}</span>
                          </div>
                        </div>
                        <div class="form-group col-md-6">
                          <label class="form-label" for="clinic_id">{{ $t('clinic.price') }} <span class="text-danger">*</span> </label>
                          <InputField type="text" :is-required="true" :icon="`${CURRENCY_SYMBOL}`" :placeholder="$t('clinic.price')" :label="`${$t('clinic.price')} (${CURRENCY_SYMBOL})`" v-model="charges"></InputField>
                          <span class="text-danger">{{ errorMessages.charges }}</span>
                        </div>
                        <div class="form-group col-md-6">
                          <label class="form-label">{{ $t('product.quantity') }} <span class="text-danger">*</span></label>
                          <InputField :is-required="true" :placeholder="$t('product.quantity')" v-model="quantity"></InputField>
                          <span class="text-danger">{{ errorMessages.quantity }}</span>
                        </div>
                        <div class="form-group col-md-6">
                          <label class="form-label">{{ $t('appointment.total') }} <span class="text-danger">*</span></label>
                          <InputField :is-required="true" :placeholder="$t('appointment.total')" v-model="total" :isReadOnly="true"></InputField>
                          <span class="text-danger">{{ errorMessages.total }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer border-top">
                      <div class="d-flex align-items-center justify-content-end gap-3">
                        <span class="btn btn-danger" @click="closeTemplate()">
                          {{ $t('clinic.cancel') }}
                        </span>
                        <button v-if="isLoading == 0" :disabled="!isFormValid" class="btn btn-primary" type="button" @click="addBillingItem">
                          {{ $t('appointment.save') }}
                        </button>

                        <button v-else :disabled="isLoading == 1" class="btn btn-secondary" type="button">
                          {{ $t('appointment.loading') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </b-collapse>
              </div>

              <div class="row">
                <div class="col-md-12 table-responsive">
                  <table class="table table-sm text-center table-bordered custom-table">
                    <thead class="thead-light">
                      <tr>
                        <th>{{ $t('appointment.sr_no') }}</th>
                        <th>{{ $t('appointment.lbl_services') }}</th>
                        <th>{{ $t('service.discount') }}</th>
                        <th>{{ $t('product.quantity') }}</th>
                        <th>{{ $t('appointment.price') }}</th>
                        <th>{{ $t('appointment.total') }}</th>
                        <th>{{ $t('appointment.lbl_action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-if="ServiceDetails.length > 0">
                        <tr v-for="(service, index) in ServiceDetails" :key="service.name">
                          <td>{{ ++index }}</td>
                          <td>{{ service.name ?? '-' }}</td>
                          <td v-if="service.discount_type == 'fixed'">{{ formatCurrencyVue(service.discount_value) ?? '-' }}</td>
                          <td v-else>{{ service.discount_value !== null && service.discount_value !== undefined ? service.discount_value + '%' : '-' }}</td>
                          <td>{{ service.quantity ?? 0 }}</td>
                          <td>{{ formatCurrencyVue(service.service_price) ?? 0 }}</td>
                          <td>{{ formatCurrencyVue(service.total_amount) ?? 0 }}</td>
                          <td>
                            <div class="d-flex gap-3 align-items-center" v-if="serviceId !== service.item_id">
                              <button type="button" class="btn text-success p-0 fs-5" @click="editbillingitem(service.id)">
                                <i class="ph ph-pencil-simple-line align-middle"></i>
                              </button>
                              <button type="button" class="btn text-danger p-0 fs-4" @click="deletebillingitem(service.id, 'Are you sure you want to delete it?')">
                                <i class="ph ph-trash align-middle"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </template>
                      <tr v-else>
                        <td colspan="3">
                          <span class="text-primary mb-0">{{ $t('clinic.no_data_available') }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-label" for="category-discount">{{ $t('service.discount') }}</label>
                  <div class="d-flex justify-content-between align-items-center form-control">
                    <label class="form-label m-0" for="category-discount">{{ $t('service.discount') }}</label>
                    <div class="form-check form-switch">
                      <input class="form-check-input" name="final_discount" id="category-discount" type="checkbox" v-model="final_discount" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4" v-if="final_discount == 1">
                <div class="form-group">
                  <label class="form-label">{{ $t('service.lbl_discount_type') }} <span class="text-danger">*</span></label>
                  <Multiselect id="final_discount_type" v-model="final_discount_type" :value="final_discount_type" v-bind="final_discount_type_data" class="form-group"></Multiselect>
                  <span v-if="errorMessages['final_discount_type']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['final_discount_type']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.final_discount_type }}</span>
                </div>
              </div>
              <div class="col-md-4" v-if="final_discount == 1">
                <label class="form-label">{{ $t('service.lbl_discount_value') }} <span class="text-danger">*</span> </label>
                <InputField type="number" :is-required="true" :label="$t('service.lbl_discount_value')" :icon="`${CURRENCY_SYMBOL}`" placeholder="" v-model="final_discount_value" :error-message="errors['final_discount_value']" :error-messages="errorMessages['final_discount_value']"></InputField>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 table-responsive">
                <table class="table table-sm text-center table-bordered custom-table">
                  <thead class="thead-light">
                    <tr>
                      <th>{{ $t('appointment.sr_no') }}</th>
                      <th>{{ $t('appointment.lbl_name') }}</th>
                      <th>{{ $t('appointment.tax') }}</th>
                    </tr>
                  </thead>
                  <tbody v-if="ServiceDetails.length > 0">
                    <tr v-if="Array.isArray(taxData) && taxData.length === 0">
                      <td colspan="3">
                        <span class="text-primary mb-0">{{ $t('appointment.no_tax_found') }}</span>
                      </td>
                    </tr>

                    <tr v-for="(tax, index) in taxData" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td>{{ tax.title }}</td>
                      <td v-if="tax.type == 'fixed'">{{ formatCurrencyVue(tax.value) }}</td>
                      <td v-else>{{ tax.value }} %</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 table-responsive">
                <div class="d-flex justify-content-between gap-1">
                  <h6>{{ $t('appointment.total_amount') }}:</h6>
                  <h6 v-if="ServiceDetails.length > 0">{{ formatCurrencyVue(totalAmount) }}</h6>
                  <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                </div>

                <div class="d-flex justify-content-between gap-1" v-if="final_discount == 1">
                  <h6>{{ $t('appointment.total_discount') }}:</h6>
                  <h6 v-if="ServiceDetails.length > 0">{{ formatCurrencyVue(DiscountAmount) }}</h6>
                  <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                </div>

                <div class="d-flex justify-content-between gap-1" v-if="final_discount == 1">
                  <h6>{{ $t('appointment.sub_total') }}:</h6>
                  <h6 v-if="ServiceDetails.length > 0">{{ formatCurrencyVue(totalAmountAfterDiscount) }}</h6>
                  <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                </div>
                <!-- <div class="d-flex"><h6> {{ $t('appointment.discount_amount') }}:</h6> <h6 v-if="ServiceDetails !=null">{{ formatCurrencyVue(totalTaxAmount) }} </h6> <h6 v-else>{{formatCurrencyVue(0)}}</h6></div> -->
                <div class="d-flex justify-content-between gap-1">
                  <h6>{{ $t('appointment.tax') }}:</h6>
                  <h6 v-if="ServiceDetails.length > 0">{{ formatCurrencyVue(TaxAmount) }}</h6>
                  <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                </div>
                <div class="d-flex justify-content-between gap-1">
                  <h6>{{ $t('appointment.total_payable_amount') }}:</h6>
                  <h6 v-if="ServiceDetails.length > 0">{{ formatCurrencyVue(totaltaxAmount) }}</h6>
                  <h6 v-else>{{ formatCurrencyVue(0) }}</h6>
                </div>

                <div v-if="appointment?.appointmenttransaction?.advance_payment_status === 1">
                  <div class="d-flex justify-content-between gap-1">
                    <h6>{{ $t('service.advance_payment_amount') }} ({{ appointment.advance_payment_amount }}%)</h6>
                    <h6>{{ formatCurrencyVue(appointment.advance_paid_amount) }}</h6>
                  </div>

                  <div v-if="appointment?.appointmenttransaction?.payment_status != 1 && appointment?.status != 'cancelled'">
                    <div class="d-flex justify-content-between gap-1">
                      <h6>
                        {{ $t('service.remaining_amount') }} <span class="text-capitalize badge bg-warning p-2">{{ $t('appointment.pending') }}</span>
                      </h6>
                      <h6>{{ formatCurrencyVue(totalRemainingAmount) }}</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <label class="form-label">{{ $t('clinic.lbl_payment_status') }}</label>
              <Multiselect id="payment_status" v-model="payment_status" :value="payment_status" v-bind="payment_status_data" class="form-group"></Multiselect>
              <span class="text-danger">{{ errors.payment_status }}</span>
            </div>
          </div>
          <div class="float-left">
            <h4 class="text-center small text-danger">{{ $t('appointment.close_encounter_note') }}</h4>
          </div>
          <div class="modal-footer">
            <button v-if="payment_status == 0" type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
            <button v-if="payment_status == 1" type="submit" class="btn btn-primary">{{ $t('messages.save') }} & {{ $t('appointment.close_encounter') }}</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('appointment.close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
import { ref, watch, onMounted, computed } from 'vue'

import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { GET_SERVICE_LIST, GET_SERVICE_DETAILS, SAVE_BILLING_DETAILS, GET_BILLING_DETAILS, CREATE_SERVICE, TAX_DATA, SAVE_BILLING_ITEM, GET_BILLING_ITEM, EDIT_BILLING_ITEM, DELETE_BILLING_ITEM } from '@/vue/constants/envoice'
import { confirmSwal } from '@/helpers/utilities'

const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)

const emit = defineEmits(['save_billing'])

const props = defineProps({
  encounter_id: { type: Number, default: 0 },
  user_id: { type: Number, default: '' },
  id: { type: Number, default: 0 }
})

const isLoading = ref(0)
const isBillingItemOpen = ref(false)

const toggleBillingItem = () => {
  isBillingItemOpen.value = !isBillingItemOpen.value
  if (isBillingItemOpen.value) {
    // Set default values and apply validations when opened
    service_id.value = ''
    charges.value = 0
    quantity.value = 1
    total.value = 0
    discount_value.value = ''
    discount_type.value = ''
  } else {
    // Clear errors when closed
    errorMessages.value = {}
  }
}

const validateBillingItem = async () => {
  try {
    await validationSchemaBillingItem.validate(
      {
        service_id: service_id.value,
        charges: charges.value,
        quantity: quantity.value,
        total: total.value
      },
      { abortEarly: false }
    )
    errorMessages.value = {}
  } catch (err) {
    const errors = err.inner.reduce((acc, e) => {
      acc[e.path] = e.message
      return acc
    }, {})
    errorMessages.value = errors
  }
}
const billingItemButtonText = computed(() => {
  return isBillingItemOpen.value ? 'Close Billing Item' : 'Add Billing Item'
})
const closeTemplate = () => {
  isBillingItemOpen.value = false
  isLoading.value = 0
}

const formatCurrencyVue = window.currencyFormat

const { storeRequest, listingRequest, getRequest, updateRequest, deleteRequest } = useRequest()

const currentId = ref(props.encounter_id)
const billingId = ref()
const serviceId = ref()

const appointment = ref()

// Edit Form Or Create Form
watch(
  () => props.encounter_id,

  (value) => {
    currentId.value = value
    if (value > 0) {
      listingRequest({ url: GET_BILLING_DETAILS, data: { encounter_id: value } }).then((res) => {
        if (res.status && res.data) {
          selectService(res.data.service_id)
          setFormData(res.data)
          billingId.value = res.data.billing_id
          serviceId.value = res.data.service_id
          appointment.value = res.data.appointment

          if (billingId.value != null) {
            billingItemService(billingId.value)
          } else {
            billingItemService()
          }
        }
      })
    } else {
      setFormData(defaultData())

      getServiceList()
    }

    getServiceList()
  }
)
watch(
  () => serviceId.value,
  () => {
    // Update service list when serviceId changes
    getServiceList()
  }
)
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const multiselectOption = ref({
  tag: true,
  searchable: true,
  //multiple: true,
  closeOnSelect: true,
  createOption: true
})

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    service_id: '',
    payment_status: 0,
    final_discount: 0,
    final_discount_type: 'fixed',
    final_discount_value: 0
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      service_id: data.service_id,
      payment_status: data.payment_status,
      final_discount: data.final_discount === 1,
      final_discount_type: data.final_discount_type || 'fixed',
      final_discount_value: data.final_discount_value
    }
  })
}

const final_discount_type_data = ref({
  searchable: true,
  options: [
    { label: 'Percentage', value: 'percentage' },
    { label: 'Fixed', value: 'fixed' }
  ],
  closeOnSelect: true,
  createOption: false
})
// Validations
const validationSchema = yup.object({
  // service_id: yup.string().required('Please Select Service'),
  payment_status: yup.string().required('Please Select payment'),
  final_discount_value: yup
    .number()
    .typeError('Discount value must be a number')
    .min(0, 'Discount value must be greater than 0')
    .test('discount_value_check', function (value) {
      const price = totalAmount.value
      const discountType = this.parent.final_discount_type
      const discount = this.parent.final_discount ? 1 : 0

      if (discount == 1) {
        if (value >= 1 && value !== null) {
          if (discountType === 'fixed') {
            if (value !== undefined && price !== undefined && value >= price) {
              return this.createError({ message: 'Fixed discount value must be less than price' })
            }
          } else if (discountType === 'percentage') {
            if (value !== undefined && value >= 100) {
              return this.createError({ message: 'Percentage discount value must be less than 100' })
            }
          }
        } else {
          return this.createError({ message: 'Discount value must be greater than 0' })
        }
      }

      return true
    })
    .nullable()
})
const validationSchemaBillingItem = yup.object({
  service_id: yup.string().required('Please Select Service'),
  charges: yup
    .number()
    .typeError('Please Enter Charges') // This handles NaN cases
    .required('Please Enter Charges')
    .min(1, 'Charges must be at least 1'),
  quantity: yup
    .number()
    .typeError('Please Enter Quantity') // This handles NaN cases
    .required('Please Enter Quantity')
    .min(1, 'Quantity must be at least 1'),
  total: yup
    .number()
    .typeError('Please Enter Total') // This handles NaN cases
    .required('Please Enter Total')
    .min(1, 'Total must be at least 1')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: service_id } = useField('service_id')
const { value: payment_status } = useField('payment_status')
const { value: charges } = useField('charges')
const { value: quantity } = useField('quantity')
const { value: total } = useField('total')
const { value: discount_value } = useField('discount_value')
const { value: discount_type } = useField('discount_type')
const { value: final_discount } = useField('final_discount')
const { value: final_discount_value } = useField('final_discount_value')
const { value: final_discount_type } = useField('final_discount_type')
const errorMessages = ref({})

onMounted(() => {
  gettaxData()
  setFormData(defaultData())
})

const services = ref({ options: [], list: [] })

const getServiceList = () => {
  listingRequest({ url: GET_SERVICE_LIST, data: { encounter_id: props.encounter_id } }).then((res) => {
    services.value.list = res

    services.value.options = res.map((item) => ({
      value: item.id,
      label: item.name,
      disabled: item.id === serviceId.value
    }))
  })
}

// watch(service_id, (newServiceId) => {
//       if (newServiceId) {
//         const selectedService = services.value.list.find(service => service.id === newServiceId);
//         console.log(selectedService);
//         charges.value = selectedService ? selectedService.charges : null;
//         discount_value.value = selectedService ? selectedService.discount_value : null;
//         discount_type.value = selectedService ? selectedService.discount_type : null;
//       }
//     });

const ServiceDetails = ref([])
const taxData = ref([])
const ServiceDetail = ref([])
const discount = ref([])

const selectService = (value) => {
  listingRequest({ url: GET_SERVICE_DETAILS, data: { encounter_id: props.encounter_id, service_id: value } }).then((res) => {
    ServiceDetail.value = res.data

    if (res.data.doctor_service && res.data.doctor_service.length > 0) {
      charges.value = res.data.doctor_service[0].charges // Set charges from the first item in the array
    } else {
      charges.value = null // Handle the case where doctor_service is empty or undefined
    }
    discount_value.value = res.data ? res.data.discount_value : null
    discount_type.value = res.data ? res.data.discount_type : null
  })
}

const billingItemService = (value) => {
  const billingsid = value ?? null
  listingRequest({ url: GET_BILLING_ITEM, data: { billing_id: billingsid } }).then((res) => {
    if (res.data && res.data.length > 0) {
      ServiceDetails.value = res.data
      selectService(res.data[0].item_id)
    } else {
      ServiceDetails.value = []
    }
  })
}
const taxCalculation = (amount) => {
  let tax_amount = 0
  if (taxData.value) {
    taxData.value.forEach((item) => {
      if (item.type === 'fixed') {
        tax_amount += item.value
      } else if (item.type === 'percent') {
        tax_amount += amount * (item.value / 100)
      }
    })
  }
  return tax_amount
}

const totalAmount = computed(() => {
  return ServiceDetails.value.reduce((sum, service) => sum + service.total_amount, 0)
})

const totalAmountAfterDiscount = computed(() => {
  // let discount = 0
  if (final_discount.value) {
    if (final_discount_type.value === 'fixed') {
      discount.value = final_discount_value.value
    } else if (final_discount_type.value === 'percentage') {
      discount.value = (totalAmount.value * final_discount_value.value) / 100
    }
  }
  return totalAmount.value - discount.value
})

const TaxAmount = computed(() => {
  return taxCalculation(totalAmountAfterDiscount.value)
})
const DiscountAmount = computed(() => {
  return discount.value
  // return totalAmount.value * final_discount_value.value / 100
})
const totaltaxAmount = computed(() => {
  return totalAmountAfterDiscount.value + TaxAmount.value
})

const totalRemainingAmount = computed(() => {
  return totaltaxAmount.value - appointment.value.advance_paid_amount
})

const gettaxData = () => {
  const module_type = 'services'

  listingRequest({ url: TAX_DATA, data: module_type }).then((res) => {
    taxData.value = res
  })
}
const createNewService = (newServiceName) => {
  const doctorService = ServiceDetail.value.doctor_service[0]
  const newData = {
    name: newServiceName.value,
    duration_min: ServiceDetail.value.duration_min,
    category_id: ServiceDetail.value.category_id,
    charges: doctorService ? doctorService.charges : [],
    doctor_id: doctorService ? doctorService.doctor_id : [],
    clinic_id: doctorService ? doctorService.clinic_id : [],
    is_video_consultancy: ServiceDetail.value.is_video_consultancy,
    type: ServiceDetail.value.type,
    time_slot: ServiceDetail.value.time_slot,
    status: ServiceDetail.value.status,
    vendor_id: ServiceDetail.value.vendor_id,
    service_type: 'billing_service'
  }

  storeRequest({ url: CREATE_SERVICE, body: newData }).then((res) => {
    if (res.status) {
      getServiceList()

      // service_id.value = res.data.service_id
      // selectService(res.data.service_id)
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
  })
}

const payment_status_data = ref({
  searchable: true,
  options: [
    { label: 'Pending', value: 0 },
    { label: 'Paid', value: 1 }
  ],
  closeOnSelect: true
})

const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    // ServiceDetails.value=null
    // taxData.value=[]
    // currentId.value=0
    bootstrap.Modal.getInstance('#Billing_Modal').hide()
    // setFormData(defaultData())
    emit('save_billing')
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}
const addBillingItem = () => {
  if (!isBillingItemOpen.value) return

  isLoading.value = 1
  validateBillingItem().then(() => {
    if (Object.keys(errorMessages.value).length === 0) {
      const itemData = {
        billing_id: billingId.value,
        item_id: service_id.value,
        service_amount: charges.value,
        quantity: quantity.value,
        total_amount: total.value,
        discount_value: discount_value.value,
        discount_type: discount_type.value
      }

      storeRequest({ url: SAVE_BILLING_ITEM, body: itemData }).then((res) => {
        if (res.status) {
          billingItemService(res.data.billing_id)
          isLoading.value = 0
          isBillingItemOpen.value = false
          service_id.value = ''
          charges.value = 0
          quantity.value = 1
          total.value = 0
          discount_type.value = ''
          discount_value.value = ''
          let itemId = null
          if (res.data.length > 0) {
            itemId = res.data[0].item_id // Set the item_id from the first element
          }

          const serviceToSelect = serviceId.value ? serviceId.value : itemId
          selectService(serviceToSelect)
        } else {
          isLoading.value = 0
          window.errorSnackbar(res.message)
        }
      })
    } else {
      isLoading.value = 0
    }
  })
}

const editbillingitem = (value) => {
  if (value > 0) {
    getRequest({ url: EDIT_BILLING_ITEM, id: value }).then((res) => {
      if (res.status && res.data) {
        isBillingItemOpen.value = true
        service_id.value = res.data.item_id
        charges.value = res.data.service_amount
        quantity.value = res.data.quantity
        total.value = res.data.total_amount
        discount_type.value = res.data.discount_type
        discount_value.value = res.data.discount_value
      }
    })
  } else {
    service_id.value = ''
    charges.value = 0
    quantity.value = 1
    total.value = 0
    discount_type.value = ''
    discount_value.value = ''
  }
}

const deletebillingitem = (id, message) => {
  confirmSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return
    deleteRequest({ url: DELETE_BILLING_ITEM, id }).then((res) => {
      if (res.status) {
        billingItemService(res.billing_id)
        Swal.fire({
          title: 'Deleted',
          text: res.message,
          icon: 'success',
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__zoomOut'
          }
        })
      }
    })
  })
}

const isFormValid = computed(() => {
  return errorMessages.value && Object.keys(errorMessages.value).length === 0
})

// watch(
//   [service_id, charges, quantity, total],
//   () => {
//     if (isBillingItemOpen.value) {
//       validateBillingItem()
//     }
//   },
//   { deep: true }
// )

const formSubmit = handleSubmit((values) => {
  isLoading.value = 1

  values.user_id = props.user_id
  values.encounter_id = props.encounter_id
  values.service_details = ServiceDetail.value
  values.encounter_status = 0
  values.final_discount = final_discount.value ? 1 : 0
  values.final_discount_value = final_discount_value.value
  values.final_total_amount = totaltaxAmount.value ?? 0
  values.final_tax_amount = TaxAmount.value ?? 0

  storeRequest({ url: SAVE_BILLING_DETAILS, body: values }).then((res) => {
    if (res.status) {
      reset_datatable_close_offcanvas(res)
      isLoading.value = 0
    }
  })
})
watch([charges, quantity, discount_type, discount_value], ([newCharges, newQuantity, newDiscountType, newDiscountValue]) => {
  if (newDiscountType === 'fixed') {
    total.value = newCharges * newQuantity - newDiscountValue * newQuantity
  } else if (newDiscountType === 'percentage') {
    total.value = newCharges * newQuantity - newCharges * newQuantity * (newDiscountValue / 100)
  } else {
    total.value = newCharges * newQuantity
  }
})
</script>
