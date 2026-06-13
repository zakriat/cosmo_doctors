<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <div class="form-group">
               <Multiselect id="user_id" v-model="customer_id" :disabled="ISREADONLY" placeholder="Select Customer" :value="customer_id" v-bind="singleSelectOption" :options="customer.options" @select="customerSelect" class="form-group"></Multiselect>
        </div>

        <InputField class="col-md-6" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_height_meter')" placeholder="" v-model="height_meter" :error-message="errors['height_cm']" :error-messages="errorMessages['height_cm']"></InputField>

          <div class="row">
          <InputField class="col-md-6" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_height_CM')" placeholder="" v-model="height_cm" :error-message="errors['height_cm']" :error-messages="errorMessages['height_cm']"></InputField>

          <InputField class="col-md-6" type="number"  step="any" :is-required="true" :label="$t('vital.lbl_height_Inch')" placeholder="" v-model="height_inch" :error-message="errors['height_inch']" :error-messages="errorMessages['height_inch']"></InputField>

        </div>
        <div class="row">
          <InputField class="col-md-6"  type="number"  step="any"   :is-required="true" :label="$t('vital.lbl_weight_kg')" placeholder="" v-model="weight_kg" :error-message="errors['weight_kg']" :error-messages="errorMessages['weight_kg']"></InputField>

          <InputField class="col-md-6" type="number"  step="any"   :is-required="true" :label="$t('vital.lbl_weight_pound')" placeholder="" v-model="weight_pound" :error-message="errors['weight_pound']" :error-messages="errorMessages['weight_pond']"></InputField>
        </div>
        <div class="row">
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_bmi')" placeholder="" v-model="bmi" :error-message="errors['bmi']" :error-messages="errorMessages['bmi']" :isReadOnly=true></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_tbf')" placeholder="" v-model="tbf" :error-message="errors['tbf']" :error-messages="errorMessages['tbf']" :isReadOnly=true></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_vfi')" placeholder="" v-model="vfi" :error-message="errors['vfi']" :error-messages="errorMessages['vfi']" ></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_tbw')" placeholder="" v-model="tbw" :error-message="errors['tbw']" :error-messages="errorMessages['tbw']" :isReadOnly=true></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_sm')" placeholder="" v-model="sm" :error-message="errors['sm']" :error-messages="errorMessages['sm']" :isReadOnly=true></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_bmc')" placeholder="" v-model="bmc" :error-message="errors['bmc']" :error-messages="errorMessages['bmc']"></InputField>
          <InputField class="col-md-4" type="number"  step="any"  :is-required="true" :label="$t('vital.lbl_bmr')" placeholder="" v-model="bmr" :error-message="errors['bmr']" :error-messages="errorMessages['bmr']" :isReadOnly=true></InputField>
        </div>
          <div v-for="field in customefield" :key="field.id">
            <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
          </div>
        </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
      </div>
    </form>
  </template>
  <script setup>
  import { ref, onMounted , computed ,watchEffect} from 'vue'
  import { EDIT_URL, STORE_URL, UPDATE_URL,CUSTOMER_LIST } from '../constant'
  import { useField, useForm } from 'vee-validate'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import { readFile } from '@/helpers/utilities'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
  import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
  import FormElement from '@/helpers/custom-field/FormElement.vue'
  import { useSelect } from '@/helpers/hooks/useSelect'

  // props
  defineProps({
    createTitle: { type: String, default: '' },
    editTitle: { type: String, default: '' },
    customefield: { type: Array, default: () => [] }
  })

  const { getRequest, storeRequest, updateRequest } = useRequest()

  // Edit Form Or Create Form

  const currentId = useModuleId(() => {
    if (currentId.value > 0) {
      getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
        if (res.status) {
          setFormData(res.data)
          ISREADONLY.value = true
        }
      })
    } else {
      setFormData(defaultData())
    }
  })
  useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

  /*
   * Form Data & Validation & Handeling
   */
  // Default FORM DATA
  const defaultData = () => {
    ISREADONLY.value = false
    errorMessages.value = {}
    return {
      height_cm: 0,
      height_inch:0,
      weight_kg:0,
      weight_pound:0,
      bmi:0,
      bmr:0,
      tbf:0,
      sm:0,
      tbw:0,
      bmc:0,
      vfi:null,
      customer_id:null,
      height_meter:0,

    }
  }

const singleSelectOption = ref({
  createOption: true,
  closeOnSelect: true,
  searchable: true
})


const customer = ref({ options: [], list: [] })

  const customerSelect = (value) => {
  if (_.isString(value)) {
    newCustomerData.value = {
      first_name: value.split(' ')[0] || '',
      last_name: value.split(' ')[1] || ''
    }
    bootstrap.Modal.getOrCreateInstance($('#exampleModal')).show()
    customer_id.value = null
  }
}

  const getCustomers = (cb) =>
  useSelect({ url: CUSTOMER_LIST }, { value: 'id', label: 'full_name' }).then((data) => {
    console.log(data);
    customer.value = data
    if (typeof cb == 'function') {
      cb()
    }
  })


  const setFormData = (data) => {
    //ImageViewer.value = data.file_url
    resetForm({
      values: {
      height_cm: data.height_cm,
      height_inch:data.height_inch,
      weight_kg:data.weight_kg,
      weight_pound:data.weight_pound,
      bmi:data.bmi,
      bmr:data.bmr,
      tbf:data.tbf,
      sm:data.sm,
      tbw:data.tbw,
      bmc:data.bmc,
      vfi:data.vfi,
      customer_id:data.customer_id,
      }
    })
  }

   // Reload Datatable, SnackBar Message, Alert, Offcanvas Close
   const reset_datatable_close_offcanvas = (res) => {
    IS_SUBMITED.value = false
    if (res.status) {
      window.successSnackbar(res.message)
      renderedDataTable.ajax.reload(null, false)
      bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
      setFormData(defaultData())

    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
  }

// Validations
const validationSchema = yup.object({
customer_id: yup.string().required('Please select a customer'),
height_cm: yup.number().required('Height in cm is required').positive('Height must be a positive number'),
height_inch: yup.number().required('Height in inches is required').positive('Height must be a positive number'),
weight_kg: yup.number().required('Weight in kg is required').positive('Weight must be a positive number'),
weight_pound: yup.number().required('Weight in pounds is required').positive('Weight must be a positive number'),
bmi: yup.string().required('bmi is required'),
tbf: yup.string().required('tbf is required'),
vfi: yup.string().required('vfi is required'),
tbw: yup.string().required('tbw is required'),
sm: yup.string().required('sm is required'),
bmc: yup.string().required('bmc is required'),
bmr: yup.string().required('bmr is required'),

})

  const { handleSubmit, errors, resetForm } = useForm({
    validationSchema
  })


  const { value: height_cm} = useField('height_cm')
  const { value: height_inch} = useField('height_inch')
  const { value: weight_kg} = useField('weight_kg')
  const { value: weight_pound } = useField('weight_pound')
  const { value: bmi} = useField('bmi')
  const { value: customer_id} = useField('customer_id')
  const { value: tbf} = useField('tbf')
  const { value: tbw} = useField('tbw')
  const { value: sm} = useField('sm')
  const { value: bmr} = useField('bmr')
  const { value: vfi} = useField('vfi')
  const { value: bmc} = useField('bmc')
 const { value: height_meter} =useField('height_meter')

  const errorMessages = ref({})
  const ISREADONLY = ref(false)
  //  Customer Select & Unselect & Selected Values
const selectedCustomer = computed(() => {
  const selected = customer.value.list.find((customer) => customer.id === customer_id.value);
  return selected || null;
});

const gender = computed(() => {
  const selected = selectedCustomer.value;
  return selected ? selected.gender : null;
});

const age = computed(() => {
  const selected = selectedCustomer.value;
  if (selected && selected.date_of_birth) {
    const birthDate = new Date(selected.date_of_birth);
    const currentDate = new Date();
    const age = currentDate.getFullYear() - birthDate.getFullYear();
    return age;
  }
  return null;
});

  watchEffect(() => {
  height_cm.value = parseFloat((height_inch.value * 2.54).toFixed(4));
  weight_kg.value = parseFloat((weight_pound.value/2.2046).toFixed(4));
  bmi.value = parseFloat(((weight_kg.value / ((height_cm.value/ 100) ** 2)).toFixed(4)));
  tbf.value=gender.value=='male'?((1.20 * bmi.value) + (0.23 * age.value)) - 16.2:((1.20 * bmi.value) + (0.23 * age.value)) - 5.4;
  tbw.value= gender.value=='male'? 2.447 - (0.09156 * age.value) + (0.1074 * height_cm.value) + (0.3362 * weight_kg.value):-2.097 + (0.1069 * height_cm.value) + (0.2466 * weight_kg.value);
  sm.value=Math.ceil(100 - (tbf.value + tbw.value), 2)
  bmr.value=gender.value=='male'?(10 * weight_kg.value) + (6.25 * height_cm.value) - (5 * age.value) + 5:(10 * weight_kg.value) + (6.25 * height_cm.value) - (5 * age.value) - 161;
});

watchEffect(() => {
  //height_cm.value = parseFloat((height_meter.value * 100).toFixed(4));
  height_meter.value =parseFloat((height_cm.value/ 100).toFixed(4));
  height_inch.value = parseFloat((height_cm.value / 2.54).toFixed(4));
  weight_pound.value = parseFloat((weight_kg.value*2.2046).toFixed(4));

});

  onMounted(() => {
    setFormData(defaultData())
    getCustomers()
  })

  // Form Submit
  const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false
    values.custom_fields_data = JSON.stringify(values.custom_fields_data);
    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values })
        .then((res) => reset_datatable_close_offcanvas(res))
    } else {
      storeRequest({ url: STORE_URL, body: values })
        .then((res) => reset_datatable_close_offcanvas(res))
    }
  });

  </script>


<style scoped>
@media only screen and (min-width: 768px) {
  .offcanvas {
    width: 80%;
  }
}

@media only screen and (min-width: 1280px) {
  .offcanvas {
    width: 60%;
  }
}

</style>

