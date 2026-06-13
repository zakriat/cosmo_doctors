<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('category.lbl_name') }} <span class="text-danger">*</span></label>
              <InputField :is-required="true" :label="$t('category.lbl_name')" placeholder="" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>
            </div>

            <div class="form-group">
              <div v-for="field in customefield" :key="field.id">
                <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
              </div>
            </div>

            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('appointment.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
            </div>            
          </div>
        </div>
      </div>

      <div class="offcanvas-footer border-top pt-4">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">          
          <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.close') }}
          </button>
          <button class="btn btn-secondary" name="submit">
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
import { ref, onMounted, computed } from 'vue'
import moment from 'moment'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../../constant/encounter-template'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FlatPickr from 'vue-flatpickr-component'
import InputField from '@/vue/components/form-elements/InputField.vue'

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
const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const IS_SUBMITED = ref(false)
 
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
  name: yup.string().required('Template name is required'),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
 
const { value: name } = useField('name')
const { value: status } = useField('status')


const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})

// Default FORM DATA
const defaultData = () => {
  const currentDate = new Date().toISOString().substr(0, 10);
  errorMessages.value = {}
  return {
    name:'',
    status: 1,
  
  }
}
 

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      status:data.status,
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

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }

})

</script>
