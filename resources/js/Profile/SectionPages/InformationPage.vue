<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center">
      <CardTitle :title="$t('settings.personal_info')" icon="fa-solid fa-user"></CardTitle>
    </div>
    <div class="row">
      <div class="col-12 row">
        <div class="col-md-8">
          <div class="row">
            <div class="form-group">
              <label class="form-label" for="service">{{ $t('employee.lbl_first_name') }}</label> <span class="text-danger">*</span>
              <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_first_name')" :value="first_name" :placeholder="$t('profile.lbl_first_name')" v-model="first_name" :error-message="errors['first_name']"></InputField>
            </div>
            <div class="form-group">
              <label class="form-label" for="service">{{ $t('employee.lbl_last_name') }}</label> <span class="text-danger">*</span>
              <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_last_name')" :value="last_name" :placeholder="$t('profile.lbl_last_name')" v-model="last_name" :error-message="errors['last_name']"></InputField>
            </div>
            <div class="form-group">
              <label class="form-label" for="service">{{ $t('employee.lbl_Email') }}</label> <span class="text-danger">*</span>
              <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_email')" :value="email" :placeholder="$t('profile.lbl_email')" v-model="email" :error-message="errors['email']"></InputField>
            </div>
            <div class="form-group col-md-6">
              <label class="form-label"> {{ $t('profile.lbl_contact_number') }} <span class="text-danger">*</span></label>
              <vue-tel-input type="number" :value="mobile" :placeholder="$t('profile.lbl_contact_number')" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }" :defaultCountry="'GB'"></vue-tel-input>
              <span class="text-danger">{{ errors['mobile'] }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="address">{{ $t('clinic.lbl_address') }}</label>
              <div class="input-group">
                <input class="form-control" v-model="address" id="address" :placeholder="$t('clinic.lbl_address')" />
                <span class="input-group-text"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="date_of_birth">{{ $t('profile.lbl_date_of_birth') }}</label>
              <InputField
                class="col-md-6"
                type="date"
                :is-required="true"
                :label="$t('profile.lbl_date_of_birth')"
                :value="date_of_birth"
                :placeholder="$t('profile.lbl_date_of_birth')"
                v-model="date_of_birth"
                ></InputField>
            </div>
            <div class="form-group">
              <label class="form-label" for="country">{{ $t('profile.lbl_country') }}</label>
              <Multiselect
                v-model="country"
                :options="countries.options"
                :placeholder="$t('profile.lbl_country')"
                class="col-md-6"
                :is-required="true"
                :label="label"
                :error-message="errors['country']"
                @select="getState"
              />
              <span class="text-danger">{{ errors['country'] }}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="state">{{ $t('profile.lbl_state') }}</label>
              <Multiselect
                id="state-list"
                v-model="state"
                :options="states.options"
                :placeholder="$t('profile.lbl_state')"
                class="col-md-6"
                :is-required="true"
                :label="label"
                :error-message="errors['state']"
                @select="getCity"
              />
              <span class="text-danger">{{ errors['state'] }}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="city">{{ $t('profile.lbl_city') }}</label>
              <Multiselect
                id="city-list"
                v-model="city"
                :options="cities.options"
                :placeholder="$t('profile.lbl_city')"
                class="col-md-6"
                :is-required="true"
                :label="label"
                :error-message="errors['city']"
              />
              <span class="text-danger">{{ errors['city'] }}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="pincode">{{ $t('profile.lbl_pincode') }}</label>
              <InputField
                class="col-md-6"
                :is-required="true"
                :label="$t('profile.lbl_postal_code')"
                :value="pincode"
                :placeholder="$t('profile.lbl_postal_code')"
                v-model="pincode"
                :error-message="errors['pincode']"
              ></InputField>
            </div>
          </div>
        </div>

        <div class="col-md-4 text-center">
          <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
          <div class="d-flex align-items-center justify-content-center gap-2">
            <input type="file" ref="profileInputRef" class="form-control d-none" id="logo" name="profile_image" accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
            <label class="btn btn-info" for="logo">{{ $t('messages.upload') }}</label>
            <input type="button" class="btn btn-danger" name="remove" :value="$t('settings.remove')" @click="removeLogo()" v-if="ImageViewer" />
          </div>
          <span class="text-danger">{{ errors.profile_image }}</span>
        </div>
        <div class="form-group col-md-4">
          <label for="" class="w-100">{{ $t('profile.lbl_gender') }}</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male" :checked="gender == 'male'" />
            <label class="form-check-label" for="male"> {{ $t('clinic.lbl_male') }} </label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female" :checked="gender == 'female'" />
            <label class="form-check-label" for="female"> {{ $t('clinic.lbl_female') }} </label>
          </div>

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other" :checked="gender == 'other'" />
            <label class="form-check-label" for="other"> {{ $t('messages.intersex') }} </label>
          </div>
          <p class="mb-0 text-danger">{{ errors.gender }}</p>
        </div>
        <!-- <div class="form-group m-0 col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0" v-model="show_in_calender"
              id="show-in-calender" :checked="show_in_calender" />
            <label class="form-check-label" for="show-in-calender">
              {{ $t('profile.lbl_show_in_calender') }}
            </label>
          </div>
        </div> -->

        <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
      </div>
    </div>
  </form>
</template>

<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { onMounted, ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { VueTelInput } from 'vue3-tel-input'
import { INFORMATION_STORE,COUNTRY_URL, STATE_URL, CITY_URL, GET_URL } from '@/vue/constants/users'
import { useSelect } from '@/helpers/hooks/useSelect'

import { readFile } from '@/helpers/utilities'
import { createRequest } from '@/helpers/utilities'
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import SubmitButton from './Forms/SubmitButton.vue'

const IS_SUBMITED = ref(false)

const { storeRequest } = useRequest()

// File Upload Function
const ImageViewer = ref(null)
const profileInputRef = ref(null)
const defaultImage = window.auth_profile_image
const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    profileInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const changeLogo = (e) => {
  Swal.fire({
    title: 'Do you want to upload this image?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, upload it',
    cancelButtonText: 'No, cancel',
    showClass: {
      popup: 'animate__animated animate__zoomIn'
    },
    hideClass: {
      popup: 'animate__animated animate__zoomOut'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: profile_image })
      Swal.fire({
        title: 'Image Uploaded',
        text: 'Your image has been successfully uploaded.',
        icon: 'success'
      })
    }
  })
}

const removeLogo = () => {
  Swal.fire({
    title: 'Are you sure you want to remove this image?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#858482',
    confirmButtonText: 'Yes, remove it',
    cancelButtonText: 'No, cancel',
    showClass: {
      popup: 'animate__animated animate__zoomIn'
    },
    hideClass: {
      popup: 'animate__animated animate__zoomOut'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      removeImage({ imageViewerBS64: ImageViewer, changeFile: profile_image })
      Swal.fire({
        title: 'Image Removed',
        text: 'The image has been successfully removed.',
        icon: 'success'
      })
    }
  })
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.profile_image
  resetForm({
    values: {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      address: data.address,
      date_of_birth: data.date_of_birth,
      city: data.city,
      state: data.state,
      country: data.country,
      pincode: data.pincode,
      show_in_calender: data.show_in_calender,
      gender: data.gender,
      profile_image: data.profile_image
    }
  })
  // Fetch and set country
  if (data.country) {
    getState(data.country)
    country.value = data.country
  }
  // Fetch and set state
  if (data.state) {
    getCity(data.state)
    state.value = data.state
  }
  // Set city
  if (data.city) {
    city.value = data.city
  }
}

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  // if (phoneObject?.formatted) {
  // mobile.value = phoneObject.formatted
  // }
  console.log(phoneObject?.country?.dialCode, phoneObject?.nationalNumber )
  if (phoneObject?.country?.dialCode && phoneObject?.nationalNumber) {
    // Ensure dialCode starts with "+"
    const dialCode = phoneObject.country.dialCode.startsWith('+')
      ? phoneObject.country.dialCode
      : `+${phoneObject.country.dialCode}`;
    mobile.value = `${dialCode} ${phoneObject.nationalNumber}`;
  } else {
    // Fallback: ensure starts with "+"
    mobile.value = phone.startsWith('+') ? phone : `+${phone}`;
  }
}

const validationSchema = yup.object({
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().required('Email is required'),
  mobile: yup
    .string()
    .required('Mobile number is required')
    .test('is-valid-phone', 'Invalid phone number with country code', function (value) {
      const digits = value.replace(/\D/g, '')
      return value && digits.length >= 7 && digits.length <= 15 && value.startsWith('+') && /^[+\d\s]*$/.test(value)
    }),
  date_of_birth: yup.string().required('Date of birth is required'),
  city: yup.string().required('City is required'),
  state: yup.string().required('State is required'),
  country: yup.string().required('Country is required'),
  pincode: yup.string().required('Postal code is required'),
  show_in_calender: yup.string().required('Show in calender is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const errorMessages = ref(null)
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: mobile } = useField('mobile')
const { value: address } = useField('address')
const { value: date_of_birth } = useField('date_of_birth')
const { value: city } = useField('city')
const { value: state } = useField('state')
const { value: country } = useField('country')
const { value: pincode } = useField('pincode')
const { value: show_in_calender } = useField('show_in_calender')
const { value: gender } = useField('gender')
const { value: profile_image } = useField('profile_image')

//fetch data
const data = 'first_name'
onMounted(() => {
  createRequest(GET_URL()).then((response) => {
    if (response.status) {
      setFormData(response.data)
    }
  })
})

const countries = ref({ options: [], list: [] })
const states = ref({ options: [], list: [] })
const cities = ref({ options: [], list: [] })

const getCountry = () => {
  useSelect({ url: COUNTRY_URL }, { value: 'id', label: 'name' })
    .then((data) => {
      const list = data.list || data.options || data;
      countries.value.options = list.map(item => ({
        value: item.id ?? item.country_id ?? item.value,
        label: item.name ?? item.country_name ?? item.text ?? item.label
      }));
      // If country.value is set, ensure it's selected
      if (country.value) {
        country.value = country.value;
      }
    });
}

const getState = (countryId) => {
  useSelect({ url: STATE_URL, data: countryId }, { value: 'id', label: 'name' })
    .then((data) => {
      states.value.options = (data.options || data).map(item => ({
        value: item.id ?? item.state_id ?? item.value,
        label: item.name ?? item.state_name ?? item.text ?? item.label
      }));
      // If state.value is set, ensure it's selected
      if (state.value) {
        state.value = state.value;
      }
    });
}

const getCity = (stateId) => {
  useSelect({ url: CITY_URL, data: stateId }, { value: 'id', label: 'name' })
    .then((data) => {
      cities.value.options = (data.options || data).map(item => ({
        value: item.id ?? item.city_id ?? item.value,
        label: item.name ?? item.city_name ?? item.text ?? item.label
      }));
      // If city.value is set, ensure it's selected
      if (city.value) {
        city.value = city.value;
      }
    });
}

onMounted(() => {
  getCountry()
})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    // errorMessages.value = res.all_message
  }
}

//Form Submit
// const formSubmit = handleSubmit((values) => {
//   IS_SUBMITED.value = true
//   storeRequest({ url: INFORMATION_STORE, body: values, type: 'file' }).then((res) => display_submit_message(res))
// })

const formSubmit = handleSubmit((values) => {
  if (!values.mobile) {
    // Agar mobile null hai to form submit nahi hoga, aur error message dikhega
    errors.mobile = 'Mobile number is required'
    return // Form submit ko yahin rok denge
  }

  IS_SUBMITED.value = true
  storeRequest({ url: INFORMATION_STORE, body: values, type: 'file' }).then((res) => display_submit_message(res))
})
</script>

<style>
.favicon-image {
  width: 50px;
  height: 50px;
}
</style>
