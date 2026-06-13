<template>
  <form @submit="formSubmit">
    <div class="row">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <CardTitle
          :title="$t('setting_sidebar.lbl_cancellation_charge')"
          icon="fa-solid fa-calendar-xmark"
        />
        <div class="form-check form-switch m-0 position-relative">
          <input
            class="form-check-input"
            type="checkbox"
            id="is_cancellation_charge"
            v-model="is_cancellation_charge"
            :true-value="1"
            :false-value="0"
            name="is_cancellation_charge"
            :value="is_cancellation_charge"
            :checked="is_cancellation_charge == 1 ? true : false"
          />
        </div>
      </div>
      <div v-if="is_cancellation_charge == 1" class="row">
        <div class="col-md-4">
          <label class="form-label">{{ $t('settings.lbl_cancellation_type') }}</label>
          <Multiselect
            id="cancellation_type"
            v-model="cancellation_type"
            :placeholder="$t('settings.lbl_cancellation_type')"
            v-bind="cancellation_type_data"
            class="form-group"
            :value="cancellation_type"
          />
          <span class="text-danger">{{ errors.cancellation_type }}</span>
        </div>
        <div class="col-md-4 position-relative">
          <label class="form-label">{{ $t('settings.lbl_cancellation_charge') }}</label>
          <InputField
            type="number"
            min="1"
            :label="$t('settings.lbl_cancellation_charge')"
            :placeholder="$t('settings.lbl_cancellation_charge')"
            v-model="cancellation_charge"
            :errorMessage="errors.cancellation_charge"
          ></InputField>
          <span
  class="tooltip-icon"
  @mouseenter="showTooltip('cancellation_charge')"
  @mouseleave="hideTooltip"
>
  <i class="fa-solid fa-info-circle"></i>
</span>
<div
  class="tooltip visible"
  v-if="showTooltipFlag === 'cancellation_charge'"
  ref="tooltip"
>
  {{ $t('messages.tooltip_cancellation_charge') }}
</div>
        </div>
        <div class="col-md-4 position-relative">
          <label class="form-label">{{ $t('settings.cancellation_charge_hours') }}</label>
          <InputField
            type="number"
            min="1"
            :label="$t('settings.cancellation_charge_hours')"
            :placeholder="$t('settings.cancellation_charge_hours')"
            v-model="cancellation_charge_hours"
            :errorMessage="errors.cancellation_charge_hours"
          ></InputField>
          <span
  class="tooltip-icon"
  @mouseenter="showTooltip('cancellation_charge_hours')"
  @mouseleave="hideTooltip"
>
  <i class="fa-solid fa-info-circle"></i>
</span>
<div
  class="tooltip visible"
  v-if="showTooltipFlag === 'cancellation_charge_hours'"
  ref="tooltip"
>
  {{ $t('messages.tooltip_cancellation_charge_hours') }}
</div>
        </div>
      </div>
    </div>
    <div class="row py-4">
      <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
    </div>
  </form>
</template>

  <script setup>
  import CardTitle from '@/Setting/Components/CardTitle.vue'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { onMounted, ref } from 'vue'
  import { useField, useForm } from 'vee-validate'
  import { STORE_URL, GET_URL} from '@/vue/constants/setting'
  import { useSelect } from '@/helpers/hooks/useSelect'
  import { createRequest, buildMultiSelectObject } from '@/helpers/utilities'
  import { useRequest } from '@/helpers/hooks/useCrudOpration'
  import SubmitButton from './Forms/SubmitButton.vue'
  import { confirmSwal } from '@/helpers/utilities'
  import * as yup from 'yup'

  // flatepicker
  const role = () => {
      return window.auth_role[0];
  }

  const IS_SUBMITED = ref(false)
  const { storeRequest, listingRequest } = useRequest()

  // options


  const singleSelectOption = ref({
    closeOnSelect: true,
    searchable: true,
    clearable: false
  })
    const cancellation_type_data = ref({
        searchable: true,
        options: [
            { label: 'Percentage', value: 'percentage' },

        ],
        closeOnSelect: true,
        createOption: true
    })

    // Tooltip state
      const showTooltipFlag = ref(null)

  const showTooltip = (key) => {
    showTooltipFlag.value = key
  }

  const hideTooltip = () => {
    showTooltipFlag.value = null
  }

  //  Reset Form
  const setFormData = (data) => {
    resetForm({
      values: {
        cancellation_charge: data.cancellation_charge,
        cancellation_type: data.cancellation_type,
        cancellation_charge_hours: data.cancellation_charge_hours,
        is_cancellation_charge: data.is_cancellation_charge ?? 0
      }
    })
  }
  const validationSchema = yup.object({
  cancellation_type: yup
    .string()
    .required('Cancellation type is required'),

  cancellation_charge: yup
    .number()
    .transform(value => (isNaN(value) ? undefined : value))
    .required('Cancellation charge is required')
    .min(1, 'Cancellation charge must be at least 1')
    .when('cancellation_type', {
      is: (val) => val === 'percentage',
      then: (schema) => schema.max(99, 'Percentage must be less than 100'),
      otherwise: (schema) => schema,
    }),

  cancellation_charge_hours: yup
    .number()
    .transform(value => (isNaN(value) ? undefined : value))
    .required('Cancellation charge hours is required')
    .min(1, 'Cancellation charge hours must be at least 1'),
});


  const { handleSubmit, errors, resetForm, validate } = useForm({validationSchema})
  const errorMessage = ref(null)
  const { value: cancellation_charge } = useField('cancellation_charge', { initialValue: 0 })
  const { value: cancellation_type } = useField('cancellation_type')
  const { value: cancellation_charge_hours } = useField('cancellation_charge_hours', { initialValue: 0 })
  const { value: is_cancellation_charge } = useField('is_cancellation_charge');

  const data = 'cancellation_charge,cancellation_type,cancellation_charge_hours,is_cancellation_charge'
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
    const newValues = {}
    Object.keys(values).forEach((key) => {
      newValues[key] = values[key] || ''
    })

    storeRequest({ url: STORE_URL, body: values }).then((res) => display_submit_message(res))
  })
  </script>
  
  <style scoped>
.tooltip-icon {
  margin-left: 8px;
  cursor: pointer;
  position: absolute;
  top: 10px;
  right: 10px;
}

.tooltip {
  visibility: hidden;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 5px;
  padding: 10px;
  position: absolute;
  z-index: 1;
  bottom: 125%; /* Position the tooltip above the text */
  left: 50%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s;
  white-space: nowrap;
}

.tooltip::after {
  content: '';
  position: absolute;
  top: 100%; /* At the bottom of the tooltip */
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #333 transparent transparent transparent;
}

.tooltip.visible {
  visibility: visible;
  opacity: 1;
}
</style>

