<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_integration_page.app_configuration')" icon="fa-solid fa-sliders"></CardTitle>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-enable_user_push_notification">{{ $t('setting_integration_page.lbl_enable_user_push_notification') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_user_push_notification" :checked="is_user_push_notification == 1 ? true : false" name="is_user_push_notification" id="category-is_user_push_notification" type="checkbox" v-model="is_user_push_notification" />
        </div>
      </div>
    </div>

    <!-- <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-enable_chat_gpt">{{ $t('setting_integration_page.lbl_enable_chat_gpt') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="enable_chat_gpt" :checked="enable_chat_gpt == 1 ? true : false" name="enable_chat_gpt" id="category-enable_chat_gpt" type="checkbox" v-model="enable_chat_gpt" />
        </div>
      </div>
    </div>
    <div v-if="enable_chat_gpt == 1"> -->
      <!-- <div class="form-group border-bottom pb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0 fw-normal" for="category-test_without_key">{{ $t('setting_integration_page.lbl_test_without_key') }} </label>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" :true-value="1" :false-value="0" :value="test_without_key" :checked="test_without_key == 1 ? true : false" name="test_without_key" id="category-test_without_key" type="checkbox" v-model="test_without_key" />
            </div>
          </div>
        </div> -->
      <!-- <div v-if="test_without_key == 0"> -->
      <!-- <div class="form-group border-bottom pb-3">
        <label for="category-chatgpt_key">{{ $t('setting_integration_page.key') }}</label>
        <input type="text" class="form-control" v-model="chatgpt_key" id="chatgpt_key" name="chatgpt_key" :errorMessage="errors.chatgpt_key" :errorMessages="errorMessages.chatgpt_key" />
        <p class="text-danger" v-for="msg in errorMessages.chatgpt_key" :key="msg">{{ msg }}</p>
      </div> -->
      <!-- </div> -->
    <!-- </div> -->

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-firebase_notification">{{ $t('setting_integration_page.lbl_firebase') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="firebase_notification" :checked="firebase_notification == 1 ? true : false" name="firebase_notification" id="category-firebase_notification" type="checkbox" v-model="firebase_notification" />
        </div>
      </div>
    </div>

    <div v-if="firebase_notification == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group mb-0">
            <label class="form-label" for="firebase_project_id">{{ $t('setting_integration_page.lbl_firebase_project_id') }}</label>
            <input type="text" class="form-control" v-model="firebase_project_id" placeholder="Firebase Project ID" id="firebase_project_id" name="firebase_project_id" :errorMessage="errors.firebase_project_id" :errorMessages="errorMessages.firebase_project_id" />
            <p class="text-danger" v-for="msg in errorMessages.firebase_project_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.firebase_project_id }}</span>
          </div>
        </div>
        <div class="form-group col-sm-6 mb-0"> 
          <label for="json_file" class="form-control-label">
            {{ $t('setting_integration_page.firebase_json_file') }}
            <span class="ml-3">
              <a class="text-primary" href="https://console.firebase.google.com/">{{ $t('setting_integration_page.Download_JSON_File') }}</a>
            </span>
          </label>
          <div class="custom-file">
            <input type="file" class="form-control" id="json_file" name="json_file" ref="refInput" accept=".json" @change="fileUpload" />
            <div v-if="file_name">
              <label class="custom-file-label upload-label border-0" style="font-weight: bold;">{{ file_name }}</label>
            </div>
            <div v-else>
              <label class="custom-file-label upload-label border-0">{{ $t('setting_integration_page.Upload_Firebase_JSON_files_only_once') }}.</label>
            </div>
            <small class="help-block with-errors text-danger"></small>
          </div>
        </div>
      </div>
    </div>

    <!-- submitbtn -->
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL, GET_JSON } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
const fileUpload = (e) => {
  let files = e.target.files
  json_file.value = files[0]
}

const file_name = ref(null)
const fetchJsonFiles = async () => {
  const jsonContent = {
    data: null
  }
  try {
    const response = await createRequest(GET_JSON())
    console.log(response[0])
    file_name.value = response[0]
  } catch (error) {
    console.error('Error fetching JSON files:', error)
  }
}
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      // is_event: data.is_event || 0,
      // is_blog: data.is_blog || 0,
      is_user_push_notification: data.is_user_push_notification || 0,
      // is_provider_push_notification: data.is_provider_push_notification || 0,
      enable_chat_gpt: data.enable_chat_gpt || 0,
      // test_without_key: data.test_without_key || 1,
      chatgpt_key: data.chatgpt_key || '',
      firebase_notification: data.firebase_notification || 0,
      firebase_project_id: data.firebase_project_id || '',
      json_file: data.json_file
    }
  })
}
//validation
const validationSchema = yup.object({
  chatgpt_key: yup.string().test('chatgpt_key', 'Must be a valid ChatGPT key', function (value) {
    if (this.parent.enable_chat_gpt == '1' && !value) {
      return false
    }
    return true
  }),

  firebase_project_id: yup.string().test('firebase_project_id', 'Must be a valid Firebase Project ID', function (value) {
    if (this.parent.firebase_notification == '1' && !value) {
      return false
    }
    return true
  })
})
const { handleSubmit, errors, resetForm, validate } = useForm({ validationSchema })
const errorMessages = ref({})
// const { value: is_event } = useField('is_event')
// const { value: is_blog } = useField('is_blog')
const { value: is_user_push_notification } = useField('is_user_push_notification')
// const { value: is_provider_push_notification } = useField('is_provider_push_notification')
const { value: enable_chat_gpt } = useField('enable_chat_gpt')
// const { value: test_without_key } = useField('test_without_key')
const { value: chatgpt_key } = useField('chatgpt_key')
const { value: firebase_notification } = useField('firebase_notification')
const { value: firebase_project_id } = useField('firebase_project_id')
const { value: json_file } = useField('json_file')

watch(
  () => enable_chat_gpt.value,
  (value) => {
    if (value == '0') {
      chatgpt_key.value = ''
    }
  },
  { deep: true }
)

watch(
  () => firebase_notification.value,
  (value) => {
    if (value == '0') {
      firebase_project_id.value = ''
    }
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
const data = [
  // 'is_event',
  // 'is_blog',
  'is_user_push_notification',
  // 'is_provider_push_notification',
  'enable_chat_gpt',
  // 'test_without_key',
  'chatgpt_key',
  'firebase_notification',
  'firebase_project_id',
  'json_file'
]

onMounted(() => {
  const customData = [...data].join(',')

  createRequest(GET_URL(customData)).then((response) => {
    setFormData(response)
  })
  fetchJsonFiles()
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const formData = new FormData()
  Object.keys(values).forEach((key) => {
    const value = values[key]
    if (value !== '') {
      if (typeof value === 'string') {
        formData[key] = value.trim()
      } else if (!isNaN(value) && value !== null && value !== undefined) {
        formData[key] = parseInt(value, 10)
      } else {
        formData[key] = value
      }
    }
  })
  if (json_file.value) {
    formData.append('json_file', json_file.value)
  }
  storeRequest({
    url: STORE_URL,
    body: formData,
    type: 'file'
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
