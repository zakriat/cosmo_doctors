<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="name">{{ $t('tax.lbl_title') }}<span class="text-danger">*</span></label>
              <InputField :is-required="true" :label="$t('tax.lbl_title')" :placeholder="$t('tax.lbl_title')" v-model="title" :error-message="errors.title" :error-messages="errorMessages['title']"></InputField>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="name">{{ $t('tax.lbl_value') }}<span class="text-danger">*</span></label>
              <InputField :is-required="true" :label="$t('tax.lbl_value')" :placeholder="$t('tax.lbl_value')" v-model="value" :error-message="errors.value" :error-messages="errorMessages['value']"></InputField>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="name">{{ $t('tax.lbl_select_type') }}<span class="text-danger">*</span></label>
              <Multiselect id="type" v-model="type" :value="type" v-bind="type_data" class="form-group" :placeholder="$t('tax.lbl_select_type')"></Multiselect>
              <span class="text-danger">{{ errors.type }}</span>
            </div>
          </div>
          <div class="form-group" style="display: none;">
          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="module_type">{{ $t('tax.lbl_module_type') }}</label>
              <Multiselect id="module_type" v-model="module_type" :value="module_type" :placeholder="$t('tax.lbl_module_type')" v-bind="module_type_data" class="form-group"></Multiselect>
              <span class="text-danger">{{ errors.module_type_data }}</span>
            </div>
          </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="tax_type">{{ $t('tax.lbl_tax_type') }}</label>
              <Multiselect id="tax_type" v-model="tax_type" :value="tax_type" :placeholder="$t('tax.lbl_tax_type')" v-bind="tax_type_data" class="form-group"></Multiselect>
              <span class="text-danger">{{ errors.tax_type_data }}</span>
            </div>
          </div>

          <div class="col-md-12">
            <label class="form-label" for="category-status">{{ $t('tax.lbl_status') }}</label>
            <div class="d-flex justify-content-between align-items-center form-control">
              <label class="form-label m-0" for="category-status">{{ $t('tax.lbl_status') }}</label>
              <div class="form-check form-switch m-2">
                <input class="form-check-input" :value="status" :true-value="1" :false-value="0" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch, toRaw } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, SERVICE_LIST } from '../constant/tax'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

onMounted(() => {
  setFormData(defaultData())
  getServiceList()
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

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    title: '',
    value: '',
    type: '',
    module_type_data: null,
    tax_type_data: null,
    status: true,
  }
}

const type_data = ref({
  searchable: true,
  options: [
    { label: 'Percent', value: 'percent' },
    { label: 'Fixed', value: 'fixed' }
  ],
  closeOnSelect: true,
  createOption: true,
  removeSelected: false
})

const module_type_data = ref({
  searchable: true,
  options: [
    { label: 'Products', value: 'products' },
    { label: 'Services', value: 'services' }
  ],
  closeOnSelect: true,
  createOption: true,
  removeSelected: false
})

const tax_type_data = ref({
  searchable: true,
  options: [
    { label: 'Exclusive', value: 'exclusive' },
    { label: 'Inclusive', value: 'inclusive' }
  ],
  closeOnSelect: true,
  createOption: true,
  removeSelected: false
})

const setFormData = (data) => {
  resetForm({
    values: {
      title: data.title,
      value: data.value,
      type: data.type,
      module_type: data.module_type,
      tax_type: data.tax_type,
      status: data.status,
    }
  })
}

const servicelist = reactive({
  options: [{ value: 'all_services', label: 'all_services' }], // Initial "All Services" option
  list: []
})

const getServiceList = () => {
  useSelect({ url: SERVICE_LIST }, { value: 'id', label: 'name' }).then((data) => {
    console.log(data)
    servicelist.options.push(...data.options)
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
    if (res.all_message) {
      errorMessages.value = res.all_message
    }
    if (res.errors) {
      errorMessages.value = res.errors
    }
  }
}

const numberRegex = /^\d+$/
// Validations
const validationSchema = yup.object({
  title: yup
    .string()
    .required('Title is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  value: yup
    .string()
    .required('Value is a required field')
    .matches(/^\d+(\.\d+)?$/, 'Only numbers are allowed'),
  type: yup.string().required('Type is a required field'),

})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: title } = useField('title')
const { value: value } = useField('value')
const { value: type } = useField('type')
const { value: status } = useField('status')
const { value: module_type } = useField('module_type')
const { value: tax_type } = useField('tax_type')

const errorMessages = ref({})

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
<style>
.multiselect-clear {
  display: none !important;
}
</style>
