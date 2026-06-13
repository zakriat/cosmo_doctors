import { createRouter, createWebHashHistory } from 'vue-router'
import SettingLayout from '@/Setting/SettingLayout.vue'
import GeneralPage from '@/Setting/SectionPages/GeneralPage.vue'
import CustomCodePage from '@/Setting/SectionPages/CustomCodePage.vue'
import CustomizationPage from '@/Setting/SectionPages/CustomizationPage.vue'
import MailPage from '@/Setting/SectionPages/MailPage.vue'
import NotificationSetting from '@/Setting/SectionPages/NotificationSetting.vue'
import IntegrationPage from '@/Setting/SectionPages/IntegrationPage.vue'
import CustomFieldsPage from '@/Setting/SectionPages/CustomFieldsPage.vue'
import CurrencySettingPage from '@/Setting/SectionPages/CurrencySettingPage.vue'
import CommissionPage from '@/Setting/SectionPages/CommissionPage.vue'
import PaymentMethod from '@/Setting/SectionPages/PaymentMethod.vue'
import LanguagePage from '@/Setting/SectionPages/LanguagePage.vue'
import OnlineServicePage from '@/Setting/SectionPages/OnlineServicePage.vue'
import Holiday from '@/Setting/SectionPages/Holiday.vue'
import DoctorHoliday from '@/Setting/SectionPages/DoctorHoliday.vue'
import MiscSettingPage from '@/Setting/SectionPages/MiscSettingPage.vue'
import QuickBooking from '@/Setting/SectionPages/QuickBooking.vue'
import NotFound from '@/Setting/Components/NotFound.vue'
import UnauthRole from '@/Setting/Components/UnauthRole.vue'
import InvoiceSetting from '@/Setting/SectionPages/InvoiceSetting.vue'
import BodyChartSetting from '../SectionPages/BodyChartSetting.vue'
import OtherSetting from '@/Setting/SectionPages/OtherSetting.vue'
import ModuleSettingPage from '@/Setting/SectionPages/ModuleSetting.vue'
import CustomForm from '@/Setting/SectionPages/CustomForm.vue'
import CancellationCharge from '@/Setting/SectionPages/CancellationCharge.vue'
const routes = [
  {
    path: '/404',
    component: NotFound,
    name: 'notfound'
  },
  {
    path: '/403',
    component: UnauthRole,
    name: 'auth.role'
  },
  {
    path: '/',
    component: SettingLayout,
    children: [
      {
        path: '',
        name: 'Settings.home',
        meta: {permission: 'setting_bussiness'},
        component: GeneralPage
      },
      {
        path: 'module-setting',
        name: 'Settings.module',
        meta: {permission: 'setting_module'},
        component: ModuleSettingPage
      },
      {
        path: 'misc-setting',
        name: 'Settings.misc',
        meta: {permission: 'setting_misc'},
        component: MiscSettingPage
      },
      {
        path: 'quick-booking',
        name: 'Settings.quick-booking',
        meta: {permission: 'setting_quick_booking'},
        component: QuickBooking
      },
      {
        path: 'custom-code',
        name: 'Settings.custom-code',
        meta: {permission: 'setting_custom_code'},
        component: CustomCodePage
      },
      {
        path: 'other-settings',
        name: 'Settings.other-settings',
        meta: {permission: 'setting_other_setting'},
        component: OtherSetting
      },
      {
        path: 'customization',
        name: 'Settings.customization',
        meta: {permission: 'setting_customization'},
        component: CustomizationPage
      },
      {
        path: 'mail',
        name: 'Settings.mail',
        meta: {permission: 'setting_mail'},
        component: MailPage
      },
      {
        path: 'notificationsetting',
        name: 'Settings.notificationsetting',
        meta: {permission: 'setting_notification'},
        component: NotificationSetting
      },
      {
        path: 'integration',
        name: 'Settings.integration',
        meta: {permission: 'setting_intigrations'},
        component: IntegrationPage
      },
      {
        path: 'custom-fields',
        name: 'Settings.custom-fields',
        meta: {permission: 'setting_custom_fields'},
        component: CustomFieldsPage
      },
      {
        path: 'currency-settings',
        name: 'Settings.currency-settings',
        meta: {permission: 'setting_currency'},
        component: CurrencySettingPage
      },
      {
        path: 'commission',
        name: 'Settings.commission',
        meta: {permission: 'setting_commission'},
        component: CommissionPage
      },
      {
        path: 'payment-method',
        name: 'Settings.payment-method',
        meta: {permission: 'setting_payment_method'},
        component: PaymentMethod
      },
      {
        path: 'language-settings',
        name: 'Settings.language-settings',
        meta: {permission: 'setting_language'},
        component: LanguagePage
      },
      {
        path: 'telemedservice-settings',
        name: 'Settings.telemedservice-settings',
        meta: {permission: 'setting_telemed_service'},
        component: OnlineServicePage
      },

      {
        path: 'holiday',
        name: 'Settings.holiday',
        meta: {permission: 'setting_holiday'},
        component: Holiday
      },

      {
        path: 'customform',
        name: 'Settings.customform',
        meta: {permission: 'setting_holiday'},
        component: CustomForm
      },

      {
        path: 'doctorholiday',
        name: 'Settings.doctorholiday',
        meta: {permission: 'setting_doctor_holiday'},
        component: DoctorHoliday
      },

      {
        path: 'invoice-setting',
        name: 'Settings.invoice-setting',
        meta: {permission: 'setting_invoice'},
        component: InvoiceSetting
      },
       {
        path: 'bodychart-setting',
        name: 'Settings.bodychart-setting',
        meta: {permission: 'setting_body_chart'},
        component: BodyChartSetting
      },
      {
        path: 'cancellation-charge',
        name: 'Settings.cancellation-charge',
        meta: {permission: 'setting_cancellation_charge'},
        component: CancellationCharge
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

router.beforeEach((to, from, next) => {
  if(to.meta.permission) {
    if (window.auth_role == 'vendor') {
      if (to.meta.permission == 'setting_bussiness' && to.name === 'Settings.home') {
        return next({ name: 'Settings.quick-booking' })
      }
    }else if(window.auth_role == 'doctor'){
      if (to.meta.permission == 'setting_bussiness' && to.name === 'Settings.home') {
        return next({ name: 'Settings.doctorholiday' })
      }
    }
    console.log(window)
    if(window.auth_permissions.includes(to.meta.permission)) {
      next()
    } else {
      return next({name: 'auth.role'})
    }
  } else {
    next()
  }
})
