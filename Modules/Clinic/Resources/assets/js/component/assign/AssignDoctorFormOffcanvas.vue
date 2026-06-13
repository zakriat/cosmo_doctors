<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="service-doctor-assign-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom" v-if="service">
        <h6 class="m-0 h5">
          Service: <span>{{ service.name }}</span>
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body">
        <div class="form-group">
          <div class="d-grid">
            <div class="d-flex flex-column">
              <div class="form-group">
                <label class="form-label">{{ $t('clinic.lbl_clinic_name') }}<span class="text-danger">*</span></label>
                <Multiselect id="clinic_id" v-model="clinic_id" v-bind="singleSelectOption" :options="clinicList.options" @select="selectClinic" class="form-group"></Multiselect>
              </div>
              <div v-if="isLoading" class="list-group-item">
                  <p class="m-0"> {{ $t('appointment.loading') }}</p>
                </div>
              <div class="form-group" v-else-if="doctors.options.length > 0">

                  <Multiselect v-if="role() == 'doctor' &&  assign_ids.length == 0" v-model="assign_ids" placeholder="Select Doctor" :canClear="false" :value="assign_ids" v-bind="doctors" @select="selectDoctor" id="doctors_ids">
                    <template v-slot:multiplelabel="{ values }">
                      <div class="multiselect-multiple-label">Select Doctor</div>
                    </template>
                  </Multiselect>

                  <Multiselect v-else-if="role() != 'doctor'" v-model="assign_ids" placeholder="Select Doctor" :canClear="false" :value="assign_ids" v-bind="doctors" @select="selectDoctor" id="doctors_ids">
                    <template v-slot:multiplelabel="{ values }">
                      <div class="multiselect-multiple-label">Select Doctor</div>
                    </template>
                  </Multiselect>
                  
              </div>
              <div v-else>
                <p> {{$t('clinic.doctor_not_available')}}</p>
              </div>
            </div>
            <div class="row" v-if="doctors.options.length > 0">
              <div v-for="(item, index) in selectedDOCTOR" :key="item" class="col-md-6">
                <div class="card mb-3">                  
                  <div class="card-body p-3 d-flex justify-content-between align-items-center flex-grow-1 gap-2">
                    <div class="d-flex align-items-center gap-2">
                      <!-- <span>{{ ++index }} - </span> -->
                      <img :src="item.avatar" class="avatar avatar-40 img-fluid rounded-pill" alt="user" />
                      <div class="flex-grow-1"> {{ item.doctor_name }}</div>
                    </div>
                    <div>
                    <div class="d-flex justify-content-end align-items-center gap-2 ">
                      <div class="d-flex align-items-center gap-1">{{ CURRENCY_SYMBOL }}<input v-model="item.charges" class="form-control" style="width:100px" @input="validateCharges(item)" min="0"/>
                       
                      </div>
                      <button type="button" @click="removeDoctor(item.doctor_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                    </div> 
                    <span v-if="item.chargesError" class="text-danger">{{ item.chargesError }}</span>
                  </div>
                  </div>                 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer">
        <p class="text-center"><small>{{$t('clinic.assign_doctor')}}</small></p>
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3" v-if="doctors.options.length > 0">
          <button class="btn btn-white" type="button" data-bs-dismiss="offcanvas">
            {{ $t('messages.close') }}
          </button>
          <button class="btn btn-secondary" :disabled="!isValid">
            {{ $t('messages.update') }}
          </button>
      </div>
    </div>
  </div>
</form>
</template>
<script setup>
import { ref, onMounted,watch,computed } from 'vue'
import { GET_ASSIGN_DOCTOR_LIST, ASSIGN_DOCTOR_STORE, EDIT_URL, CLINIC_CENTER_LIST,DOCTOR_LIST } from '../../constant/clinic-service'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

const { listingRequest, getRequest, updateRequest } = useRequest()

const doctors = ref({
  mode: 'multiple',
  searchable: true,
  options: []
})
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false
})
const clinic_id = ref([]);
const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
const selectedDOCTOR = ref([])
const isLoading = ref(false) 
// Form Values
const assign_ids = ref([])
const service = ref([])
const serviceId = useModuleId(() => {
  getRequest({url: EDIT_URL, id: serviceId.value}).then((res) => {
        if(res.status && res.data) {
          service.value = res.data;
        fetchClinicList(service.value.clinic_id);
        }
    })
}, 'doctor_assign');

const role = () => {
  return window.auth_role[0]
}

const fetchAssignDoctorList = (clinicIds) => {
  listingRequest({ url: GET_ASSIGN_DOCTOR_LIST, data: { id: serviceId.value, clinic_id: clinicIds } }).then(res => {
    if (res.status && res.data) {
      selectedDOCTOR.value = res.data;
      assign_ids.value = res.data.map((item) => item.doctor_id);
    }
  });
};

watch(clinic_id, (newValue, oldValue) => {
  if (newValue !== null) {
    fetchAssignDoctorList(newValue)
  }
});

const clinicList = ref([]);

const fetchClinicList = (clinicIds) => {
  useSelect({ url: CLINIC_CENTER_LIST, data: { clinic_id: clinicIds } }, { value: 'id', label: 'clinic_name' }).then(data => {
    clinicList.value.options = data.options;
    if (data.options.length > 0) {
      clinic_id.value = data.options[0].value;
      fetchDoctorList(clinic_id.value);
      fetchAssignDoctorList(clinic_id.value);
    }
  });
};

const selectClinic = (value) => { 

  clinic_id.value = value
  fetchDoctorList(clinic_id.value);
};

const doctorList = ref([]);

const fetchDoctorList = (clinicIds) => {
  isLoading.value = true
  listingRequest({ url: DOCTOR_LIST, data: { clinic_id: clinicIds } }).then(res => {
    doctorList.value = res;
    doctors.value.options = buildMultiSelectObject(res, { value: 'doctor_id', label: 'doctor_name' });

    isLoading.value = false
  })
};

onMounted(() => {
  fetchClinicList(clinic_id.value);
});

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const errorMessages = ref([])
const reset_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Offcanvas.getInstance('#service-doctor-assign-form').hide()
    renderedDataTable.ajax.reload(null, false)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formSubmit = () => {
  const data = { doctors: selectedDOCTOR.value,
                  clinic_id : clinic_id.value }
  updateRequest({ url: ASSIGN_DOCTOR_STORE, id: serviceId.value, body: data }).then((res) => reset_close_offcanvas(res));
}

const selectDoctor = (value) => {
  selectedDOCTOR.value = [...selectedDOCTOR.value, ...doctorList.value.filter((doctor) => doctor.doctor_id === value)]
}

const removeDoctor = (value) => {
  selectedDOCTOR.value = [...selectedDOCTOR.value.filter((doctor) => doctor.doctor_id !== value)]
  assign_ids.value = [...assign_ids.value.filter((id) => id !== value)]
}

const validateCharges = (item) => {
  if (item.charges <= 0) {
    item.chargesError = 'Charges must be greater than 0';
  } else {
    item.chargesError = null;
  }
}

watch(selectedDOCTOR, (newVal) => {
  newVal.forEach((item) => {
    if (item.charges <= 0) {
      item.chargesError = 'Charges must be greater than 0';
    }
    else if(service.value.discount_type == 'fixed' && item.charges <= service.value.discount_value) {
      item.chargesError = 'Charges must be greater than or equal to the discount value';
    }
    else {
      item.chargesError = null;
    }
  });
}, { deep: true });

const isValid = computed(() => {
  return selectedDOCTOR.value.every(item => item.charges > 0 && !item.chargesError);
});

</script>
