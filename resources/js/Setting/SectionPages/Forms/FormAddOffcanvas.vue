<template>
  <form @submit="formSubmit">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="offcanvas-title">
              <template v-if="currentId != 0">
                <span>{{ $t('messages.lbl_edit') }}</span>
              </template>
              <template v-else>
                <span>{{ $t('messages.lbl_add') }}</span>
              </template>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-4">
                <div class="mb-2">
                  <div class="d-flex justify-content-between align-items-center gap-5 mb-2">
                    <span class="btn btn-primary" @click="changeForm('previous')"> <i class="fa fa-angle-left"></i></span>
                    <h5>{{ getCurrentFormName }}</h5>
                    <span class="btn btn-primary" @click="changeForm('next')"><i class="fa fa-angle-right"></i></span>
                  </div>

                  <!-- Form 1 -->
                  <div v-if="currentForm === 'form1'">
                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_module_type') }} <span class="text-danger">*</span></label>
                      <Multiselect id="module_type" v-model="module_type" :disabled="currentId > 0" :placeholder="$t('messages.lbl_select_module')" :value="module_type" v-bind="module_type_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.module_type }}</span>
                    </div>
                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_form_icon') }}</label>
                      <InputField :placeholder="$t('messages.lbl_form_icon')" v-model="form_icon" :errorMessage="errors.form_icon"></InputField>
                    </div>
                    <div class="form-group" v-if="module_type == 'appointment_module'">
                      <label class="form-label">{{ $t('messages.lbl_appointment_status') }}</label>
                      <Multiselect id="appiontment_status" v-model="appiontment_status" :placeholder="$t('messages.lbl_select_module')" :value="appiontment_status" v-bind="appiontment_status_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.appiontment_status }}</span>
                    </div>
                    <div class="form-group" v-if="module_type == 'appointment_module'">
                      <label class="form-label">{{ $t('messages.lbl_show_in') }}</label>
                      <Multiselect id="show_in" v-model="show_in" :placeholder="$t('messages.lbl_show_in')" :value="show_in" v-bind="show_in_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.show_in }}</span>
                      <label class="text-secondry">Note: If encounter is selected, form will only be displayed if there has been an appointment encounter</label>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="clinic_center">{{ $t('clinic.lbl_select_clinic_center') }}</label>
                      <Multiselect id="clinic_id" v-model="clinic_id" :value="clinic_id" :multiple="true" :placeholder="$t('clinic.lbl_select_clinic_center')" v-bind="multiSelectOption" :options="clinicCenter.options" class="form-group"> </Multiselect>
                      <label class="text-secondry">Note: To show form to all clinics, please leave the selection blank.</label>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="category-status">{{ $t('clinic.lbl_status') }}</label>
                      <div class="d-flex align-items-center justify-content-between gap-3 form-control">
                        <label class="form-label m-0" for="category-status">{{ $t('clinic.lbl_status') }}</label>
                        <div class="form-check form-switch">
                          <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Form 2 -->
                  <div v-if="currentForm === 'form2'">
                    <!-- Label -->
                    <div class="form-group" v-if="input_type == 'text' || input_type == 'number' || input_type == 'textarea' || input_type == 'multi_select' || input_type == 'select' || input_type == 'radio' || input_type == 'checkbox' || input_type == 'file' || input_type == 'calender' || input_type == 'heading'">
                      <label class="form-label">{{ $t('messages.lbl_lable') }} <span class="text-danger">*</span></label>
                      <InputField :placeholder="$t('messages.lbl_lable')" v-model="label" :errorMessage="errors.label"></InputField>
                    </div>

                    <!-- Input Type -->
                    <!-- Input Type -->
                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_input_type') }} <span class="text-danger">*</span></label>
                      <Multiselect id="input_type" v-model="input_type" :placeholder="$t('messages.lbl_input_type')" :options="input_type_data.options" :searchable="true" :close-on-select="true" value-prop="value" label-prop="label" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.input_type }}</span>
                    </div>

                    <!-- File Type -->
                    <div class="form-group" v-if="input_type === 'file'">
                      <label class="form-label">{{ $t('messages.lbl_file_type') }} <span class="text-danger">*</span></label>
                      <Multiselect id="file_type" v-model="file_type" :placeholder="$t('messages.lbl_select_file_type')" :value="file_type" v-bind="file_type_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.file_type }}</span>
                    </div>

                    <!-- Options -->
                    <div class="form-group" v-if="input_type === 'multi_select' || input_type === 'select' || input_type === 'radio' || input_type === 'checkbox'">
                      <label class="form-label">{{ $t('messages.lbl_option') }}</label>
                      <Multiselect id="option" v-model="option" :placeholder="$t('messages.lbl_select_option')" :value="option" v-bind="option_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.option }}</span>
                    </div>

                    <!-- Heading Title -->
                    <!-- <div class="form-group" v-if="input_type==='heading'">
                        <label class="form-label">{{ $t('messages.lbl_heading_title') }} <span class="text-danger">*</span></label>
                        <InputField :placeholder="$t('messages.lbl_heading_title')" v-model="heading_title" :errorMessage="errors.heading_title"></InputField>
                      </div>  -->

                    <!-- Heading Tag -->
                    <div class="form-group" v-if="input_type === 'heading'">
                      <label class="form-label">{{ $t('messages.lbl_heading_tag') }} <span class="text-danger">*</span></label>
                      <Multiselect id="type" v-model="placeholder" :placeholder="$t('messages.lbl_select_heading_tag')" :value="placeholder" v-bind="heading_tag_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.type }}</span>
                    </div>

                    <!-- Placeholder -->
                    <div class="form-group" v-if="input_type === 'text' || input_type === 'number' || input_type === 'textarea'">
                      <label class="form-label">{{ $t('messages.lbl_placeholder') }}</label>
                      <InputField :placeholder="$t('messages.lbl_placeholder')" v-model="placeholder" :errorMessage="errors.placeholder"></InputField>
                    </div>

                    <!-- Validation -->
                    <div class="form-group" v-if="input_type === 'text' || input_type === 'number' || input_type === 'textarea' || input_type === 'multi_select' || input_type === 'select' || input_type === 'radio' || input_type === 'checkbox' || input_type === 'file'">
                      <label class="form-label" for="validation-status">{{ $t('messages.lbl_validation') }}</label>
                      <div class="d-flex align-items-center justify-content-between gap-3 form-control">
                        <label class="form-label m-0" for="validation-status">{{ $t('messages.lbl_mandatory') }}</label>
                        <div class="form-check form-switch">
                          <input class="form-check-input" :value="1" :checked="validation_status === 1" name="validation_status" id="validation-status" type="checkbox" @change="(e) => (validation_status = e.target.checked ? 1 : 0)" />
                        </div>
                      </div>
                    </div>

                    <!-- Class -->
                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_class') }}</label>
                      <InputField :placeholder="$t('messages.lbl_class_place')" v-model="class_value" :errorMessage="errors.class_value"></InputField>
                    </div>
                  </div>

                  <!-- Form 3 -->
                  <div v-if="currentForm === 'form3'">
                    <!-- Field Type Selection -->
                    <div class="card-body">
                      <div class="card-container">
                        <div v-for="field in inputType" :key="field.id" class="card d-flex justify-start gap-1 align-items-center cursor-pointer" @click="addNewField(field)">
                          <div class="card-icon">
                            <i :class="field.icon"></i>
                          </div>
                          <div class="card-label">{{ field.label }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Form 4 -->
                  <div v-if="currentForm === 'form4'">
                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_form_title') }} <span class="text-danger">*</span></label>
                      <InputField :placeholder="$t('messages.lbl_form_title')" v-model="form_title" :errorMessage="errors.form_title"></InputField>
                    </div>

                    <div class="form-group">
                      <label class="form-label">{{ $t('messages.lbl_title_color') }} <span class="text-danger">*</span></label>
                      <Multiselect id="title_color" v-model="title_color" :placeholder="$t('messages.lbl_title_color')" :value="title_color" v-bind="title_color_data" class="form-group"></Multiselect>
                      <span class="text-danger">{{ errors.title_color }}</span>
                    </div>

                    <!-- <div class="form-group">
                        <label class="form-label">{{ $t('messages.lbl_title_alignment') }} <span class="text-danger">*</span></label>
                        <Multiselect id="title_alignment" v-model="title_alignment" :placeholder="$t('messages.lbl_title_alignment')" :value="title_alignment" v-bind="title_alignment_data" class="form-group"></Multiselect>
                        <span class="text-danger">{{ errors.title_alignment }}</span>
                      </div> -->
                  </div>
                </div>
              </div>
              <div class="col-8">
                <!-- Dynamic Form with Draggable Functionality -->
                <div>
                  <div class="col-12 p-2 mb-2" id="full_form" ref="fullfromDiv">
                    <span><i class="fas fa-edit delete-icon text-primary" @click="OpenForm()"></i></span>

                    <div class="col-12 p-2 mb-2 d-flex" id="From_title" ref="fromTitleDiv">
                      <h4 :class="[title_alignment, title_color]" class="gap-1 col-10">{{ form_title }}</h4>
                      <span><i class="fas fa-edit m-5 delete-icon text-primary" @click="OpenFormtitleEdit()"></i></span>
                    </div>

                    <div class="form-fields-container" @dragover.prevent @drop="handleDrop">
                      <div v-for="(element, index) in formFields" :key="element.id" class="form-field" draggable="true" @dragstart="handleDragStart(index)" @dragenter.prevent="handleDragEnter(index)" @dragend="handleDragEnd">
                        <!--
                        <div  v-if="field.type==='text' || field.type==='number' || field.type==='textarea'">
                        <label>{{ field.label }} <span v-if="field.validation_status==1" class="text-danger">*</span></label>
                        <input :type="field.type" :placeholder="field.placeholder" class="form-control" />
                        </div> -->

                        <!-- Field Type Specific Input -->
                        <!-- Text Field -->
                        <div v-if="element.type === 'text'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <input type="text" :id="'text_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
                        </div>

                        <!-- Number Field -->
                        <div v-else-if="element.type === 'number'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <input type="number" :id="'number_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder" />
                        </div>

                        <!-- Checkbox -->
                        <div v-else-if="element.type === 'checkbox'" :ref="'field_' + element.id">
                          <label>
                            {{ element.label }}
                            <span v-if="element.validation_status == 1" class="text-danger">*</span>
                          </label>
                          <div v-for="(option, index) in element.option" :key="index" class="form-check">
                            <input type="checkbox" :id="'checkbox_' + element.id + '_' + option" :value="option" v-model="element.value" class="form-check-input" />
                            <label :for="'checkbox_' + element.id + '_' + option" class="form-check-label">
                              {{ option }}
                            </label>
                          </div>
                        </div>

                        <!-- TextArea -->
                        <div v-else-if="element.type === 'textarea'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <textarea :id="'textarea_' + element.id" v-model="element.value" class="form-control" :placeholder="element.placeholder"></textarea>
                        </div>

                        <!-- File Upload -->
                        <div v-else-if="element.type === 'file'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <input type="file" :id="'file_' + element.id" @change="handleFileUpload($event, element.id)" class="form-control" />
                        </div>

                        <!-- Multi Select -->
                        <div v-else-if="element.type === 'multi_select'" :ref="'field_' + element.id">
                          <label
                            >{{ element.label }}
                            <span v-if="element.validation_status == 1" class="text-danger">*</span>
                          </label>
                          <select multiple :id="'multiselect_' + element.id" class="form-control">
                            <option v-if="!element.option.length">No options available</option>
                            <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
                              {{ option }}
                            </option>
                          </select>
                        </div>

                        <!-- Select -->
                        <div v-else-if="element.type === 'select'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status === 1" class="text-danger">*</span></label>
                          <select :id="'select_' + element.id" class="form-control">
                            <option v-if="!element.option.length">No options available</option>
                            <option v-else v-for="(option, index) in element.option" :key="index" :value="option">
                              {{ option }}
                            </option>
                          </select>
                        </div>

                        <!-- Radio -->
                        <div v-else-if="element.type === 'radio'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <div v-for="(option, index) in element.option" :key="index" class="form-check">
                            <input type="radio" :id="'radio_' + element.id + '_' + option" :value="option" class="form-check-input" />
                            <label :for="'radio_' + element.id + '_' + option" class="form-check-label">
                              {{ option }}
                            </label>
                          </div>
                        </div>

                        <!-- Calendar -->
                        <div v-else-if="element.type === 'calender'" :ref="'field_' + element.id">
                          <label>{{ element.label }} <span v-if="element.validation_status == 1" class="text-danger">*</span></label>
                          <input type="date" :id="'calendar_' + element.id" v-model="element.value" class="form-control" />
                        </div>

                        <!-- Heading -->
                        <div v-else-if="element.type === 'heading'" :ref="'field_' + element.id">
                          <component :is="element.placeholder">
                            {{ element.label }}
                          </component>
                        </div>

                        <!-- Hr Tag -->
                        <div v-else-if="element.type === 'hr_tag'" :ref="'field_' + element.id">
                          <hr />
                        </div>

                        <!-- Edit and Delete Icons -->
                        <div class="icon-container d-flex top-0 end-0">
                          <span><i class="fas fa-edit me-4 delete-icon text-primary" @click="openEditForm(element)"></i></span>
                          <span><i class="fas fa-trash-alt delete-icon" @click="removeField(element.id)"></i></span>
                        </div>

                        <!--
                        <div  v-if="field.type=='file'">
                        <label>{{ field.label }} <span v-if="field.validation_status==1" class="text-danger">*</span></label>
                        <input :type="field.type" :placeholder="field.placeholder" @change="handleFileUpload($event, field.id)" class="form-control" />
                        </div>

                        <div v-if="field.type=='select'">
                        <select class="form-control">
                            <option v-for="option in element.options" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>

                        </div>

                        <div v-if="field.type=='multi_select'">
                          <select class="form-control" multiple>
                              <option v-for="option in element.options" :key="option.value" :value="option.value">
                                {{ option.label }}
                              </option>
                          </select>

                        </div> -->
                      </div>
                    </div>
                    <h6 class="text-primary" @click="addNewFieldData()">+ Add</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="border-top">
            <div class="d-grid d-md-flex gap-3 p-3">
              <button class="btn btn-primary d-block">
                <i class="fa-solid fa-floppy-disk"></i>
                {{ $t('messages.save') }}
              </button>
              <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                <i class="fa-solid fa-angles-left"></i>
                {{ $t('messages.cancel') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watch, computed, reactive, watchEffect } from 'vue'
import { STORE_URL, EDIT_URL, UPDATE_URL, CLINIC_LIST } from '@/vue/constants/custom_form'
import { useField, useForm } from 'vee-validate'
import { useRequest, useOnModalHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import draggable from 'vuedraggable'
import { nextTick } from 'vue'

const formFields = ref([
  { id: 1, label: 'Example field 1', type: 'text', placeholder: 'Enter field 1', validation_status: 0, option: [] },
  { id: 2, label: 'Example field 2', type: 'text', placeholder: 'Enter field 2', validation_status: 0, option: [] },
  { id: 3, label: 'Example field 3', type: 'file', file_type: 'jpeg', placeholder: 'Enter field 3', validation_status: 0, option: [] }
])

let draggedItemIndex = null

const handleDragStart = (index) => {
  draggedItemIndex = index
}

const handleDragEnter = (index) => {
  // Swap the dragged item with the current item
  const draggedItem = formFields.value[draggedItemIndex]
  formFields.value.splice(draggedItemIndex, 1) // Remove dragged item from the original position
  formFields.value.splice(index, 0, draggedItem) // Insert dragged item at the new position
  draggedItemIndex = index // Update the index to the new position
}

const handleDragEnd = () => {
  draggedItemIndex = null
}

const handleDrop = () => {
  console.log('Dropped')
}

const props = defineProps({
  id: { type: Number, default: 0 }
})
const inputType = ref([
  { id: 'text', icon: 'fas fa-font', label: 'Text Field' },
  { id: 'number', icon: 'fas fa-hashtag', label: 'Number' },
  { id: 'checkbox', icon: 'fas fa-check-square', label: 'Checkbox' },
  { id: 'textarea', icon: 'fas fa-align-left', label: 'TextArea' },
  { id: 'file', icon: 'fas fa-upload', label: 'File Upload' },
  { id: 'multi_select', icon: 'fas fa-list-ul', label: 'Multi Select' },
  { id: 'select', icon: 'fas fa-list', label: 'Select' },
  { id: 'radio', icon: 'fas fa-dot-circle', label: 'Radio' },
  { id: 'calender', icon: 'fas fa-calendar-alt', label: 'Calendar' },
  { id: 'heading', icon: 'fas fa-heading', label: 'Heading' },
  { id: 'hr_tag', icon: 'fas fa-minus', label: 'Hr Tag' }
])

const addNewField = (field) => {
  const newId = formFields.value.length > 0 ? Math.max(...formFields.value.map((f) => f.id)) + 1 : 1

  const newField = {
    id: newId,
    type: field.id,
    label: field.label,
    placeholder: '',
    option: [],
    validation_status: 0
  }

  formFields.value.push(newField)

  console.log(formFields.value)
}

// const openEditForm = (element) => {
//   // Set the current form to form2
//   currentForm.value = 'form2';
//   // Set the values for Form 2
//   label.value = element.label;
//   input_type.value = element.type;
// };

// Remove a field by its ID
const removeField = (fieldId) => {
  formFields.value = formFields.value.filter((field) => field.id !== fieldId)
}

const currentForm = ref('form1')

const changeForm = (direction) => {
  if (direction === 'previous') {
    if (currentForm.value === 'form1') {
      //currentForm.value = 'form4';
      OpenFormtitleEdit()
    } else if (currentForm.value === 'form2') {
      OpenForm()
      // currentForm.value = 'form1';
    } else if (currentForm.value === 'form3') {
      currentForm.value = 'form2'
    } else if (currentForm.value === 'form4') {
      currentForm.value = 'form3'
    }
  } else if (direction === 'next') {
    if (currentForm.value === 'form1') {
      currentForm.value = 'form2'
    } else if (currentForm.value === 'form2') {
      currentForm.value = 'form3'
    } else if (currentForm.value === 'form3') {
      // currentForm.value = 'form4';
      OpenFormtitleEdit()
    } else if (currentForm.value === 'form4') {
      OpenForm()
      // currentForm.value = 'form1';
    }
  }
}

const getCurrentFormName = computed(() => {
  switch (currentForm.value) {
    case 'form1':
      return 'Form settings'
    case 'form2':
      return 'Edit field'
    case 'form3':
      return 'Field list'
    case 'form4':
      return 'Edit form name'
    default:
      return ''
  }
})

const fromTitleDiv = ref(null)
const fullfromDiv = ref(null)

const OpenFormtitleEdit = () => {
  currentForm.value = 'form4'
  fullfromDiv.value.style.border = 'none'

  if (fromTitleDiv.value) {
    fromTitleDiv.value.style.border = '2px dotted #000'
  }
}

const OpenForm = () => {
  currentForm.value = 'form1'

  fromTitleDiv.value.style.border = 'none'

  if (fullfromDiv.value) {
    fullfromDiv.value.style.border = '2px dotted #000'
  }
}

const addNewFieldData = () => {
  currentForm.value = 'form3'
  fromTitleDiv.value.style.border = 'none'
  fullfromDiv.value.style.border = 'none'
}

const selectedField = ref({})

watchEffect(() => {
  if (selectedField.value != null) {
    const index = formFields.value.findIndex((field) => field.id === selectedField.value.id)
    if (index !== -1) {
      formFields.value[index] = {
        ...formFields.value[index],
        label: label.value,
        file_type: file_type.value,
        type: input_type.value,
        placeholder: placeholder.value,
        validation_status: Number(validation_status.value), // Ensure it's a number
        option: [...(option.value || [])]
      }
    }
  }
})

const openEditForm = (element) => {
  selectedField.value = element
  currentForm.value = 'form2'
  console.log('element', element)
  // Set values from the selected field
  file_type.value = Array.isArray(element.file_type) ? element.file_type : [element.file_type]

  input_type.value = element.type
  label.value = element.label
  placeholder.value = element.placeholder
  validation_status.value = element.validation_status
  option.value = element.option ? [...element.option] : []
  option_data.value.options = element.option ? [...element.option] : []
  nextTick(() => {
    const multiselect = document.querySelector('#input_type')
    if (multiselect) {
      multiselect.__vue__?.refresh()
    }
  })
}
const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
  multiple: true
})

const module_type_data = ref({
  searchable: true,
  options: [
    { label: 'Doctor Module', value: 'doctor_module' },
    { label: 'Patient Module', value: 'patient_module' },
    { label: 'Patient Encounter Module', value: 'patient_encounter_module' },
    { label: 'Appointment Module', value: 'appointment_module' }
  ],
  closeOnSelect: true
})

const appiontment_status_data = ref({
  searchable: true,
  options: [
    { label: 'Pending', value: 'pending' },
    { label: 'Confirm', value: 'confirm' },
    { label: 'Check In', value: 'check_in' },
    { label: 'Complate', value: 'checkout' }
  ],
  closeOnSelect: true,
  multiple: true,
  mode: 'tags'
})

const option_data = ref({
  searchable: true,
  options: [],
  closeOnSelect: true,
  createOption: true,
  multiple: true,
  mode: 'tags'
})

const show_in_data = ref({
  searchable: true,
  options: [
    { label: 'Encounter', value: 'encounter' },
    { label: 'Appointment', value: 'appointment' }
  ],
  closeOnSelect: true,
  multiple: true,
  mode: 'tags'
})

const input_type_data = ref({
  searchable: true,
  options: [
    { label: 'Text', value: 'text' },
    { label: 'Number', value: 'number' },
    { label: 'Textarea', value: 'textarea' },
    { label: 'Multi Select', value: 'multi_select' },
    { label: 'File Upload', value: 'file' },
    { label: 'Select', value: 'select' },
    { label: 'Radio', value: 'radio' },
    { label: 'Checkbox', value: 'checkbox' },
    { label: 'Calendar', value: 'calender' },
    { label: 'Heading', value: 'heading' },
    { label: 'Hr tag', value: 'hr_tag' }
  ],
  closeOnSelect: true
})

const file_type_data = ref({
  searchable: true,
  multiple: true,
  mode: 'tags',
  options: [
    { label: 'PNG', value: '.png' },
    { label: 'JPEG', value: '.jpeg' },
    { label: 'JPG', value: '.jpg' },
    { label: 'PDF', value: '.pdf' },
    { label: 'GIF', value: '.gif' },
    { label: 'CSV', value: '.csv' },
    { label: 'Json', value: '.json' }
  ],
  closeOnSelect: true
})

const heading_tag_data = ref({
  searchable: true,
  options: [
    { label: 'Heading 1', value: 'h1' },
    { label: 'Heading 2', value: 'h2' },
    { label: 'Heading 3', value: 'h3' },
    { label: 'Heading 4', value: 'h4' },
    { label: 'Heading 5', value: 'h5' },
    { label: 'Heading 6', value: 'h6' },
    { label: 'Div', value: 'div' },
    { label: 'Paragraph', value: 'p' }
  ],
  closeOnSelect: true
})

const title_color_data = ref({
  searchable: true,
  options: [
    { label: 'Primary', value: 'text-primary' },
    { label: 'Secondary', value: 'text-secondary' },
    { label: 'Warning', value: 'text-warning' },
    { label: 'Danger', value: 'text-danger' },
    { label: 'Dark', value: 'heading-color' }
  ],
  closeOnSelect: true
})

const title_alignment_data = ref({
  searchable: true,
  options: [
    { label: 'Center', value: 'text-center' },
    { label: 'Left', value: 'text-left' },
    { label: 'Right', value: 'text-end' }
  ],
  closeOnSelect: true
})

const clinicCenter = ref({ options: [], list: [] })

const getClinic = () => {
  useSelect({ url: CLINIC_LIST }, { value: 'id', label: 'clinic_name' }).then((data) => {
    clinicCenter.value = data
  })
}

useOnModalHide('exampleModal', () => setFormData(defaultData()))

onMounted(() => {
  // getServiceList()
  // getManagerList()
  setFormData(defaultData())
  getClinic()
  OpenForm()
  // customFormData();
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    module_type: 'appointment_module',
    form_icon: 'fas fa-book-medical',
    appiontment_status: ['pending', 'confirm'],
    show_in: ['encounter', 'appointment'],
    clinic_id: null,
    status: 1,
    label: 'Example field 1',
    input_type: 'text',
    file_type: [],
    option: null,
    heading_title: '',
    heading_tag: '',
    placeholder: 'Enter field 1',
    validation_status: true,
    class_value: '',
    form_title: 'Form name',
    title_color: 'text-primary',
    title_alignment: 'text-left'
  }
}

const setFormData = (data) => {
  console.log('data', data)

  resetForm({
    values: {
      module_type: data.module_type,
      form_icon: data.form_icon,
      appiontment_status: data.appiontment_status,
      show_in: data.show_in,
      clinic_id: data.clinic_id,
      status: data.status,
      label: data.label,
      input_type: data.input_type,
      file_type: Array.isArray(data.file_type) ? data.file_type : [data.file_type], // Ensure it's an array

      option: data.option,
      heading_title: data.heading_title,
      heading_tag: data.heading_tag,
      placeholder: data.placeholder,
      validation_status: data.validation_status,
      class_value: data.class_value,
      form_title: data.form_title,
      title_color: data.title_color,
      title_alignment: data.title_alignment
    }
  })
}

const numberRegex = /^\d+$/
// Validations
const validationSchema = yup.object({
  module_type: yup.string().required('Module type is a required field'),
  input_type: yup.string().required('Input type is a required field')
  // currency_symbol: yup.string().required("Currency Symbol is a required field"),
  // currency_code: yup.string().required("Currency Code is a required field"),
  // no_of_decimal: yup.number()
  //   .required("No. Of Decimal is a required field")
  //   .integer("No. Of Decimal must be an integer")
  //   .typeError("No. Of Decimal must be a number"),
  // thousand_separator: yup.string().required("Thousand Separator is a required field"),
  // decimal_separator: yup.string().required("Decimal Separator is a required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: module_type } = useField('module_type')
const { value: form_icon } = useField('form_icon')
const { value: appiontment_status } = useField('appiontment_status')
const { value: show_in } = useField('show_in')
const { value: clinic_id } = useField('clinic_id')
const { value: status } = useField('status')
const { value: label } = useField('label')
const { value: input_type } = useField('input_type')
const { value: file_type } = useField('file_type')
const { value: option } = useField('option')
const { value: heading_title } = useField('heading_title')
const { value: heading_tag } = useField('heading_tag')
const { value: placeholder } = useField('placeholder')
const { value: validation_status } = useField('validation_status')
const { value: class_value } = useField('class_value')
const { value: form_title } = useField('form_title')
const { value: title_color } = useField('title_color')
const { value: title_alignment } = useField('title_alignment')

const errorMessages = ref({})

const emit = defineEmits(['onSubmit'])

const { getRequest, storeRequest, updateRequest } = useRequest()

const currentId = ref(props.id)

// Edit Form Or Create Form
// watch(() => props.id, async (value) => {
//   currentId.value = value;

//   console.log(props.id);
//   setFormData(value > 0 ? (await getRequest({ url: EDIT_URL, id: value })).data || defaultData() : defaultData());
// });

// watch(() => props.id, async (value) => {
//   currentId.value = value;

//   let formData = value > 0
//     ? (await getRequest({ url: EDIT_URL, id: value })).data || defaultData()
//     : defaultData();

//     if(currentId.value >0){

//           formFields2.value = JSON.parse(res.data.fields);

//           console.log(formFields2.value)

//        setFormData(formData);
//     }else{

//       const formFields1 = ref([
//   { id: 1, label: 'Example field 1', type: 'text', placeholder: 'Enter field 1', validation_status: 0, option: [] },
//   { id: 2, label: 'Example field 2', type: 'text', placeholder: 'Enter field 2', validation_status: 0, option: [] },
//   { id: 3, label: 'Example field 3', type: 'file', placeholder: 'Enter field 3', validation_status: 0, option: [] },
// ]);
//       formFields.value =formFields1.value
//       setFormData(defaultData())

//     }

// });

watch(
  () => props.id,
  (value) => {
    currentId.value = value

    if (value > 0) {
      getRequest({ url: EDIT_URL, id: value }).then((res) => {
        if (res.status && res.data) {
          try {
            // Parse the fields from the response data
            const parsedFields = JSON.parse(res.data.fields)
            formFields.value = parsedFields // Assign parsed fields directly

            console.log(res.data)
            setFormData(res.data)
          } catch (error) {
            console.error('Error parsing fields:', error)
          }
        }
      })
    } else {
      const formFields1 = [
        { id: 1, label: 'Example field 1', type: 'text', placeholder: 'Enter field 1', validation_status: 0, option: [] },
        { id: 2, label: 'Example field 2', type: 'text', placeholder: 'Enter field 2', validation_status: 0, option: [] },
        { id: 3, label: 'Example field 3', type: 'file', file_type: 'jpeg', placeholder: 'Enter field 3', validation_status: 0, option: [] }
      ]

      formFields.value = formFields1 // Set directly
      setFormData(defaultData())
    }
  }
)

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Modal.getInstance('#exampleModal').hide()
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }

  window.location.reload()

  emit('onSubmit')
}

// Form Submit
const formSubmit = handleSubmit((values) => {
  if (!formFields.value.length) {
    window.errorSnackbar('Form fields cannot be empty!')
    return
  }

  values.formFields = JSON.stringify(formFields.value)
  values.show_in = JSON.stringify(show_in.value)
  values.appointment_status = JSON.stringify(appiontment_status.value)

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>

<style scoped>
.form-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.form-field {
  border: 1px solid #ddd;
  padding: 10px;
  background: #f9f9f9;
  border-radius: 5px;
  position: relative;
}

.delete-icon {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}

.delete-icon:hover {
  color: red;
}

.field-label {
  cursor: pointer;
  display: inline-block;
  padding: 5px;
  font-weight: bold;
  color: #000000;
}

.field-label:hover {
  text-decoration: underline;
}

.input-group {
  margin-bottom: 10px;
}

.icon-container {
  top: 10px;
  right: 10px;
}

.edit-icon,
.delete-icon {
  cursor: pointer;
}

.edit-icon {
  color: #007bff; /* Blue color for edit icon */
}

.delete-icon {
  color: #dc3545; /* Red color for delete icon */
}
</style>
