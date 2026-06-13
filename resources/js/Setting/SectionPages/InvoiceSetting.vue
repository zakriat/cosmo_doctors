<template>
  <form @submit="formSubmit">
    <CardTitle :title="$t('setting_sidebar.lbl_inv_setting')" icon="fa-solid fa-file-invoice"></CardTitle>
    <div class="row">
      <div class="form-group col-md-6">
        <label class="form-label">{{$t('setting_invoice.lbl_order_prefix')}} <span class="text-danger">*</span></label>
      <InputField  :is-required="false" :label="$t('setting_invoice.lbl_order_prefix')" placeholder="# - INV" v-model="inv_prefix" :error-message="errors.inv_prefix"></InputField>
      </div>
      <div class="form-group col-md-6">
        <label class="form-label">{{$t('setting_invoice.lbl_order_starts')}} <span class="text-danger">*</span></label>
      <InputField  type="number" :is-required="false" :label="$t('setting_invoice.lbl_order_starts')" placeholder="1" v-model="order_code_start" :error-message="errors.order_code_start"></InputField>
      </div>
      <div class="form-group col-12">
        <label class="form-label">{{$t('setting_invoice.lbl_spacial_note')}} <span class="text-danger">*</span></label>
      <InputField class="col-12" :is-required="false" :label="$t('setting_invoice.lbl_spacial_note')" :placeholder="$t('setting_invoice.spacial_note')" v-model="spacial_note" :error-message="errors.spacial_note"></InputField>
      </div>
    </div>
    <div class="d-grid d-md-flex gap-3 align-items-center">
      <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'

import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      inv_prefix: data.inv_prefix,
      order_code_start: data.order_code_start,
      spacial_note: data.spacial_note,
    }
  })
}

//validation
const validationSchema = yup.object({
  inv_prefix: yup.string().required('Invoice Prefix Is Required'),
  order_code_start: yup.number().required('Order Starts Is Required'),
  spacial_note: yup.string().required('Spacial Note Is Required'),
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

const { value: inv_prefix } = useField('inv_prefix')
const { value: order_code_start } = useField('order_code_start')
const { value: spacial_note } = useField('spacial_note')
//fetch data
const data = 'inv_prefix,order_code_start,spacial_note'

onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  storeRequest({ url: STORE_URL, body: values }).then((res) => display_submit_message(res))
})
</script>
