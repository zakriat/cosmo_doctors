<template>
  <form @submit="formSubmit" class="">
    <div class="card" id="addMedicalReport">
      <div class="card-header mb-4">
        <h5 class="card-title mb-0">Add Medical Records</h5>
      </div>
      <div class="card-body border-top">
        <div class="row">
          <div class="form-group col-md-4">
            <label class="form-label">{{ $t('category.lbl_name') }} <span class="text-danger">*</span></label>
            <InputField :is-required="true" :label="$t('category.lbl_name')" :placeholder="$t('category.lbl_name')" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>
          </div>
          <div class="col-md-4 form-group">
            <label class="form-label" for="date">{{ $t('clinic.lbl_date') }} <span class="text-danger">*</span></label>

            <flat-pickr :placeholder="$t('clinic.lbl_date')" id="date" class="form-control" v-model="date" :value="date" :config="config"></flat-pickr>
            <span v-if="errorMessages['date']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['date']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.date }}</span>
          </div>
          <div class="form-group col-md-4">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label" for="file_url">{{ $t('clinic.lbl_file_url') }} </label>
            </div>
            <input type="file" class="form-control" id="file_url" ref="refInput" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif, .pdf" />
            <span v-if="errorMessages['file_url']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['file_url']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.file_url }}</span>
          </div>
        </div>
      </div>
      <div class="card-footer border-top">
        <div class="d-flex align-items-center justify-content-end gap-3">
          <button class="btn btn-light d-block" type="button" @click="closeTemplate()">
            {{ $t('messages.cancel') }}
          </button>
          <button v-if="isLoading == 0" class="btn btn-secondary" type="submit">
            {{ $t('appointment.save') }}
          </button>

          <button v-else :disabled="isLoading == 1" class="btn btn-secondary" type="submit">
            {{ $t('appointment.loading') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { STORE_MEDICAL_REPORT, EDIT_MEDICAL_REPORT, UPDATE_MEDICAL_REPORT } from '../../constant/encouter_report'
import FlatPickr from 'vue-flatpickr-component'
import { readFile } from '@/helpers/utilities'

const emit = defineEmits(['submitMedical', 'closeTemplate'])

const props = defineProps({
  encounter_id: { type: Number, default: '' },
  user_id: { type: Number, default: '' },
  report_id: { type: Number, default: 0 }
})

const isLoading = ref(0)

const ImageViewer = ref(null)
const fileUpload = async (e) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    ImageViewer.value = fileB64
  })
  file_url.value = file
}

const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  maxDate: 'today'
})

const { storeRequest, listingRequest, getRequest, updateRequest } = useRequest()

const currentId = ref(props.report_id)

// Edit Form Or Create Form
watch(
  () => props.report_id,
  (value) => {
    currentId.value = value
    if (value > 0) {
      getRequest({ url: EDIT_MEDICAL_REPORT, id: value }).then((res) => {
        if (res.status && res.data) {
          setFormData(res.data)
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  createOption: true
})

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  const currentDate = new Date().toISOString().substr(0, 10)
  errorMessages.value = {}
  return {
    name: '',
    date: currentDate,
    file_url: null
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.file_url
  resetForm({
    values: {
      name: data.name,
      date: data.date,
      file_url: data.file_url
    }
  })
}

// Validations
const validationSchema = yup.object({
  name: yup.string().required('Name is required'),
  date: yup.string().required('Date is required')
  // file_url: yup.string().required('Medical Report is Required'),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: date } = useField('date')
const { value: file_url } = useField('file_url')

const errorMessages = ref({})

onMounted(() => {
  if (props.report_id > 0) {
    getRequest({ url: EDIT_MEDICAL_REPORT, id: props.report_id }).then((res) => {
      if (res.status && res.data) {
        setFormData(res.data)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const formSubmit = handleSubmit((values) => {
  isLoading.value = 1

  values.user_id = props.user_id
  values.encounter_id = props.encounter_id

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_MEDICAL_REPORT, body: values, id: currentId.value }).then((res) => {
      if (res.status) {
        emit('submitMedical', { type: 'create_customer', value: res.medical_report })
        setFormData(defaultData())
        isLoading.value = 0
      }
    })

    currentId.value = 0
  } else {
    storeRequest({ url: STORE_MEDICAL_REPORT, body: values, type: 'file' }).then((res) => {
      if (res.status) {
        emit('submitMedical', { type: 'create_customer', value: res.medical_report })
        setFormData(defaultData())
        isLoading.value = 0
      }
    })
  }
})

const closeTemplate = () => {
  emit('closeTemplate')
}
</script>
