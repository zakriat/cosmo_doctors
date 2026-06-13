<template>
    <form @submit="formSubmit" class="">
    <div class="card" id="sendMedicalReport">
        <div class="card-header mb-4">
           <h5 class="card-title mb-0">{{ $t('appointment.send_medical_report_on_email') }}</h5>
        </div>
        <div class="card-body border-top">
           <div class="row">
            <div class="form-group col-md-4">
                <label class="form-label">{{ $t('category.lbl_name') }} <span class="text-danger">*</span></label>
                <Multiselect id="report" v-model="report_id" placeholder="Select Report" v-bind="singleSelectOption" :options="reports.options" class="form-group"></Multiselect>
                <span v-if="errorMessages['report_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['report_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['report_id'] }}</span>
              </div>
             
              
           </div>
        </div>
        <div class="card-footer border-top">
           <div class="d-flex align-items-center justify-content-end gap-3">
              
              <button class="btn btn-light d-block" type="button" @click="closeTemplate()">
            {{ $t('messages.cancel') }}
          </button>
              <button class="btn btn-secondary" type="submit" v-if="isLoading==0">
                 <i class="ph ph-file me-1"></i>
                  {{ $t('messages.send') }}
              </button>

              <button class="btn btn-secondary" type="submit" v-if="isLoading==1">
                <i class="ph ph-file me-1"></i>
                  {{ $t('appointment.sending') }}
             </button>
           </div>
        </div>
     </div>
    </form>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import { buildMultiSelectObject } from '@/helpers/utilities'

 import {GET_MEDICAL_REPORT_LIST,SEND_MEDICAL_REPORT} from '../../constant/encouter_report'


const props = defineProps({
  encounter_id: { type: Number, default: '' },
  user_id: { type: Number, default: '' },
})


const emit = defineEmits(['send_report'])

const { storeRequest,listingRequest,getRequest,updateRequest} = useRequest()

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,

})

const reports = ref({ options: [], list: [] })

const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    setFormData(defaultData())

    emit('send_report')
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
 
}


const getreports = () => {

listingRequest({ url: GET_MEDICAL_REPORT_LIST, data: {id: props.encounter_id} }).then((res) => {
  reports.value.options = buildMultiSelectObject(res.medical_report, {
    value: 'id',
    label: 'name'
  })
})

}

const setFormData = (data) => {

  resetForm({
    values: {
      name: data.name,
    }
  })
}

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    report_id: '',
  
  }
}


// Validations
const validationSchema = yup.object({
    report_id: yup.string().required('Please select report'),
   
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: report_id } = useField('report_id')


const errorMessages = ref({})

onMounted(() => {
  getreports()
  setFormData(defaultData())
    
})

const isLoading=ref(0);

const formSubmit = handleSubmit((values) => {

   isLoading.value=1
  values.user_id=props.user_id
  values.encounter_id=props.encounter_id

    storeRequest({ url:SEND_MEDICAL_REPORT, body: values ,type: 'file' }).then((res) => {reset_datatable_close_offcanvas(res) })

})

const closeTemplate=()=>{

  emit('send_report')

}






</script>
