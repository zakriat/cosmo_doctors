<template>
  <form @submit="formSubmit">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="offcanvas-title">
              <template v-if="currentId != 0">
                <span>{{ $t('commission.lbl_edit_commission') }}</span>
              </template>
              <template v-else>
                <span>{{ $t('commission.lbl_add_commission') }}</span>
              </template>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group col-md-12">
                    <label class="form-label">{{$t('commission.lbl_title')}} <span class="text-danger">*</span></label>
                  <InputField class="col-md-12" :is-required="true" :label="$t('commission.lbl_title')" :placeholder="$t('commission.lbl_title')" v-model="title" :error-message="errors.title" :error-messages="errorMessages['title']"></InputField>
                </div>
                <div class="form-group">
                  <label class="form-label" for="commission_type">{{ $t('commission.lbl_commission_type') }}<span class="text-danger">*</span></label>
                  <Multiselect v-model="commission_type" :value="commission_type" v-bind="type_commission" :placeholder="$t('commission.lbl_commission_type')" id="commission_type" autocomplete="off"></Multiselect>
                  <span v-if="errorMessages['commission_type']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['commission_type']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.commission_type }}</span>
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">{{$t('commission.lbl_value')}} <span class="text-danger">*</span></label>
                  <InputField   type="text" 
                  pattern="[0-9]*\.?[0-9]*" class="col-md-12" :is-required="true" :label="$t('commission.lbl_value')" :placeholder="$t('commission.lbl_value')" v-model="commission_value" :error-message="errors.commission_value" :error-messages="errorMessages['commission_value']"></InputField>
                </div>
                <div class="form-group">
                  <label class="form-label" for="type">{{ $t('commission.lbl_type') }}<span class="text-danger">*</span></label>
                  <Multiselect v-model="type" :value="type" v-bind="types" :placeholder="$t('commission.lbl_type')" id="type" autocomplete="off"></Multiselect>
                  <span v-if="errorMessages['type']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['type']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.type }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="d-grid d-md-flex gap-3 p-3">
              <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                <i class="fa-solid fa-angles-left"></i>
                {{$t('messages.close')}}
              </button>
              <button class="btn btn-primary d-block" :disabled="isSubmitting" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                {{$t('messages.save')}}
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
import { DATATABLE_URL, EDIT_URL, STORE_URL, UPDATE_URL } from '@/vue/constants/commission'
import { useField, useForm } from 'vee-validate'
import { useRequest , useOnModalHide} from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

useOnModalHide('exampleModal', () => setFormData(defaultData()))

onMounted(() => {
  // getServiceList()
  // getManagerList()
  setFormData(defaultData())
})

const emit = defineEmits(['onSubmit'])

// props
const props = defineProps({
  id: { type: Number, default: 0 }
})

const { getRequest, storeRequest, updateRequest } = useRequest()

const currentId = ref(props.id)
// Edit Form Or Create Form
watch(
  () => props.id,
  (value) => {
    currentId.value = value
    if (value > 0) {
      getRequest({ url: EDIT_URL, id: value }).then((res) => {
        if (res.status && res.data) {
          setFormData(res.data)
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)
const Title_data = ref({
  searchable: true,
  options: [],
  closeOnSelect: true
})

const type_commission = ref({
  searchable: true,
  options: [
    // { label: 'Weekly', value: 'Weekly'},
    { label: 'Percentage', value: 'percentage' },
    { label: 'Fixed', value: 'fixed' }
  ],
  closeOnSelect: true
})

const types = ref({
  searchable: true,
  options: [],
  closeOnSelect: true
})

const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}

onMounted(() => {
  const baseOptions = [
    // { label: 'Product Commission', value: 'product_commission' },
    { label: 'Doctor Commission', value: 'doctor_commission' }
  ]

  if (enable_multi_vendor() == 1) {
    types.value.options = [
      { label: 'Admin Fees', value: 'admin_fees' },
      ...baseOptions
    ]
  } else {
    types.value.options = baseOptions
  }

  setFormData(defaultData())
})


/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    title: '',
    commission_value: '',
    commission_type: '',
    type: '',
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      title: data.title,
      commission_value: data.commission_value,
      commission_type: data.commission_type,
      type: data.type,
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    // renderedDataTable.ajax.reload(null, false)
    bootstrap.Modal.getInstance('#exampleModal').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
  emit('onSubmit')
}

const numberRegex = /^\d+$/;
// Validations
const validationSchema = yup.object({
  title: yup.string()
    .required("Title is a required field")
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  commission_value: yup.string()
    .required("Commission Value is a required field")
    .matches(/^\d*\.?\d+$/, 'Please enter a valid number with optional decimals')
    .test('max-percentage', 'Percentage cannot be greater than 100', function(value) {
      return this.parent.commission_type !== 'percentage' || (parseFloat(value) <= 100);
    }),
  commission_type: yup.string()
    .required("Commission Type is a required field"),
  type: yup.string()
    .required("Type is a required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: title } = useField('title')
const { value: commission_value } = useField('commission_value')
const { value: commission_type } = useField('commission_type')
const { value: type } = useField('type')

const errorMessages = ref({})


// Form Submit
// const formSubmit = handleSubmit((values) => {

//   if (currentId.value > 0) {
//     updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
//   } else {
//     storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
//   }
// })

const isSubmitting = ref(false);  

const formSubmit = handleSubmit(async (values) => {
  if (isSubmitting.value) return; 

  isSubmitting.value = true; 

  try {
    if (currentId.value > 0) {
      const res = await updateRequest({ url: UPDATE_URL, id: currentId.value, body: values });
      reset_datatable_close_offcanvas(res);
    } else {
      const res = await storeRequest({ url: STORE_URL, body: values });
      reset_datatable_close_offcanvas(res);
    }
  } catch (error) {
    console.error('Error saving data:', error);
  } finally {
    isSubmitting.value = false; 
  }
});
watch(commission_type, (newValue) => {
  if (newValue === 'percentage' && parseFloat(commission_value.value) > 100) {
    commission_value.value = '100';
  }
})

</script>
