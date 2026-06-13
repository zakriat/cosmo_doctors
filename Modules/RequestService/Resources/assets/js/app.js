import { InitApp } from '@/helpers/main'

import RequestServiceOffcanvas from './components/RequestServiceOffcanvas.vue'

const app = InitApp()

app.component('request-service-offcanvas', RequestServiceOffcanvas)

app.mount('[data-render="app"]');
