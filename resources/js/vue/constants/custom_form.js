export const MODULE = 'customforms'
export const LISTING_URL = ({ page = 1, perPage = 1 } = {}) => {
  return {
    path: `${MODULE}/index_data?page=${page}&perPage=${perPage}`,
    method: 'GET'
  }
}
export const CLINIC_LIST = () => {
  return { path: `clinics/index_list`, method: 'GET' }
}
export const EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/edit`, method: 'GET' }
}
export const STORE_URL = () => {
  return { path: `${MODULE}`, method: 'POST' }
}
export const UPDATE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'PUT' }
}
export const DELETE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'DELETE' }
}
export const COUNTRY_URL = () => {
  return { path: `country/index_list`, method: 'GET' }
}
