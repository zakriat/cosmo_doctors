<template>
  <form @submit="formSubmit">
    <div class="body-chart-offcanvas" tabindex="-1" id="body-chart-offcanvas"  >
      <div class="row">
        <h4>{{ $t('appointment.body_chart') }}</h4>

        <div class="d-flex align-items-center justify-content-end gap-2 p-2">
          <div class="col-md-2">
            <Multiselect v-model="selectedImages" :value="selectedImages" v-bind="singleSelectOption" :options="templateImageList.options" placeholder="Select images" @select="selectTemplate" class="form-group">
              <template #option="{ option }">
                <img class="img-fluid avatar avatar-60" :src="option.value" alt="image" />
                <span>{{ option.label }}</span>
              </template>
            </Multiselect>
          </div>
          <input type="file" ref="profileInputRef" class="form-control d-none" id="body_image" name="body_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
          <label class="btn btn-md btn-primary" for="body_image">{{ $t('messages.upload') }}</label>
          <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()" v-if="ImageViewer" />
        </div>
        <div class="mb-3 col-md-12">
          <label for="name" class="form-label">{{ $t('messages.name') }} <span class="text-danger">*</span></label>
          <input type="text" id="name" class="form-control" v-model="name" :placeholder="$t('clinic.lbl_name')" />
          <span v-if="errorMessages['name']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['name']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors['name'] }}</span>
        </div>

        <div class="mb-3 col-md-12">
          <label for="bodychart_description" class="form-label">{{ $t('messages.description') }} <span class="text-danger">*</span></label>
          <textarea id="bodychart_description" rows="4" cols="12" class="form-control" v-model="description" :placeholder="$t('clinic.lbl_description')"></textarea>
          <span v-if="errorMessages['description']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
              0
            </ul>
          </span>
          <span class="text-danger">{{ errors['description'] }}</span>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-center gap-2 p-2">
        <div ref="editor"></div>
      </div>
      <span v-if="editorError" class="text-danger">{{ editorError }}</span>

      <div class="d-grid d-md-flex gap-3 p-3">

        <button type="button" class="btn btn-white d-block" @click="closeoffcanvas">
          {{ $t('messages.close') }}
        </button>
        <button class="btn btn-secondary" name="submit">
          <template v-if="IS_SUBMITED">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ $t('appointment.loading') }}
          </template>
          <template v-else> <i class="fa-solid fa-floppy-disk"></i> {{ $t('messages.save') }}</template>
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'

import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import ImageEditor from 'tui-image-editor'
import { useSelect } from '@/helpers/hooks/useSelect'
import { APPOINTMNET_BODYCHART_EDIT_URL, APPOINTMNET_BODYCHART_STORE_URL, TEMPLATE_IMAGE_LIST, BODYCHART_TEMPLATEDATA, APPOINTMNET_UPDATE_BODYCHART } from '../constant/clinic-appointment'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { readFile } from '@/helpers/utilities'
import 'tui-image-editor/dist/tui-image-editor.css' // Import CSS
import 'tui-color-picker/dist/tui-color-picker.css'

const { getRequest, updateRequest } = useRequest()
const editor = ref(null)
const ImageViewer = ref(null)

const props = defineProps({
  bodychart_id: { type: String, default: 0 },
  encounter_id: { type: String, default: 0 },
  appointment_id: { type: String, default: 0 },
  patient_id: { type: String, default: 0 },
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
function closeoffcanvas() {
  window.location.href = `${baseUrl}/app/encounter/encounter-detail-page/${props.encounter_id}#nav-contact`;
}

const fileUpload = async (e) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    initEditor(fileB64)
  })
}
const bodychart = reactive({
  menuBarPosition: '',
  menu: [],
  theme: ''
})

if (props.encounter_id > 0) {
  getRequest({ url: BODYCHART_TEMPLATEDATA, id: props.encounter_id }).then((res) => {
    bodychart.menuBarPosition = res.bodysetting.Menubar_position
    bodychart.theme = res.bodysetting.theme_mode
    const menudata = res.bodysetting.menu_items.split(',')
    menudata.forEach((item) => {
      bodychart.menu.push(item)
    })
    console.log(res.bodysetting.image)
    initEditor(res.bodysetting.image)
    setFormData(res.bodysetting)
  })
}

if (props.bodychart_id > 0) {
  getRequest({ url: APPOINTMNET_BODYCHART_EDIT_URL, id: props.bodychart_id }).then((res) => {
    setFormData(res.data)
    initEditor(res.data.file_url)
  })
}

// Validations
const validationSchema = yup.object({
  name: yup.string().required("Name is required"),
  description: yup.string().required('Description is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: selectedImages } = useField('selectedImages')

const errorMessages = ref({})

const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    description: '',
    selectedImages: ''
  }
}
function selectTemplate(value) {
  initEditor(value)

}
const editorInastance = ref(null)
const editorError = ref(null)

const initEditor = (image) => {
  console.log('Initializing editor with image:', image)
  editorInastance.value = new ImageEditor(editor.value, {
    includeUI: {
      loadImage: {
        path: image,
        name: 'SampleImage'
      },
      menu: bodychart.menu,
      theme: bodychart.theme == 'whiteTheme' ? whiteTheme : blackTheme,
      uiSize: {
        width: '1000px',
        height: '700px'
      },
      menuBarPosition: bodychart.menuBarPosition
    },
    cssMaxWidth: 700,
    cssMaxHeight: 500,
    selectionStyle: {
      cornerSize: 20,
      rotatingPointOffset: 70
    }
  })
  console.log('Editor initialized:', editorInastance.value)
}

const IS_SUBMITED = ref(false)
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      selectedImages: data.selectedImages
    }
  })
}

onMounted(() => {
  setFormData(defaultData())
  getTemplate()
})

const templateImageList = ref({ options: [], list: [] })
const getTemplate = () => {
  useSelect({ url: TEMPLATE_IMAGE_LIST }, { value: 'image', label: 'name' }).then((data) => (templateImageList.value = data))
}

//convert data url to file
function dataURLtoFile(dataURL, filename = 'image.png') {
  const base64Data = dataURL.split(',')[1]
  const binaryString = atob(base64Data)
  const uint8Array = new Uint8Array(binaryString.length)
  for (let i = 0; i < binaryString.length; i++) {
    uint8Array[i] = binaryString.charCodeAt(i)
  }
  const blob = new Blob([uint8Array], { type: 'image/png' })
  return new File([blob], filename, { type: 'image/png' })
}

const reloadDataTable = () => {
      const dataTable = $('#datatable').DataTable();
      if (dataTable) {
        dataTable.ajax.reload();
      }
    }

const formSubmit = handleSubmit((values) => {
  editorError.value = null

  // Check if an image is loaded in the editor
  if (!editor.value || editorInastance.value.toDataURL() === 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAACWCAYAAABkW7XSAAAAAXNSR0IArs4c6QAABGJJREFUeF7t1AEJAAAMAsHZv/RyPNwSyDncOQIECEQEFskpJgECBM5geQICBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAAYPlBwgQyAgYrExVghIgYLD8AAECGQGDlalKUAIEDJYfIEAgI2CwMlUJSoCAwfIDBAhkBAxWpipBCRAwWH6AAIGMgMHKVCUoAQIGyw8QIJARMFiZqgQlQMBg+QECBDICBitTlaAECBgsP0CAQEbAYGWqEpQAgQdWMQCX4yW9owAAAABJRU5ErkJggg==') {
    editorError.value = 'Please upload an image to the editor before submitting.'
    return
  }

  IS_SUBMITED.value = true
  const dataURL = editorInastance.value.toDataURL()
  values.file_url = dataURLtoFile(dataURL)
  values.encounter_id = props.encounter_id
  values.appointment_id = props.appointment_id
  values.patient_id = props.patient_id
  if (props.bodychart_id > 0) {

    updateRequest({ url: APPOINTMNET_UPDATE_BODYCHART, id: props.bodychart_id, body: values, type: 'file' }).then((res) => {
      if (res.status) {
        window.location.href = `${baseUrl}/app/encounter/encounter-detail-page/${props.encounter_id}#nav-contact`;
      } else {
        setFormData(defaultData())
      }
    })
  } else {
    updateRequest({ url: APPOINTMNET_BODYCHART_STORE_URL, id: props.encounter_id, body: values, type: 'file' }).then((res) => {
      if (res.status) {
        window.location.href = `${baseUrl}/app/encounter/encounter-detail-page/${props.encounter_id}#nav-contact`;
      } else {
        setFormData(defaultData())
      }
    })
  }
})



var blackTheme = {
  'common.bi.image': 'https://uicdn.toast.com/toastui/img/tui-image-editor-bi.png',
  'common.bisize.width': '251px',
  'common.bisize.height': '21px',
  'common.backgroundImage': 'none',
  'common.backgroundColor': '#1e1e1e',
  'common.border': '0px',

  // header
  'header.backgroundImage': 'none',
  'header.backgroundColor': 'transparent',
  'header.border': '0px',

  // load button
  'loadButton.backgroundColor': '#fff',
  'loadButton.border': '1px solid #ddd',
  'loadButton.color': '#222',
  'loadButton.fontFamily': "'Noto Sans', sans-serif",
  'loadButton.fontSize': '12px',

  // download button
  'downloadButton.backgroundColor': '#fdba3b',
  'downloadButton.border': '1px solid #fdba3b',
  'downloadButton.color': '#fff',
  'downloadButton.fontFamily': "'Noto Sans', sans-serif",
  'downloadButton.fontSize': '12px',

  // main icons
  'menu.normalIcon.color': '#8a8a8a',
  'menu.activeIcon.color': '#555555',
  'menu.disabledIcon.color': '#434343',
  'menu.hoverIcon.color': '#e9e9e9',
  'menu.iconSize.width': '24px',
  'menu.iconSize.height': '24px',

  // submenu icons
  'submenu.normalIcon.color': '#8a8a8a',
  'submenu.activeIcon.color': '#e9e9e9',
  'submenu.iconSize.width': '32px',
  'submenu.iconSize.height': '32px',

  // submenu primary color
  'submenu.backgroundColor': '#1e1e1e',
  'submenu.partition.color': '#3c3c3c',

  // submenu labels
  'submenu.normalLabel.color': '#8a8a8a',
  'submenu.normalLabel.fontWeight': 'lighter',
  'submenu.activeLabel.color': '#fff',
  'submenu.activeLabel.fontWeight': 'lighter',

  // checkbox style
  'checkbox.border': '0px',
  'checkbox.backgroundColor': '#fff',

  // range style
  'range.pointer.color': '#fff',
  'range.bar.color': '#666',
  'range.subbar.color': '#d1d1d1',

  'range.disabledPointer.color': '#414141',
  'range.disabledBar.color': '#282828',
  'range.disabledSubbar.color': '#414141',

  'range.value.color': '#fff',
  'range.value.fontWeight': 'lighter',
  'range.value.fontSize': '11px',
  'range.value.border': '1px solid #353535',
  'range.value.backgroundColor': '#151515',
  'range.title.color': '#fff',
  'range.title.fontWeight': 'lighter',

  // colorpicker style
  'colorpicker.button.border': '1px solid #1e1e1e',
  'colorpicker.title.color': '#fff'
}
var whiteTheme = {
  'common.bi.image': 'https://uicdn.toast.com/toastui/img/tui-image-editor-bi.png',
  'common.bisize.width': '251px',
  'common.bisize.height': '21px',
  'common.backgroundImage': './img/bg.png',
  'common.backgroundColor': '#fff',
  'common.border': '1px solid #c1c1c1',

  // header
  'header.backgroundImage': 'none',
  'header.backgroundColor': 'transparent',
  'header.border': '0px',

  // load button
  'loadButton.backgroundColor': '#fff',
  'loadButton.border': '1px solid #ddd',
  'loadButton.color': '#222',
  'loadButton.fontFamily': "'Noto Sans', sans-serif",
  'loadButton.fontSize': '12px',

  // download button
  'downloadButton.backgroundColor': '#fdba3b',
  'downloadButton.border': '1px solid #fdba3b',
  'downloadButton.color': '#fff',
  'downloadButton.fontFamily': "'Noto Sans', sans-serif",
  'downloadButton.fontSize': '12px',

  // main icons
  'menu.normalIcon.color': '#8a8a8a',
  'menu.activeIcon.color': '#555555',
  'menu.disabledIcon.color': '#434343',
  'menu.hoverIcon.color': '#e9e9e9',
  'menu.iconSize.width': '24px',
  'menu.iconSize.height': '24px',

  // submenu icons
  'submenu.normalIcon.color': '#8a8a8a',
  'submenu.activeIcon.color': '#555555',
  'submenu.iconSize.width': '32px',
  'submenu.iconSize.height': '32px',

  // submenu primary color
  'submenu.backgroundColor': 'transparent',
  'submenu.partition.color': '#e5e5e5',

  // submenu labels
  'submenu.normalLabel.color': '#858585',
  'submenu.normalLabel.fontWeight': 'normal',
  'submenu.activeLabel.color': '#000',
  'submenu.activeLabel.fontWeight': 'normal',

  // checkbox style
  'checkbox.border': '1px solid #ccc',
  'checkbox.backgroundColor': '#fff',

  // rango style
  'range.pointer.color': '#333',
  'range.bar.color': '#ccc',
  'range.subbar.color': '#606060',

  'range.disabledPointer.color': '#d3d3d3',
  'range.disabledBar.color': 'rgba(85,85,85,0.06)',
  'range.disabledSubbar.color': 'rgba(51,51,51,0.2)',

  'range.value.color': '#000',
  'range.value.fontWeight': 'normal',
  'range.value.fontSize': '11px',
  'range.value.border': '0',
  'range.value.backgroundColor': '#f5f5f5',
  'range.title.color': '#000',
  'range.title.fontWeight': 'lighter',

  // colorpicker style
  'colorpicker.button.border': '0px',
  'colorpicker.title.color': '#000'
}
</script>

<style lang="scss">
$background-color_1: #fff;
$background-color_2: #151515;
.whiteTheme {
  .tui-image-editor-container {
    .tui-image-editor-help-menu .tui-image-editor-controls {
      background-color: $background-color_1 !important;
    }
  }
}

.tui-image-editor-header-logo {
  display: none;
}
.tui-image-editor-header-buttons {
  display: none;
}
.tui-image-editor-container .tui-image-editor-controls-logo,
.tui-image-editor-container .tui-image-editor-controls-buttons {
  display: none !important;
}
.tui-image-editor-container.top .tui-image-editor-controls {
  display: flex;
  align-items: center;
}
</style>
