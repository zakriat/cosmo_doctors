<template>
  <form @submit.prevent="formSubmit" class="bussiness-hour">
    <div id="customform">
      <div v-for="(element, index) in field_data" :key="element.id" class="form-group">
        <!-- Text Field -->
        <div v-if="element.type === 'text'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <input type="text" :id="'text_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Number Field -->
        <div v-else-if="element.type === 'number'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <input type="number" :id="'number_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Checkbox -->
        <div v-else-if="element.type === 'checkbox'" :ref="'field_' + element.id">
          <label class="form-label">
            {{ element.label }}
            <span v-if="element.validation_status == 1" class="text-danger">*</span>
          </label>
          <div v-for="(option, index) in element.option" :key="index" class="form-check">
            <input type="checkbox" :id="'checkbox_' + element.id + '_' + option" :value="option" class="form-check-input" />
            <label :for="'checkbox_' + element.id + '_' + option" class="form-check-label">
              {{ option }}
            </label>
          </div>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- TextArea -->
        <div v-else-if="element.type === 'textarea'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <textarea :id="'textarea_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder"></textarea>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- File Upload -->
        <div v-else-if="element.type === 'file'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <input type="file" :id="'file_' + element.id" class="form-control" @change="handleFileUpload($event, element)" />

          <div v-if="element.value" class="mt-2">
            <img :src="element.value" alt="Uploaded Image" class="img-fluid" height="150px" width="200px" />
          </div>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Multi Select -->
        <div v-else-if="element.type === 'multi_select'" :ref="'field_' + element.id" class="form-group">
          <label class="form-label">{{ element.label }}
            <span v-if="element.validation_status == 1" class="text-danger">*</span>
          </label>
          <Multiselect :id="'multiselect_' + element.id" :options="element.option" v-model="element.value" :searchable="true" multiple="true" v-bind="multiSelectOption"> </Multiselect>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Select -->
        <div v-else-if="element.type === 'select'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status === 1" class="text-danger">*</span></label>
          <select :id="'select_' + element.id" class="form-control" v-model="element.value">
            <option value="" disabled>Select option</option>
            <option v-if="!element.option.length">No options available</option>
            <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
              {{ option }}
            </option>
          </select>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Radio -->
        <div v-else-if="element.type === 'radio'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <div v-for="(option, optIndex) in element.option" :key="optIndex" class="form-check">
            <input type="radio" :id="'radio_' + element.id + '_' + option" :value="option" v-model="element.value" class="form-check-input" :checked="optIndex === 0" />
            <label :for="'radio_' + element.id + '_' + option" class="form-check-label">
              {{ option }}
            </label>
          </div>
          <div v-if="element.error" class="text-danger">{{ element.error }}</div>
        </div>

        <!-- Calendar -->
        <div v-else-if="element.type === 'calender'" :ref="'field_' + element.id">
          <label class="form-label">{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
          <input type="date" :id="'calendar_' + element.id" v-model="element.value" class="form-control" />
        </div>

        <!-- Heading -->

        <div v-else-if="element.type === 'heading'" :ref="'field_' + element.id">
          <component :is="element.placeholder">
            {{ element.label }}
          </component>
        </div>

        <!-- Hr Tag -->
        <div v-else-if="element.type === 'hr_tag'" :ref="'field_' + element.id">
          <hr />
        </div>
      </div>

      <div class="border-top pt-4">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
          <button class="btn btn-secondary-outline d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.cancel') }}
          </button>
          <button class="btn btn-secondary" name="submit">
            {{ $t('messages.save') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watchEffect, onActivated, computed, watch } from 'vue'
import { useForm, useField } from 'vee-validate'
import { GET_CUSTOM_FORM, STORE_DATA_URL } from '../constant/custom-form'
import moment from 'moment'
import { useModuleId, useRequest, useFormModuleId } from '@/helpers/hooks/useCrudOpration'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import * as yup from 'yup'

const title = ref(null)
const title_alignment = ref(null)
const title_color = ref(null)
const field_data = ref([])

const props = defineProps({
  form_id: { type: Number, default: 0 },
  appointment_id: { type: Number, default: 0 },
  type: { type: String, default: '' },
  title: { type: String, default: '' }
})
const { storeRequest, listingRequest } = useRequest()
watch(
  () => props.form_id,
  (newFormId, oldFormId) => {
    fetchFormData()
  },
  { immediate: true } // Trigger on mount as well
)

function fetchFormData() {
  listingRequest({ url: GET_CUSTOM_FORM, data: { form_id: props.form_id, appointment_id: props.appointment_id, type: props.type } })
    .then((res) => {
      if (res.data) {
        title.value = res.data.form_title
        title_alignment.value = res.data.title_alignment
        title_color.value = res.data.title_color
        field_data.value = res.data.field_data.map((field) => ({
          ...field,
          error: null
        }))
      }
    })
    .catch((error) => {
      console.error('Error fetching service list:', error)
    })
}

const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
  multiple: true
})

const validationSchema = ref(yup.object({}))

const IS_SUBMITED = ref(false)

const { handleSubmit, errors } = useForm({ validationSchema })

function handleFileUpload(event, element) {
  const file = event.target.files[0]
  const reader = new FileReader()

  reader.onload = (e) => {
    element.value = e.target.result
  }

  reader.readAsDataURL(file) // Convert file to Base64
}

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formData = computed(() =>
  field_data.value.map((field) => ({
    id: field.id,
    label: field.label,
    type: field.type,
    placeholder: field.placeholder,
    validation_status: field.validation_status,
    option: field.option,
    value: field.type === 'file' ? field.value : field.value
  }))
)
const isFormValid = ref(false)
watch(
  field_data,
  () => {
    if (IS_SUBMITED.value) {
      field_data.value.forEach((field) => {
        if ((field.validation_status === 1 || field.validation_status === true) && field.type !== 'hr_tag' && field.type !== 'heading' && field.type !== 'checkbox') {
          const isEmpty = field.value === null || field.value === undefined || (typeof field.value === 'string' && field.value.trim() === '') || (Array.isArray(field.value) && field.value.length === 0)

          field.error = isEmpty ? `${field.label} is required.` : null
        }
      })
    }
  },
  { deep: true }
)

const formSubmit = () => {
  field_data.value.forEach((field) => {
    field.error = null
  })
  let isValid = true
  field_data.value.forEach((field) => {
    if ((field.validation_status === 1 || field.validation_status === true) && field.type !== 'hr_tag' && field.type !== 'heading' && field.type !== 'checkbox') {
      const isEmpty = field.value === null || field.value === undefined || (typeof field.value === 'string' && field.value.trim() === '') || (Array.isArray(field.value) && field.value.length === 0)

      if (isEmpty) {
        isValid = false
        field.error = `${field.label} is required.`
      }
    }
  })
  // Proceed with submission if valid
  const values = {
    form_data: JSON.stringify(formData.value),
    form_id: props.form_id,
    module_id: props.appointment_id,
    module: props.type
  }
  console.log(isValid)
  IS_SUBMITED.value = true
  if (isValid) {
    storeRequest({ url: STORE_DATA_URL, body: values, type: 'file' }).then((res) => {
      if (res.status) {
        display_submit_message(res)
        reset_datatable_close_offcanvas(res)
      }
    })
  }
}
</script>
