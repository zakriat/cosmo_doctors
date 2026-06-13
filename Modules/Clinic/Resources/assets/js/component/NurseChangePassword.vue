<template>
  <form @submit="formSubmit">
    <div class="modal fade" id="nurse-change-password" tabindex="-1" aria-labelledby="nurse-change-passwordLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="nurse-change-passwordLabel">{{ createTitle }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="" class="form-label">{{ $t('nurse.lbl_old_password') }} <span class="text-danger">*</span></label>
                  <InputField type="password" class="" :is-required="true" :label="$t('nurse.lbl_old_password')" :placeholder="$t('nurse.placeholder_old_password')" v-model="old_password" :error-message="errors['old_password']" :error-messages="errorMessages['old_password']"></InputField>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="" class="form-label">{{ $t('nurse.lbl_new_password') }} <span class="text-danger">*</span></label>
                  <InputField type="password" class="" :is-required="true" :label="$t('nurse.lbl_new_password')" :placeholder="$t('nurse.placeholder_new_password')" v-model="password" :error-message="errors['password']" :error-messages="errorMessages['password']"></InputField>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="" class="form-label">{{ $t('nurse.lbl_confirm_password') }} <span class="text-danger">*</span></label>
                  <InputField type="password" class="" :is-required="true" :label="$t('nurse.lbl_confirm_password')" :placeholder="$t('nurse.placeholder_confirm_password')" v-model="confirm_password" :error-message="errors['confirm_password']" :error-messages="errorMessages['confirm_password']"></InputField>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('messages.close') }}</button>
            <button type="submit" class="btn btn-primary" :disabled="IS_SUBMITED">
              <i class="fa fa-spinner fa-spin" v-if="IS_SUBMITED"></i>
              {{ $t('messages.save') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { CHANGE_PASSWORD_URL } from '../constant/nurse'
import { useField, useForm } from 'vee-validate'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// props
const props = defineProps({
  createTitle: { type: String, default: '' }
})

const { storeRequest } = useRequest()

const nurse_id = ref(0)

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    nurse_id: 0,
    old_password: '',
    password: '',
    confirm_password: ''
  }
}

// Reload Datatable, SnackBar Message, Alert, Modal Close
const reset_datatable_close_modal = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Modal.getInstance('#nurse-change-password').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Validations
const validationSchema = yup.object({
  old_password: yup.string().required(t('messages.old_password_required')),
  password: yup
    .string()
    .required(t('messages.password_required'))
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])\S{8,14}$/,
      'Password must be 8-14 chars, include upper, lower, digit, special, no spaces'
    ),
  confirm_password: yup
    .string()
    .required(t('messages.confirm_password_required'))
    .oneOf([yup.ref('password')], t('messages.passwords_must_match'))
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: old_password } = useField('old_password')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')

const errorMessages = ref({})

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      nurse_id: data.nurse_id,
      old_password: data.old_password,
      password: data.password,
      confirm_password: data.confirm_password
    }
  })
}

const IS_SUBMITED = ref(false)

// Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.nurse_id = nurse_id.value
  storeRequest({ url: CHANGE_PASSWORD_URL, body: values }).then((res) => reset_datatable_close_modal(res))
})

onMounted(() => {
  setFormData(defaultData())
  
  // Listen for nurse change password event
  document.addEventListener('nurse_change_password', function (event) {
    nurse_id.value = event.detail.nurse_id
    setFormData(defaultData())
  })
})
</script>