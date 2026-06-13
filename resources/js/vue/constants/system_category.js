export const MODULE = 'systemcategory'
export const LISTING_URL = () => {return {path: `${MODULE}/index_data`, method: 'GET'}}
export const INDEX_LIST_URL = () => {return {path: `${MODULE}/index_list`, method: 'GET'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const DELETE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'DELETE'}}
export const UPDATE_STATUS_URL = (id) => {
    
    return { path: `${MODULE}/update-status/${id}`, method: 'POST' };
  };
  export const BULK_ACTION_URL = () => {return {path: `${MODULE}/bulk-action`, method: 'POST'}} 