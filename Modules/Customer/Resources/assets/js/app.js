import { InitApp } from '@/helpers/main'
import CustomerOffcanvas from './components/CustomerOffcanvas.vue'
import ChangePassword from './components/ChangePassword.vue'
import FormOffcanvas from './components/FormOffcanvas.vue'
import CustomerDetailsOffcanvas from './components/CustomerDetailsOffcanvas.vue'
import CustomFormOffCanvas from './components/CustomFormOffCanvas.vue'
import AddOtherPatientOffcanvas from './components/AddOtherPatientOffcanvas.vue'
import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const app = InitApp()

app.use(VueTelInput)
app.component('customer-offcanvas', CustomerOffcanvas)
app.component('change-password', ChangePassword)
app.component('vitalform-offcanvas', FormOffcanvas)
app.component('customer-details-offcanvas', CustomerDetailsOffcanvas)
app.component('customform-offcanvas', CustomFormOffCanvas)
app.component('add-other-patient-offcanvas', AddOtherPatientOffcanvas)

app.mount('[data-render="app"]');
