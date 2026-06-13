<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <CardTitle :title="$t('setting_sidebar.lbl_General')" icon="fas fa-cube"></CardTitle>
      <a type="submit" class="btn btn-primary float-right" @click="clearcache()"><i class="fa-solid fa-arrow-rotate-left mx-2"></i>{{ $t('settings.purge_cache')}}</a>
    </div>
    <div class="form-group">
    <label class="form-label">{{$t('setting_bussiness_page.lbl_app')}} <span class="text-danger">*</span></label>
    <InputField :label="$t('setting_bussiness_page.lbl_app')" :placeholder="$t('setting_bussiness_page.lbl_app')" :value="app_name" v-model="app_name" :errorMessage="errors.app_name"></InputField>
    </div>
    <div class="form-group">
    <label class="form-label">{{$t('setting_bussiness_page.lbl_doctor_app')}} <span class="text-danger">*</span></label>
    <InputField :label="$t('setting_bussiness_page.lbl_doctor_app')" :placeholder="$t('setting_bussiness_page.lbl_doctor_app')" :value="doctor_app_name" v-model="doctor_app_name" :errorMessage="errors.doctor_app_name"></InputField>
    </div>
    <div class="form-group">
    <label class="form-label">{{$t('setting_bussiness_page.lbl_user_app')}} <span class="text-danger">*</span></label>
    <InputField :label="$t('setting_bussiness_page.lbl_user_app')" :placeholder="$t('setting_bussiness_page.lbl_user_app')" :value="user_app_name" v-model="user_app_name" :errorMessage="errors.user_app_name"></InputField>
    </div>
<!--
    <InputField :label="$t('setting_bussiness_page.lbl_footer')" :value="footer_text" v-model="footer_text" :errorMessage="errors.footer_text"></InputField>
    <InputField :label="$t('setting_bussiness_page.lbl_copyright')" :value="copyright_text" v-model="copyright_text" :errorMessage="errors.copyright_text"></InputField>
    <InputField :label="$t('setting_bussiness_page.lbl_uitext')" :value="ui_text" v-model="ui_text" :errorMessage="errors.ui_text"></InputField> -->
    <!-- <div class="form-group">
      <label class="form-label">{{$t('setting_bussiness_page.lbl_contact_no')}} <span class="text-danger">*</span></label>
      <InputField :label="$t('setting_bussiness_page.lbl_contact_no')" :placeholder="$t('setting_bussiness_page.lbl_contact_no')" :value="helpline_number" v-model="helpline_number" :errorMessage="errors.helpline_number"></InputField>
    </div> -->

    <div class="form-group">
      <label class="form-label">
        {{$t('setting_bussiness_page.lbl_contact_no')}} <span class="text-danger">*</span>
      </label>
      <vue-tel-input
        class="w-100 bg-light"
        :value="helpline_number"
        @input="handleInput"
        v-bind="{ mode: 'international', maxLen: 15 }"
        autocomplete="new-password"
        :inputOptions="{ placeholder: $t('employee.lbl_phone_number_placeholder') }"
      />
      <span class="text-danger">{{ errors.helpline_number }}</span>
    </div>


    <div class="form-group">
      <label class="form-label">{{$t('setting_bussiness_page.lbl_inquiry_email')}} <span class="text-danger">*</span></label>
      <InputField :label="$t('setting_bussiness_page.lbl_inquiry_email')" :placeholder="$t('setting_bussiness_page.lbl_inquiry_email')" :value="inquriy_email" v-model="inquriy_email" :errorMessage="errors.inquriy_email"></InputField>
    </div>
    <div class="form-group">
      <label class="form-label">{{$t('setting_bussiness_page.lbl_site_description')}} <span class="text-danger">*</span></label>
      <InputField :label="$t('setting_bussiness_page.lbl_site_description')" :placeholder="$t('setting_bussiness_page.lbl_site_description')" :value="short_description" v-model="short_description" :errorMessage="errors.short_description"></InputField>
    </div>
    <div class="row">
      <h5 class="my-3">{{ $t('setting_bussiness_page.business_add') }}</h5>
      <div class="form-group col-md-6">
        <label class="form-label">{{$t('service_providers.lbl_shop_number')}}</label>
        <InputField :is-required="false" :label="$t('service_providers.lbl_shop_number')" :placeholder="$t('setting_bussiness_page.lbl_shop_number')" v-model="bussiness_address_line_1"></InputField>
      </div>
      <div class="form-group col-md-6">
        <label class="form-label">{{$t('service_providers.lbl_landmark')}}</label>
        <InputField  :is-required="false" :label="$t('service_providers.lbl_landmark')" :placeholder="$t('service_providers.lbl_landmark')" v-model="bussiness_address_line_2"></InputField>
      </div>
          <div class="col-md-2">
            <label class="form-label">{{ $t('setting_bussiness_page.lbl_country') }} </label>
            <Multiselect id="country-list" :is-required="false" v-model="country" :placeholder="$t('setting_bussiness_page.lbl_country')" :value="country" v-bind="singleSelectOption" :options="countries.options" @select="getState" class="form-group"></Multiselect>

          </div>
          <div class="col-md-2">
            <label class="form-label">{{ $t('setting_bussiness_page.lbl_state') }} </label>
            <Multiselect id="state-list" :is-required="false" v-model="state" :placeholder="$t('setting_bussiness_page.lbl_state')" :value="state" v-bind="singleSelectOption" :options="states.options" @select="getCity" class="form-group"></Multiselect>

          </div>
          <div class="col-md-2">
            <label class="form-label">{{ $t('setting_bussiness_page.lbl_city') }} </label>
            <Multiselect id="city-list" :is-required="false" v-model="city" :placeholder="$t('setting_bussiness_page.lbl_city')" :value="city" v-bind="singleSelectOption" :options="cities.options" class="form-group"></Multiselect>

          </div>
        <div class="form-group col-md-2">
        <label class="form-label">{{$t('setting_bussiness_page.lbl_postal_code')}}</label>
          <InputField  :is-required="false" :label="$t('setting_bussiness_page.lbl_postal_code')" :placeholder="$t('setting_bussiness_page.lbl_postal_code')" v-model="bussiness_address_postal_code"></InputField>
        </div>
        <div class="form-group col-md-2">
        <label class="form-label">{{$t('setting_bussiness_page.lbl_lat')}}</label>
          <InputField :is-required="false" :label="$t('setting_bussiness_page.lbl_lat')" :placeholder="$t('setting_bussiness_page.lbl_lat')" v-model="bussiness_address_latitude"></InputField>
        </div>
        <div class="form-group col-md-2">
        <label class="form-label">{{$t('setting_bussiness_page.lbl_long')}}</label>
          <InputField :is-required="false" :label="$t('setting_bussiness_page.lbl_long')" :placeholder="$t('setting_bussiness_page.lbl_long')" v-model="bussiness_address_longitude"></InputField>
        </div>
    </div>

    <div class="col row">
      <h5 class="my-3">{{ $t('setting_bussiness_page.branding') }}</h5>
      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block bg-light">
              <div class="card-body">
                <img :src="logoViewer || DEFAULT_LOGO" class="img-fluid" alt="logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="logoInputRef" class="form-control d-none" id="logo" name="logo" accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
              <label class="btn btn-primary mb-5" for="logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeLogo()" v-if="logo" />
            </div>
            <span class="text-danger">{{ errors.logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.mini_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body">
                <img :src="miniLogoViewer || DEFAULT_MINI_LOGO" class="img-fluid" alt="mini_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="logoMiniInputRef" class="form-control d-none" id="mini-logo" name="mini_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeMiniLogo" />
              <label class="btn btn-primary mb-5" for="mini-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeMiniLogo()" v-if="mini_logo" />
            </div>
            <span class="text-danger">{{ errors.mini_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.dark_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body bg-dark">
                <img :src="darkLogoViewer || DEFAULT_DARK_LOGO" class="img-fluid" alt="dark_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="darkLogoInputRef" class="form-control d-none" id="dark-logo" name="dark_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeDarkLogo" />
              <label class="btn btn-primary mb-5" for="dark-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeDarkLogo()" v-if="dark_logo" />
            </div>
            <span class="text-danger">{{ errors.dark_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('setting_bussiness_page.dark_mini_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body bg-dark">
                <img :src="darkMiniLogoViewer || DEFAULT_DARK_MINI_LOGO" class="img-fluid" alt="dark_mini_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="darkLogoMiniInputRef" class="form-control d-none" id="dark-mini-logo" name="dark_mini_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeDarkMiniLogo" />
              <label class="btn btn-primary mb-5" for="dark-mini-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5 mb-5" name="remove" value="Remove" @click="removeDarkMiniLogo()" v-if="dark_mini_logo" />
            </div>
            <span class="text-danger">{{ errors.dark_mini_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.favicon') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4 r-0">
            <div class="card text-center inline-block">
              <div class="card-body">
                <img :src="faviconViewer || DEFAULT_FAVICON" class="img-fluid favicon-image" alt="favicon" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="faviconInputRef" class="form-control d-none" id="favicon-logo" name="favicon" accept=".jpeg, .jpg, .png, .gif" @change="changeFavicon" />
              <label class="btn btn-primary mb-5" for="favicon-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeFavicon()" v-if="favicon" />
            </div>
            <span class="text-danger">{{ errors.favicon }}</span>
          </div>
        </div>
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { onMounted, ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL, CACHE_CLEAR, COUNTRY_URL, STATE_URL, CITY_URL } from '@/vue/constants/setting'
import { useSelect } from '@/helpers/hooks/useSelect'
import { readFile } from '@/helpers/utilities'
import { createRequest } from '@/helpers/utilities'
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import SubmitButton from './Forms/SubmitButton.vue'
import { VueTelInput } from 'vue3-tel-input'


const DEFAULT_LOGO = document.querySelector('[name="logo"]').value
const DEFAULT_MINI_LOGO = document.querySelector('[name="mini-logo"]').value
const DEFAULT_DARK_LOGO = document.querySelector('[name="dark-logo"]').value
const DEFAULT_DARK_MINI_LOGO = document.querySelector('[name="dark-mini-logo"]').value
const DEFAULT_FAVICON = document.querySelector('[name="favicon"]').value
const IS_SUBMITED = ref(false)

const { storeRequest,deleteRequest  } = useRequest()

let logoInputRef = ref(null)
let logoMiniInputRef = ref(null)
let darkLogoInputRef = ref(null)
let darkLogoMiniInputRef = ref(null)
let faviconInputRef = ref(null)

const handleInput = (Number, phoneObject) => {
  if (phoneObject?.formatted) {
    helpline_number.value = phoneObject.formatted;
  }
};

const preventInvalidChar = (event) => {
  const char = String.fromCharCode(event.which)
  const allowed = /[0-9+\s()-]/
  if (!allowed.test(char)) {
    event.preventDefault()
  }
}


const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    logoInputRef.value.value = ''
    logoMiniInputRef.value.value = ''
    darkLogoInputRef.value.value = ''
    darkLogoMiniInputRef.value.value = ''
    faviconInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const logoViewer = ref(DEFAULT_LOGO)
const changeLogo = (e) => fileUpload(e, { imageViewerBS64: logoViewer, changeFile: logo })
const removeLogo = () => removeImage({ imageViewerBS64: logoViewer, changeFile: logo })

const miniLogoViewer = ref(DEFAULT_MINI_LOGO)
const changeMiniLogo = (e) => fileUpload(e, { imageViewerBS64: miniLogoViewer, changeFile: mini_logo })
const removeMiniLogo = () => removeImage({ imageViewerBS64: miniLogoViewer, changeFile: mini_logo })

const darkLogoViewer = ref(DEFAULT_DARK_LOGO)
const changeDarkLogo = (e) => fileUpload(e, { imageViewerBS64: darkLogoViewer, changeFile: dark_logo })
const removeDarkLogo = () => removeImage({ imageViewerBS64: darkLogoViewer, changeFile: dark_logo })

const darkMiniLogoViewer = ref(DEFAULT_DARK_MINI_LOGO)
const changeDarkMiniLogo = (e) => fileUpload(e, { imageViewerBS64: darkMiniLogoViewer, changeFile: dark_mini_logo })
const removeDarkMiniLogo = () => removeImage({ imageViewerBS64: darkMiniLogoViewer, changeFile: dark_mini_logo })

const faviconViewer = ref(DEFAULT_FAVICON)
const changeFavicon = (e) => fileUpload(e, { imageViewerBS64: faviconViewer, changeFile: favicon })
const removeFavicon = () => removeImage({ imageViewerBS64: faviconViewer, changeFile: favicon })

//  Reset Form
const setFormData = (data) => {
  console.log(data);
  ;(logoViewer.value = data.logo),
    (miniLogoViewer.value = data.mini_logo),
    (darkLogoViewer.value = data.dark_logo),
    (darkMiniLogoViewer.value = data.dark_mini_logo),
    (faviconViewer.value = data.favicon),
    getState(data.country)
    getCity(data.state)
    resetForm({
      values: {
        app_name: data.app_name,
        doctor_app_name: data.doctor_app_name,
        user_app_name: data.user_app_name,
        // footer_text: data.footer_text,
        // ui_text: data.ui_text,
        // copyright_text: data.copyright_text,
        helpline_number: data.helpline_number,
        inquriy_email: data.inquriy_email,
        short_description: data.short_description,
        logo: '',
        mini_logo: '',
        dark_logo: '',
        dark_mini_logo: '',
        favicon: '',
        bussiness_address_line_1: data.bussiness_address_line_1,
        bussiness_address_line_2: data.bussiness_address_line_2,
        country: data.country,
        state: data.state,
        city: data.city,
        bussiness_address_postal_code: data.bussiness_address_postal_code,
        bussiness_address_latitude: data.bussiness_address_latitude,
        bussiness_address_longitude: data.bussiness_address_longitude
      }
    })
}

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const countries = ref({ options: [], list: [] })

  const getCountry = () => {

    useSelect({ url: COUNTRY_URL }, { value: 'id', label: 'name' }).then((data) => (countries.value = data))
  }

  const states = ref({ options: [], list: [] })

  const getState = (value) => {

    useSelect({ url: STATE_URL, data: value }, { value: 'id', label: 'name' }).then((data) => (states.value = data))
  }

  const cities = ref({ options: [], list: [] })

  const getCity = (value) => {
    useSelect({ url: CITY_URL, data: value }, { value: 'id', label: 'name' }).then((data) => (cities.value = data))
  }



const numberRegex = /^\d+$/
const mobile_valid = ref(false)

const validationSchema = yup.object({
  app_name: yup.string().required('Business name is required'),
  doctor_app_name: yup.string().required('Doctor App name is required'),
  user_app_name: yup.string().required('User App name is required'),
  helpline_number: yup.string()
    .required('Phone Number is a required field')
    .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
  inquriy_email: yup.string().email('must be a valid email'),
  short_description: yup.string()
})


const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const errorMessage = ref(null)
const { value: app_name } = useField('app_name')
const { value: doctor_app_name } = useField('doctor_app_name')
const { value: user_app_name } = useField('user_app_name')
// const { value: ui_text } = useField('ui_text')
// const { value: footer_text } = useField('footer_text')
const { value: helpline_number } = useField('helpline_number')
// const { value: copyright_text } = useField('copyright_text')
const { value: inquriy_email } = useField('inquriy_email')
const { value: short_description } = useField('short_description')
const { value: logo } = useField('logo')
const { value: mini_logo } = useField('mini_logo')
const { value: dark_logo } = useField('dark_logo')
const { value: dark_mini_logo } = useField('dark_mini_logo')
const { value: favicon } = useField('favicon')
const { value: bussiness_address_line_1 } = useField('bussiness_address_line_1')
const { value: bussiness_address_line_2 } = useField('bussiness_address_line_2')
const { value: country } = useField('country')
const { value: state } = useField('state')
const { value: city } = useField('city')
const { value: bussiness_address_postal_code } = useField('bussiness_address_postal_code')
const { value: bussiness_address_latitude } = useField('bussiness_address_latitude')
const { value: bussiness_address_longitude } = useField('bussiness_address_longitude')
//fetch data
const data = 'app_name,doctor_app_name,user_app_name,helpline_number,inquriy_email,short_description,logo,mini_logo,dark_logo,dark_mini_logo,favicon,bussiness_address_postal_code,bussiness_address_line_1,bussiness_address_line_2,country,state,city,bussiness_address_latitude,bussiness_address_longitude'
onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  });
  getCountry();
})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  console.log(res)
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

//Form Submit
const formSubmit = handleSubmit((values) => {

  const filteredValues = {}
  Object.entries(values).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      filteredValues[key] = value
    }
  })

  IS_SUBMITED.value = true
  storeRequest({ url: STORE_URL, body: filteredValues, type: 'file' }).then((res) => display_submit_message(res))
})

const clearcache = () => {
  deleteRequest({ url: CACHE_CLEAR }).then((res) => {
    if (res.status) {
      display_submit_message(res)
    }
  })
}
</script>

<style>
.favicon-image {
  width: 50px;
  height: 50px;
}
</style>