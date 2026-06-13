<template>
  <form @submit="formSubmit" class="">
    <div class="modal fade" id="custom_form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <span :class="[title_alignment, title_color]">{{ title }}</span>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row" id="form-offcanvas">

              <div v-for="(element, index) in field_data" :key="element.id" class="form-field">
                <!-- Text Field -->
                <div v-if="element.type === 'text'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <input type="text" :id="'text_' + element.id" v-model="element.value" class="form-control"
                    :placeholder="element.placeholder" />
                </div>

                <!-- Number Field -->
                <div v-else-if="element.type === 'number'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <input type="number" :id="'number_' + element.id" v-model="element.value" class="form-control"
                    :placeholder="element.placeholder" />
                </div>

                <!-- Checkbox -->
                <div v-else-if="element.type === 'checkbox-group'" :ref="'field_' + element.id">
                  <label>
                    {{ element.label }}
                    <span v-if="element.validation_status == 1" class="text-danger">*</span>
                  </label>
                  <div v-for="(option, index) in element.option" :key="index" class="form-check">
                    <input type="checkbox" :id="'checkbox_' + element.id + '_' + option" :value="option"
                      v-model="element.value" class="form-check-input" />
                    <label :for="'checkbox_' + element.id + '_' + option" class="form-check-label">
                      {{ option }}
                    </label>
                  </div>
                </div>

                <!-- TextArea -->
                <div v-else-if="element.type === 'textarea'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <textarea :id="'textarea_' + element.id" v-model="element.value" class="form-control"
                    :placeholder="element.placeholder"></textarea>
                </div>

                <!-- File Upload -->
                <div v-else-if="element.type === 'file'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <input type="file" :id="'file_' + element.id" class="form-control"
                    @change="handleFileUpload($event, element)" />

                  <div v-if="element.value" class="mt-2">
                    <img :src="element.value" alt="Uploaded Image" class="img-fluid" height="100px" width="100px" />
                  </div>
                </div>

                <!-- Multi Select -->
                <!-- <div v-else-if="element.type === 'multi_select'" :ref="'field_' + element.id">
            <label>{{ element.label }}
              <span v-if="element.validation_status == 1" class="text-danger">*</span>
            </label>
            <select multiple :id="'multiselect_' + element.id" class="form-control">
              <option v-if="!element.option.length">No options available</option>
              <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
                {{ option }}
              </option>
            </select>
          </div> -->

                <div v-else-if="element.type === 'multi_select'" :ref="'field_' + element.id" class="form-group">
                  <label>{{ element.label }}
                    <span v-if="element.validation_status == 1" class="text-danger">*</span>
                  </label>
                  <Multiselect :id="'multiselect_' + element.id" :options="element.option" v-model="element.value"
                    :searchable="true" multiple="true" v-bind="multiSelectOption">
                  </Multiselect>
                </div>

                <!-- Select -->
                <div v-else-if="element.type === 'select'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status === 1"
                      class="text-danger">*</span></label>
                  <select :id="'select_' + element.id" class="form-control">
                    <option v-if="!element.option.length">No options available</option>
                    <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
                      {{ option }}
                    </option>
                  </select>
                </div>

                <!-- Radio -->
                <div v-else-if="element.type === 'radio'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <div v-for="(option, optIndex) in element.option" :key="optIndex" class="form-check">
                    <input type="radio" :id="'radio_' + element.id + '_' + option" :value="option"
                      v-model="element.value" class="form-check-input" />
                    <label :for="'radio_' + element.id + '_' + option" class="form-check-label">
                      {{ option }}
                    </label>
                  </div>
                </div>

                <!-- Calendar -->
                <div v-else-if="element.type === 'calender'" :ref="'field_' + element.id">
                  <label>{{ element.label }} <span v-if="element.validation_status == 1"
                      class="text-danger">*</span></label>
                  <input type="date" :id="'calendar_' + element.id" v-model="element.value" class="form-control" />
                </div>

                <!-- Heading -->
                <div v-else-if="element.type === 'heading'" :ref="'field_' + element.id">
                  <h3>{{ element.label }}</h3>
                </div>

                <!-- Hr Tag -->
                <div v-else-if="element.type === 'hr_tag'" :ref="'field_' + element.id">
                  <hr />
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>

              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('appointment.close')
                }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>

import { ref, computed,watch} from 'vue'
import { useForm } from 'vee-validate'
import {  useRequest } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { GET_CUSTOM_FORM, STORE_DATA_URL } from '@/vue/constants/custom-form'


const props = defineProps({
encounter_id: { type: Number, default: 0 },
form_id: { type: Number, default: 0 },
})


const emit = defineEmits(['save_customform'])


const title = ref(null)
const title_alignment = ref(null)
const title_color = ref(null)
const field_data = ref([])


watch(

() => props.form_id,

(value) => {
  props.form_id = value
  if (value > 0) {

     listingRequest({ url: GET_CUSTOM_FORM, data: { form_id: props.form_id, appointent_id: props.encounter_id, type: 'encounter' } }).then((res) => {
      if (res.data) {
        title.value = res.data.form_title;
        title_alignment.value = res.data.title_alignment;
        title_color.value = res.data.title_color;
        field_data.value = res.data.field_data;
      }
    }).catch(error => {
      console.error('Error fetching service list:', error);
    });
  
}

}


)


const IS_SUBMITED = ref(false)


const { storeRequest, listingRequest } = useRequest()

const validationSchema = yup.object({

})

const { handleSubmit, errors } = useForm({ validationSchema })


function handleFileUpload(event, element) {
  const file = event.target.files[0]; // Capture the first selected file
  const reader = new FileReader();

  reader.onload = (e) => {
    element.value = e.target.result; // Store the Base64 string in the element's value property
  };

  reader.readAsDataURL(file); // Convert file to Base64
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

const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
  multiple: true
})

const reset_datatable_close_offcanvas = (res) => {

if (res.status) {
  window.successSnackbar(res.message)
  bootstrap.Modal.getInstance('#custom_form_modal').hide()
  emit('save_customform')
 
} else {
  window.errorSnackbar(res.message)
  errorMessages.value = res.all_message
}
}


const formData = computed(() => field_data.value.map(field => ({
  id: field.id,
  label: field.label,
  type: field.type,
  placeholder: field.placeholder,
  validation_status: field.validation_status,
  option: field.option,
  value: field.type === 'file' ? field.value : field.value
})));

//Form Submit
const formSubmit = handleSubmit((values) => {

  values.form_data = JSON.stringify(formData.value)
  values.form_id = props.form_id,
    values.module_id = props.encounter_id,
    values.module = 'encounter',

    IS_SUBMITED.value = true

  storeRequest({ url: STORE_DATA_URL, body: values, type: 'file' }).then((res) => {
    if (res.status) {
      display_submit_message(res)
      reset_datatable_close_offcanvas(res)
    }
  })
})



</script>
