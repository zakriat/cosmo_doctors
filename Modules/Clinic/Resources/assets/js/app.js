import { InitApp } from '@/helpers/main'

// import ClinicFormOffcanvas from './component/ClinicFormOffcanvas.vue'
import ClinicGalleryOffcanvas from './component/ClinicGalleryOffcanvas.vue'
// import ClinicServiceOffcanvas from './component/ClinicServiceOffcanvas.vue'
// import ClinicCategoryOffcanvas from './component/ClinicCategoryOffcanvas.vue'
// import ClinicAppointmentOffcanvas from './component/ClinicAppointmentOffcanvas.vue'
// import DoctorOffcanvas from './component/DoctorOffcanvas.vue'
// import doctordetail from './component/doctordetail.vue'
import AssignDoctorFormOffCanvas from './component/assign/AssignDoctorFormOffcanvas.vue'
// import DoctorSessionOffcanvas from './component/DoctorSessionOffcanvas.vue'
// import ChangePassword from './component/ChangePassword.vue'
// import ClinicSessionOffcanvas from './component/ClinicSessionOffcanvas.vue'
import DoctorSessionClinicOffcanvas from './component/DoctorSessionClinicOffcanvas.vue'
import ReceptionistOffcanvas from './component/ReceptionistOffcanvas.vue'
import ReceptionistChangePassword from './component/ReceptionistChangePassword.vue'
import NurseOffcanvas from './component/NurseOffcanvas.vue'
import NurseChangePassword from './component/NurseChangePassword.vue'
// import ClinicListFormOffcanvas from './component/ClinicListFormOffcanvas.vue'
// import ClinicSessionListFormOffcanvas from './component/ClinicSessionListFormOffcanvas.vue'
// import ClinicDetailOffcanvas from './component/ClinicDetailOffcanvas.vue'
// import SystemServiceOffcanvas from './component/SystemServiceOffcanvas.vue'
import CustomFormOffCanvas from './component/CustomFormOffCanvas.vue'
import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'




const app = InitApp()

app.use(VueTelInput)
// app.component('clinic-form-offcanvas',ClinicFormOffcanvas)
app.component('clinic-gallery-offcanvas', ClinicGalleryOffcanvas)
// app.component('clinic-service-offcanvas', ClinicServiceOffcanvas)
// app.component('clinic-category-offcanvas',ClinicCategoryOffcanvas)
// app.component('clinic-appointment-offcanvas',ClinicAppointmentOffcanvas)
// app.component('doctor-offcanvas',DoctorOffcanvas)
// app.component('doctor-details-offcanvas',doctordetail)
app.component('assign-doctor-form-offcanvas', AssignDoctorFormOffCanvas)
// app.component('doctor-session-offcanvas', DoctorSessionOffcanvas)
// app.component('change-password', ChangePassword)
// app.component('clinic-session-offcanvas',ClinicSessionOffcanvas)
app.component('doctor-session-clinic-offcanvas', DoctorSessionClinicOffcanvas)
app.component('receptionist-offcanvas', ReceptionistOffcanvas)
app.component('receptionist-change-password', ReceptionistChangePassword)
app.component('nurse-offcanvas', NurseOffcanvas)
app.component('nurse-change-password', NurseChangePassword)
// app.component('clinic-list-form-offcanvas',ClinicListFormOffcanvas)
// app.component('clinic-session-form-offcanvas',ClinicSessionListFormOffcanvas)
// app.component('clinic-details-offcanvas',ClinicDetailOffcanvas)
// app.component('system-service-offcanvas',SystemServiceOffcanvas)
app.component('customform-offcanvas', CustomFormOffCanvas)
app.mount('[data-render="app"]');

