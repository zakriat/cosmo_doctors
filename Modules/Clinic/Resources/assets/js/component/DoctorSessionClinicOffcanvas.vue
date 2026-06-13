<template>
  <form @submit="formSubmit" class="bussiness-hour">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas"
      aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-md-6" v-if="doctorlist.options.length > 0 && currentId === 0">
            <div class="form-group">
              <label class="form-label">{{ $t('clinic.lbl_select_doctor') }}<span class="text-danger">*</span></label>
              <Multiselect id="doctor_id" v-model="doctor_id" :placeholder="$t('clinic.lbl_select_doctor')" :options="doctorlist.options" @select="getClinicList" class="form-group"></Multiselect>
              <span v-if="errorMessages['doctor_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['doctor_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.doctor_id }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label col-md-6">{{ $t('clinic.lbl_select_clinic') }} <span
                  class="text-danger">*</span></label>
              <Multiselect id="clinic_id" v-model="clinic_id" :value="clinic_id" placeholder="Select Clinic"
                v-bind="singleSelectOption" :options="cliniclist.options" @select="ClinicSelect" class="form-group" :disabled="currentId !== 0">
              </Multiselect>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <ul class="data-scrollbar list-group list-group-flush">
                  <li v-if="isLoading" class="list-group-item p-0 mb-3">
                    <p>Loading...</p>
                  </li>
                  <li v-else v-for="(day, index) in weekdays" class="list-group-item p-0 mb-3" :key="++index">
                    <div class="form-group d-flex align-items-center justify-content-between" :class="{ 'is-loading': isLoading }">
                      <div class="d-flex align-items-center gap-1 col-md-3">
                        <div class="form-check">
                          <input class="form-check-input" :value="1" name="is_holiday" :id="`${index}-dayoff`"
                            type="checkbox" :true-value="1" :false-value="0" v-model="day.is_holiday"
                            :disabled="clinicHolidays.includes(day.day)"
                            :checked="day.is_holiday || clinicHolidays.includes(day.day)" />
                        </div>
                        <h6 class="text-capitalize m-0 session-day"> {{ day.day }}
                          <!-- <i v-if="index === 1" class="fa fa-copy copy-icon" aria-hidden="true" @click="handleCopy"></i> -->
                        </h6>

                      </div>
                      <div class="col-md-6 d-flex align-items-center gap-2" v-if="!day.is_holiday && !clinicHolidays.includes(day.day)">
                        <flat-pickr id="start_time" class="session-time" v-model="day.start_time" :config="config"
                          :disabled="day.is_holiday ? true : false || clinicHolidays.includes(day.day)"
                          :class="{ background_colour: day.is_holiday }"></flat-pickr>
                        <flat-pickr id="end_time" class="session-time" v-model="day.end_time" :config="config"
                          :disabled="day.is_holiday ? true : false || clinicHolidays.includes(day.day)"
                          :class="{ background_colour: day.is_holiday }"></flat-pickr>
                        <div v-if="day.startTimeError" class="text-danger">{{ day.startTimeError }}</div>
                        <div v-if="day.endTimeError" class="text-danger">{{ day.endTimeError }}</div>
                      </div>
                      <div v-else-if="clinicHolidays.includes(day.day)" :class="{'col-sm-9': clinicHolidays.includes(day.day) }">
                        <p class="m-0"> {{ $t('clinic.clinic_closed') }}</p>
                      </div>
                      <div v-else-if="day.is_holiday" :class="{'col-sm-9': day.is_holiday }">
                        <p class="m-0"> {{ $t('clinic.unavailable') }}</p>
                      </div>
                      <template v-if="!day.is_holiday && !clinicHolidays.includes(day.day)" class="col-md-3">
                        <div>
                          <a @click="addInputField(day)" class="clickable-text text-secondary">
                            {{ $t('clinic.lbl_add_break') }}
                          </a>
                        </div>
                      </template>
                    </div>
                    <div v-for="(input, index) in day.breaks" :key="index"
                      class="form-group d-flex align-items-center justify-content-between">
                      <h6 class="col-md-3">{{ $t('clinic.lbl_break') }}</h6>
                      <div class="col-md-6 d-flex align-items-center gap-2">
                        <flat-pickr id="start_break" class="form-control session-time" v-model="input.start_break"
                          :config="config"></flat-pickr>
                        <flat-pickr id="end_break" class="form-control session-time" v-model="input.end_break"
                          :config="config"></flat-pickr>
                      </div>
                      <div class="col-md-3 text-end">
                        <a class="btn btn-sm btn-primary" @click="deleteInputField(day, index)">{{ $t('messages.remove')
                          }}</a>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watchEffect, computed } from 'vue'
import * as yup from 'yup'
import moment from 'moment'
import FlatPickr from 'vue-flatpickr-component'
import { useForm, useField } from 'vee-validate'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import { CLINICMAPPING_LIST, LISTING_URL, STORE_URL, DOCTOR_DATA, DOCTOR_LIST, EDIT_URL, CLINIC_SESSION_LIST, DOCTORMAPPING_EDIT_URL } from '../constant/doctor-session'

const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
})
const isLoading=ref(false)
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false
})
const currentId = useModuleId(() => {
  // doctor_id.value = currentId.value;
  if (currentId.value > 0) {
    isLoading.value = true;
    getRequest({ url: DOCTORMAPPING_EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        doctor_id.value = res.data.doctor_id;
        clinic_id.value = res.data.clinic_id
        getClinicList(clinic_id.value);
      }
    }
    )
  } else {
    weekdays.value = defaultData();
  }
})


const IS_SUBMITED = ref(false)

const config = ref({
  dateFormat: 'H:i:S',
  altInput: true,
  altFormat: 'h:i K',
  enableTime: true,
  noCalendar: true,
  defaultHour: '09', // Update default hour to 9
  defaultMinute: '00',
  defaultSeconds: '00',
  static: true
})

const { storeRequest, getRequest } = useRequest()

const validationSchema = yup.object({
  doctor_id: yup.string().required('Doctor name is required')
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const { value: doctor_id } = useField('doctor_id')
const { value: clinic_id } = useField('clinic_id')
const clinic = ref({ options: [], list: [] })

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}


//  Reset Form


const defaultData = () => {
  doctor_id.value = ''
  return [
    { day: 'monday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'tuesday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'wednesday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'thursday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'friday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'saturday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'sunday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] }
  ]
}
const weekdays = ref(defaultData())

const handleCopy = () => {
  weekdays.value.forEach((day, index) => {
    if (index !== 0) {
      day.start_time = weekdays.value[0].start_time
      day.end_time = weekdays.value[0].end_time
      day.is_holiday = weekdays.value[0].is_holiday
      day.breaks = [...weekdays.value[0].breaks]
    }
  })
}


const doctorlist = ref({ options: [], list: [] })
//get all doctor
const getDoctorList = (value) => {
  useSelect({ url: DOCTOR_LIST, data: value }, { value: 'doctor_id', label: 'doctor_name' }).then((data) => {
    doctorlist.value = data;
    if (data.options.length > 0) {
      doctor_id.value = data.options[0].value
    }
    const selectedDoctorId = doctor_id.value;
    getClinicList(selectedDoctorId);
  })
}

const cliniclist = ref({ options: [], list: [] })

//get clinic
const getClinicList = (doctorId) => {
  isLoading.value = true; // Start loading
  useSelect({ url: CLINICMAPPING_LIST, data: clinic_id.value }, { value: 'id', label: 'clinic_name' }).then((data) => {
    cliniclist.value = data
    if (data.options.length > 0) {
      clinic_id.value = data.options[0].value
    }
    
    getRequest({ url: LISTING_URL, id: { clinic_id: clinic_id.value, doctor_id: doctor_id.value } }).then((res) => {
      if (res.status) {
        if (res.data != '') {
          weekdays.value = res.data
        } else {
          getClinicSession(clinic_id.value);
        }
      }
    }).finally(() => {
      isLoading.value = false; // End loading
    });
  }).catch(() => {
    isLoading.value = false; // End loading on error
    getClinicSession(clinic_id.value);
  });
}

const clinicHolidays = ref([]);
const clinicStartTime = ref([]);
const clinicEndTime = ref([]);
const getClinicSession = () => {

  getRequest({ url: CLINIC_SESSION_LIST, id: clinic_id.value }).then((res) => {
    if (res.status) {
      if (res.data != '') {
        weekdays.value = res.data;
        clinicStartTime.value = res.data.map(day => day.start_time);
        clinicEndTime.value = res.data.map(day => day.end_time);
        clinicHolidays.value = res.data.filter(day => day.is_holiday).map(day => day.day);
      } else {
        weekdays.value = defaultData();
      }
    }
  });
};

const ClinicSelect = (e) => {
  isLoading.value = true; 
  const ClinicId = clinic_id.value
  const doctorId = doctor_id.value
  
  getRequest({ url: LISTING_URL, id: { clinic_id: ClinicId, doctor_id: doctorId } }).then((res) => {
    if (res.status) {
      if (res.data != '') {
        weekdays.value = res.data
      } else {
        getClinicSession(ClinicId);
      }
    }
  }).finally(() => {
      isLoading.value = false; // End loading
      getClinicSession(ClinicId);
    });
}

onMounted(() => {
  //getClinicSession();
  getDoctorList();
  if (doctor_id.value) {
    getClinicList(doctor_id.value);
  }
});

watchEffect(() => {
  if (clinicStartTime && clinicEndTime && clinicStartTime.value && clinicEndTime.value) {
    if (Array.isArray(weekdays.value)) {
      weekdays.value.forEach((day, index) => {
        const doctorStartTime = moment(day.start_time, 'HH:mm');
        const doctorEndTime = moment(day.end_time, 'HH:mm');
        const clinicStartTimes = moment(clinicStartTime.value[index], 'HH:mm');
        const clinicEndTimes = moment(clinicEndTime.value[index], 'HH:mm');

        delete day.startTimeError;
        delete day.endTimeError;
        if (doctorStartTime.isBefore(clinicStartTimes) || doctorStartTime.isAfter(clinicEndTimes)) {
          day.startTimeError = `Doctor start time for ${day.day} must be between clinic operating hours`;
        }

        if (doctorEndTime.isBefore(clinicStartTimes) || doctorEndTime.isAfter(clinicEndTimes)) {
          day.endTimeError = `Doctor end time for ${day.day} must be between clinic operating hours`
        }
      });
    }
  }
});

const hasTimeSlotErrors = computed(() => {
  return weekdays.value.some(day => day.startTimeError || day.endTimeError);
});

const addInputField = (day) => {
  day.breaks.push({ start_break: moment().set({ hour: 0, minute: 0, second: 0 }).format('HH:mm'), end_break: moment().set({ hour: 0, minute: 0, second: 0 }).format('HH:mm') })
}

const deleteInputField = (day, index) => {
  day.breaks.splice(index, 1)
}
const errorMessages = ref({})
const reset_datatable_close_offcanvas = (res) => {

  IS_SUBMITED.value = false

  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()

  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.weekdays = weekdays.value
  if (hasTimeSlotErrors.value) {
    window.errorSnackbar('Please correct the errors before submitting.');
    IS_SUBMITED.value = false
    return;
  }
  values.clinic_id = clinic_id.value
  values.doctor_id = doctor_id.value

  values.weekdays.forEach(day => {
    day.is_holiday = clinicHolidays.value.includes(day.day) || day.is_holiday === 1 ? 1 : 0;
  });
  storeRequest({ url: STORE_URL, body: values }).then((res) => {
    if (res.status) {
      weekdays.value = res.data
      IS_SUBMITED.value = false
      reset_datatable_close_offcanvas(res)

    }
  })
})

</script>
<style>
.multiselect-clear {
  display: none !important;
}

.clickable-text {
  display: inline-block;
  cursor: pointer;
}

.background_colour {
  background-color: #50494917 !important;
  cursor: not-allowed;
}

.copy-icon {
  color: gray;
}
</style>
