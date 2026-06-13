<template>
  <form @submit="formSubmit" class="bussiness-hour">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="appointment-customform" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h6 class="m-0 h5">
          <span :class="[title_alignment, title_color]">{{ title }}</span>
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div v-for="(element, index) in field_data" :key="element.id" class="form-field">
          <!-- Text Field -->
          <div v-if="element.type === 'text'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
            <input type="text" :id="'text_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
          </div>

          <!-- Number Field -->
          <div v-else-if="element.type === 'number'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
            <input type="number" :id="'number_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
          </div>

          <!-- Checkbox -->
          <div v-else-if="element.type === 'checkbox'" :ref="'field_' + element.id">
            <label>
              {{ element.label }}
              <span v-if="element.validation_status == 1" class="text-danger">*</span>
            </label>
            <div v-for="(option, index) in element.option" :key="index" class="form-check">
              <input type="checkbox" :id="'checkbox_' + element.id + '_' + option" :value="option" class="form-check-input" />
              <label :for="'checkbox_' + element.id + '_' + option" class="form-check-label">
                {{ option }}
              </label>
            </div>
          </div>

          <!-- TextArea -->
          <div v-else-if="element.type === 'textarea'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
            <textarea :id="'textarea_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder"></textarea>
          </div>

          <!-- File Upload -->
          <div v-else-if="element.type === 'file'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
            <input type="file" :id="'file_' + element.id" class="form-control" @change="handleFileUpload($event, element)" />

            <div v-if="element.value" class="mt-2">
              <img :src="element.value" alt="Uploaded Image" class="img-fluid" height="150px" width="200px" />
            </div>
          </div>

          <!-- Multi Select -->
          <div v-else-if="element.type === 'multi_select'" :ref="'field_' + element.id" class="form-group">
            <label
              >{{ element.label }}
              <span v-if="element.validation_status == 1" class="text-danger">*</span>
            </label>
            <Multiselect :id="'multiselect_' + element.id" :options="element.option" v-model="element.value" :searchable="true" multiple="true" v-bind="multiSelectOption"> </Multiselect>
          </div>

          <!-- Select -->
          <div v-else-if="element.type === 'select'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status === 1" class="text-danger">*</span></label>
            <Multiselect :id="'select_' + element.id" :options="element.option" v-model="element.value" :searchable="true" multiple="false" v-bind="singleSelectOption"> </Multiselect>
            <!-- <select :id="'select_' + element.id" class="form-control" v-model="element.value">
              <option value="" disabled>Select option</option>
              <option v-if="!element.option.length">No options available</option>
              <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
                {{ option }}
              </option>
            </select> -->
          </div>

          <!-- Radio -->
          <div v-else-if="element.type === 'radio'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
            <div v-for="(option, optIndex) in element.option" :key="optIndex" class="form-check">
              <input type="radio" :id="'radio_' + element.id + '_' + option" :value="option" v-model="element.value" class="form-check-input" :checked="optIndex === 0" />
              <label :for="'radio_' + element.id + '_' + option" class="form-check-label">
                {{ option }}
              </label>
            </div>
          </div>

          <!-- Calendar -->
          <div v-else-if="element.type === 'calender'" :ref="'field_' + element.id">
            <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
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
      </div>

      <div class="offcanvas-footer border-top pt-4">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
          <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.cancel') }}
          </button>
          <button :disabled="!isFormValid" class="btn btn-secondary" name="submit">
            {{ $t('messages.save') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watchEffect, computed, watch } from 'vue'
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

const { storeRequest, listingRequest } = useRequest()

const { formId, appointmentId, appointmentType } = useFormModuleId(() => {
  if (formId.value) {
    listingRequest({ url: GET_CUSTOM_FORM, data: { form_id: formId.value, appointent_id: appointmentId.value, type: appointmentType.value } })
      .then((res) => {
        if (res.data) {
          title.value = res.data.form_title
          title_alignment.value = res.data.title_alignment
          title_color.value = res.data.title_color
          field_data.value = res.data.field_data

          console.log(res.data.field_data)
        }
      })
      .catch((error) => {
        console.error('Error fetching service list:', error)
      })
  }
}, 'custom_form_assign')

const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
  multiple: true
})

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  multiple: false
})

const IS_SUBMITED = ref(false)

const validationSchema = yup.object({})

const { handleSubmit, errors } = useForm({ validationSchema })

function handleFileUpload(event, element) {
  const file = event.target.files[0] // Capture the first selected file
  const reader = new FileReader()

  reader.onload = (e) => {
    element.value = e.target.result // Store the Base64 string in the element's value property
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
    bootstrap.Offcanvas.getInstance('#appointment-customform').hide()
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

watch(
  field_data,
  () => {
    isFormValid.value = field_data.value.every((field) => {
      console.log(field.type + ':' + field.value)

      if ((field.validation_status === 1 || field.validation_status === true) && field.type != 'hr_tag' && field.type != 'heading' && field.type != 'checkbox') {
        return field.value !== null && field.value !== undefined && (typeof field.value !== 'string' || field.value.trim() !== '') && (!Array.isArray(field.value) || field.value.length > 0)
      }
      return true
    })
  },
  { deep: true }
)

const isFormValid = ref(false) // Use ref to make it reactive

//Form Submit
const formSubmit = handleSubmit((values) => {
  values.form_data = JSON.stringify(formData.value)
  ;(values.form_id = formId.value), (values.module_id = appointmentId.value), (values.module = appointmentType.value), (IS_SUBMITED.value = true)

  storeRequest({ url: STORE_DATA_URL, body: values, type: 'file' }).then((res) => {
    if (res.status) {
      display_submit_message(res)
      reset_datatable_close_offcanvas(res)
    }
  })
})
</script>
