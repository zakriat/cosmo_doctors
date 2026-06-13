export const MODULE = 'clinics'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}

export const COUNTRY_URL = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_URL = (id) => {return {path: `state/index_list?country_id=${id}`, method: 'GET'}}
export const CITY_URL = (id) => {return {path: `city/index_list?state_id=${id}`, method: 'GET'}}


// Gallery Images
export const GET_GALLERY_URL = (id) => {return {path: `${MODULE}/gallery-images/${id}`, method: 'GET'}}
export const POST_GALLERY_URL = (id) => {return {path: `${MODULE}/gallery-images/${id}`, method: 'POST'}}
export const CLINIC_CATEGORY = () => {return { path: `specializations/index_list`, method: 'GET' }}
export const VENDOR_LIST = () => {return {path: `multivendors/index_list`, method: 'GET'}}
export const APP_CONFIGURATION = () => {return {path: `app-configuration`, method: 'GET'}}
export const CLINIC_URL = (id) => {return {path: `${MODULE}/clinic-details/${id}`, method: 'GET'}}
export const SAVE_SPECIALITY  = () => {return {path: `specializations`, method: 'POST'}}
