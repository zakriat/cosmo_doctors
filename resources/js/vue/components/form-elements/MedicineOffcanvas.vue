<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvas">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_name') }} <span class="text-danger">*</span></label>
              <InputField 
                :is-required="true" 
                :placeholder="$t('medicines.lbl_name')" 
                v-model="name" 
                :error-message="errors.name" 
                :error-messages="errorMessages['name']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_generic_name') }}</label>
              <InputField 
                :placeholder="$t('medicines.lbl_generic_name')" 
                v-model="generic_name" 
                :error-message="errors.generic_name" 
                :error-messages="errorMessages['generic_name']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_brand_name') }}</label>
              <InputField 
                :placeholder="$t('medicines.lbl_brand_name')" 
                v-model="brand_name" 
                :error-message="errors.brand_name" 
                :error-messages="errorMessages['brand_name']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_strength') }}</label>
              <InputField 
                :placeholder="$t('medicines.lbl_strength')" 
                v-model="strength" 
                :error-message="errors.strength" 
                :error-messages="errorMessages['strength']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_dosage_form') }}</label>
              <select class="form-control" v-model="dosage_form">
                <option value="">{{ $t('messages.select_option') }}</option>
                <option value="tablet">{{ $t('medicines.dosage_forms.tablet') }}</option>
                <option value="capsule">{{ $t('medicines.dosage_forms.capsule') }}</option>
                <option value="syrup">{{ $t('medicines.dosage_forms.syrup') }}</option>
                <option value="injection">{{ $t('medicines.dosage_forms.injection') }}</option>
                <option value="cream">{{ $t('medicines.dosage_forms.cream') }}</option>
                <option value="ointment">{{ $t('medicines.dosage_forms.ointment') }}</option>
                <option value="drops">{{ $t('medicines.dosage_forms.drops') }}</option>
                <option value="inhaler">{{ $t('medicines.dosage_forms.inhaler') }}</option>
                <option value="patch">{{ $t('medicines.dosage_forms.patch') }}</option>
                <option value="suppository">{{ $t('medicines.dosage_forms.suppository') }}</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_manufacturer') }}</label>
              <InputField 
                :placeholder="$t('medicines.lbl_manufacturer')" 
                v-model="manufacturer" 
                :error-message="errors.manufacturer" 
                :error-messages="errorMessages['manufacturer']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_category') }}</label>
              <select class="form-control" v-model="category">
                <option value="">{{ $t('messages.select_option') }}</option>
                <option value="antibiotic">{{ $t('medicines.categories.antibiotic') }}</option>
                <option value="analgesic">{{ $t('medicines.categories.analgesic') }}</option>
                <option value="antacid">{{ $t('medicines.categories.antacid') }}</option>
                <option value="antihistamine">{{ $t('medicines.categories.antihistamine') }}</option>
                <option value="antihypertensive">{{ $t('medicines.categories.antihypertensive') }}</option>
                <option value="antidiabetic">{{ $t('medicines.categories.antidiabetic') }}</option>
                <option value="vitamin">{{ $t('medicines.categories.vitamin') }}</option>
                <option value="supplement">{{ $t('medicines.categories.supplement') }}</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_price') }}</label>
              <InputField 
                type="number" 
                step="0.01" 
                :placeholder="$t('medicines.lbl_price')" 
                v-model="price" 
                :error-message="errors.price" 
                :error-messages="errorMessages['price']">
              </InputField>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_url') }}</label>
              <InputField 
                type="url" 
                :placeholder="$t('medicines.lbl_url_placeholder')" 
                v-model="url" 
                :error-message="errors.url" 
                :error-messages="errorMessages['url']">
              </InputField>
              <small class="text-muted">{{ $t('medicines.lbl_url_help') }}</small>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_formulae') }}</label>
              <textarea 
                class="form-control" 
                v-model="formulae" 
                :placeholder="$t('medicines.lbl_formulae')" 
                rows="3">
              </textarea>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_indication') }}</label>
              <textarea 
                class="form-control" 
                v-model="indication" 
                :placeholder="$t('medicines.lbl_indication')" 
                rows="3">
              </textarea>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_side_effects') }}</label>
              <textarea 
                class="form-control" 
                v-model="side_effects" 
                :placeholder="$t('medicines.lbl_side_effects')" 
                rows="3">
              </textarea>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_contraindication') }}</label>
              <textarea 
                class="form-control" 
                v-model="contraindication" 
                :placeholder="$t('medicines.lbl_contraindication')" 
                rows="3">
              </textarea>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_drug_interactions') }}</label>
              <textarea 
                class="form-control" 
                v-model="drug_interactions" 
                :placeholder="$t('medicines.lbl_drug_interactions')" 
                rows="3">
              </textarea>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_pregnancy_category') }}</label>
              <select class="form-control" v-model="pregnancy_category">
                <option value="">{{ $t('messages.select_option') }}</option>
                <option value="A">A - {{ $t('medicines.pregnancy.category_a') }}</option>
                <option value="B">B - {{ $t('medicines.pregnancy.category_b') }}</option>
                <option value="C">C - {{ $t('medicines.pregnancy.category_c') }}</option>
                <option value="D">D - {{ $t('medicines.pregnancy.category_d') }}</option>
                <option value="X">X - {{ $t('medicines.pregnancy.category_x') }}</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('medicines.lbl_storage_conditions') }}</label>
              <InputField 
                :placeholder="$t('medicines.lbl_storage_conditions')" 
                v-model="storage_conditions" 
                :error-message="errors.storage_conditions" 
                :error-messages="errorMessages['storage_conditions']">
              </InputField>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <div class="form-check form-switch">
                <input 
                  class="form-check-input" 
                  :value="status" 
                  :checked="status" 
                  name="status" 
                  id="medicine-status" 
                  type="checkbox" 
                  v-model="status" />
                <label class="form-check-label" for="medicine-status">
                  {{ $t('medicines.lbl_status') }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <FormFooter></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// Props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

// Emits
const emit = defineEmits(['onSubmit'])

// Composables
const { storeRequest, updateRequest, getRequest } = useRequest()

// Reactive variables
const currentId = ref(0)
const errorMessages = ref({})

// Form validation schema
const validationSchema = yup.object({
  name: yup.string().required('Name is required'),
  generic_name: yup.string().nullable(),
  brand_name: yup.string().nullable(),
  strength: yup.string().nullable(),
  dosage_form: yup.string().nullable(),
  manufacturer: yup.string().nullable(),
  category: yup.string().nullable(),
  price: yup.number().nullable().min(0, 'Price must be greater than or equal to 0'),
  url: yup.string().url('Please enter a valid URL').nullable(),
  formulae: yup.string().nullable(),
  indication: yup.string().nullable(),
  side_effects: yup.string().nullable(),
  contraindication: yup.string().nullable(),
  drug_interactions: yup.string().nullable(),
  pregnancy_category: yup.string().nullable(),
  storage_conditions: yup.string().nullable(),
  status: yup.boolean()
})

// Form setup
const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

// Form fields
const { value: name } = useField('name')
const { value: generic_name } = useField('generic_name')
const { value: brand_name } = useField('brand_name')
const { value: strength } = useField('strength')
const { value: dosage_form } = useField('dosage_form')
const { value: manufacturer } = useField('manufacturer')
const { value: category } = useField('category')
const { value: price } = useField('price')
const { value: url } = useField('url')
const { value: formulae } = useField('formulae')
const { value: indication } = useField('indication')
const { value: side_effects } = useField('side_effects')
const { value: contraindication } = useField('contraindication')
const { value: drug_interactions } = useField('drug_interactions')
const { value: pregnancy_category } = useField('pregnancy_category')
const { value: storage_conditions } = useField('storage_conditions')
const { value: status } = useField('status')

// Default form data
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    generic_name: '',
    brand_name: '',
    strength: '',
    dosage_form: '',
    manufacturer: '',
    category: '',
    price: null,
    url: '',
    formulae: '',
    indication: '',
    side_effects: '',
    contraindication: '',
    drug_interactions: '',
    pregnancy_category: '',
    storage_conditions: '',
    status: true
  }
}

// Set form data
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      generic_name: data.generic_name,
      brand_name: data.brand_name,
      strength: data.strength,
      dosage_form: data.dosage_form,
      manufacturer: data.manufacturer,
      category: data.category,
      price: data.price,
      url: data.url,
      formulae: data.formulae,
      indication: data.indication,
      side_effects: data.side_effects,
      contraindication: data.contraindication,
      drug_interactions: data.drug_interactions,
      pregnancy_category: data.pregnancy_category,
      storage_conditions: data.storage_conditions,
      status: data.status
    }
  })
}

// Form submit handler
const formSubmit = handleSubmit((values) => {
  if (currentId.value > 0) {
    updateRequest({ 
      url: `medicines/${currentId.value}`, 
      body: values, 
      id: currentId.value 
    }).then((res) => {
      if (res.status) {
        emit('onSubmit', res)
        bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
        setFormData(defaultData())
      }
    })
  } else {
    storeRequest({ 
      url: 'medicines', 
      body: values 
    }).then((res) => {
      if (res.status) {
        emit('onSubmit', res)
        bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
        setFormData(defaultData())
      }
    })
  }
})

// Initialize form
onMounted(() => {
  setFormData(defaultData())
})

// Expose methods for parent component
defineExpose({
  openOffcanvas: (id = 0) => {
    currentId.value = id
    if (id > 0) {
      getRequest({ url: `medicines/${id}/edit`, id }).then((res) => {
        if (res.status && res.medicine) {
          setFormData(res.medicine)
        }
      })
    } else {
      setFormData(defaultData())
    }
    bootstrap.Offcanvas.getOrCreateInstance('#form-offcanvas').show()
  }
})
</script>