export const STORE_URL = () => {return {path: `settings`, method: 'POST'}}
export const STORE_HOLIDAY = () => {return {path: `holidays`, method: 'POST'}}
export const STORE_DOCTORHOLIDAY = () => {return {path: `doctorholidays`, method: 'POST'}}


export const GET_URL = (data) => {return {path: `settings-data?fields=${data}`, method: 'GET'}}

export const GET_URL1 = () => {return {path: `settings-data`, method: 'GET'}}

export const CACHE_CLEAR = () => {return {path: `clear-cache`, method: 'GET'}}

// Mini Logo Routes
// export const GET_MINI_LOGOS = () => {return {path: `mini-logos`, method: 'GET'}}
// export const UPDATE_MINI_LOGOS = () => {return {path: `mini-logos`, method: 'POST'}}
// export const DELETE_MINI_LOGO = (type) => {return {path: `mini-logos/${type}`, method: 'DELETE'}}

// Dedicated Logo API Routes
export const GET_MINI_LOGO = () => {return {path: `logo/mini`, method: 'GET'}}
export const UPDATE_MINI_LOGO = () => {return {path: `logo/mini`, method: 'POST'}}
export const DELETE_MINI_LOGO_API = () => {return {path: `logo/mini`, method: 'DELETE'}}

export const GET_NOTIFICATION_URL = () => {return {path: `notifications-templates/index_list`, method: 'GET'}}

export const CHANNEL_UPDATE_URL = () => {return {path: `notifications-templates/channels-update`, method: 'POST'}}

export const TIME_ZONE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}

export const DATE_FORMATE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}

export const TIME_FORMATE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}

export const VERIFIED_EMAIL = (mailObject) => { return { path: `verify-email`, method: 'POST', request: mailObject  };};

export const CURRENCY_LIST = () => {return {path: `currencies/index_list`, method: 'GET'}}

// Menu Routes
export const MENU_LIST = ({type}) => {return {path: `menu?type=${type}`, method: 'GET'}}
export const MENU_STORE = () => {return {path: `menu`, method: 'POST'}}
export const MENU_UPDATE = (id) => {return {path: `menu/${id}`, method: 'PUT'}}
export const MENU_EDIT = (id) => {return {path: `menu/${id}/edit`, method: 'GET'}}

// menu list update
export const MENU_LIST_UPDATE = ({type}) => {return {path: `menu-sequance?type=${type}`, method: 'POST'}}

export const APPOINTMENT_DETAILS = () => {return {path: `settings-data`, method: 'GET'}}
export const TEMPLATE_IMAGE_LIST =  () => {return {path: `bodychart/bodychart_image_list`, method: 'GET'}}
export const GOOGLE_ID = () => {return {path: `/auth/google`, method: 'POST'}}
export const GOOGLE_TOKEN = () => {return {path: `callback`, method: 'GET'}}
export const GET_JSON = () => {return {path: `download-json`, method: 'GET'}}


export const COUNTRY_URL = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_URL = (id) => {return {path: `state/index_list?country_id=${id}`, method: 'GET'}}
export const CITY_URL = (id) => {return {path: `city/index_list?state_id=${id}`, method: 'GET'}}
export const CLINIC_LIST = () => {return {path: `clinics/index_list`, method: 'GET'}}
export const DOCTOR_LIST = () => {return {path: `doctor/index_list`, method: 'GET'}}
export const DOCTORHOLIDAY_LIST = ({doctor_id}) => {return {path: `get_doctorpickers?doctor_id=${doctor_id}`, method: 'GET'}}
export const HOLIDAY_LIST = ({clinic_id}) => {return {path: `get_pickers?clinic_id=${clinic_id}`, method: 'GET'}}
export const DELETE_HOLIDAY = ({id}) => {return {path: `delete_pickers?id=${id}`, method: 'GET'}}
export const DELETE_DOCTORHOLIDAY = ({id}) => {return {path: `delete_doctorpickers?id=${id}`, method: 'GET'}}

