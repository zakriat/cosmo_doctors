export const MODULE = 'doctor-session'
export const LISTING_URL = ({clinic_id, doctor_id}) => {return {path: `doctor-session/index_list?clinic_id=${clinic_id}&doctor_id=${doctor_id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const DOCTORMAPPING_EDIT_URL = (id) => {return {path: `${MODULE}/edit-doctor-mapping?id=${id}`, method: 'GET'}}
export const CLINIC_LIST = (id) => {return {path: `clinics/index_list?doctor_id=${id}`, method: 'GET'}}
export const CLINICMAPPING_LIST = (id) => {return {path: `clinics/index_list?clinic_id=${id}`, method: 'GET'}}

export const DOCTOR_DATA = (id) => {return {path: `doctor/${id}/edit`, method: 'GET'}}
export const DOCTOR_LIST = (id) => {return {path: `doctor/index_list`, method: 'GET'}}

export const CLINIC_SESSION_LIST = (id) => {return {path: `clinic-session/index_list?clinic_id=${id}`, method: 'GET'}}
export const DOCTOR_DAYS_LIST = ({clinic_id, doctor_id}) => {return {path: `${MODULE}/day-list?clinic_id=${clinic_id}&doctor_id=${doctor_id}`, method: 'GET'}}
export const EDIT_DOCTOR_SESSION = ({doctor_id,clinic_id}) => {return {path: `${MODULE}/edit-session-data?clinic_id=${clinic_id}&doctor_id=${doctor_id}`, method: 'GET'}}
