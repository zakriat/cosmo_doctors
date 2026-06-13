import { InitApp } from '@/helpers/main'
import SystemServiceCategoryFormOffcanvas from './components/SystemServiceCategoryFormOffcanvas.vue'

// // import ServiceFormOffcanvas from './components/ServiceFormOffcanvas.vue'
// // import ServicePackageFormOffcanvas from './components/ServicePackageFormOffcanvas.vue'
// // import GalleryFormOffcanvas from './components/GalleryFormOffcanvas.vue'
// import AssignEmployeeFormOffCanvas from './components/assign/AssignEmployeeFormOffCanvas.vue'
// import AssignServiceProviderFormOffCanvas from './components/assign/AssignServiceProviderFormOffCanvas.vue'
// // import SystemServiceCategoryFormOffcanvas from './components/SystemServiceCategoryFormOffcanvas.vue'

const app = InitApp()

// // app.component('service-form-offcanvas', ServiceFormOffcanvas)
// // app.component('service-package-form-offcanvas', ServicePackageFormOffcanvas)

// // // Assign Staff & Service Provider Offcanvas
// app.component('assign-employee-form-offcanvas', AssignEmployeeFormOffCanvas)
// app.component('assign-service-provider-form-offcanvas', AssignServiceProviderFormOffCanvas)

// // // Gallery Offcanvas
// // app.component('gallery-form-offcanvas', GalleryFormOffcanvas)
app.component('system-service-category-form-offcanvas', SystemServiceCategoryFormOffcanvas)
app.mount('[data-render="app"]')
