export const MODULE = 'services'
export const EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/edit`, method: 'GET' }
}
export const STORE_URL = () => {
  return { path: `${MODULE}`, method: 'POST' }
}
export const UPDATE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'POST' }
}
export const CATEGORY_LIST = ({ type = 'select', id = null }) => {
  return { path: `categories/index_list?type=${type}&parent_id=${id}`, method: 'GET' }
}
export const EMPLOYEE_LIST = ({ service_provider_id }) => {
  return { path: ` employees/employee_list?service_provider_id=${service_provider_id}`, method: 'GET' }
}
export const SERVICE_PROVIDER_LIST = () => {
  return { path: `service-providers/index_list`, method: 'GET' }
}
// Employee Assign
export const GET_EMPLOYEE_ASSIGN_URL = (id) => {
  return { path: `${MODULE}/assign-employee/${id}`, method: 'GET' }
}
export const POST_EMPLOYEE_ASSIGN_URL = (id) => {
  return { path: `${MODULE}/assign-employee/${id}`, method: 'POST' }
}

// Service Provider Assign
export const GET_SERVICE_PROVIDER_ASSIGN_URL = (id) => {
  return { path: `${MODULE}/assign-service-provider/${id}`, method: 'GET' }
}
export const POST_SERVICE_PROVIDER_ASSIGN_URL = (id) => {
  return { path: `${MODULE}/assign-service-provider/${id}`, method: 'POST' }
}

// Gallery Assign
export const GET_GALLERY_URL = (id) => {
  return { path: `${MODULE}/gallery-images/${id}`, method: 'GET' }
}
export const POST_GALLERY_URL = (id) => {
  return { path: `${MODULE}/gallery-images/${id}`, method: 'POST' }
}

