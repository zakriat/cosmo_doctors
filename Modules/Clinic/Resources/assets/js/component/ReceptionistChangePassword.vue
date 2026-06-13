<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" id="receptionist_change_password" aria-labelledby="form-offcanvasLabel">
      <FormHeader :createTitle="createTitle"></FormHeader>

      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">

              <!-- Old Password -->
              <label class="form-label" for="old_password">{{ $t('employee.lbl_old_password') }}</label>
              <span class="text-danger">*</span>
              <InputField
                type="password" class="col-md-12"
                :is-required="true"
                :label="$t('employee.lbl_old_password')"
                :placeholder="$t('employee.placeholder_oldpassword')"
                v-model="old_password"
                :error-message="errors['old_password']"
                :error-messages="errorMessages['old_password']"
              />

              <!-- New Password -->
              <label class="form-label" for="password">{{ $t('employee.lbl_password') }}</label>
              <span class="text-danger">*</span>
              <InputField
                type="password" class="col-md-12"
                :is-required="true"
                :label="$t('employee.lbl_password')"
                :placeholder="$t('employee.placeholder_newpassword')"
                v-model="password"
                :error-message="errors['password']"
                :error-messages="errorMessages['password']"
              />

              <!-- Confirm Password -->
              <label class="form-label" for="confirm_password">{{ $t('employee.lbl_confirm_password') }}</label>
              <span class="text-danger">*</span>
              <InputField
                type="password" class="col-md-12"
                :is-required="true"
                :label="$t('employee.lbl_confirm_password')"
                :placeholder="$t('employee.placeholder_confirmpassword')"
                v-model="confirm_password"
                :error-message="errors['confirm_password']"
                :error-messages="errorMessages['confirm_password']"
              />

            </div>
          </div>
        </div>
      </div>
      <FormFooter></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import { CHANGE_PASSWORD_URL } from '../constant/receptionist'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useI18n } from 'vue-i18n'

// props
defineProps({
  createTitle: { type: String, default: '' }
})

const { t } = useI18n()
const { storeRequest } = useRequest()
const currentId = useModuleId(() => {}, 'receptionist_assign')

// ✅ Validation Schema
const validationSchema = yup.object({
  old_password: yup
    .string()
    .required(t('employee.old_password_required')),

  password: yup
    .string()
    .required(t('employee.new_password_required'))
    .min(8, t('employee.password_min_length'))
    .max(14, t('employee.password_max_length'))
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/,
      t('employee.password_complexity')
    )
    .notOneOf([yup.ref('old_password')], t('employee.password_not_same')),

  confirm_password: yup
    .string()
    .required(t('employee.confirm_password_required'))
    .oneOf([yup.ref('password'), null], t('employee.passwords_must_match'))
})

// ✅ Reset default data
const defaultData = () => {
  errorMessages.value = {}
  return {
    old_password: '',
    password: '',
    confirm_password: ''
  }
}

const setFormData = () => {
  resetForm({
    values: {
      old_password: '',
      password: '',
      confirm_password: ''
    }
  })
}

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const { value: old_password } = useField('old_password')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const errorMessages = ref({})

// ✅ Form Submit
const formSubmit = handleSubmit((values) => {
  values.receptionist_id = currentId.value

  storeRequest({ url: CHANGE_PASSWORD_URL, body: values })
    .then((res) => reset_datatable_close_offcanvas(res))
})

// ✅ Handle API response
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#receptionist_change_password').hide()
    setFormData(defaultData())
    currentId.value = 0
  } else {

    errorMessages.value = res.all_message || {}
  }
}

useOnOffcanvasHide('receptionist_change_password', () => setFormData(defaultData()))
</script>
