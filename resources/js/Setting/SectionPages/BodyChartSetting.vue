<template>
  <form @submit="formSubmit">

    <CardTitle :title="$t('setting_language_page.lbl_body_chart')" icon="fa-solid fa-screwdriver-wrench"></CardTitle>

    <div class="row">

      <div class="col-md-6 mb-2">
        <div class="form-group">
          <label class="form-label">{{ $t('setting_language_page.lbl_theme_mode') }}</label>
          <Multiselect id="theme_mode" v-model="theme_mode" :value="theme_mode" :placeholder="$t('setting_language_page.lbl_theme_mode')" v-bind="theme_mode_data"></Multiselect>
          <span class="text-danger">{{ errors.theme_mode }}</span>
        </div>
      </div>

      <div class="col-md-6 mb-2">
        <div class="form-group">
          <label class="form-label" for="menu_items">{{ $t('setting_language_page.lbl_menu_items') }}</label>
          <Multiselect id="menu_items" v-model="menu_items" :placeholder="$t('setting_language_page.lbl_menu_items')" :multiple="true" :value="menu_items" v-bind="multiSelectOption" :options="menu_items_data.options"> </Multiselect>
        </div>
      </div>
      
      <div class="col-md-6 mb-2">
        <div class="form-group">
          <label class="form-label">{{ $t('setting_language_page.lbl_image_handling') }}</label>
          <Multiselect id="image_handling" v-model="image_handling" :placeholder="$t('setting_language_page.lbl_image_handling')" :value="image_handling" v-bind="image_handling_data"></Multiselect>
          <span class="text-danger">{{ errors.image_handling }}</span>
        </div>
      </div>

      <div class="col-md-6 mb-2">
        <div class="form-group">
          <label class="form-label">{{ $t('setting_language_page.Menubar_position') }}</label>
          <Multiselect id="Menubar_position" v-model="Menubar_position" :placeholder="$t('setting_language_page.Menubar_position')" :value="Menubar_position" v-bind="Menubar_position_data"></Multiselect>
          <span class="text-danger">{{ errors.Menubar_position }}</span>
        </div>
      </div>

        </div>
    <div>


      <h4>{{ $t('setting_language_page.image_template_section_title') }}</h4>
          <div class="table-responsive">
            <table class="table dataTable no-footer">
                        <thead>
                          <tr>
                            <th>{{ $t('setting_language_page.tbl_header_name') }}</th>
                            <th>{{ $t('setting_language_page.tbl_header_image') }}</th>
                            <th>{{ $t('setting_language_page.tbl_header_default') }}</th>
                            <th>{{ $t('setting_language_page.tbl_header_action') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(image, index) in selectedImages" :key="index">
                            <td><InputField type="text" class="input-select-image-box"  v-model="image.name"/></td>
                            <td><input type="file"  @change="handleFileUpload($event, index)" class="form-control"/>
                            <span>{{ image.image_name }}</span></td>
                            <!-- <td> <input type="radio" v-model="image.default" @input="setDefaultImage(index)" class="form-check-input"/></td> -->
                              <td> <input type="radio"
                                        :id="'defaultImage_' + index"
                                        :name="'defaultImage'"
                                        :value="image.default"
                                        v-model="image.default"
                                        @input="setDefaultImage(index)"
                                        class="form-check-input"/>
                                  <label :for="'defaultImage_' + index">{{ $t('setting_language_page.lbl_default') }}</label></td>
                            <!-- <td><input type="checkbox" v-model="image.default"@change="setDefaultImage(index)" class="form-check-input"/> Default</td> -->
                            <td><button type="button" @click="removeImage(index)" class="btn btn-danger">{{ $t('setting_language_page.btn_remove') }}</button></td>
                          </tr>
                        </tbody>
            </table>
          </div>
          <button @click="addMore" type="button" class="btn btn-info">{{ $t('setting_language_page.btn_add_new_images') }}</button>
</div>

      <div class="row py-4">
        <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
      </div>
  </form>
</template>
<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { onMounted, ref,reactive } from 'vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL,TEMPLATE_IMAGE_LIST } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { createRequest,readFile,confirmcancleSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
import * as yup from 'yup'
const IS_SUBMITED = ref(false)
const { storeRequest, listingRequest} = useRequest()


  // options
  const multiSelectOption = ref({
    mode: 'tags',
    closeOnSelect: true,
    searchable: true
  })


  const selectedImages = reactive([]);
  const unique_Id = () => {
    return Date.now().toString(36) + Math.random().toString(36).substring(2);
  };
  const defaultImages = () => {
  return { id:'', name: '', uniqueId:unique_Id(), file: '', image_name:'', default: false }
}


const validationSchema = yup.object({

});

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const setImageData=(body_image)=>{
  if (body_image !== null && body_image.length > 0){
  body_image.forEach((image, index)=>{
    const checkboxValue = image.default === 1 ? true : false;
    selectedImages.push({ id:image.id,imagesrc:image.image,name: image.name,uniqueId:image.uniqueId,image_name:image.image_name, file: image.image, default:image.default });
  })
  }
}
  const addMore = () => {
    selectedImages.push(defaultImages());
  };

  const theme_mode_data = ref({
    searchable: true,
    options: [
      { label: 'Black Theme', value: 'blackTheme' },
      { label: 'White Theme', value: 'whiteTheme' },
    ],
    closeOnSelect: true,
    createOption: true
  })
  const Menubar_position_data = ref({
    searchable: true,
    options: [
      { label: 'Top', value: 'top' },
      { label: 'Bottom', value: 'bottom' },
      { label: 'Right', value: 'right' },
      { label: 'Left', value: 'left' },
    ],
    closeOnSelect: true,
    createOption: true
  })
  const menu_items_data = ref({
    options: [
      { label: 'Crop', value: 'crop' },
      { label: 'Flip', value: 'flip' },
      { label: 'Rotation', value: 'rotate' },
      { label: 'Drawing', value: 'draw' },
      { label: 'Shape', value: 'shape' },
      { label: 'Icon', value: 'icon' },
      { label: 'Text', value: 'text' },
      { label: 'Mask Filter', value: 'mask' },
  ]
  });
  const image_handling_data=ref({
    searchable: true,
    options: [
      { label: 'Create New Image', value: 'new_image' },
      { label: 'Used saved Image', value: 'Saved_image' },
    ],
    closeOnSelect: true,
    createOption: true

  })

  const setDefaultImage = (selectedIndex) => {
  selectedImages.forEach((image, index) => {
    if (index === selectedIndex) {
      image.default = true;
    }else{
      image.default = false;
    }
  });
}


  const defaultData = () => {
  errorMessages.value = {}
  return {
    theme_mode: '',
      Menubar_position:'',
      menu_items:[],
      image_handling:'',
  }
}

const data='theme_mode,Menubar_position,image_handling,menu_items,body_image'
onMounted(() => {
  createRequest(TEMPLATE_IMAGE_LIST()).then((response) => {
    console.log(response)
    setImageData(response)

  })


  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })

})

//  Reset Form
const setFormData = (data) => {
  const menuItems = data.menu_items.split(',');
  //selectedImages.value = data.images
  resetForm({
    values: {
      theme_mode: data.theme_mode,
      Menubar_position:data.Menubar_position,
      menu_items:menuItems,
      image_handling:data.image_handling,
    }
  })
}

    const handleFileUpload = (event, index) => {
      console.log(index);
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          const updatedImage = { ...selectedImages[index] };
            updatedImage.file = file;
            updatedImage.image_name=file.name;
            console.log(file.name);
            selectedImages[index] = updatedImage;
            console.log(selectedImages);
        };
        reader.readAsDataURL(file);
      }

    };

    const removeImage = (index) => {

      console.log(selectedImages);
      confirmcancleSwal({ title: 'Are you sure you want to delete it?' }).then((result) => {
        
        if(result.isConfirmed){
          selectedImages.splice(index, 1);
        }


     })
    };


  const errorMessage = ref(null)
  const {value: theme_mode } = useField('theme_mode')
  const {value: Menubar_position}=useField('Menubar_position')
  const {value: menu_items} =useField('menu_items')
  const {value: image_handling}=useField('image_handling')

  // message
  const display_submit_message = (res) => {
    IS_SUBMITED.value = false
    if (res.status) {
      window.successSnackbar(res.message)
    } else {
      window.errorSnackbar(res.message)
    }
  }


  //Form Submit
  const formSubmit = handleSubmit((values) => {
    console.log(selectedImages);
      IS_SUBMITED.value = true
      selectedImages.forEach((image, index) => {
          values[index] = image.file;
        });

      values.body_chart_images = JSON.stringify(selectedImages)
      console.log(values);
              const newValues = {}
              Object.keys(values).forEach((key) => {
                newValues[key] = values[key] || ''
              })
              storeRequest({ url: STORE_URL, body: values, type:'file'}).then((res) => display_submit_message(res))
  })
</script>
