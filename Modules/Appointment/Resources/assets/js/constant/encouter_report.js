
export const STORE_MEDICAL_REPORT = () => {return {path: `encounter/save-medical-report`, method: 'POST'}}
export const EDIT_MEDICAL_REPORT = (id) => {return {path: `encounter/edit-medical-report/${id}`, method: 'GET'}}
export const UPDATE_MEDICAL_REPORT = (id) => {return {path: `encounter/update-medical-report/${id}`, method: 'POST'}}

export const GET_MEDICAL_REPORT_LIST = ({ id = '' }) => {return { path: `encounter/get_report_data?encounter_id=${id}`, method: 'GET' }}

export const SEND_MEDICAL_REPORT = () => {return {path: `encounter/send-medical-report`, method: 'POST'}}

