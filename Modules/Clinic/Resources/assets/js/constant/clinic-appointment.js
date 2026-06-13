export const MODULE = 'clinic-appointments'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}

export const COUNTRY_URL = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_URL = (id) => {return {path: `state/index_list?country_id=${id}`, method: 'GET'}}
export const CITY_URL = (id) => {return {path: `city/index_list?state_id=${id}`, method: 'GET'}}

export const CLINIC_LIST = () => {return {path: `clinics/index_list`, method: 'GET'}}
export const DOCTOR_LIST = (id) => {return {path: `?clinic_doctor/index_listid=${id}`, method: 'GET'}}

export const SERVICE_LIST = (id) => {return {path: `services/index_list?clinic_id=${id}`, method: 'GET'}}




