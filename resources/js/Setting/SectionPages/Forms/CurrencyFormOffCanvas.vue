<template>
  <form @submit="formSubmit">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="offcanvas-title">
              <template v-if="currentId != 0">
                <span>{{ $t('currency.lbl_edit') }}</span>
              </template>
              <template v-else>
                <span>{{ $t('currency.lbl_add') }}</span>
              </template>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label">{{ $t('clinic.lbl_currency_name') }} <span class="text-danger">*</span></label>
                      <Multiselect id="curname-list" v-model="currency_name" :value="currency_name" :placeholder="$t('clinic.lbl_currency_name')" v-bind="singleSelectOption" :options="curr_name.options" @select="getSymbol" class="form-group"> </Multiselect>
                      <span v-if="errorMessages['currency_name']">
                        <ul class="text-danger">
                          <li v-for="err in errorMessages['currency_name']" :key="err">{{ err }}</li>
                        </ul>
                      </span>
                      <span class="text-danger">{{ errors['currency_name'] }}</span>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label">{{ $t('clinic.lbl_symbol') }} <span class="text-danger">*</span></label>
                      <InputField :is-required="true" :label="$t('clinic.lbl_symbol')" placeholder="" v-model="currency_symbol" :error-message="errors.currency_symbol" :error-messages="errorMessages['currency_symbol']"> </InputField>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label">{{ $t('clinic.lbl_currency_code') }} <span class="text-danger">*</span></label>
                      <InputField :is-required="true" :label="$t('clinic.lbl_currency_code')" placeholder="" v-model="currency_code" :error-message="errors.currency_code" :error-messages="errorMessages['currency_code']"></InputField>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="category-status">{{ $t('currency.lbl_is_primary') }}</label>
                    <div class="form-check form-switch">
                      <input class="form-check-input" :value="is_primary" :checked="is_primary" name="is_primary" id="is_primary" type="checkbox" v-model="is_primary" @change="checkPrimary" />
                    </div>
                  </div>
                </div>

                <h6>
                  <b>{{ $t('currency.currency_format') }}</b>
                </h6>

                <div class="form-group">
                  <label class="form-label" for="label">{{ $t('currency.lbl_currency_position') }}</label>
                  <select class="form-select" v-model="currency_position">
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                    <option value="left_with_space">Left With Space</option>
                    <option value="right_with_space">Right With Space</option>
                  </select>
                </div>
                <div class="form-group col-md-12">
                  <label class="form-label">{{ $t('currency.lbl_thousand_separatorn') }} <span class="text-danger">*</span></label>
                  <InputField :is-required="true" :label="$t('currency.lbl_thousand_separatorn')" placeholder="" v-model="thousand_separator" :error-message="errors.thousand_separator" :error-messages="errorMessages['thousand_separator']"></InputField>
                </div>
                <div class="form-group col-md-12">
                  <label class="form-label">{{ $t('currency.lbl_decimal_separator') }} <span class="text-danger">*</span></label>
                  <InputField :is-required="true" :label="$t('currency.lbl_decimal_separator')" placeholder="" v-model="decimal_separator" :error-message="errors.decimal_separator" :error-messages="errorMessages['decimal_separator']"></InputField>
                </div>
                <div class="form-group col-md-12">
                  <label class="form-label">{{ $t('currency.lbl_number_of_decimals') }} <span class="text-danger">*</span></label>
                  <InputField :is-required="true" :label="$t('currency.lbl_number_of_decimals')" placeholder="" v-model="no_of_decimal" :error-message="errors.no_of_decimal" :error-messages="errorMessages['no_of_decimal']"></InputField>
                </div>
                <div class="form-group">
                  <label class="form-label" for="label">{{ $t('currency.example') }}:- {{ formatCurrencyVue(labelValue, no_of_decimal, decimal_separator, thousand_separator, currency_position, currency_symbol) }}</label>
                </div>
              </div>
            </div>
          </div>
          <div class="border-top">
            <div class="d-grid d-md-flex gap-3 p-3">
              <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                <i class="fa-solid fa-angles-left"></i>
                {{ $t('messages.cancel') }}
              </button>
              <button class="btn btn-primary d-block">
                <i class="fa-solid fa-floppy-disk"></i>
                {{ $t('messages.save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { STORE_URL, EDIT_URL, UPDATE_URL, COUNTRY_URL, STATE_URL, CITY_URL } from '@/vue/constants/currency'
import { useField, useForm } from 'vee-validate'
import { useRequest, useOnModalHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'

const props = defineProps({
  id: { type: Number, default: 0 }
})
// useOnModalHide('exampleModal', () => setFormData(defaultData()))

onMounted(() => {
  // getServiceList()
  // getManagerList()
  setFormData(defaultData())
  getCurrName()
})

const emit = defineEmits(['onSubmit'])

const { getRequest, storeRequest, updateRequest } = useRequest()

const currentPrimary = ref(false)

const checkPrimary = () => {
  if (currentPrimary.value == 1 && is_primary.value == false) {
    window.errorSnackbar('You must have at least one primary currency')
    is_primary.value = true
  }
}

const labelValue = ref(1234567.89)

const currentId = ref(props.id)
const curr_name = ref({ options: [], list: [] })

const getCurrName = () => {
  useSelect({ url: COUNTRY_URL }, { value: 'currency_name', label: 'currency_name' })
    .then((data) => {
      const uniqueCurrencies = Array.from(new Set(data.options.map((item) => item.value)))
        .map((currency) => data.options.find((item) => item.value === currency))
    
      curr_name.value = { ...data, options: uniqueCurrencies }
    })
}

const getSymbol = async (currencyName) => {
  try {
    const [nameData, symbolData, codeData] = await Promise.all([useSelect({ url: COUNTRY_URL, data: { currency_name: currencyName } }, { value: 'currency_name', label: 'currency_name' }), useSelect({ url: COUNTRY_URL, data: { currency_name: currencyName } }, { value: 'currency_name', label: 'symbol' }), useSelect({ url: COUNTRY_URL, data: { currency_name: currencyName } }, { value: 'currency_name', label: 'currency_code' })])

    const findLabel = (data) => {
      const option = data.options.find((option) => option.value === currencyName)
      if (option) {
        console.log(`Label for ${currencyName}: ${option.label}`)
        return option.label
      } else {
        console.log(`No label found for ${currencyName}`)
        return null
      }
    }
    const nameLabel = findLabel(symbolData, 'currency_name')
    const symbolLabel = findLabel(symbolData, 'symbol')
    const codeLabel = findLabel(codeData, 'currency_code')

    if (symbolLabel !== null) {
      currency_symbol.value = symbolLabel
    }

    if (codeLabel !== null) {
      currency_code.value = codeLabel
    }
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

// Edit Form Or Create Form
watch(
  () => props.id,
  async (value) => {
    currentId.value = value
    if (value > 0) {
      getRequest({ url: EDIT_URL, id: value }).then((res) => {
        if (res.status && res.data) {
          setFormData(res.data)
          currentPrimary.value = res.data.is_primary
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    currency_name: '',
    currency_symbol: '',
    currency_code: '',
    currency_position: 'left',
    no_of_decimal: 2,
    thousand_separator: ',',
    decimal_separator: '.',
    is_primary: false
  }
}
const formatCurrencyVue = window.formatCurrency

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      currency_name: data.currency_name,
      currency_symbol: data.currency_symbol,
      currency_code: data.currency_code,
      currency_position: data.currency_position,
      no_of_decimal: data.no_of_decimal,
      thousand_separator: data.thousand_separator,
      decimal_separator: data.decimal_separator,
      is_primary: data.is_primary
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Modal.getInstance('#exampleModal').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
  emit('onSubmit')
}
const numberRegex = /^\d+$/
// Validations
const validationSchema = yup.object({
  currency_name: yup.string().required('Currency Name is a required field'),
  currency_symbol: yup.string().required('Currency Symbol is a required field'),
  currency_code: yup.string().required('Currency Code is a required field'),
  no_of_decimal: yup.number().required('No. Of Decimal is a required field').integer('No. Of Decimal must be an integer').typeError('No. Of Decimal must be a number'),
  thousand_separator: yup.string().required('Thousand Separator is a required field'),
  decimal_separator: yup.string().required('Decimal Separator is a required field')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: currency_name } = useField('currency_name')
const { value: currency_symbol } = useField('currency_symbol')
const { value: currency_code } = useField('currency_code')
const { value: currency_position } = useField('currency_position')
const { value: no_of_decimal } = useField('no_of_decimal')
const { value: thousand_separator } = useField('thousand_separator')
const { value: decimal_separator } = useField('decimal_separator')
const { value: is_primary } = useField('is_primary')
const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  console.log(values.currency_name)
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>
