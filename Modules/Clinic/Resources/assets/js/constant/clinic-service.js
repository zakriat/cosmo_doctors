export const MODULE = 'services'
export const EDIT_URL = (id) => {return { path: `${MODULE}/${id}/edit`, method: 'GET' }}
export const STORE_URL = () => {return { path: `${MODULE}`, method: 'POST' }}
export const UPDATE_URL = (id) => {return { path: `${MODULE}/${id}`, method: 'POST' }}
export const CATEGORY_LIST = () => { return { path: `category/index_list`, method: 'GET' }}

export const CLINIC_CENTER_LIST = ({id = '',clinic_id = ''}) => {return {path: `clinics/index_list?vendor_id=${id}&clinic_id=${clinic_id}`, method: 'GET'}}
export const SUB_CATEGORY_LIST = ({id = ''}) => {return {path: `category/index_list?parent_id=${id}`, method: 'GET'}}

export const DOCTOR_LIST = ({clinic_id = ''}) => {return {path: ` doctor/index_list?clinic_id=${clinic_id}`, method: 'GET'}}

// doctor Assign
export const GET_ASSIGN_DOCTOR_LIST = ({id = '',clinic_id = ''}) => {return {path: `doctor/assign-doctor-list?service_id=${id}&clinic_id=${clinic_id}`, method: 'GET'}}
export const ASSIGN_DOCTOR_STORE = (id) => {return {path: `${MODULE}/assign-doctor-store/${id}`, method: 'POST'}}
export const VENDOR_LIST = ({system_service = ''}) => {return {path: `multivendors/index_list?system_service=${system_service}`, method: 'GET'}}
export const APP_CONFIGURATION = () => {return {path: `app-configuration`, method: 'GET'}}

export const SYSTEM_SERVICE_LIST = () => {return {path: `system-service/index_list`, method: 'GET'}}
export const SETTING_DATA = () => {return {path: `google-key`, method: 'GET'}}
export const TAX_LIST = ({module_type,tax_type}) => {
    return { path: `tax/index_list?tax_type=${tax_type}&module_type=${module_type}`, method: 'GET' }
  }