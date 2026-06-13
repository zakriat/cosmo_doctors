<template>
  <div class="col-md-4 col-lg-3" id="setting-sidebar-menu">
    <div id="setting-sidebar" class="setting-sidebar-inner">
      <div class="card">
        <div class="card-body">
          <div class="list-group list-group-flush" id="setting-list">
            <div :class="hasPermissions('setting_bussiness') ? 'active-menu' : ''" v-if="hasPermissions('setting_bussiness')" class="mb-3" id="Settings.home" @click="toggle()">
              <router-link :to="{ name: 'Settings.home' }" class="btn btn-border fw-normal"><i class="ph ph-presentation-chart"></i>{{ $t('setting_sidebar.lbl_General') }}</router-link>
            </div>
            <div :class="hasPermissions('setting_module') ? 'active-menu' : ''" v-if="hasPermissions('setting_module')" class="mb-3" id="Settings.module" @click="toggle()">
              <router-link :to="{ name: 'Settings.module' }" class="btn btn-border fw-normal"><i class="ph ph-sliders-horizontal"></i>{{ $t('setting_sidebar.lbl_module_setting') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_misc') ? 'active-menu' : ''" v-if="hasPermissions('setting_misc')"  id="Settings.misc" @click="toggle()">
              <router-link :to="{ name: 'Settings.misc' }" class="btn btn-border fw-normal"><i class="ph ph-cpu"></i>{{ $t('setting_sidebar.lbl_misc_setting') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_quick_booking') ? 'active-menu' : ''" v-if="hasPermissions('setting_quick_booking')" id="Settings.quick-booking" @click="toggle()">
              <router-link :to="{ name: 'Settings.quick-booking' }" class="btn btn-border fw-normal"><i class="ph ph-calendar-check"></i>{{ $t('setting_sidebar.lbl_quick_booking') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_invoice') ? 'active-menu' : ''" v-if="hasPermissions('setting_invoice')" id="Settings.invoice-setting"  @click="toggle()">
              <router-link :to="{ name: 'Settings.invoice-setting' }" class="btn btn-border fw-normal"><i class="ph ph-invoice" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_inv_setting') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_custom_code') ? 'active-menu' : ''" v-if="hasPermissions('setting_custom_code')"  id="Settings.custom-code"  @click="toggle()">
              <router-link :to="{ name: 'Settings.custom-code' }" class="btn btn-border fw-normal"><i class="ph ph-code-block"></i>{{ $t('setting_sidebar.lbl_custom_code') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_other_setting') ? 'active-menu' : ''" v-if="hasPermissions('setting_other_setting')" id="Settings.other-settings" @click="toggle()">
              <router-link :to="{name: 'Settings.other-settings'}" class="btn btn-border fw-normal"><i class="ph ph-device-mobile"></i>{{ $t('setting_sidebar.lbl_app_configuration') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_customization') ? 'active-menu' : ''" v-if="hasPermissions('setting_customization')" id="Settings.customization"  @click="toggle()">
              <router-link :to="{ name: 'Settings.customization' }" class="btn btn-border fw-normal"><i class="ph ph-qr-code"></i>{{ $t('setting_sidebar.lbl_customization') }}</router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_mail') ? 'active-menu' : ''" v-if="hasPermissions('setting_mail')" id="Settings.mail"  @click="toggle()">
              <router-link :to="{ name: 'Settings.mail' }" class="btn btn-border fw-normal"><i class="ph ph-envelope-simple"></i>{{ $t('setting_sidebar.lbl_mail') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_notification') ? 'active-menu' : ''" v-if="hasPermissions('setting_notification')" id="Settings.integration"  @click="toggle()">
              <router-link :to="{ name: 'Settings.notificationsetting' }" class="btn btn-border fw-normal"><i class="ph ph-bell-ringing"></i>{{ $t('setting_sidebar.lbl_notification') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_intigrations') ? 'active-menu' : ''" v-if="hasPermissions('setting_intigrations')" id="Settings.notificationsetting"  @click="toggle()">
              <router-link :to="{ name: 'Settings.integration' }" class="btn btn-border fw-normal"><i class="ph ph-vector-three"></i>{{ $t('setting_sidebar.lbl_integration') }}</router-link>
            </div>
            <!-- <div class="mb-3" :class="hasPermissions('setting_custom_fields') ? 'active-menu' : ''" v-if="hasPermissions('setting_custom_fields')"  id="Settings.custom-fields"  @click="toggle()">
              <router-link :to="{ name: 'Settings.custom-fields' }" class="btn btn-border fw-normal"><i class="fa-solid fa-bars"></i>{{ $t('setting_sidebar.lbl_custom_field') }} </router-link>
            </div> -->
            <div class="mb-3" :class="hasPermissions('setting_currency') ? 'active-menu' : ''" v-if="hasPermissions('setting_currency')"  id="Settings.currency-settings"  @click="toggle()">
              <router-link :to="{ name: 'Settings.currency-settings' }" class="btn btn-border fw-normal"><i class="ph ph-currency-circle-dollar"></i>{{ $t('setting_sidebar.lbl_currency_setting') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_commission') ? 'active-menu' : ''" v-if="hasPermissions('setting_commission')"  id="Settings.commission"  @click="toggle()">
              <router-link :to="{ name: 'Settings.commission' }" class="btn btn-border fw-normal"><i class="ph ph-seal-percent"></i>{{ $t('setting_sidebar.lbl_commission') }}</router-link>
            </div>

            <div class="mb-3" :class="hasPermissions('setting_payment_method') ? 'active-menu' : ''" v-if="hasPermissions('setting_payment_method')"  id="Settings.payment-method"  @click="toggle()">
              <router-link :to="{ name: 'Settings.payment-method' }" class="btn btn-border fw-normal"><i class="ph ph-money-wavy"></i>{{ $t('setting_sidebar.lbl_payment') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_language') ? 'active-menu' : ''" v-if="hasPermissions('setting_language')" id="Settings.language-settings"  @click="toggle()">
              <router-link :to="{ name: 'Settings.language-settings' }" class="btn btn-border fw-normal"><i class="ph ph-globe-hemisphere-east" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_language') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_telemed_service') ? 'active-menu' : ''" v-if="hasPermissions('setting_telemed_service') && telemedSettingEnabled==1" id="Settings.telemedservice-settings"  @click="toggle()">
              <router-link :to="{ name: 'Settings.telemedservice-settings' }" class="btn btn-border fw-normal"><i class="ph ph-monitor-play" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_telemed_service') }} </router-link>
            </div>

            <div class="mb-3" :class="hasPermissions('setting_holiday') ? 'active-menu' : ''" v-if="hasPermissions('setting_holiday')" id="Settings.holiday"  @click="toggle()">
              <router-link :to="{ name: 'Settings.holiday' }" class="btn btn-border fw-normal"><i class="ph ph-hospital" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_holiday') }} </router-link>
            </div>

            <div class="mb-3" :class="hasPermissions('setting_doctor_holiday') ? 'active-menu' : ''" v-if="hasPermissions('setting_doctor_holiday')" id="Settings.doctorholiday"  @click="toggle()">
              <router-link :to="{ name: 'Settings.doctorholiday' }" class="btn btn-border fw-normal"><i class="ph ph-airplane-takeoff" aria-hidden="true"></i>
                  {{ role() == 'doctor' ? $t('setting_sidebar.holiday') : $t('setting_sidebar.lbl_doctorholiday') }}
              </router-link>
            </div>


            <div class="mb-3" :class="hasPermissions('setting_body_chart') ? 'active-menu' : ''" v-if="hasPermissions('setting_body_chart') && enable_bodychart==1" id="Settings.bodychart-setting"  @click="toggle()">
              <router-link :to="{ name: 'Settings.bodychart-setting' }" class="btn btn-border fw-normal"><i class="ph ph-person" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_bodychart') }} </router-link>
            </div>
            <div class="mb-3" :class="hasPermissions('setting_cancellation_charge') ? 'active-menu' : ''" v-if="hasPermissions('setting_cancellation_charge')" id="Settings.cancellation-charge"  @click="toggle()">
              <router-link :to="{ name: 'Settings.cancellation-charge' }" class="btn btn-border fw-normal"><i class="ph ph-person" aria-hidden="true"></i>{{ $t('setting_sidebar.lbl_cancellation_charge') }} </router-link>
            </div>
            <!-- <div class="mb-3" id="Settings.system-category"  @click="toggle()">
              <router-link :to="{ name: 'Settings.system-category' }" class="btn btn-border fw-normal"><i class="fa fa-dollar fa-lg mr-2"></i>{{ $t('setting_sidebar.lbl_system_category') }} </router-link>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { useRouter } from 'vue-router'
const toggle = () => {
  const formOffcanvas = document.getElementById('offcanvas')
  formOffcanvas.classList.add('show')
}

const hasPermissions = (name) => {
  return window.auth_permissions.includes(name)
}
const role = () => {
    return window.auth_role[0];
}

const enable_bodychart=window.bodyChartEnabled;
const telemedSettingEnabled=window.telemedSettingEnabled;
</script>
<style scoped>
.btn-border {
  text-align: left;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
</style>