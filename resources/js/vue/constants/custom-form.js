export const MODULE = 'customforms'

export const STORE_URL = () => {return {path: `customforms`, method: 'POST'}}

export const GET_CUSTOM_FORM = ({form_id, appointent_id, type}) => {return {path: `${MODULE}/get-form-data?form_id=${form_id}&appointent_id=${appointent_id}&type=${type}`, method: 'GET'}}

export const STORE_DATA_URL = () => {return {path: `${MODULE}/store-form-data`, method: 'POST'}}
