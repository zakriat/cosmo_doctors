export const MODULE = 'customforms'
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');

export const STORE_URL = () => {
  return { path: `customforms`, method: 'POST' }
}

export const GET_CUSTOM_FORM = ({ form_id, appointment_id, type }) => {
  return { path: `${baseUrl}/app/${MODULE}/get-form-data?form_id=${form_id}&appointment_id=${appointment_id}&type=${type}`, method: 'GET' }
}

export const STORE_DATA_URL = () => {
  return { path: `${baseUrl}/app/${MODULE}/store-form-data`, method: 'POST' }
}
