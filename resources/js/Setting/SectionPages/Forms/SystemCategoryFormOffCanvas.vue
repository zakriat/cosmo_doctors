<template>
    <form @submit="formSubmit">
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="offcanvas-title">
                <template v-if="currentId != 0">
                  <span>{{ $t('currency.lbl_edit') }}</span>
                </template>
                <template v-else>
                  <span>{{ $t('currency.lbl_add') }}</span>
                </template>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <div class="text-center">
                            <img :src="ImageViewer || defaultImage" alt="feature-image" class="img-fluid mb-2 avatar-140 avatar-rounded" />
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <input type="file" ref="profileInputRef" class="form-control d-none" id="file_url" name="file_url" @change="changeLogo" accept=".jpeg, .jpg, .png, .gif" />
                                <label class="btn btn-info" for="file_url">{{ $t('messages.upload') }}</label>
                                <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()" v-if="ImageViewer" />
                            </div>
                        </div>
                    </div>
                   
                <div class="form-group" >
                    <label for="category" class="form-label">{{$t('category.lbl_parent_category')}}</label>
                    <Multiselect v-bind="singleSelectOption" v-model="parent_id" :placeholder="$t('category.category_name')" :value="parent_id" id="parent_id" :options="categories"></Multiselect>
                    <span v-if="errorMessages['parent_id']">
                        <ul class="text-danger">
                            <li v-for="err in errorMessages['parent_id']" :key="err">{{ err }}</li>
                        </ul>
                    </span>
                    <span class="text-danger">{{ errors.parent_id }}</span>
                </div>
                <InputField :is-required="true" :placeholder="$t('category.subcategory_name')" :label="$t('category.lbl_category_name')" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="category-status">{{$t('category.lbl_status')}}</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox" v-model="status" />
                    </div>
                    </div>
                </div>
                  
                </div>
              </div>
            </div>
            <div class="border-top">
              <div class="d-grid d-md-flex gap-3 p-3">
                <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                  <i class="fa-solid fa-angles-left"></i>
                  {{$t('messages.cancel')}}
                </button>
                <button class="btn btn-primary d-block">
                  <i class="fa-solid fa-floppy-disk"></i>
                  {{$t('messages.save')}}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue'
  import { STORE_URL, EDIT_URL, UPDATE_URL,INDEX_LIST_URL } from '@/vue/constants/system_category'
  import { useField, useForm } from 'vee-validate'
  import { useRequest , useOnModalHide} from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { buildMultiSelectObject,readFile } from '@/helpers/utilities'

  useOnModalHide('exampleModal', () => setFormData(defaultData()))
 
  onMounted(() => {
    getCategories(1)
    setFormData(defaultData())
  })
  
  const emit = defineEmits(['onSubmit'])
  
  // props
  const props = defineProps({
    id: { type: Number, default: 0 },
    labelValue: 123456789
  })
  
  const { getRequest, storeRequest, updateRequest,listingRequest } = useRequest()
  
  const singleSelectOption = ref({
    closeOnSelect: true,
    searchable: true
  })
  const categories = ref([])
  const category_name = ref(null)

  

  const getCategories = (value) => {
    listingRequest({ url: INDEX_LIST_URL }).then((res) => (categories.value= buildMultiSelectObject(res, { value: 'id', label: 'name' })))
  }

  
  const currentId = ref(props.id)
  
  // Edit Form Or Create Form
  watch(
    () => props.id,
    (value) => {
      currentId.value = value
      if (value > 0) {
        getRequest({ url: EDIT_URL, id: value }).then((res) => {
          if (res.status && res.data) {
            setFormData(res.data)
          }
        })
      } else {
        setFormData(defaultData())
      }
    }
  )

  let profileInputRef = ref(null)
  const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
    let file = e.target.files[0]
    await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64
      profileInputRef.value.value = ''
    })
    changeFile.value = file
  }

  const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}
const defaultImage = 'https://dummyimage.com/600x300/cfcfcf/000000.png';
const ImageViewer = ref(defaultImage)
const changeLogo = (e) => fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: file_url })
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: file_url })
  // Default FORM DATA
  const defaultData = () => {
    errorMessages.value = {}
    return {
        name: '',
        parent_id: props.categoryId ?? null,
        status: true,
        file_url: null,
    }
  }
  
  
  //  Reset Form
  const setFormData = (data) => {
    ImageViewer.value = data.file_url
    category_name.value = data.category_name
    resetForm({
      values: {
        name: data.name,
        parent_id: data.parent_id,
        status: data.status ? true : false,
        file_url:  data.file_url,
        
      }
    })
  }
  const errorMessages = ref({})
  // Reload Datatable, SnackBar Message, Alert, Offcanvas Close
  const reset_datatable_close_offcanvas = (res) => {
    if (res.status) {
      window.successSnackbar(res.message)
      bootstrap.Modal.getInstance('#exampleModal').hide()
      setFormData(defaultData())
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
    emit('onSubmit')
  }
  const numberRegex = /^\d+$/;
  // Validations
  const validationSchema = yup.object({



    name: yup.string()
      .required('Sub Category name is a required field')
      .test('is-string', 'Only strings are allowed', (value) => {
        // Regular expressions to disallow special characters and numbers
        const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/;
        return !specialCharsRegex.test(value) && !numberRegex.test(value);
      }),

  })

  
  const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

  const { value: name } = useField('name')
  const { value: parent_id } = useField('parent_id')
  const { value: status } = useField('status')
  const { value: file_url } = useField('file_url')
  
  
  
  // Form Submit
  const formSubmit = handleSubmit((values) => {
    if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
    } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
    }
    })
  </script>
  