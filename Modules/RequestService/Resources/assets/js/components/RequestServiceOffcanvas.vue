<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <label class="form-label">{{ $t('service.lbl_name') }} <span class="text-danger">*</span></label>
        <InputField class="col-md-12" type="text" :is-required="true" 
          :label="$t('service.lbl_name')" 
          :placeholder="$t('service.lbl_name')" 
          v-model="name" 
          :error-message="errors['name']" 
          :error-messages="errorMessages['name']">
        </InputField>

        <div class="form-group col-md-12">
          <label class="form-label" for="description">{{ $t('service.lbl_description') }}<span
              class="text-danger">*</span></label>
              <textarea class="form-control" v-model="description" id="description" :placeholder="$t('service.lbl_description')" @input="updateCharCount"></textarea>
              <div class="text-muted d-flex justify-content-end">{{ description ? description.length : 0 }} / 190</div>
          <span v-if="errorMessages['description']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.description }}</span>
        </div>

        <div class="form-group">
          <label class="form-label" for="type">{{ $t('service.lbl_type') }} <span class="text-danger">*</span> </label>
          <Multiselect 
            v-model="type" 
            :value="type" 
            v-bind="type_data" 
            id="type" 
            autocomplete="off"
            :placeholder="$t('service.lbl_type')">
          </Multiselect>

          <span v-if="errorMessages['type']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['type']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.type }}</span>
        </div>
        <div v-for="field in customefield" :key="field.id">
          <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type"
            :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
        </div>
        <div class="form-group">
          <label class="form-label" for="requestservice-status">{{ $t('service.lbl_status') }}</label>
          <div class="d-flex justify-content-between align-items-center form-control">
            <label class="form-label m-0" for="requestservice-status">{{ $t('service.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="requestservice-status"
                type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/requestservices'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { readFile } from '@/helpers/utilities'
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

const type_data = ref({
  searchable: true,
  options: [
    { label: 'Specialization', value: 'specialization' },
    { label: 'System Service', value: 'system_service' },
    { label: 'Category', value: 'category' },
  ],
  closeOnSelect: true,
  createOption: true,
  removeSelected: false
})

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
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    description: '',
    type: '',
    status: true
  }
}

const updateCharCount = () => {
  if (description.value?.length > 190) {
    description.value = description.value.substring(0, 190)
  }
}
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      type: data.type,
      status: data.status,
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

// Validations
const validationSchema = yup.object({
  name: yup.string().required('Name is a required field'),
  description: yup.string().required('Description is a required field').max(190, 'Description cannot exceed 190 characters'),

  type: yup.string().required('Type is a required field'),
})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: type } = useField('type')
const { value: status } = useField('status')

const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.custom_fields_data = JSON.stringify(values.custom_fields_data);
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values })
      .then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values })
      .then((res) => reset_datatable_close_offcanvas(res))
  }
});

</script>
