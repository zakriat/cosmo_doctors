<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-md-6 create-service-image">
            <label for="file_url" class="form-label">{{ $t('nurse.lbl_profile_image') }}</label>

            <!-- Image Preview Section -->
            <div class="text-center">
              <img :src="profile_image || defaultImage" alt="Profile Image" class="img-fluid mb-2 avatar-140 avatar-rounded" />

              <!-- File Upload Section -->
              <div class="d-flex align-items-center justify-content-center gap-2">
                <!-- Hidden file input to trigger file dialog -->
                <input 
                  type="file" 
                  ref="profileInputRef" 
                  class="form-control d-none" 
                  id="file_url" 
                  name="file_url" 
                  @change="fileUpload" 
                  accept=".jpeg, .jpg, .png, .gif" 
                />

                <!-- Button to trigger file selection -->
                <label class="btn btn-light" for="file_url">{{ $t('messages.upload') }}</label>
              </div>
              <span class="text-danger">Only .jpeg, .jpg, .png files are allowed.</span>
            </div>

            <!-- Display error message if any -->
            <span class="text-danger">{{ errors.profile_image }}</span>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_first_name') }} <span class="text-danger">*</span></label>
              <InputField class="" :is-required="true" :label="$t('employee.lbl_first_name')" :placeholder="$t('nurse.placeholder_first_name')" v-model="first_name" :error-message="errors['first_name']" :error-messages="errorMessages['first_name']"></InputField>
            </div>
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_last_name') }} <span class="text-danger">*</span></label>
              <InputField class="" :is-required="true" :label="$t('employee.lbl_last_name')" :placeholder="$t('nurse.placeholder_last_name')" v-model="last_name" :error-message="errors['last_name']" :error-messages="errorMessages['last_name']" autocomplete="off"> </InputField>
            </div>
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_Email') }} <span class="text-danger">*</span></label>
              <InputField class="" :is-required="true" :label="$t('employee.lbl_Email')" :placeholder="$t('nurse.placeholder_email')" v-model="email" :error-message="errors['email']" :error-messages="errorMessages['email']"></InputField>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('clinic.lbl_gender') }}</label>
              <Multiselect id="gender" v-model="gender" :value="gender" v-bind="gender_data" class="form-group"></Multiselect>
              <span v-if="errorMessages['gender']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['gender']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.gender }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('employee.lbl_phone_number') }}<span class="text-danger">*</span> </label>
              <vue-tel-input :value="mobile" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15, placeholder: $t('nurse.placeholder_phone_number') }" autocomplete="new-password"></vue-tel-input>
              <span class="text-danger">{{ errors['mobile'] }}</span>
            </div>
          </div>
          <div class="col-md-6" v-if="currentId === 0">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_password') }} <span class="text-danger">*</span></label>
              <InputField type="password" class="" :is-required="true" :label="$t('employee.lbl_password')" :placeholder="$t('nurse.placeholder_password')" v-model="password" :error-message="errors['password']" :autocomplete="newpassword" :error-messages="errorMessages['password']"></InputField>
            </div>
          </div>
          <div class="col-md-6" v-if="currentId === 0">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_confirm_password') }} <span class="text-danger">*</span></label>
              <InputField type="password" class="" :is-required="true" :label="$t('employee.lbl_confirm_password')" :placeholder="$t('nurse.placeholder_confirm_password')" v-model="confirm_password" :error-message="errors['confirm_password']" :error-messages="errorMessages['confirm_password']"></InputField>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label" for="date_of_birth">{{ $t('customer.lbl_date_of_birth') }} </label>
              <flat-pickr :placeholder="$t('nurse.placeholder_date_of_birth')" id="date_of_birth" class="form-control" v-model="date_of_birth" :value="date_of_birth" :config="config"></flat-pickr>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_status') }}</label>
              <div class="d-flex justify-content-between align-items-center form-control">
                <label class="form-label m-0" for="category-status">{{ $t('employee.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6" v-if="enable_multi_vendor() == 1 && (role() === 'admin' || role() === 'demo_admin')">
            <div class="form-group">
              <label class="form-label">{{ $t('nurse.select_vendors') }} </label>
              <Multiselect class="form-group" v-model="vendor_id" :value="vendor_id" :options="vendors.options" v-bind="singleSelectOption" @select="getClinic" :placeholder="$t('nurse.placeholder_select_vendor')" id="vendor_id"></Multiselect>
            </div>
          </div>
          <div class="col-md-6" v-if="!singleClinic">
            <div class="form-group">
              <label class="form-label" for="address">{{ $t('nurse.select_clinic_centre') }} <span class="text-danger">*</span></label>
              <Multiselect id="clinic_id" v-model="clinic_id" :value="clinic_id" :placeholder="$t('nurse.placeholder_select_clinic_center')" v-bind="singleSelectOption" :options="clinic_centers.options" class="form-group"> </Multiselect>
              <span v-if="errorMessages['clinic_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['clinic_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.clinic_id }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_specialization') }}</label>
              <InputField class="" :label="$t('nurse.lbl_specialization')" :placeholder="$t('nurse.placeholder_specialization')" v-model="specialization" :error-message="errors['specialization']" :error-messages="errorMessages['specialization']"></InputField>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.lbl_license_number') }}</label>
              <InputField class="" :label="$t('nurse.lbl_license_number')" :placeholder="$t('nurse.placeholder_license_number')" v-model="license_number" :error-message="errors['license_number']" :error-messages="errorMessages['license_number']"></InputField>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label" for="address">{{ $t('clinic.lbl_address') }}</label>
              <textarea class="form-control" v-model="address" id="address" :placeholder="$t('nurse.placeholder_address')" rows="3"></textarea>
              <span v-if="errorMessages['address']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['address']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.address }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('clinic.lbl_city') }}</label>
              <Multiselect id="city-list" v-model="city" :value="city" :placeholder="$t('nurse.placeholder_select_city')" v-bind="singleSelectOption" :options="cities.options" class="form-group"></Multiselect>
              <span v-if="errorMessages['city']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['city']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors['city'] }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">{{ $t('clinic.lbl_state') }}</label>
              <Multiselect id="state-list" v-model="state" :value="state" :placeholder="$t('nurse.placeholder_select_state')" v-bind="singleSelectOption" :options="states.options" class="form-group"></Multiselect>
              <span v-if="errorMessages['state']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['state']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors['state'] }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="form-label">{{ $t('nurse.postal_code') }}</label>
              <InputField class="" type="text" :is-required="true" :label="$t('clinic.lbl_postal_code')" :placeholder="$t('nurse.placeholder_postal_code')" v-model="pincode" :error-message="errors['pincode']" :error-messages="errorMessages['pincode']"></InputField>
            </div>
          </div>
          <!-- Country field hidden - UK auto-set -->
          <input type="hidden" v-model="country" value="229" />
        </div>
      </div>

      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, COUNTRY_URL, STATE_URL, CITY_URL, CLINIC_CENTER_LIST, VENDOR_LIST } from '../constant/nurse'
import { useField, useForm } from 'vee-validate'

import { VueTelInput } from 'vue3-tel-input'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'
import FlatPickr from 'vue-flatpickr-component'
import ImageComponent from '@/vue/components/form-elements/imageComponent.vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  defaultImage: { type: String, default: 'https://dummyimage.com/140x140/cfcfcf/000000.png' },
  customefield: { type: Array, default: () => [] },
  selectedSessionServiceProviderId: { type: Number, default: null }
})
const role = () => {
  return window.auth_role[0]
}
const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}

const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  maxDate: 'today'
})

// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  createOption: true // Allow custom text input
})
const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true
})

const gender_data = ref({
  searchable: true,
  options: [
    { label: 'Male', value: 'male' },
    { label: 'Female', value: 'female' },
    { label: 'Intersex', value: 'intersex' }
  ],
  closeOnSelect: true,
  createOption: false
})

const { getRequest, storeRequest, updateRequest } = useRequest()

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status && res.data) {
        // First load all UK states and cities
        getClinic()
        getState(229)
        getCity(null)
        
        // Then set form data after a short delay to ensure dropdowns are loaded
        setTimeout(() => {
          setFormData(res.data)
        }, 500)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const vendors = ref({ options: [], list: [] })
const getVendorList = () => {
  useSelect({ url: VENDOR_LIST, data: { system_service: 'clinic' } }, { value: 'id', label: 'name' }).then((data) => (vendors.value = data))
}

const clinic_centers = ref({ options: [], list: [] })
const singleClinic = ref(false)

const getClinic = (value) => {
  const currentClinicId = currentId.value > 0 ? String(clinic_id.value || '') : ''
  useSelect({
    url: CLINIC_CENTER_LIST,
    data: { id: value, exclude_assigned_nurse: true, current_nurse_clinic_id: currentClinicId }
  }, { value: 'id', label: 'clinic_name' }).then((data) => {
    clinic_centers.value = data
    if (data.options.length === 1) {
      singleClinic.value = true
      // Use nextTick to ensure vee-validate field is ready
      setTimeout(() => {
        clinic_id.value = String(data.options[0].value)
      }, 0)
    } else {
      singleClinic.value = false
    }
  })
}

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
  // Load all UK cities, not filtered by state (independent selection)
  useSelect({ url: CITY_URL, data: { country_id: 229 } }, { value: 'id', label: 'name' }).then((data) => (cities.value = data))
}

onMounted(() => {
  getVendorList()
  getClinic()
  getState(229)
  getCity(null)
})

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    id: null,
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    password: '',
    confirm_password: '',
    gender: 'male',
    profile_image: null,
    date_of_birth: '',
    address: '',
    city: '',
    country: 229, // UK - auto-set
    pincode: '',
    state: '',
    specialization: '',
    license_number: '',
    custom_fields_data: {},
    clinic_id: '',
    vendor_id: '',
    status: 1
  }
}

const image_url = ref()

// Add a ref to store the file object for upload
const profileFile = ref(null)

//  Reset Form
const setFormData = (data) => {
  image_url.value = data.profile_image
  resetForm({
    values: {
      id: data.id,
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      password: data.password,
      confirm_password: data.confirm_password,
      date_of_birth: data.date_of_birth,
      address: data.address,
      city: data.city,
      state: data.state,
      country: data.country,
      pincode: data.pincode,
      specialization: data.specialization || '',
      license_number: data.license_number || '',
      profile_image: data.profile_image,
      custom_fields_data: data.custom_field_data,
      vendor_id: data.vendor_id,
      clinic_id: data.clinic_id ? String(data.clinic_id) : '',
      gender: data.gender,
      status: data.status ? true : false
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}
const numberRegex = /^\d+$/
const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/
// Validations

const validationSchema = yup.object({
  first_name: yup
    .string()
    .required(t('messages.first_name_required'))
    .test('is-string', 'Only strings are allowed', (value) => {
      const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  last_name: yup
    .string()
    .required(t('messages.last_name_required'))
    .test('is-string', 'Only strings are allowed', (value) => {
      const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  email: yup
    .string()
    .required(t('messages.email_required'))
    .test('is-string', 'Only alphabetic characters are allowed at the beginning', 
      (value) => !numberRegex.test(value))
    .matches(EMAIL_REGX, 'Must be a valid email'),
  mobile: yup
    .string()
    .required(t('messages.phone_number_required'))
    .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),

  password: yup
    .string()
    .test('password', t('messages.password_required'), function (value) {
      if (currentId.value === 0 && !value) {
        return false
      }
      return true
    })
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])\S{8,14}$/,
      'Password must be 8-14 chars, include upper, lower, digit, special, no spaces'
    ),

  confirm_password: yup
    .string()
    .test('confirm_password', t('messages.passwords_must_match'), function (value) {
      if (currentId.value === 0 && !value) {
        return this.createError({ message: t('messages.confirm_password_required') })
      }
      if (value !== this.parent.password) {
        return this.createError({ message: t('messages.passwords_must_match') })
      }
      return true
    }),

  clinic_id: yup.string().nullable()
})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: id } = useField('first_name')
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: gender } = useField('gender')
const { value: mobile } = useField('mobile')
const { value: profile_image } = useField('profile_image')
const { value: custom_fields_data } = useField('custom_fields_data')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const { value: date_of_birth } = useField('date_of_birth')
const { value: address } = useField('address')
const { value: city } = useField('city')
const { value: state } = useField('state')
const { value: country } = useField('country')
const { value: pincode } = useField('pincode')
const { value: specialization } = useField('specialization')
const { value: license_number } = useField('license_number')
const { value: vendor_id } = useField('vendor_id')
const { value: clinic_id } = useField('clinic_id')
const { value: status } = useField('status')

const errorMessages = ref({})

// Method to handle file upload
const fileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()

    reader.onload = () => {
      // Set the uploaded image as the source for profile_image (for preview)
      profile_image.value = reader.result
    }

    // Read the file as a data URL (base64 encoded)
    reader.readAsDataURL(file)
    profileFile.value = file // store the file for upload
  }
}

const profileInputRef = ref(null)

const removeLogo = () => {
  profile_image.value = null
  if (profileInputRef.value) {
    profileInputRef.value.value = '' // clear input
  }
  profileFile.value = null
}

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
 if (phoneObject?.countryCallingCode && phoneObject?.nationalNumber) {
    // Ensure country code starts with "+"
    const dialCode = phoneObject.countryCallingCode.startsWith('+')
      ? phoneObject.countryCallingCode
      : `+${phoneObject.countryCallingCode}`;
    mobile.value = `${dialCode} ${phoneObject.nationalNumber}`;
  } else if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted;
  }
}
const IS_SUBMITED = ref(false)
// Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  // If clinic was auto-selected (single clinic), inject it since it bypasses vee-validate
  if (singleClinic.value && clinic_centers.value.options.length === 1) {
    values.clinic_id = String(clinic_centers.value.options[0].value)
  }
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  // Attach the file object for upload
  values.profile_image = profileFile.value
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>