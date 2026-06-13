<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('promotion.lbl_name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
        <InputField class="col-md-12" type="textarea" :is-required="true" :label="$t('promotion.description')" placeholder="" v-model="description" :error-message="errors['description']" :error-messages="errorMessages['description']"></InputField>
        <InputField class="col-md-12" type="datetime-local" :is-required="true" :label="$t('promotion.start_datetime')" placeholder="" v-model="start_date_time" :error-message="errors['start_date_time']" :error-messages="errorMessages['start_date_time']"></InputField>
        <InputField class="col-md-12" type="datetime-local" :is-required="true" :label="$t('promotion.end_datetime')" placeholder="" v-model="end_date_time" :error-message="errors['end_date_time']" :error-messages="errorMessages['end_date_time']"></InputField>
        <!-- <div class="row">
          <div class="col-md-12">
            <label class="form-label">{{ $t('setting_language_page.lbl_timezone') }}</label>
            <Multiselect id="timezone" v-model="timezone" :value="timezone" v-bind="TimeZoneSelectOption" :options="time_zone.options" class="form-group"></Multiselect>
            <span class="text-danger">{{ errors.timezone }}</span>
          </div>
        </div> -->
        <div class="row">
          <div class="col-md-12">
            <label class="form-label">{{ $t('promotion.coupon_type') }}</label>
            <Multiselect v-model="coupon_type" :value="coupon_type" v-bind="singleSelectOption" :options="couponOptions" id="type" autocomplete="off" :disabled="ISREADONLY"></Multiselect>
          </div>
          <div class="form-group col-md-12" v-if="coupon_type">
            <div v-if="coupon_type == 'custom'">
              <InputField class="col-md-12" type="text" :is-required="true" :label="$t('promotion.coupon_code')" placeholder="" v-model="coupon_code" :error-message="errors['coupon_code']" :error-messages="errorMessages['coupon_code']" :is-read-only="ISREADONLY"></InputField>
            </div>
            <div v-else-if="coupon_type == 'bulk'">
              <InputField class="col-md-12" type="number" :is-required="true" :label="$t('promotion.number_of_coupon')" placeholder="" v-model="number_of_coupon" :error-message="errors['number_of_coupon']" :error-messages="errorMessages['number_of_coupon']" :is-read-only="ISREADONLY"></InputField>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label class="form-label">{{ $t('promotion.percent_or_fixed') }}</label>
            <Multiselect v-model="discount_type" :value="discount_type" v-bind="singleSelectOption" :options="typeOptions" id="type" autocomplete="off"></Multiselect>
          </div>
          <div class="form-group col-md-12" v-if="discount_type">
            <div v-if="discount_type == 'percent'">
              <InputField type="number" step=any :label="$t('promotion.discount_percentage')" placeholder="" v-model="discount_percentage" :error-message="errors['discount_percentage']"></InputField>
            </div>
            <div v-else-if="discount_type == 'fixed'">
              <InputField type="number" :label="$t('promotion.discount_amount')" placeholder="" v-model="discount_amount" :error-message="errors['discount_amount']"></InputField>
            </div>
          </div>
        </div>
        <div class="form-group col-md-12">
            <div>
              <InputField class="col-md-12" type="number" :is-required="true"  :label="$t('promotion.use_limit')" placeholder="" v-model="use_limit" :error-message="errors['use_limit']" :error-messages="errorMessages['use_limit']" :is-read-only="coupon_type=='bulk'"></InputField>

            </div>
          </div>

        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="category-status">{{ $t('service.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :true-value="1" :false-value="0" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
        <!-- <div v-for="field in customefield" :key="field.id">
          <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
        </div> -->
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted,computed  } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, TIME_ZONE_LIST } from '../constant'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'


// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()



const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})





const getTimeZoneList = () => {
  listingRequest({ url: TIME_ZONE_LIST, data: { type: type } }).then((res) => {
    time_zone.value.options = buildMultiSelectObject(res.results, {
      value: 'id',
      label: 'text'
    })
  })
}




// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
        ISREADONLY.value = true
      }
    })
  } else {
    resetForm()
    setFormData(defaultData())
  }
})


const resetMyForm=()=>resetForm();

useOnOffcanvasHide('form-offcanvas', () => {
setFormData(defaultData());
resetMyForm();
});

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  ISREADONLY.value = false
  return {
    name: '',
    description: '',
    start_date_time: '',
    end_date_time: '',
    status: 1,

    coupon:[{
    discount_amount: 0,
    discount_percentage: 0,
    discount_type: 'fixed',
    coupon_type: 'bulk',
    number_of_coupon: '0',
    coupon_code:'',
    use_limit:1,
    }
    ]
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      start_date_time: data.start_date_time,
      end_date_time: data.end_date_time,
      status: data.status,
      discount_amount: data.coupon[0].discount_amount || 0,
      discount_percentage: data.coupon[0].discount_percentage,
      discount_type: data.coupon[0].discount_type || 'percent',
      coupon_type: data.coupon.length === 1 ? 'custom' : 'bulk',
      number_of_coupon: data.coupon.length,
      coupon_code: data.coupon[0].coupon_code,
      use_limit:data.coupon[0].use_limit
    }
  })
}
// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
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

// Validations
const validationSchema = yup.object({
  name: yup.string().required('Name is a required field'),
  start_date_time: yup.string().required('start date time is a required field'),
  end_date_time: yup.date()
        .min(new Date(), 'end datetime cannot be in the past')
        .required('Start datetime is required'),

  coupon_code: yup.string().required('coupon code is a required field'),
  description: yup.string().required('description is a required field'),
  use_limit: yup.number().required('Use limit is required').min(1, 'Use limit must be greater than or equal to 1').typeError('Use limit must be a valid number'),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})



const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: start_date_time } = useField('start_date_time')
const { value: end_date_time } = useField('end_date_time')
const { value: status } = useField('status')
const { value:discount_amount } = useField('discount_amount')
const { value: discount_percentage } = useField('discount_percentage')
const { value: discount_type } = useField('discount_type')
const { value: coupon_type } = useField('coupon_type')
const { value: number_of_coupon } = useField('number_of_coupon')
const { value: coupon_code } = useField('coupon_code')
const { value: use_limit } = useField('use_limit')

const errorMessages = ref({})


const typeOptions = [
  { label: 'Percent', value: 'percent' },
  { label: 'Fixed', value: 'fixed' }
]

const couponOptions = [
  { label: 'Custom', value: 'custom' },
  { label: 'bulk', value: 'bulk' }
]

onMounted(() => {
  setFormData(defaultData())

})
const ISREADONLY = ref(false)
// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if (IS_SUBMITED.value) return false

  values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>
