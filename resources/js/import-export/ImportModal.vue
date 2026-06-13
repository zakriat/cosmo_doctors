<template>
  <BModal @hide="onHide" title="Import Data" v-model="modal" centered>
    <!-- File Format Selection -->
    <div class="form-group mt-3">
      <label for="fileFormat">Select File Format</label>
      <select id="fileFormat" v-model="fileFormat" class="form-select" required>
        <option disabled value="">Select file format</option>
        <option value="csv">CSV</option>
        <option value="xlsx">XLSX</option>
        <option value="xls">XLS</option>
        <option value="ods">ODS</option>
        <option value="html">HTML</option>
      </select>
    </div>

    <!-- File Input -->
    <div class="form-group mt-3">
      <label for="fileInput">Upload File</label>
      <input id="fileInput" type="file" class="form-control" @change="validateFile" :accept="getAcceptedFileTypes()" />
    </div>
    <a href="#" @click.prevent="downloadSampleFile">Click here to download sample file</a>

    <!-- Sample File and Description -->
    <div class="mt-3">
      <div class="form-group">
        <div v-if="moduleName == 'appointments'">
          <p><strong>Following fields are required in the file:</strong></p>
          <ul>
            <li v-for="option in MODULE_COLUMNS" :key="option.value" class="">
              {{ option.text }}
            </li>
          </ul>
        </div>

        <ul v-if="moduleName != 'appointments'">
          <li>first_name</li>
          <li>last_name</li>
          <li>email</li>
          <li>mobile</li>
          <li>gender</li>
          <li>date_of_birth</li>
          <li>password</li>
        </ul>

        <div v-if="moduleName != 'appointments'">
          <p><strong>Send Notification when user registers</strong></p>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="emailNotification" v-model="emailNotification" />
            <label class="form-check-label" for="emailNotification"> Email </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="smsNotification" v-model="smsNotification" />
            <label class="form-check-label" for="smsNotification"> SMS/Whatsapp </label>
          </div>
          <small class="form-text text-info">Note: If notification is enabled, demo import will take time.</small>
        </div>
      </div>
    </div>

    <!-- Modal Footer Buttons -->
    <template #footer>
      <button class="btn btn-secondary" @click="onClose" :disabled="isSubmitting">Cancel</button>
      <button class="btn btn-primary" @click="submitFile" :disabled="!fileFormat || !file || isSubmitting">
        <template v-if="isSubmitting">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          {{ $t('appointment.loading') }}
        </template>
        <template v-else> <i class="fa-solid fa-file-arrow-up"></i> {{ $t('export.import') }}</template>
      </button>
    </template>
  </BModal>
</template>

<script setup>
import { ref } from 'vue'
import { useModel } from '@/helpers/hooks/bootstrap-components'

const modal = useModel(() => {}, 'import_modal')
// const baseUrl =  window.base_url;
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content')

const props = defineProps({
  importUrl: { type: String },
  moduleName: { type: String },
  exportDoctorId: { type: String },
  moduleColumnProp: { type: Array, default: () => [] }
})
const MODULE_COLUMNS = ref(props.moduleColumnProp)
// State Variables
const fileFormat = ref('csv')
const file = ref(null)
const emailNotification = ref('')
const smsNotification = ref('')
const isSubmitting = ref(false) // Track submission status
const fileInput = ref(null)

// Reset Form Data
const resetFormData = () => {
  fileFormat.value = ''
  file.value = null
  emailNotification.value = false
  smsNotification.value = false
  isSubmitting.value = false

  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Handle Modal Hide
const onHide = () => {
  modal.value = false
  resetFormData()
}

// Handle Modal Close
const onClose = () => {
  modal.value = false
  resetFormData()
}
// Validate File Input
const validateFile = (event) => {
  const selectedFile = event.target.files[0]
  if (selectedFile) {
    const fileExtension = selectedFile.name.split('.').pop().toLowerCase()
    if (!isValidFileType(fileExtension)) {
      window.errorSnackbar('Please upload a valid file of the selected format.')
      event.target.value = '' // Reset the file input
      file.value = null
    } else {
      file.value = selectedFile
    }
  }
}

// Check if the file extension is valid for the selected format
const isValidFileType = (extension) => {
  const validFormats = {
    csv: ['csv'],
    xlsx: ['xlsx'],
    xls: ['xls'],
    ods: ['ods'],
    html: ['html']
  }
  return validFormats[fileFormat.value]?.includes(extension)
}

// Get accepted file types based on the selected format
const getAcceptedFileTypes = () => {
  const acceptedFormats = {
    csv: '.csv',
    xlsx: '.xlsx',
    xls: '.xls',
    ods: '.ods',
    html: '.html'
  }
  return acceptedFormats[fileFormat.value] || ''
}

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//download sample file

const downloadSampleFile = async () => {
  try {
    let appointmentUrl = `${baseUrl}/app/appointments/download-sample/${fileFormat.value}`
    let patientUrl = `${baseUrl}/app/customers/download-sample/${fileFormat.value}`
    let currentUrl = patientUrl
    if (props.moduleName === 'appointments') {
      currentUrl = appointmentUrl
    }
    const response = await fetch(currentUrl, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    })
    if (response.status === 200) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `${props.moduleName}.${fileFormat.value}`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      window.URL.revokeObjectURL(url)
    }
  } catch (error) {
    console.error('Error downloading the sample file:', error)
  }
}

// Submit File for Import
const submitFile = async () => {
  if (!file.value || !fileFormat.value) {
    window.errorSnackbar('Please select a file format and upload a file.')
    return
  }

  isSubmitting.value = true // Show the spinner

  const formData = new FormData()
  formData.append('file_format', fileFormat.value)
  formData.append('file', file.value)
  formData.append('email_notification', emailNotification.value)
  formData.append('sms_notification', smsNotification.value)

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  // const response = await fetch(`${baseUrl}/app/customers/import-users`, {
  try {
    const response = await fetch(props.importUrl, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request header
      }
    })

    if (response.ok && response.headers.get('Content-Type').includes('application/json')) {
      const res = await response.json() // Parse JSON response
      modal.value = false
      window.location.reload()
      window.successSnackbar(res.message)
      modal.value = false
    } else if (response.status === 422) {
      const error = await response.json()
      if (error.error && error.error.headers) {
        window.errorSnackbar(error.error.headers)
      } else if (error.errors && Array.isArray(error.errors) && error.errors.length > 0) {
        window.errorSnackbar(error.errors[0].message)
      } else {
        window.errorSnackbar('Validation failed. Please check your file.')
      }
    } else {
      // If not JSON, handle it as an error page (HTML)
      const errorMessage = await response.text() // Get raw HTML response
      window.errorSnackbar('Failed to import file. Please check your data and try again.')
      console.error('Error response:', errorMessage) // Log the error response (HTML content)
    }
  } catch (error) {
    console.log('Error uploading file:', error)
    window.errorSnackbar(`An error occurred: ${error.message || 'Please try again.'}`)
    window.renderedDataTable.ajax.reload(null, false)
  } finally {
    isSubmitting.value = false // Hide the spinner
  }
}
</script>
