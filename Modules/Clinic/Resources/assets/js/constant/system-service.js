export const MODULE = 'system-service'
export const EDIT_URL = (id) => {return { path: `${MODULE}/${id}/edit`, method: 'GET' }}
export const STORE_URL = () => {return { path: `${MODULE}`, method: 'POST' }}
export const UPDATE_URL = (id) => {return { path: `${MODULE}/${id}`, method: 'POST' }}
export const CATEGORY_LIST = ({id = ''}) => { return { path: `category/index_list?clinic_id=${id}`, method: 'GET' }}
export const SUB_CATEGORY_LIST = ({id = ''}) => {return {path: `category/index_list?parent_id=${id}`, method: 'GET'}}
export const SETTING_DATA = () => {return {path: `google-key`, method: 'GET'}}