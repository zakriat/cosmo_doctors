export const MODULE = 'nurse'
export const EDIT_URL = (id) => { return { path: `${MODULE}/${id}/edit`, method: 'GET' } }
export const STORE_URL = () => { return { path: `${MODULE}`, method: 'POST' } }
export const UPDATE_URL = (id) => { return { path: `${MODULE}/${id}`, method: 'POST' } }
export const CHANGE_PASSWORD_URL = () => { return { path: `${MODULE}/change-password`, method: 'POST' } }

export const COUNTRY_URL = () => { return { path: `world/country-list`, method: 'GET' } }
export const STATE_URL = (data) => { return { path: `world/state-list?country_id=${data}`, method: 'GET' } }
export const CITY_URL = (data) => { return { path: `world/city-list?state_id=${data}`, method: 'GET' } }
export const CLINIC_CENTER_LIST = () => { return { path: `clinics/index_list`, method: 'GET' } }
export const VENDOR_LIST = () => { return { path: `multivendors/index_list`, method: 'GET' } }