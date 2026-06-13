export const MODULE = 'encounter'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `encounter`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}

export const CLINIC_LIST = ({id = ''}) => {return {path: `clinics/index_list?vendor_id=${id}`, method: 'GET'}}

// export const CLINIC_LIST = () => {return {path: `clinics/index_list`, method: 'GET'}}
export const DOCTOR_LIST = (id) => {return {path: `doctor/index_list?clinic_id=${id}`, method: 'GET'}}
export const PATIENT_LIST = () => {return {path: `customers/index_list`, method: 'GET'}}

export const VENDOR_LIST = () => {return {path: `multivendors/index_list`, method: 'GET'}}