<template>
   
    <form @submit="formSubmit" class="">
        <div class="modal fade" id="template_exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                      <template v-if="currentId != 0">
                        <span>{{ $t('clinic.edit_prescription') }}</span>
                      </template>
                      <template v-else>
                        <span>{{ $t('clinic.add_prescription') }}</span>
                      </template>
                     
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="form-offcanvas">
                      
                              <div class="form-group">
                                <label class="form-label col-md-12">{{ $t('clinic.lbl_select_name') }} <span class="text-danger">*</span></label>
                                <Multiselect id="name" v-model="name" :value="name" placeholder="Select Name" v-bind="singleSelectOption" :options="prescriptions.options"  class="form-group"></Multiselect>
                                <p class="mt-2 mb-0 fs-12"><b>Note: Type and press enter to create new problem</b></p>
                                <span v-if="errorMessages['name']">
                                  <ul class="text-danger">
                                    <li v-for="err in errorMessages['name']" :key="err">{{ err }}</li>
                                  </ul>
                                </span>
                                <span class="text-danger">{{ errors['name'] }}</span>
                             
                              </div>
                             
                              <label class="form-label col-md-12">{{ $t('clinic.lbl_frequency') }} <span class="text-danger">*</span></label>
                              <InputField class="col-md-12" :is-required="true" placeholder="" v-model="frequency" :error-message="errors.frequency" :error-messages="errorMessages['frequency']"></InputField>

                              <label class="form-label col-md-12">{{ $t('clinic.lbl_duration') }} <span class="text-danger">*</span></label>
                              <InputField type="number" class="col-md-12" :is-required="true" placeholder="" v-model="duration" :error-message="errors.duration" :error-messages="errorMessages['duration']"></InputField>
                           

                            <div class="form-group">
                              <label class="form-label" for="description">{{ $t('clinic.lbl_instruction') }}</label>
                               <textarea class="form-control" v-model="instruction" id="instruction"></textarea>
                                <span v-if="errorMessages['instruction']">
                                  <ul class="text-danger">
                                   <li v-for="err in errorMessages['instruction']" :key="err">{{ err }}</li>
                                 </ul>
                                </span>
                              <span class="text-danger">{{ errors.description }}</span>
                           </div>       
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue'

import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { GET_SEARCH_DATA,PRESCRIPTION_STORE,EDIT_PRESCRIPTION_URL, PRESCRIPTION_UPDATE } from '@/vue/constants/template_prescription'

const emit = defineEmits(['submit'])

const props = defineProps({
  template_id: { type: Number, default: '' },
  id: { type: Number, default: 0 }
  
})

const { storeRequest,listingRequest,getRequest,updateRequest} = useRequest()

const currentId = ref(props.id)
// Edit Form Or Create Form
watch(
  () => props.id,
  (value) => {
    currentId.value = value
  
    if (value > 0) {
      getRequest({ url: EDIT_PRESCRIPTION_URL, id: value }).then((res) => {
        if (res.status && res.data) {
          setFormData(res.data)
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  createOption: true,
})



/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    frequency: '',
    duration: 1,
    instruction: '',
  
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      frequency: data.frequency,
      duration: data.duration,
      instruction: data.instruction,
    }
  })
}

// Validations
const validationSchema = yup.object({
    name: yup.string().required(),
    frequency: yup.string().required(),
    duration: yup.number().required(),
  
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: frequency } = useField('frequency')
const { value: duration } = useField('duration')
const { value: instruction } = useField('instruction')

const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
  getPrescription()
})


const prescriptions = ref({ options: [], list: [] })

const getPrescription = () => {
    
   listingRequest({ url: GET_SEARCH_DATA, data: {type: 'encounter_prescription'} }).then((res) => {
    prescriptions.value.list=res.results
    prescriptions.value.options = buildMultiSelectObject(res.results, {
     value: 'name',
     label: 'name'
    })
  })
    
}
 

const formSubmit = handleSubmit((values) => {

     values.template_id=props.template_id
     values.type='encounter_prescription'

  if (currentId.value > 0) {   
    
    updateRequest({ url: PRESCRIPTION_UPDATE, body: values ,id: currentId.value}).then((res) => {
    if(res.status) {
      emit('submit', {type: 'create_customer', value: res.prescription})
      setFormData(defaultData())
      bootstrap.Modal.getInstance(document.getElementById("template_exampleModal")).hide()
      } 
    })
    
    currentId.value=0;
  }else{

    storeRequest({ url: PRESCRIPTION_STORE, body: values }).then((res) => {
    if(res.status) {
      emit('submit', {type: 'create_customer', value: res.prescription})
      setFormData(defaultData())
      bootstrap.Modal.getInstance(document.getElementById("template_exampleModal")).hide()
      } 
   })

  }

})



</script>
