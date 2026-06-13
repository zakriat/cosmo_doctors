export const MODULE = 'promotions'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const TIME_ZONE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}