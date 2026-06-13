import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import ChangePassword from './components/ChangePassword.vue'
import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'
const app = InitApp()

app.component('form-offcanvas', FormOffcanvas)
app.component('change-password', ChangePassword)
app.mount('[data-render="app"]');
