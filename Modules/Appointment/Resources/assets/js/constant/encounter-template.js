export const MODULE = 'encounter-template'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `encounter-template`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}


export const TEMPLATE_DETAIL = (id) => {return {path: `${MODULE}/template-detail/${id}`, method: 'GET'}}
export const SAVE_TEMPLATE_OPTION_DATA = () => {return {path: `${MODULE}/save-template-histroy`, method: 'POST'}}
export const REMOVE_TEMPLATE_HISTROY_DATA = ({ id = '',type=' '}) => {return { path: `${MODULE}/remove-template-histroy?id=${id}&type=${type}`, method: 'GET' }}

export const DELETE_PRESCRIPTION_URL = (id) => {return {path: `${MODULE}/delete-prescription/${id}`, method: 'GET'}}
export const SAVE_OTHER_DETAIL_DATA = () => {return {path: `${MODULE}/save-other-details`, method: 'POST'}}