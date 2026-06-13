export const MODULE = 'appointments'
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content')

export const EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/edit`, method: 'GET' }
}
export const PATIENT_OTHER_STORE_URL = () => {
  return { path: `appointment/other-patient`, method: 'POST' }
}
export const STORE_URL = () => {
  return { path: `appointment`, method: 'POST' }
}
export const UPDATE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'POST' }
}

export const SAVE_PAYMENT = () => {
  return { path: `appointment/save-payment`, method: 'POST' }
}

export const COUNTRY_URL = () => {
  return { path: `country/index_list`, method: 'GET' }
}
export const STATE_URL = (id) => {
  return { path: `state/index_list?country_id=${id}`, method: 'GET' }
}
export const CITY_URL = (id) => {
  return { path: `city/index_list?state_id=${id}`, method: 'GET' }
}

export const CLINIC_LIST = () => {
  return { path: `clinics/index_list`, method: 'GET' }
}
export const DOCTOR_LIST = (id) => {
  return { path: `doctor/index_list?clinic_id=${id}`, method: 'GET' }
}
export const SERVICE_LIST = ({ doctor_id, clinic_id }) => {
  return { path: `services/index_list?doctorId=${doctor_id}&clinicId=${clinic_id}`, method: 'GET' }
}

export const OTHERPATIENT_LIST = (id) => {
  return { path: `appointment/other-patientlist?patient_id=${id}`, method: 'GET' }
};

export const PATIENT_LIST = () => {
  return { path: `customers/index_list?filter=all`, method: 'GET' }
}

export const SERVICE_PRICE = ({ ServiceId, DoctorId }) => {
  return { path: `services/service-price?service_id=${ServiceId}&doctor_id=${DoctorId}`, method: 'GET' }
}

export const TAX_DATA = ({module_type,tax_type}) => {
  console.log('module_type', module_type)
  return { path: `tax/index_list?module_type=${module_type}&tax_type=${tax_type}`, method: 'GET' }
}

export const GET_AVALABLE_SLOT = ({ Appointment_date, DoctorId, ClinicId, serviceId }) => {
  return { path: `doctor/get-available-slot?appointment_date=${Appointment_date}&doctor_id=${DoctorId}&clinic_id=${ClinicId}&service_id=${serviceId}`, method: 'GET' }
}

export const APPOINTMNET_RECORD_STORE_URL = (id) => {
  return { path: `${MODULE}/appointment_patient/${id}`, method: 'POST' }
}
export const APPOINTMNET_RECORD_EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/appointment_patient_data`, method: 'GET' }
}
export const APPOINTMNET_BODYCHART_STORE_URL = (id) => {
  return { path: `${baseUrl}/app/${MODULE}/appointment_bodychart/${id}`, method: 'POST' }
}
export const BODYCHART_TEMPLATEDATA = (id) => {
  return { path: `${baseUrl}/app/bodychart/bodychart_form/${id}/bodychart_templatedata`, method: 'GET' }
}
export const TEMPLATE_IMAGE_LIST = () => {
  return { path: `${baseUrl}/app/bodychart/bodychart_image_list`, method: 'GET' }
}
export const APPOINTMNET_BODYCHART_EDIT_URL = (id) => {
  return { path: `${id}/appointment_bodychart_data`, method: 'GET' }
}

export const APPOINTMNET_UPDATE_BODYCHART = (id) => {
  return { path: `appointment_upadtebodychart/${id}`, method: 'POST' }
}
export const CLINIC_APPOINTMNET_URL = (id) => {
  return { path: `${MODULE}/appointment-details/${id}`, method: 'GET' }
}
export const APPOINTMNET_URL = (id) => {
  return { path: `patient_list/${id}`, method: 'GET' }
}
export const SETTING_URL = (id) => {
  return { path: `settings`, method: 'GET' }
}
