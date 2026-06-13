<template>
    <form @submit.prevent="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="clinic-gallery-form" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom" v-if="clinic">
          <h6 class="m-0 h5">
            {{ $t('clinic.singular_title') }}: <span>{{ clinic.name }}</span>
          </h6>
          <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ph ph-x-circle"></i></button>
        </div>
        <div class="d-flex flex-column border-bottom p-3">
          <div class="">
            <Dashboard id="drag-drop-area" class="custom-file-upload mb-3" :uppy="uppy" :open="open" />
          </div>
        </div>
        <div class="offcanvas-body">
          <div v-if="featureImages.length === 0" class="text-center mb-0">{{ $t('messages.data_not_available') }}</div>
          <div v-else>
          <div class="gallery-images">
            <div v-for="(feature, index) in featureImages" :key="index" class="image-container col">
              <button class="delete-button" @click="removeImage(index)" type="button">
                <i class="fa-solid fa-xmark"></i>
              </button>
              <img :src="feature.full_url" alt="Selected Image" class="img-fluid selected-image" />
            </div>
          </div>
          </div>
        </div>
        <div class="offcanvas-footer">
          <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
            <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">{{ $t('messages.close') }}</button>
            <!-- <button class="btn btn-secondary d-block">{{ $t('messages.upload') }}</button> -->
            <button :disabled="IS_SUBMITED" class="btn btn-secondary d-block">
              <template v-if="IS_SUBMITED">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ $t('appointment.loading') }}
              </template>
              <template v-else> {{ $t('messages.upload') }}</template>
            </button>
          </div>
        </div>
      </div>
    </form>
  </template>


<script setup>
import { ref,computed, onMounted } from 'vue'
import { GET_GALLERY_URL, POST_GALLERY_URL } from '../constant/clinic'
import { useRequest, useModuleId } from '@/helpers/hooks/useCrudOpration'
import { createRequestWithFormData } from '@/helpers/utilities'

// uppy
import { Dashboard } from '@uppy/vue'
import Uppy from '@uppy/core'
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'
const open = ref(false)
const uppy = computed(() => {
  return new Uppy({
    restrictions: {
      allowedFileTypes: ['image/jpeg', 'image/png', 'image/jpg'],
    }
  })
})

defineProps({
  IS_SUBMITED: false
})
const IS_SUBMITED = ref(false)
// Request
const { getRequest} = useRequest()


  // Branch Name Values
  const clinic = ref(null)
  const clinicId = useModuleId(() => {
    getRequest({ url: GET_GALLERY_URL, id: clinicId.value }).then((res) => {
      if(res.status) {
        clinic.value = res.clinic
        console.log(clinic.value);
        featureImages.value = res.data
        console.log( featureImages.value)
      }
    })
  }, 'branch_gallery')

  // Select Images
  let featureImages = ref([])

  // Function to delete Images
  const removeImage = (index) => {
    featureImages.value.splice(index, 1)
  }

  // Reload Datatable, SnackBar Message, Alert, Offcanvas Close
  const errorMessages = ref([])
  const reset_close_offcanvas = (res) => {
    IS_SUBMITED.value = false
    if (res.status) {
      uppy.value.getFiles().forEach(file => {
    uppy.value.removeFile(file.id)
  })
      window.successSnackbar(res.message)
      bootstrap.Offcanvas.getInstance('#clinic-gallery-form').hide()
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
  }
  IS_SUBMITED.value = false
  //Form Submit
  const formSubmit = () => {
const formData=new FormData()
// for uppy files
IS_SUBMITED.value = true
  const files = uppy.value.getFiles(); // Accessing uppy's value before calling getFiles
  files.forEach((file) => {
    featureImages.value.push({
      file: file.data,
      full_url: URL.createObjectURL(file.data),
      id: null
    })


  });

    Object.keys(featureImages.value).map((index) => {
      Object.keys(featureImages.value[index]).map((fieldName) => {
        let value = featureImages.value[index][fieldName]
        if(fieldName == 'full_url') value = ''
        formData.append(`gallery[${index}][${fieldName}]`, value);
      })
    });
    createRequestWithFormData(POST_GALLERY_URL(clinicId.value), {'Accept': 'application/json'}, formData).then((res) => reset_close_offcanvas(res));
  }

  </script>

  <style scoped>
  .gallery-images {
    display: grid;
      grid-template-columns: repeat(auto-fill, minmax(104px, 1fr));
      grid-gap: 1rem;
      align-items: stretch;
  }
  .image-container {
    position: relative;
    max-width: 100%;
  }

  .delete-button {
    position: absolute;
    top: -8px;
    right: -8px;
    z-index: 1;
    color: var(--bs-white);
    background-color: var(--bs-danger);
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .selected-image {
    object-fit: cover;
    height: 100px;
    width: 100%;
  }
  .uppy-Dashboard-progressindicators{
  display: none;
}
  </style>
