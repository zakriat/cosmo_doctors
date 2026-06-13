export const MODULE = 'billing-record'

export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `billing-record`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}

export const CLINIC_LIST = () => {return {path: `clinics/index_list`, method: 'GET'}}
// export const CLINIC_LIST = () => {return {path: `clinics/index_list`, method: 'GET'}}
export const DOCTOR_LIST = () => {return {path: `doctor/index_list`, method: 'GET'}}
export const PATIENT_LIST = () => {return {path: `customers/index_list`, method: 'GET'}}
export const ENCOUNTER_LIST = ({id}) => {return {path: `encounter/index_list?encounter_id=${id}`, method: 'GET'}}
export const SERVICE_LIST = ({doctor_id, clinic_id}) => {return {path: `services/index_list?doctorId=${doctor_id}&clinicId=${clinic_id}`, method: 'GET'}}
export const APPOINTMENT_SERVICE_LIST = ({id}) => {return {path: `services/index_list?service_id=${id}`, method: 'GET'}}
export const GET_SERVICE_DETAILS = ({encounter_id,service_id}) => {return {path: `services/service-details?service_id=${service_id}&encounter_id=${encounter_id}`, method: 'GET'}}
export const SAVE_BILLING_DETAILS = () => {return {path: `billing-record/save-billing-details`, method: 'POST'}}
