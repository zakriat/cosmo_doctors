<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_integration')" icon="fa-solid fa-sliders"></CardTitle>
    </div>
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_google_login">{{ $t('setting_integration_page.lbl_google_login') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_google_login" :checked="is_google_login == 1 ? true : false" name="is_google_login" id="category-is_google_login" type="checkbox" v-model="is_google_login" />
        </div>
      </div>
    </div>

    
    <!-- <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_custom_webhook_notification">{{ $t('setting_integration_page.lbl_webhook') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_custom_webhook_notification" :checked="is_custom_webhook_notification == 1 ? true : false" name="is_custom_webhook_notification" id="category-is_custom_webhook_notification" type="checkbox" v-model="is_custom_webhook_notification" />
        </div>
      </div>
    </div>
    <div v-if="is_custom_webhook_notification == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_custom_webhook_content_key')" placeholder="" v-model="custom_webhook_content_key" :error-message="errors['custom_webhook_content_key']" :error-messages="errorMessages['custom_webhook_content_key']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_custom_webhook_url')" placeholder="" v-model="custom_webhook_url" :error-message="errors['custom_webhook_url']" :error-messages="errorMessages['custom_webhook_url']"></InputField>
        </div>
      </div>
    </div> -->
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_map_key">{{ $t('setting_integration_page.lbl_google') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_map_key" :checked="is_map_key == 1 ? true : false" name="is_map_key" id="category-is_map_key" type="checkbox" v-model="is_map_key" />
        </div>
      </div>
    </div>
    <div v-if="is_map_key == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_google_key')" placeholder="" v-model="google_maps_key" :error-message="errors['google_maps_key']" :error-messages="errorMessages['google_maps_key']"></InputField>
        </div>
      </div>
    </div>
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_application_link">{{ $t('setting_integration_page.lbl_application') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_application_link" :checked="is_application_link == 1 ? true : false" name="is_application_link" id="category-is_application_link" type="checkbox" v-model="is_application_link" />
        </div>
      </div>
    </div>
    <div v-if="is_application_link == 1">
      <div class="row">
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.lbl_playstore') }}</label>
          <InputField class="col-md-12" type="text" :is-required="true" placeholder="" v-model="patient_app_play_store" :error-message="errors['patient_app_play_store']" :error-messages="errorMessages['patient_app_play_store']"></InputField>
        </div>
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.lbl_appstore') }}</label>
          <InputField class="col-md-12" type="text" :is-required="true" placeholder="" v-model="patient_app_app_store" :error-message="errors['patient_app_app_store']" :error-messages="errorMessages['patient_app_app_store']"></InputField>
        </div>
      </div>

      <div class="row">
          <div class="col-md-6">
            <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.lbl_clinicadmin_playstore') }}</label>
            <InputField class="col-md-12" type="text" :is-required="true" placeholder="" v-model="clinicadmin_app_play_store" :error-message="errors['clinicadmin_app_play_store']" :error-messages="errorMessages['clinicadmin_app_play_store']"></InputField>
          </div>
          <div class="col-md-6">
            <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.lbl_clinicadmin_appstore') }}</label>
            <InputField class="col-md-12" type="text" :is-required="true" placeholder="" v-model="clinicadmin_app_app_store" :error-message="errors['clinicadmin_app_app_store']" :error-messages="errorMessages['clinicadmin_app_app_store']"></InputField>
          </div>
        </div>
      </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-isForceUpdateforAndroid">{{ $t('setting_integration_page.lbl_isforceupdate_android') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="isForceUpdateforAndroid" :checked="isForceUpdateforAndroid == 1 ? true : false" name="isForceUpdateforAndroid" id="category-isForceUpdateforAndroid" type="checkbox" v-model="isForceUpdateforAndroid" />
        </div>
      </div>
    </div>
    <div v-if="isForceUpdateforAndroid == 1" class="bg-body mt-3 px-5 py-4 mb-5">
      <div class="row">
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.patient_android_min_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="patient_android_min_force_update_code" :error-message="errors['patient_android_min_force_update_code']" :error-messages="errorMessages['patient_android_min_force_update_code']"></InputField>
        </div>
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.patient_android_latest_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="patient_android_latest_version_update_code" :error-message="errors['patient_android_latest_version_update_code']" :error-messages="errorMessages['patient_android_latest_version_update_code']"></InputField>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.clinicadmin_android_min_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="clinicadmin_android_min_force_update_code" :error-message="errors['clinicadmin_android_min_force_update_code']" :error-messages="errorMessages['clinicadmin_android_min_force_update_code']"></InputField>
        </div>
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.clinicadmin_android_latest_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="clinicadmin_android_latest_version_update_code" :error-message="errors['clinicadmin_android_latest_version_update_code']" :error-messages="errorMessages['clinicadmin_android_latest_version_update_code']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-isForceUpdateforIos">{{ $t('setting_integration_page.lbl_isforceupdate_ios') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="isForceUpdateforIos" :checked="isForceUpdateforIos == 1 ? true : false" name="isForceUpdateforIos" id="category-isForceUpdateforIos" type="checkbox" v-model="isForceUpdateforIos" />
        </div>
      </div>
    </div>
    <div v-if="isForceUpdateforIos == 1" class="bg-body mt-3 px-5 py-4 mb-5">
      <div class="row">
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.patient_ios_min_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="patient_ios_min_force_update_code" :error-message="errors['patient_ios_min_force_update_code']" :error-messages="errorMessages['patient_ios_min_force_update_code']"></InputField>
        </div>
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.patient_ios_latest_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="patient_ios_latest_version_update_code" :error-message="errors['patient_ios_latest_version_update_code']" :error-messages="errorMessages['patient_ios_latest_version_update_code']"></InputField>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.clinicadmin_ios_min_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="clinicadmin_ios_min_force_update_code" :error-message="errors['clinicadmin_ios_min_force_update_code']" :error-messages="errorMessages['clinicadmin_ios_min_force_update_code']"></InputField>
        </div>
        <div class="col-md-6">
          <label for="category-chatgpt_key" class="mb-2">{{ $t('setting_integration_page.clinicadmin_ios_latest_version_code') }}</label>
          <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="clinicadmin_ios_latest_version_update_code" :error-message="errors['clinicadmin_ios_latest_version_update_code']" :error-messages="errorMessages['clinicadmin_ios_latest_version_update_code']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_sms_integration">{{ $t('setting_integration_page.enable_sms_integration') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_sms_integration" :checked="is_sms_integration == 1 ? true : false" name="is_sms_integration" id="category-is_sms_integration" type="checkbox" v-model="is_sms_integration" />
        </div>
      </div>

      <div v-if="is_sms_integration == 1" class="bg-body mt-3 px-5 py-4 mb-5">
        <div class="row">
          <div class="col-md-6">
            <label for="category-sms_account_sid" class="mb-2">{{ $t('setting_integration_page.account_sid') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="sms_account_sid" :error-message="errors['sms_account_sid']" :error-messages="errorMessages['sms_account_sid']"></InputField>
          </div>
          <div class="col-md-6">
            <label for="category-sms_auth_token" class="mb-2">{{ $t('setting_integration_page.auth_token') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="sms_auth_token" :error-message="errors['sms_auth_token']" :error-messages="errorMessages['sms_auth_token']"></InputField>
          </div>
          <div class="col-md-6">
            <label for="category-mobile_number" class="mb-2">{{ $t('setting_integration_page.mobile_number') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="mobile_number" :error-message="errors['mobile_number']" :error-messages="errorMessages['mobile_number']"></InputField>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_whatsapp_integration">{{ $t('setting_integration_page.enable_whatsapp_integration') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_whatsapp_integration" :checked="is_whatsapp_integration == 1 ? true : false" name="is_whatsapp_integration" id="category-is_whatsapp_integration" type="checkbox" v-model="is_whatsapp_integration" />
        </div>
      </div>

      <div v-if="is_whatsapp_integration == 1" class="bg-body mt-3 px-5 py-4 mb-5">
        <div class="row">
          <div class="col-md-6">
            <label for="category-whatsapp_account_sid" class="mb-2">{{ $t('setting_integration_page.account_sid') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="whatsapp_account_sid" :error-message="errors['whatsapp_account_sid']" :error-messages="errorMessages['whatsapp_account_sid']"></InputField>
          </div>
          <div class="col-md-6">
            <label for="category-whatsapp_auth_token" class="mb-2">{{ $t('setting_integration_page.auth_token') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="whatsapp_auth_token" :error-message="errors['whatsapp_auth_token']" :error-messages="errorMessages['whatsapp_auth_token']"></InputField>
          </div>
          <div class="col-md-6">
            <label for="category-whatsapp_number" class="mb-2">{{ $t('setting_integration_page.whatsapp_number') }}</label>
            <InputField class="col-md-12 mb-0" type="text" :is-required="true" placeholder="" v-model="whatsapp_number" :error-message="errors['whatsapp_number']" :error-messages="errorMessages['whatsapp_number']"></InputField>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0 fw-normal" for="category-is_google_login">{{ $t('messages.lbl_dummy_credentials') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_dummy_credentials" :checked="is_dummy_credentials == 1 ? true : false" name="is_dummy_credentials" id="is_dummy_credentials" type="checkbox" v-model="is_dummy_credentials" />
        </div>
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      is_google_login: data.is_google_login || 0,
      is_custom_webhook_notification: data.is_custom_webhook_notification || 0,
      is_map_key: data.is_map_key  || 0,
      google_maps_key: data.google_maps_key  || '',
      is_application_link: data.is_application_link  || '',
      patient_app_play_store: data.patient_app_play_store  || '',
      patient_app_app_store: data.patient_app_app_store  || '',
      clinicadmin_app_play_store: data.clinicadmin_app_play_store  || '',
      clinicadmin_app_app_store: data.clinicadmin_app_app_store  || '',
      custom_webhook_content_key: data.custom_webhook_content_key  || '',
      custom_webhook_url: data.custom_webhook_url  || '',
      isForceUpdateforAndroid: data.isForceUpdateforAndroid  || 0,
      patient_android_min_force_update_code: data.patient_android_min_force_update_code  || '',
      patient_android_latest_version_update_code: data.patient_android_latest_version_update_code  || '',
      clinicadmin_android_min_force_update_code: data.clinicadmin_android_min_force_update_code  || '',
      clinicadmin_android_latest_version_update_code: data.clinicadmin_android_latest_version_update_code  || '',
      isForceUpdateforIos: data.isForceUpdateforIos  || 0,
      patient_ios_min_force_update_code: data.patient_ios_min_force_update_code  || '',
      patient_ios_latest_version_update_code: data.patient_ios_latest_version_update_code  || '',
      clinicadmin_ios_min_force_update_code: data.clinicadmin_ios_min_force_update_code  || '',
      clinicadmin_ios_latest_version_update_code: data.clinicadmin_ios_latest_version_update_code  || '',
      is_sms_integration: data.is_sms_integration || 0,
      sms_account_sid: data.sms_account_sid  || '',
      sms_auth_token: data.sms_auth_token  || '',
      mobile_number: data.mobile_number  || '',
      is_whatsapp_integration: data.is_whatsapp_integration || 0,
      whatsapp_account_sid: data.whatsapp_account_sid  || '',
      whatsapp_auth_token: data.whatsapp_auth_token  || '',
      whatsapp_number: data.whatsapp_number  || '',
      is_dummy_credentials: data.is_dummy_credentials || 0,
    }
  })
}
//validation
const validationSchema = yup.object({ 
  google_maps_key: yup.string().test('google_maps_key', "Must be a valid Google MapKey", function(value) {
    if(this.parent.is_map_key == '1' && !value) {
      return false;
    }
    return true
  }),
  patient_app_play_store: yup.string().test('patient_app_play_store',"Must be a valid Playstore App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  patient_app_app_store: yup.string().test('patient_app_app_store',"Must be a valid App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_app_play_store: yup.string().test('clinicadmin_app_play_store',"Must be a valid Clinic Admin Playstore App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_app_app_store: yup.string().test('clinicadmin_app_app_store',"Must be a valid Clinic Admin App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  patient_android_min_force_update_code: yup.string().test('patient_android_min_force_update_code', "Minimum version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforAndroid == '1' && !value) {
      return false;
    }
    return true
  }),
  patient_android_latest_version_update_code: yup.string().test('patient_android_latest_version_update_code', "Latest version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforAndroid == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_android_min_force_update_code: yup.string().test('clinicadmin_android_min_force_update_code', "Minimum version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforAndroid == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_android_latest_version_update_code: yup.string().test('clinicadmin_android_latest_version_update_code', "Latest version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforAndroid == '1' && !value) {
      return false;
    }
    return true
  }),
  
  patient_ios_min_force_update_code: yup.string().test('patient_ios_min_force_update_code', "Minimum version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforIos == '1' && !value) {
      return false;
    }
    return true
  }),
  patient_ios_latest_version_update_code: yup.string().test('patient_ios_latest_version_update_code', "Latest version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforIos == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_ios_min_force_update_code: yup.string().test('clinicadmin_ios_min_force_update_code', "Minimum version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforIos == '1' && !value) {
      return false;
    }
    return true
  }),
  clinicadmin_ios_latest_version_update_code: yup.string().test('clinicadmin_ios_latest_version_update_code', "Latest version code for Android is Required", function(value) {
    if(this.parent.isForceUpdateforIos == '1' && !value) {
      return false;
    }
    return true
  }),
  sms_account_sid: yup.string().test('sms_account_sid', "Account sid is Required", function(value) {
    if(this.parent.is_sms_integration == '1' && !value) {
      return false;
    }
    return true
  }),
  sms_auth_token: yup.string().test('sms_auth_token', "Auth token is Required", function(value) {
    if(this.parent.is_sms_integration == '1' && !value) {
      return false;
    }
    return true
  }),
  mobile_number: yup.string().test('mobile_number', "Mobile Number is Required", function(value) {
    if(this.parent.is_sms_integration == '1' && !value) {
      return false;
    }
    return true
  }),
  whatsapp_account_sid: yup.string().test('whatsapp_account_sid', "Account sid is Required", function(value) {
    if(this.parent.is_whatsapp_integration == '1' && !value) {
      return false;
    }
    return true
  }),
  whatsapp_auth_token: yup.string().test('whatsapp_auth_token', "Auth token is Required", function(value) {
    if(this.parent.is_whatsapp_integration == '1' && !value) {
      return false;
    }
    return true
  }),
  whatsapp_number: yup.string().test('whatsapp_number', "Whatsapp Number is Required", function(value) {
    if(this.parent.is_whatsapp_integration == '1' && !value) {
      return false;
    }
    return true
  }),
})
const { handleSubmit, errors, resetForm, validate } = useForm({validationSchema})
const errorMessages = ref({})
const { value: is_google_login } = useField('is_google_login')
const { value: is_custom_webhook_notification } = useField('is_custom_webhook_notification')
const { value: is_map_key } = useField('is_map_key')
const { value: is_application_link } = useField('is_application_link')
const { value: google_maps_key } = useField('google_maps_key')
const { value: patient_app_play_store } = useField('patient_app_play_store')
const { value: patient_app_app_store } = useField('patient_app_app_store')
const { value: clinicadmin_app_play_store } = useField('clinicadmin_app_play_store')
const { value: clinicadmin_app_app_store } = useField('clinicadmin_app_app_store')
const { value: custom_webhook_content_key} = useField('custom_webhook_content_key')
const { value: custom_webhook_url } = useField('custom_webhook_url')
const { value: isForceUpdateforAndroid } = useField('isForceUpdateforAndroid')
const { value: patient_android_min_force_update_code } = useField('patient_android_min_force_update_code')
const { value: patient_android_latest_version_update_code } = useField('patient_android_latest_version_update_code')
const { value: clinicadmin_android_min_force_update_code } = useField('clinicadmin_android_min_force_update_code')
const { value: clinicadmin_android_latest_version_update_code } = useField('clinicadmin_android_latest_version_update_code')
const { value: isForceUpdateforIos } = useField('isForceUpdateforIos')
const { value: patient_ios_min_force_update_code } = useField('patient_ios_min_force_update_code')
const { value: patient_ios_latest_version_update_code } = useField('patient_ios_latest_version_update_code')
const { value: clinicadmin_ios_min_force_update_code } = useField('clinicadmin_ios_min_force_update_code')
const { value: clinicadmin_ios_latest_version_update_code } = useField('clinicadmin_ios_latest_version_update_code')
const { value: is_sms_integration } = useField('is_sms_integration')
const { value: sms_account_sid } = useField('sms_account_sid')
const { value: sms_auth_token } = useField('sms_auth_token')
const { value: mobile_number } = useField('mobile_number')
const { value: is_whatsapp_integration } = useField('is_whatsapp_integration')
const { value: whatsapp_account_sid } = useField('whatsapp_account_sid')
const { value: whatsapp_auth_token } = useField('whatsapp_auth_token')
const { value: whatsapp_number } = useField('whatsapp_number')
const { value: is_dummy_credentials } = useField('is_dummy_credentials')

watch(() => isForceUpdateforAndroid.value, (value) => {
  if(value == '0') {
    patient_android_min_force_update_code.value = ''
    patient_android_latest_version_update_code.value = ''
    clinicadmin_android_min_force_update_code.value = ''
    clinicadmin_android_latest_version_update_code.value = ''
  }
}, {deep: true})
watch(() => isForceUpdateforIos.value, (value) => {
  if(value == '0') {
    patient_ios_min_force_update_code.value = ''
    patient_ios_latest_version_update_code.value = ''
    clinicadmin_ios_min_force_update_code.value = ''
    clinicadmin_ios_latest_version_update_code.value = ''
  }
}, {deep: true})
watch(() => is_sms_integration.value, (value) => {
  if(value == '0') {
    sms_account_sid.value = ''
    sms_auth_token.value = ''
    mobile_number.value = ''
  }
}, {deep: true})
watch(() => is_whatsapp_integration.value, (value) => {
  if(value == '0') {
    whatsapp_account_sid.value = ''
    whatsapp_auth_token.value = ''
    whatsapp_number.value = ''
  }
}, {deep: true})

// watch(() => is_map_key.value, (value) => {
//   if(value == '0') {
//     google_maps_key.value = ''
//   }
// }, {deep: true})



// message
const display_submit_message = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    IS_SUBMITED.value = false
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
    IS_SUBMITED.value = false
  }
}

//fetch data
const data = [
  'is_google_login',
  'is_mobile_notification',
  'is_map_key',
  'is_application_link',
  'is_custom_webhook_notification',
  'isForceUpdateforAndroid',
  'isForceUpdateforIos',
  'is_sms_integration',
  'is_whatsapp_integration',
  'is_dummy_credentials'
]



const custom_webhook_key = [
  'custom_webhook_content_key',
  'custom_webhook_url'
]

const customer_app = [
  'patient_app_play_store',
  'patient_app_app_store',
  'clinicadmin_app_play_store',
  'clinicadmin_app_app_store',
  'patient_ios_play_store',
  'patient_ios_app_store',
  'clinicadmin_ios_play_store',
  'clinicadmin_ios_app_store',
]
const google_map_key = [
  'google_maps_key',
]
const versions_key = [
  'patient_android_min_force_update_code',
  'patient_android_latest_version_update_code',
  'clinicadmin_android_min_force_update_code',
  'clinicadmin_android_latest_version_update_code',
  'patient_ios_min_force_update_code',
  'patient_ios_latest_version_update_code',
  'clinicadmin_ios_min_force_update_code',
  'clinicadmin_ios_latest_version_update_code',
]
const sms_key = [
  'sms_account_sid',
  'sms_auth_token',
  'mobile_number'
]
const whatsapp_key = [
  'whatsapp_account_sid',
  'whatsapp_auth_token',
  'whatsapp_number'
]

onMounted(() => {

  const customData = [
    ...data,
    ...custom_webhook_key,
    ...customer_app,
    ...google_map_key,
    ...versions_key,
    ...sms_key,
    ...whatsapp_key,
  ].join(",")

  createRequest(GET_URL(customData)).then((response) => {
    setFormData(response)
  })
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const newValues = {}
  Object.keys(values).forEach((key) => {
    if(values[key] !== '') {
      newValues[key] = values[key] || ''
    }
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
  }).then((res) => display_submit_message(res))
})

defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})
</script>
