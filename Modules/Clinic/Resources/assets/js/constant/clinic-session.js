export const MODULE = 'clinic-session'
export const LISTING_URL = ({clinic_id}) => {return {path: `clinic-session/index_list?clinic_id=${clinic_id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}

export const CLINIC_DATA = (id) => {return {path: `clinics/${id}/edit`, method: 'GET'}}