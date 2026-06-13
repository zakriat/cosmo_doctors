export const MODULE = 'clinichours'
export const LISTING_URL = ({service_provider_id}) => {return {path: `clinichours/index_list?service_provider_id=${service_provider_id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
