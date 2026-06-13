export const MODULE = 'currencies'
export const LISTING_URL = () => {return {path: `${MODULE}/index_data`, method: 'GET'}}
export const DATATABLE_URL = () => {return {path: `${MODULE}/index_list`, method: 'GET'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const DELETE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'DELETE'}}
export const COUNTRY_URL = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_URL = (id) => {return {path: `state/index_list?country_id=${id}`, method: 'GET'}}
export const CITY_URL = (id) => {return {path: `city/index_list?state_id=${id}`, method: 'GET'}}
// export const LISTING_URL = () => { return {path:`${MODULE}/index_list`,method:'GET'}}
// export const STORE_URL = () => {return {path: `currencies`, method: 'POST'}}