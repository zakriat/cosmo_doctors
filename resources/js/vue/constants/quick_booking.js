// export const SERVICE_LIST = ({ service_provider_id, employee_id }) => {
//   return { path: `api/quick-booking/services-list?service_provider_id=${service_provider_id}&employee_id=${employee_id}`, method: 'GET' }
// }
export const SERVICE_PROVIDER_LIST = ({ employee_id, service_id, start_date_time }) => {
  return { path: `api/quick-booking/service-provider-list?employee_id=${employee_id}&service_id=${service_id}&start_date_time=${start_date_time}`, method: 'GET' }
}
export const EMPLOYEE_LIST = ({ service_provider_id, service_id, start_date_time }) => {
  return { path: `api/quick-booking/employee-list?service_provider_id=${service_provider_id}&service_id=${service_id}&start_date_time=${start_date_time}`, method: 'GET' }
}
export const SLOT_TIME_LIST = ({ date,doctor_id, service_id, clinic_id}) => {
  return { path: `api/quick-booking/slot-time-list?appointment_date=${date}&doctor_id=${doctor_id}&service_id=${service_id}&clinic_id=${clinic_id}`, method: 'GET' }
}
export const STORE_URL = () => {
  return { path: `api/quick-booking/store`, method: 'POST' }
}

export const SYSTEM_SERVICE_LIST = ({ user_id }) => {
  return { path: `api/quick-booking/system-service-list?user_id=${user_id}`, method: 'GET' }
}

export const SERVICE_LIST = ({ user_id, system_service_id }) => {
  return { path: `api/quick-booking/services-list?user_id=${user_id}&system_service_id=${system_service_id}`, method: 'GET' }
}

export const CLINIC_LIST = ({ user_id, service_id }) => {
  return { path: `api/quick-booking/clinic-list?user_id=${user_id}&service_id=${service_id} `, method: 'GET' }
}

export const DOCTOR_LIST = ({ user_id, service_id, clinic_id }) => {
  return { path: `api/quick-booking/doctor-list?user_id=${user_id}&service_id=${service_id}&clinic_id=${clinic_id} `, method: 'GET' }
}

export const VERIFY_CUSTOMER = () => {return {path: `api/quick-booking/verify_customer`, method: 'POST'}}





