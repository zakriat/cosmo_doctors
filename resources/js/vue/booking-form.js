
import { InitApp } from '../helpers/main'
import GlobalAppointmentOffcanvas from '../../../Modules/Appointment/Resources/assets/js/components/GlobalAppointmentOffcanvas.vue'

const app = InitApp()

app.component('global-appointment-offcanvas', GlobalAppointmentOffcanvas)

if(document.querySelector('[data-render="global-booking"]')) {
  app.mount('[data-render="global-booking"]');
}

$(document).on('click', '#appointment-button', function() {
 
  const bookingForm = document.getElementById('appointment-form-offcanvas')
  const bsInstance = bootstrap.Offcanvas.getOrCreateInstance(bookingForm)
  bsInstance.hide()
  setTimeout(function() {
    bsInstance.show()
  }, 100)
})
