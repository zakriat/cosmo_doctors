export const MODULE = 'appointment'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const CUSTOMER_LIST = () => {return {path: `users/user-list?role=user`, method: 'GET'}}


export const PROBLEMS_STORE_URL = () => {return {path: `problems`, method: 'POST'}}
export const PROBLEMS_EDIT_URL = (id) => {return {path: `problems/${id}/edit`, method: 'GET'}}
export const PROBLEMS_UPDATE_URL = (id) => {return {path: `problems/${id}`, method: 'POST'}}

export const OBSERVATION_STORE_URL = () => {return {path: `observation`, method: 'POST'}}
export const OBSERVATION_EDIT_URL = (id) => {return {path: `observation/${id}/edit`, method: 'GET'}}
export const OBSERVATION_UPDATE_URL = (id) => {return {path: `observation/${id}`, method: 'POST'}}