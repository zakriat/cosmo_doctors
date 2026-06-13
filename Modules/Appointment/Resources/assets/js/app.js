import { InitApp } from '@/helpers/main'

// import ClinicAppointmentOffcanvas from './components/ClinicAppointmentOffcanvas.vue'
// import AppointmentPatientRecords from './components/AppointmentPatientRecords.vue'
// import appintmenteyeicon from './components/appintmenteyeicon.vue'
import BodyChartOffcanvas from './components/BodyChartOffcanvas.vue'
// import FormOffcanvas from './components/FormOffcanvas.vue'
// import PatientListDetails from './components/PatientListDetails.vue'
import PatientEncounter from './components/PatientEncounter/Create.vue'
// import EncounterDashboard from './components/PatientEncounter/EncounterDashboard.vue'
import EncounterTemplate from './components/PatientEncounter/EncounterTemplate.vue'
import GlobalAppointmentOffcanvas from './components/GlobalAppointmentOffcanvas.vue'
// import EncounterTemplateDashboard from './components/EncounterTemplate/EncounterTemplateDashboard.vue'
import BillingRecordOffcanvas from './components/PatientEncounter/BillingRecordOffcanvas.vue'
import ProblemsOffcanvas from './components/ProblemsOffcanvas.vue'
import ObservationOffcanvas from './components/ObservationOffcanvas.vue'
import AppointmentCustomForm from './components/AppointmentCustomForm.vue'
import CustomForm from './components/CustomForm.vue'
import VueCustomTabs from './components/VueCustomTabs.vue'

const app = InitApp()

// app.component('clinic-appointment-offcanvas', ClinicAppointmentOffcanvas)
// app.component('appointment_patient_records', AppointmentPatientRecords)
// app.component('appointment-offcanvas', appintmenteyeicon)
app.component('body-chart-offcanvas', BodyChartOffcanvas)
// app.component('vitalform-offcanvas', FormOffcanvas)
// app.component('patient-list-details-offcanvas', PatientListDetails)
app.component('patient-encounter-offcanvas', PatientEncounter)
// app.component('patient-encounter-dashboard', EncounterDashboard)
app.component('encounter-template', EncounterTemplate)
app.component('global-appointment-offcanvas', GlobalAppointmentOffcanvas)
// app.component('encounter-template-dashboard', EncounterTemplateDashboard)
app.component('billing-record-offcanvas', BillingRecordOffcanvas)
app.component('problems-offcanvas', ProblemsOffcanvas)
app.component('observation-offcanvas', ObservationOffcanvas)
app.component('appointment-customform', AppointmentCustomForm)
app.component('customform', CustomForm)
app.component('vue-custom-tabs', VueCustomTabs)

const mountPoint = document.querySelector('[data-render="app"]')

if (mountPoint && !mountPoint.__vue_app__) {
  app.mount(mountPoint)
} else {
  const existingApp = mountPoint.__vue_app__
  if (existingApp) {
    existingApp.unmount()
  }
  app.mount(mountPoint)
}
