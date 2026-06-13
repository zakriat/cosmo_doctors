<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_telemed_service')" icon="fa-solid fa-coins"></CardTitle>
    </div>
    <div class="row">
      <div class="form-group border-bottom pb-3">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0" for="google_meet">{{ $t('settings.lbl_google_meet') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="google_meet_method" :checked="google_meet_method == 1 ? true : false" name="google_meet_method" id="google_meet" type="checkbox" v-model="google_meet_method"  @change="handleGoogleMeetChange" />
          </div>
        </div>
      </div>
      <div v-if="google_meet_method == 1">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="google_clientid">{{ $t('settings.lbl_client_id') }}</label>
              <input type="text" class="form-control" v-model="google_clientid" id="google_clientid" :placeholder="$t('settings.lbl_client_id')" name="google_clientid" :errorMessage="errors.google_clientid" :errorMessages="errorMessages.google_clientid" />
              <p class="text-danger" v-for="msg in errorMessages.google_clientid" :key="msg">{{ msg }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="google_secret_key">{{ $t('settings.lbl_secret_key') }}</label>
              <input type="text" class="form-control" v-model="google_secret_key" id="google_secret_key" :placeholder="$t('settings.lbl_secret_key')" name="google_secret_key" :errorMessage="errors.google_secret_key" :errorMessages="errorMessages.google_secret_key" />
              <p class="text-danger" v-for="msg in errorMessages.google_secret_key" :key="msg">{{ msg }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="google_appname">{{ $t('settings.lbl_app_name') }}</label>
              <input type="text" class="form-control" v-model="google_appname" id="google_appname" :placeholder="$t('settings.lbl_app_name')" name="google_appname" :errorMessage="errors.google_appname" :errorMessages="errorMessages.google_appname" />
              <p class="text-danger" v-for="msg in errorMessages.google_appname" :key="msg">{{ msg }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="google_meet_method == 1">
      <div class="row">
        <div class="form-group">
          <label class="form-label" for="google_event_title">{{ $t('settings.lbl_google_event_title') }}</label>
          <input type="text" class="form-control" v-model="google_event" id="google_event" name="google_event" :errorMessage="errors.google_event" :errorMessages="errorMessages.google_event" />
          <p class="text-danger" v-for="msg in errorMessages.google_event" :key="msg">{{ msg }}</p>
        </div>
        <div class="form-group">
          <label class="form-label" for="google_event">{{ $t('settings.lbl_google_event') }}</label>
          <VueEditor v-model="content" :options="editorOptions" />
        </div>
      </div>
    </div>

      <div class="form-group border-bottom pb-3">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-zoom">{{ $t('settings.lbl_zoom') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_zoom" :checked="is_zoom == 1 ? true : false" name="is_zoom" id="category-zoom" type="checkbox" v-model="is_zoom" @change="handleZoomChange" />
          </div>
        </div>
      </div>
      <div v-if="is_zoom == 1">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="account_id">{{ $t('settings.lbl_account_id') }}</label>
              <input type="text" class="form-control" v-model="account_id" id="account_id" name="account_id" :errorMessage="errors.account_id" :errorMessages="errorMessages.account_id" />
              <p class="text-danger" v-for="msg in errorMessages.account_id" :key="msg">{{ msg }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="client_id">{{ $t('settings.lbl_client_id') }}</label>
              <input type="text" class="form-control" v-model="client_id" id="client_id" name="client_id" :errorMessage="errors.client_id" :errorMessages="errorMessages.client_id" />
              <p class="text-danger" v-for="msg in errorMessages.client_id" :key="msg">{{ msg }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label" for="client_secret">{{ $t('settings.lbl_client_secret') }}</label>
              <input type="text" class="form-control" v-model="client_secret" id="client_secret" name="client_secret" :errorMessage="errors.client_secret" :errorMessages="errorMessages.client_secret" />
              <p class="text-danger" v-for="msg in errorMessages.client_secret" :key="msg">{{ msg }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { VueEditor } from 'vue3-editor'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
//
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()

const content = ref('')
const google_event = ref('')
const quillContent = `
      <p>New appointment</p>
      <p>Your have new appointment on</p>
      <p>Date: {{appointment_date}}, Time: {{appointment_time}}, Patient: {{patient_name}}</p>
      <p>Clinic: {{clinic_name}}</p>
      <p>Appointment Description: {{appointment_desc}}</p>
      <p>Thank you.</p>
    `
const eventTitle = `{{service_name}}`
const editorOptions = {
  // Quill editor options if needed
}


const IS_SUBMITED = ref(false)
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      google_meet_method: data.google_meet_method || 0,
      google_clientid: data.google_clientid || '',
      google_secret_key: data.google_secret_key || '',
      google_appname: data.google_appname || '',
      is_zoom: data.is_zoom || 0,
      account_id: data.account_id || '',
      client_id: data.client_id || '',
      client_secret: data.client_secret || ''
    }
  })
}
const validationSchema = yup.object({
  google_clientid: yup.string().test('google_clientid', 'Must be a valid Google Client ID', function (value) {
    if (this.parent.google_meet_method == '1' && !value) {
      return false
    }
    return true
  }),
  google_secret_key: yup.string().test('Must be a valid Google  SecretKey', function (value) {
    if (this.parent.google_meet_method == '1' && !value) {
      return false
    }
    return true
  }),

  google_appname: yup.string().test('Must be a valid Google Appname', function (value) {
    if (this.parent.google_meet_method == '1' && !value) {
      return false
    }
    return true
  }),
  account_id: yup.string().test('account_id', 'Zoom Account ID is Required', function (value) {
    if (this.parent.is_zoom == '1' && !value) {
      return false
    }
    return true
  }),

  client_id: yup.string().test('client_id', 'Zoom Client ID is Required', function (value) {
    if (this.parent.is_zoom == '1' && !value) {
      return false
    }
    return true
  }),

  client_secret: yup.string().test('client_secret', 'Zoom Client Secret key is Required', function (value) {
    if (this.parent.is_zoom == '1' && !value) {
      return false
    }
    return true
  })
})
const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const errorMessages = ref({})
const { value: google_meet_method } = useField('google_meet_method')
const { value: google_clientid } = useField('google_clientid')
const { value: google_secret_key } = useField('google_secret_key')
const { value: google_appname } = useField('google_appname')
const { value: is_zoom } = useField('is_zoom')
const { value: account_id } = useField('account_id')
const { value: client_id } = useField('client_id')
const { value: client_secret } = useField('client_secret')


const handleGoogleMeetChange = () => {

      if (google_meet_method.value == 0) {

           is_zoom.value = 1;
      }

      if (google_meet_method.value == 1) {

            is_zoom.value = 0;
        }
    };


   const handleZoomChange = () => {

   if (is_zoom.value == 0) {

     google_meet_method.value = 1;
   }

   if (is_zoom.value == 1) {

     google_meet_method.value = 0;
     }
   };





watch(
  () => google_meet_method.value,
  (value) => {

  },
  { deep: true }
)

watch(
  () => is_zoom.value,
  (value) => {

  },
  { deep: true }
)

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
  }
}

//fetch data
const data = 'google_meet_method,google_clientid,google_secret_key,google_appname,is_zoom,account_id,client_id,client_secret,content,google_event'
onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    content.value = response.content || quillContent;
    google_event.value = response.google_event || eventTitle;
    setFormData(response)
  })
})
//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const newValues = {}
  if (content.value.trim() !== '') {
    newValues['content'] = content.value;
    newValues['google_event'] = google_event.value;
  }
  Object.keys(values).forEach((key) => {
    if (values[key] !== '') {
      newValues[key] = values[key] || ''
    }
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
  }).then((res) => display_submit_message(res))
})

defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})
</script>
