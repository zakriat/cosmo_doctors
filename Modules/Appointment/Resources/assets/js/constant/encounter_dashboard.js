export const MODULE = 'encounter'
export const ENCOUNTER_DETAIL = (id) => {return {path: `${MODULE}/encounter-detail/${id}`, method: 'GET'}}
export const GET_SEARCH_DATA = ({ type = '' }) => {return { path: `get_search_data?type=${type}`, method: 'GET' }}

export const SAVE_OPTION_DATA = () => {return {path: `${MODULE}/save-select-option`, method: 'POST'}}

export const REMOVE_DATA = ({ id = '',type=' '}) => {return { path: `${MODULE}/remove-histroy-data?id=${id}&type=${type}`, method: 'GET' }}
export const DELETE_PRESCRIPTION_URL = (id) => {return {path: `${MODULE}/delete-prescription/${id}`, method: 'GET'}}

export const SAVE_OTHER_DETAIL_DATA = () => {return {path: `${MODULE}/save-other-details`, method: 'POST'}}
export const DELETE_MEDICAL_REPORT_URL = (id) => {return {path: `${MODULE}/delete-medical-report/${id}`, method: 'GET'}}

export const SEND_PRESCRIPTION_URL = () => {return {path: `encounter/send-prescription`, method: 'POST'}}
export const EXPORT_PRESCRIPTION_URL = () => {return {path: `encounter/export-prescription`, method: 'POST'}}

export const TEMPLATE_LIST  = () => {return {path: `encounter-template/template-list`, method: 'GET'}}
export const TEMPLATE_DETAIL = (id) => {return {path: `encounter-template/get-template-detail/${id}`, method: 'GET'}}

export const SAVE_ENCOUNTER_DETAILS = () => {return {path: `encounter/save-encounter-details`, method: 'POST'}}

export const DOWNLOAD_INVOICE = ({ id = '' }) => {
    return { path: `encounter/download-encounterinvoice?id=${id}`, method: 'GET' };
};

export const DOWNLOAD_PRESCRIPTION = ({ id = '' }) => {
    return { path: `encounter/download-prescription?id=${id}`, method: 'GET' };
};




