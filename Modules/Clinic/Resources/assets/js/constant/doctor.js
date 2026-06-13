export const MODULE = 'doctor'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const BRANCH_LIST = () => {return {path: `employees/index_list`, method: 'GET'}}
export const SERVICE_LIST = ({clinic_id = '', category_id = '', employee_id = '',serviceId = '' }) => {return {path: `doctor/service_list?clinic_id=${clinic_id}&employee_id=${employee_id}&category_id=${category_id}&serviceId=${serviceId}`, method: 'GET'}}
export const COMMISSION_LIST = ({type = ''}) => {return {path: `commissions/index_list?type=${type}`, method: 'GET'}}
export const CHANGE_PASSWORD_URL = () => {return {path: `${MODULE}/change-password/`, method: 'POST'}}

export const SEND_PUSH_NOTIFICATION = () => {return {path: `${MODULE}/send-push-notification`, method: 'POST'}}

export const CLINIC_CENTER_LIST = ({id = '', clinic_id = ''}) => {return { path: `clinics/index_list?vendor_id=${id}&clinic_id=${clinic_id}`, method: 'GET' }}
export const VENDOR_LIST = ({system_service = ''}) => {return {path: `multivendors/index_list?system_service=${system_service}`, method: 'GET'}}
export const APP_CONFIGURATION = () => {return {path: `app-configuration`, method: 'GET'}}
export const DOCTOR_URL = (id) => {return {path: `${MODULE}/doctor-details/${id}`, method: 'GET'}}

export const COUNTRY_URL = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_URL = (id) => {return {path: `state/index_list?country_id=${id}`, method: 'GET'}}
export const CITY_URL = (id) => {return {path: `city/index_list?state_id=${id}`, method: 'GET'}}

export const CLINIC_LIST = ({clinic_id = ''}) => {return { path: `clinics/index_list?clinicId=${clinic_id}`, method: 'GET' }}
export const USER_LIST = () => {return {path: `${MODULE}/user-list/`, method: 'GET'}}



