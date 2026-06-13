import { InitApp } from '../helpers/main'

import AssignServiceProviderEmployeeOffcanvas from './components/service-provider/AssignServiceProviderEmployeeOffcanvas.vue'
import ServiceProviderFormOffcanvas from './components/service-provider/ServiceProviderFormOffcanvas.vue'
import ModuleOffcanvas from './components/module/ModuleOffcanvas.vue'
import ManageRoleForm from './components/role_permission/ManageRoleForm.vue'

import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'


const app = InitApp()

app.use(VueTelInput)
app.component('assign-service-provider-employee-offcanvas', AssignServiceProviderEmployeeOffcanvas)
app.component('service-provider-form-offcanvas', ServiceProviderFormOffcanvas)
app.component('module-form-offcanvas', ModuleOffcanvas)
app.component('manage-role-form', ManageRoleForm)

app.mount('[data-render="app"]')
