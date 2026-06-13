<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
              <div class="form-group">
                <label class="form-label" for="encounter_date">{{ $t('clinic.lbl_encounter_date') }} <span class="text-danger">*</span></label>

                <flat-pickr :placeholder="$t('clinic.lbl_encounter_date')" id="encounter_date" class="form-control" v-model="encounter_date" :value="encounter_date" :config="config"></flat-pickr>
                <span v-if="errorMessages['encounter_date']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['encounter_date']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.encounter_date }}</span>
              </div>

              <div class="form-group" v-if="enable_multi_vendor()==1 && (role() === 'admin' || role() === 'demo_admin')">
                <label class="form-label" for="vendor_id">{{ $t('clinic.clinic_admin') }}  </label>
                <Multiselect class="form-group" v-model="vendor_id" :value="vendor_id" :options="vendors.options"  v-bind="singleSelectOption" @select="getClinic" :placeholder="$t('clinic.clinic_admin')" id="vendor_id"></Multiselect>
                <span v-if="errorMessages['vendor_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['vendor_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.vendor_id }}</span>
              </div>

              <div class="form-group">
                <label class="form-label col-md-12">{{ $t('clinic.lbl_select_clinic') }} <span class="text-danger">*</span></label>
                <Multiselect id="clinic_id" v-model="clinic_id" :value="clinic_id" :placeholder="$t('clinic.lbl_select_clinic')" v-bind="singleSelectOption" :options="cliniclist.options" @select="getDoctorList" class="form-group"></Multiselect>
                <span v-if="errorMessages['clinic_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['clinic_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['clinic_id'] }}</span>
              </div>

              <div class="form-group">
                <label class="form-label col-md-12">{{ $t('clinic.lbl_select_doctor') }} <span class="text-danger">*</span></label>
                <Multiselect id="doctor_id" v-model="doctor_id" :value="doctor_id" :placeholder="$t('clinic.lbl_select_doctor')" v-bind="singleSelectOption" :options="doctorlist.options" @select="getServiceList" class="form-group"></Multiselect>
                <span v-if="errorMessages['doctor_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['doctor_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['doctor_id'] }}</span>
              </div>

              <div class="form-group" >
                <label class="form-label">{{ $t('clinic.lbl_select_patient') }} <span class="text-danger">*</span></label>
                <Multiselect id="user_id" v-model="user_id" :value="user_id" :placeholder="$t('clinic.lbl_select_patient')" v-bind="singleSelectOption" :options="patientlist.options" class="form-group"></Multiselect>
                <span v-if="errorMessages['user_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['user_id']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors['user_id'] }}</span>
              </div>
            
              <div class="form-group">
                <label class="form-label" for="description">{{ $t('category.lbl_description') }}</label>
                <textarea class="form-control" v-model="description" id="description"></textarea>
                <span v-if="errorMessages['description']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.description }}</span>
              </div>             

              <div v-for="field in customefield" :key="field.id">
                <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
              </div>            
          </div>
        </div>
      </div>

      <div class="offcanvas-footer border-top pt-4">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">          
          <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.close') }}
          </button>
          <button class="btn btn-secondary" name="submit" :disabled="IS_SUBMITED">
            <template v-if="IS_SUBMITED">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ $t('appointment.loading') }}
            </template>
            <template v-else> {{ $t('messages.save') }}</template>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import moment from 'moment'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { EDIT_URL, STORE_URL, UPDATE_URL, CLINIC_LIST, DOCTOR_LIST, PATIENT_LIST,VENDOR_LIST } from '../../constant/appointment-encounter'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FlatPickr from 'vue-flatpickr-component'
// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

// flatepicker

const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  minDate: 'today'
})
const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})
// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
    
         getClinic(res.data.vendor_id)
         getDoctorList(res.data.clinic_id)
         setFormData(res.data)
       
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const role = () => {
    return window.auth_role[0];
}

const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}


const vendors = ref({ options: [], list: [] })
const getVendorList = () => {
  useSelect({ url: VENDOR_LIST,data: { system_service: 'clinic' }  }, { value: 'id', label: 'name' }).then((data) => (vendors.value = data))
  }


// Validations
const validationSchema = yup.object({
  clinic_id: yup.string().required('Clinic name is required'),
  doctor_id: yup.string().required('Doctor is required'),
  encounter_date: yup.date().required('Encounter Date is required'),
  user_id: yup.string().required('Patient is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: clinic_id } = useField('clinic_id')
const { value: doctor_id } = useField('doctor_id')
const { value: encounter_date } = useField('encounter_date')
const { value: user_id } = useField('user_id')
const { value: description } = useField('description')
const { value: vendor_id } = useField('vendor_id')


const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
  getClinic()
  getPatient()
  getVendorList()
  getDoctorList()
})

// Default FORM DATA
const defaultData = () => {
  const currentDate = new Date().toISOString().substr(0, 10);
  errorMessages.value = {}
  return {
    clinic_id:'',
    doctor_id:'',
    encounter_date:currentDate,
    user_id:'',
    description:'',
    vendor_id:'',
  
  }
}
const patientlist = ref({ options: [], list: [] })

const getPatient = () => {
  useSelect({ url: PATIENT_LIST }, { value: 'id', label: 'name' }).then((data) => (patientlist.value = data))
}




const selectedPatient = computed(() => patientlist.value.list.find((patient) => patient.id == user_id.value) ?? null)

const removePatient = () => {
  user_id.value = null
}
const IS_SUBMITED = ref(false)

const cliniclist = ref({ options: [], list: [] })

const getClinic = (value) => {
  clinic_id.value = '';
  useSelect({ url: CLINIC_LIST, data: { id: value } }, { value: 'id', label: 'clinic_name' }).then((data) => (cliniclist.value = data))
}



const doctorlist = ref({ options: [], list: [] })

const getDoctorList = (value) => {
  doctor_id.value='',
  useSelect({ url: DOCTOR_LIST, data: value }, { value: 'doctor_id', label: 'doctor_name' }).then((data) => (doctorlist.value = data))
}
//  Reset Form
const setFormData = (data) => {
  const encounterDate = new Date(data.encounter_date);
  const formattedDate = encounterDate.toISOString().split('T')[0];
  resetForm({
    values: {
      clinic_id: data.clinic_id,
      doctor_id: data.doctor_id,
      encounter_date: formattedDate,
      user_id: data.user_id,
      description:data.description,
      vendor_id:data.vendor_id,
    }
  })
}

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

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }

})

</script>
