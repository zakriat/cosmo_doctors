export const MODULE = 'category'
export const INDEX_LIST_URL = (id) => {return {path: `app/${MODULE}/index_list`, method: 'GET'}}
export const EDIT_URL = (id) => {return {path: `app/${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `app/category`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `app/${MODULE}/${id}`, method: 'POST'}}