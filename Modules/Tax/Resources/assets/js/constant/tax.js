export const MODULE = 'tax'

export const STORE_URL = () => {
  return { path: `${MODULE}`, method: 'POST' }
}
export const EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/edit`, method: 'GET' }
}
export const UPDATE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'PUT' }
}
export const SERVICE_LIST = () => {
  return { path: `services/index_list`, method: 'GET' }
}
