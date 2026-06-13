
export const GET_SERVICE_LIST = ({encounter_id}) => {return {path: `services/index_list?encounter_id=${encounter_id}`, method: 'GET'}}

export const GET_SERVICE_DETAILS = ({encounter_id,service_id}) => {return {path: `services/service-details?service_id=${service_id}&encounter_id=${encounter_id}`, method: 'GET'}}

export const SAVE_BILLING_DETAILS = () => {return {path: `billing-record/save-billing-details`, method: 'POST'}}

export const GET_BILLING_DETAILS = ({encounter_id}) => {return {path: `billing-record/edit-billing-detail?encounter_id=${encounter_id}`, method: 'GET'}}

export const CREATE_SERVICE = () => {return {path: `services`, method: 'POST'}}

export const TAX_DATA = (module_type) => {return {path: `tax/index_list?module_type=${module_type}`, method: 'GET'}}

export const SAVE_BILLING_ITEM = () => {return {path: `billing-record/save-billing-items`, method: 'POST'}}

export const GET_BILLING_ITEM = ({billing_id}) => {return {path: `billing-record/billing-item-details?billing_id=${billing_id}`, method: 'GET'}}

export const EDIT_BILLING_ITEM = (id) =>  {return {path: `billing-record/edit-billing-item/${id}`, method: 'GET'}}

export const DELETE_BILLING_ITEM = (id) => {return {path: `billing-record/delete-billing-item/${id}`, method: 'GET'}}