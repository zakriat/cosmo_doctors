import { createRouter, createWebHashHistory } from 'vue-router'
import ProfileLayout from '@/Profile/ProfileLayout.vue'
import InformationPage from '@/Profile/SectionPages/InformationPage.vue'
import ChangePasswordPage from '@/Profile/SectionPages/ChangePasswordPage.vue'
import ServiceProviderSettingPage from '@/Profile/SectionPages/ServiceProviderSettingPage.vue'
import GoogleAuth from '../SectionPages/GoogleAuth.vue'

const routes = [
  {
    path: '/',
    component: ProfileLayout,
    children: [
      {
        path: '',
        name: 'profile.info',
        component: InformationPage
      },
      {
        path: 'change-password',
        name: 'profile.change.password',
        component: ChangePasswordPage
      },
      {
        path: 'google-auth',
        name: 'google.auth',
        component: GoogleAuth
      },
      {
        path: 'service-provider-setting',
        name: 'profile.serviceProviderSetting',
        component: ServiceProviderSettingPage
      }
    ]
  }
]

export const router = createRouter({
  linkActiveClass: '',
  linkExactActiveClass: 'active',
  history: createWebHashHistory(),
  routes
})
