export const GET_SEARCH_DATA = ({ type = '' }) => {return { path: `get_search_data?type=${type}`, method: 'GET' }}
export const PRESCRIPTION_STORE = () => {return {path: `encounter-template/save-prescription`, method: 'POST'}}
export const EDIT_PRESCRIPTION_URL = (id) => {return {path: `encounter-template/edit-prescription/${id}`, method: 'GET'}}
export const PRESCRIPTION_UPDATE = (id) => {return {path: `encounter-template/update-prescription/${id}`, method: 'POST'}}