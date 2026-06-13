<template>
   
    <form @submit="formSubmit" class="">
        <div class="modal fade" id="upload_prescription_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    
                        <span>{{ $t('clinic.import_prescription') }}</span>
                                           
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="form-offcanvas">

                            <div class="col-md-6">
                              <label class="form-label">{{ $t('clinic.file_type') }}</label>
                              <Multiselect id="file_type" v-model="file_type" :value="file_type" v-bind="file_types" class="form-group"></Multiselect>
                              <span class="text-danger">{{ errors.file_type }}</span>
                            </div>

                            <div class="form-group col-md-6">
                              <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label" for="file_url">{{ $t('clinic.lbl_select_file') }} </label>
                              </div>
                              <input type="file" class="form-control" id="file_url" ref="refInput" @change="fileUpload" :accept="acceptedFileTypes" />
                              <span v-if="errorMessages['file_url']">
                                <ul class="text-danger">
                                  <li v-for="err in errorMessages['file_url']" :key="err">{{ err }}</li>
                                </ul>
                              </span>
                              <span class="text-danger">{{ errors.file_url }}</span>
                            </div>

                          <div class="row">
                            <div class="col-12"> 
                              <div v-if="file_type == '.csv'">
                              <p>{{$t('appointment.following_field_required')}} {{$t('appointment.csv_file')}}:</p>
                               <p>
                                <a :href="`${baseUrl}/prescriptions/prescription.csv`" target="_blank" download="prescription.csv" class="text-primary">
                                      Click here to download sample file
                                  </a> 
                                 
                              </p>
                            </div> 

                            <div v-if="file_type == '.xlsx'">
                              <p>{{$t('appointment.following_field_required')}} {{$t('appointment.xls_file')}}:</p>
                               <p>
                                  <a :href="`${baseUrl}/prescriptions/prescription.xlsx`" target="_blank" download="prescription.xlsx" class="text-primary">
                                      Click here to download sample file
                                  </a>
                                
                              </p>
                            </div> 

                            <ul >
                              <li>{{$t('customer.lbl_name')}}</li>
                              <li>{{$t('appointment.frequency')}}</li>
                              <li>{{$t('appointment.duration')}}</li>
                            </ul>
                          </div>
                        </div>
                             
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('messages.close') }}</button>
                        <button v-if="is_loading==0"  type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
                        <button v-else  type="submit" class="btn btn-primary">{{ $t('appointment.loading') }}</button>
                      </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue'

import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import { IMPORT_PRESCRIPTION_FILE} from '@/vue/constants/encouter_prescription'
import { readFile } from '@/helpers/utilities'

const baseUrl =  window.base_url;
const emit = defineEmits(['import_prescription'])

const props = defineProps({
  encounter_id: { type: Number, default: '' },
  user_id: { type: Number, default: '' },
 
});

const is_loading=ref(0);

const file_types = ref({
  searchable: true,
  options: [
    { label: 'CSV', value: '.csv' },
    { label: 'XLS', value: '.xlsx' },

  ],
  closeOnSelect: true,
})



const { storeRequest,listingRequest,getRequest,updateRequest} = useRequest()

const ImageViewer = ref(null)
const fileUpload = async (e) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    ImageViewer.value = fileB64
  })
  file_url.value = file
}

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    file_type: '.csv',
    file_url: null,
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      file_type: data.file_type,
      file_url: data.file_url,

    }
  })
}

// Validations
const validationSchema = yup.object({
  file_type: yup.string().required('Please send file type'),
  file_url: yup.string().required('Please select file'),

})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: file_type } = useField('file_type')
const { value: file_url } = useField('file_url')

const acceptedFileTypes=ref(null)


watch(file_type, (newValue) => {
 
      if (newValue) {
        acceptedFileTypes.value = newValue;
      }
    });


const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})


const formSubmit = handleSubmit((values) => {

  is_loading.value=1

     values.user_id=props.user_id
     values.encounter_id=props.encounter_id

    storeRequest({ url: IMPORT_PRESCRIPTION_FILE, body: values , type:'file'}).then((res) => {
    if(res.status) {
      is_loading.value=0
      emit('import_prescription', {type: 'create_customer', value: res.prescription})
      setFormData(defaultData())
    
      bootstrap.Modal.getInstance(document.getElementById("upload_prescription_modal")).hide()
      } 
   })

  

})



</script>
