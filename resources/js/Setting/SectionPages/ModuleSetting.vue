<template>
    <form @submit="formSubmit">
      <div>
        <CardTitle :title="$t('setting_module_page.module_setting')" icon="fa-solid fa-sliders"></CardTitle>
      </div>
     <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_clinic_soap">{{ $t('setting_module_page.lbl_soap') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="view_patient_soap" :checked="view_patient_soap == 1 ? true : false" name="view_patient_soap" id="category-view_patient_soap" type="checkbox" v-model="view_patient_soap" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_body_chart') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_body_chart" :checked="is_body_chart == 1 ? true : false" name="is_body_chart" id="category-is_body_chart" type="checkbox" v-model="is_body_chart" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_telemed_setting') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_telemed_setting" :checked="is_telemed_setting == 1 ? true : false" name="is_telemed_setting" id="category-is_telemed_setting" type="checkbox" v-model="is_telemed_setting" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_multi_vendor') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_multi_vendor" :checked="is_multi_vendor == 1 ? true : false" name="is_multi_vendor" id="category-is_multi_vendor" type="checkbox" v-model="is_multi_vendor" />
          </div>
        </div>
      </div>

      <!-- <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_blog') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_blog" :checked="is_blog == 1 ? true : false" name="is_blog" id="category-is_blog" type="checkbox" v-model="is_blog" />
          </div>
        </div>
      </div> -->

  <div>
    <h5 class="my-3">{{ $t('setting_module_page.lbl_encounter_module')  }}</h5>
    <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_problem') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_encounter_problem" :checked="is_encounter_problem == 1 ? true : false" name="is_encounter_problem" id="category-is_encounter_problem" type="checkbox" v-model="is_encounter_problem" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_observation') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_encounter_observation" :checked="is_encounter_observation == 1 ? true : false" name="is_encounter_observation" id="category-is_encounter_observation" type="checkbox" v-model="is_encounter_observation" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_note') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_encounter_note" :checked="is_encounter_note == 1 ? true : false" name="is_encounter_note" id="category-is_encounter_note" type="checkbox" v-model="is_encounter_note" />
          </div>
        </div>
      </div>

      <div class="form-group border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label m-0 fw-normal" for="category-enable_blog">{{ $t('setting_module_page.lbl_prescription') }} </label>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="is_encounter_prescription" :checked="is_encounter_prescription == 1 ? true : false" name="is_encounter_prescription" id="category-is_encounter_prescription" type="checkbox" v-model="is_encounter_prescription" />
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
  import { STORE_URL, GET_URL } from '@/vue/constants/setting'
  import { useRequest } from '@/helpers/hooks/useCrudOpration'
  import { onMounted } from 'vue'
  import { createRequest } from '@/helpers/utilities'
  import SubmitButton from './Forms/SubmitButton.vue'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  const { storeRequest } = useRequest()
  const IS_SUBMITED = ref(false)
  //  Reset Form
  const setFormData = (data) => {
    resetForm({
      values: {
        view_patient_soap: data.view_patient_soap || 0,
        is_body_chart: data.is_body_chart || 0,
        is_telemed_setting: data.is_telemed_setting || 0,
        is_multi_vendor: data.is_multi_vendor || 0,
        is_blog: data.is_blog || 0,
        is_encounter_problem: data.is_encounter_problem || 0,
        is_encounter_observation: data.is_encounter_observation || 0,
        is_encounter_note: data.is_encounter_note || 0,
        is_encounter_prescription: data.is_encounter_prescription || 0,
      }
    })
  }
  //validation
  const validationSchema = yup.object({

  })
  const { handleSubmit, errors, resetForm, validate } = useForm({validationSchema})
  const errorMessages = ref({})
  const { value: view_patient_soap } = useField('view_patient_soap')
  const { value: is_body_chart } = useField('is_body_chart')
  const { value: is_telemed_setting } = useField('is_telemed_setting')
  const { value: is_multi_vendor } = useField('is_multi_vendor')
  const { value: is_blog } = useField('is_blog')
  const { value: is_encounter_problem } = useField('is_encounter_problem')
  const { value: is_encounter_observation } = useField('is_encounter_observation')
  const { value: is_encounter_note } = useField('is_encounter_note')
  const { value: is_encounter_prescription } = useField('is_encounter_prescription')





  // message
  const display_submit_message = (res) => {
    IS_SUBMITED.value = false
    if (res.status) {
      window.successSnackbar(res.message)
      window.location.reload();
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.errors
    }
  }

  //fetch data
  const data = [
    'view_patient_soap',
    'is_body_chart',
    'is_telemed_setting',
    'is_multi_vendor',
    'is_blog',
    'is_encounter_problem',
    'is_encounter_observation',
    'is_encounter_note',
    'is_encounter_prescription',

  ]


  onMounted(() => {

    const customData = [
      ...data,

    ].join(",")

    createRequest(GET_URL(customData)).then((response) => {
      setFormData(response)
    })
  })

  //Form Submit
  const formSubmit = handleSubmit((values) => {
    IS_SUBMITED.value = true
    const newValues = {}
    Object.keys(values).forEach((key) => {
      if(values[key] !== '') {
        newValues[key] = values[key] !== '' ? parseInt(values[key]) : 0;
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
