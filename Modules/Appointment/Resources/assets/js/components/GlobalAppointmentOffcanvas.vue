<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="appointment-form-offcanvas"
      aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h4 class="offcanvas-title" id="form-offcanvasLabel">
          <span>{{ $t('messages.create') }} {{ $t('messages.appointment') }} </span>
        </h4>
        <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="btn-close-offcanvas"><i
            class="ph ph-x-circle"></i></button>
      </div>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group" v-if="!selectedPatient">
              <label class="form-label"
                >{{ $t("clinic.lbl_select_patient") }}
                <span class="text-danger">*</span></label
              >
              <Multiselect
                id="patient_id"
                v-model="patient_id"
                :value="patient_id"
                :placeholder="$t('clinic.lbl_select_patient')"
                v-bind="singleSelectOption"
                :options="patientlist.options"
                @select="getotherpatient($event.id)"
                class="form-group"
              ></Multiselect>
              <span v-if="errorMessages['patient_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['patient_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors["patient_id"] }}</span>
            </div>

            <div class="user-block card rounded" v-else>
              <div class="card-body">
                <div class="d-flex align-items-start gap-4 mb-4">
                  <img
                    :src="selectedPatient.avatar"
                    alt="avatar"
                    class="img-fluid avatar avatar-60 rounded-pill"
                  />
                  <div class="flex-grow-1">
                    <div class="gap-2">
                      <h5>{{ selectedPatient.name }}</h5>
                      <p class="m-0">
                        {{ $t("appointment.lbl_since") }}
                        {{ moment(selectedPatient.created_at).format("MMMM YYYY") }}
                      </p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="removePatient()"
                    class="text-danger bg-transparent border-0"
                  >
                    <i class="ph ph-trash"></i>
                  </button>
                </div>
                <div class="row" v-if="selectedPatient.mobile">
                  <label class="col-3 col-xl-2 fw-500">
                    <strong class="fst-normal heading-color">{{
                      $t("booking.lbl_phone")
                    }}</strong>
                  </label>
                  <span class="col-7 col-xl-8">{{ selectedPatient.mobile }}</span>
                </div>
                <div class="row" v-if="selectedPatient.email">
                  <label class="col-3 col-xl-2 fw-500">
                    <strong
                      ><span class="fst-normal heading-color">{{
                        $t("booking.lbl_e-mail")
                      }}</span></strong
                    >
                  </label>
                  <span class="col-7 col-xl-8">{{ selectedPatient.email }}</span>
                </div>
              </div>
            </div>

            <div class="row" v-if="selectedPatient">
              <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label">{{ $t("clinic.book_for_other_patient") }}</label>
                  <div class="form-check form-switch">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="showAdditionalPatient"
                    />
                  </div>
                </div>

                <!-- Conditional Text with Modal Trigger -->
                <div v-if="showAdditionalPatient" class="mt-2">
                  <div>
                    <p
                      data-bs-toggle="modal"
                      data-bs-target="#exampleModal"
                      class="text-primary cursor-pointer"
                    >
                      {{ $t("clinic.add_other_patient") }}
                    </p>
                  </div>
                  <!-- <div v-if="otherpatientlist.list.length > 0" class="d-flex flex-wrap gap-2">
                    <div v-for="patient in otherpatientlist.list" :key="patient.id" class="book-for-appointments" :class="{ 'bg-primary text-white border-primary active': selectedPatientId === patient.id }" @click="selectPatient(patient.id)">
                        <img :src="patient.profile_image" class="img-fluid rounded-circle avatar-35 object-fit-cover" />
                        <h6 class="appointments-title">{{ patient.first_name }}</h6>
                    </div>
                  </div> -->

                  <div
                    v-if="
                      otherpatientlist.options.list &&
                      otherpatientlist.options.list.length
                    "
                    class="d-flex align-items-center flex-wrap column-gap-4 row-gap-3 mt-2"
                  >
                    <div
                      v-for="patient in otherpatientlist.options.list"
                      :key="patient.id"
                      class="book-for-appointments"
                      :class="{
                        'bg-primary border-primary active':
                          selectedPatientId === patient.id,
                      }"
                      @click="selectPatient(patient.id)"
                    >
                        <img
                          v-if="patient.profile_image"
                          :src="patient.profile_image"
                          class="img-fluid rounded-circle avatar-35 object-fit-cover"
                        />
                        <h6 class="appointments-title mb-0">{{ patient.first_name }}</h6>
                    </div>
                  </div>
                  <div v-else>
                    <p>{{ $t("clinic.no_other_patient") }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 form-group">
                <label class="form-label col-md-6"
                  >{{ $t("clinic.lbl_select_clinic") }}
                  <span class="text-danger">*</span></label
                >
                <Multiselect
                  id="clinic_id"
                  v-model="clinic_id"
                  :value="clinic_id"
                  :placeholder="$t('clinic.lbl_select_clinic')"
                  v-bind="singleSelectOption"
                  :options="cliniclist.options"
                  @select="getDoctorList"
                  class="form-group"
                ></Multiselect>
                <span v-if="errorMessages['clinic_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['clinic_id']" :key="err">
                      {{ err }}
                    </li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors["clinic_id"] }}</span>
              </div>

              <div v-if="!isDoctor" class="col-md-6 form-group">
                <label class="form-label"
                  >{{ $t("clinic.lbl_select_doctor") }}
                  <span class="text-danger">*</span></label
                >
                <Multiselect
                  id="doctor_id"
                  v-model="doctor_id"
                  :value="doctor_id"
                  :placeholder="$t('clinic.lbl_select_doctor')"
                  v-bind="singleSelectOption"
                  :options="doctorlist.options"
                  @select="getServiceList"
                  class="form-group"
                ></Multiselect>
                <span v-if="errorMessages['doctor_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['doctor_id']" :key="err">
                      {{ err }}
                    </li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors["doctor_id"] }}</span>
              </div>

              <div class="col-md-6 form-group">
                <label class="form-label"
                  >{{ $t("clinic.lbl_select_service") }}
                  <span class="text-danger">*</span></label
                >
                <Multiselect
                  id="service_id"
                  v-model="service_id"
                  :value="service_id"
                  @select="checkTotalAmount"
                  :placeholder="$t('clinic.lbl_select_service')"
                  v-bind="singleSelectOption"
                  :options="servicelist.options"
                  class="form-group"
                ></Multiselect>
                <span v-if="errorMessages['service_id']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['service_id']" :key="err">
                      {{ err }}
                    </li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors["service_id"] }}</span>
              </div>

              <div class="col-md-6 form-group appointment_date">
                <label class="form-label" for="appointment_date"
                  >{{ $t("clinic.lbl_appointment_date") }}
                  <span class="text-danger">*</span></label
                >

                <flat-pickr
                  :placeholder="$t('clinic.lbl_appointment_date')"
                  id="appointment_date"
                  class="form-control"
                  v-model="appointment_date"
                  :value="appointment_date"
                  :config="config"
                  @change="AppointmentDateChange"
                ></flat-pickr>
                <span v-if="errorMessages['appointment_date']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['appointment_date']" :key="err">
                      {{ err }}
                    </li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.appointment_date }}</span>
              </div>

              <div class="col-md-12 form-group">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label" for="availble_slot"
                    >{{ $t("clinic.lbl_availble_slots") }}
                    <span class="text-danger">*</span></label
                  >
                </div>

                <div>
                  <div
                    v-if="available_slots.length > 0"
                    class="d-flex flex-wrap align-items-center gap-3"
                  >
                    <div
                      v-for="(slot, index) in available_slots"
                      :key="index"
                      class="avb-slot clickable-text"
                    >
                      <label
                        class="clickable-text"
                        :class="{ selected_slot: slot === selectedSlot }"
                        @click="selectSlot(slot)"
                        >{{ slot }}</label
                      >
                    </div>
                  </div>
                  <div v-if="available_slots.length == 0">
                    <h4 class="text-danger text-center form-control">
                      {{ $t("clinic.lbl_Slot_not_Found") }}
                    </h4>
                  </div>
                </div>
                <span class="text-danger" id="avaible_slot_error">{{
                  errors.availble_slot
                }}</span>
              </div>

              <div class="form-group col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label" for="file_url"
                    >{{ $t("clinic.lbl_medical_report") }}
                  </label>
                </div>
                <input
                  type="file"
                  class="form-control"
                  id="file_url"
                  name="file_url"
                  ref="refInput"
                  accept=".jpeg, .jpg, .png, .gif, .pdf"
                  multiple
                  @change="fileUpload"
                />
                <span v-if="errorMessages['file_url']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['file_url']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.file_url }}</span>
              </div>

              <div v-for="field in customefield" :key="field.id">
                <FormElement
                  v-model="custom_fields_data"
                  :name="field.name"
                  :label="field.label"
                  :type="field.type"
                  :required="field.required"
                  :options="field.value"
                  :field_id="field.id"
                ></FormElement>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer">
        <div class="p-3 bg-primary-subtle border border-primary rounded">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-normal">{{ $t("appointment.service_price") }}:</span>
            <span class="fw-bold">{{ formatCurrencyVue(basePrice) }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2" v-if="discountAmount > 0">
            <span class="fw-normal">{{ $t("frontend.discount") }}:</span>
            <span class="fw-bold text-success">-{{ formatCurrencyVue(discountAmount) }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2" v-if="discountAmount > 0">
            <span class="fw-normal">{{ $t("frontend.subtotal") }}:</span>
            <span class="fw-bold">{{ formatCurrencyVue(subTotalAmount) }}</span>
          </div>

          <!-- <div class="d-flex justify-content-between align-items-center mb-2" v-if="inclusive_tax_price > 0">
            <span class="fw-normal">{{ $t('service.inclusive_tax') }} <i class="ph ph-info btn btn-link ms-2 p-0" data-bs-toggle="modal" data-bs-target="#inclusiveModalCenter"></i> :</span>
            <span class="fw-bold text-danger">{{ formatCurrencyVue(inclusive_tax_price) }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2" v-if="inclusive_tax_price > 0">
            <span class="fw-bold">{{ $t('appointment.sub_total') }}:</span>
            <span class="fw-bold text-success">{{ formatCurrencyVue(subTotalAmount) }}</span>
          </div> -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <span class="fw-normal"
                >{{ $t("appointment.total_tax") }}
                <i
                  class="ph ph-info btn btn-link ms-2 p-0"
                  data-bs-toggle="modal"
                  data-bs-target="#exampleModalCenter1"
                ></i>
                :</span
              >

              <!-- Replace with the desired icon class -->
            </div>
            <div>
              <span class="fw-bold text-danger">{{
                formatCurrencyVue(totalTaxAmount)
              }}</span>
            </div>
          </div>
          <hr class="my-2" />
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">{{ $t("appointment.total") }}:</span>
            <span class="fw-bold text-success">{{ formatCurrencyVue(totalAmount) }}</span>
          </div>
        </div>
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3 p-3">
          <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
            {{ $t("messages.close") }}
          </button>
          <button :disabled="isDisabled" class="btn btn-secondary" name="submit">
            <template v-if="IS_SUBMITED">
              <span
                class="spinner-border spinner-border-sm"
                role="status"
                aria-hidden="true"
              ></span>
              {{ $t("appointment.loading") }}
            </template>
            <template v-else> {{ $t("messages.save") }}</template>
          </button>
        </div>
      </div>
    </div>
  </form>
  <div class="modal fade" id="exampleModalCenter1" tabindex="-1" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Applied Taxes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>


        <div class="modal-body">
          <div>
            <div v-for="tax in apliedTax" :key="tax.name" class="d-flex justify-content-between align-items-center">
              {{ tax.type === 'percent' ? tax.name + ' (' + tax.value + '%):' : tax.name + ':' }}
              <span>{{ tax.type === 'percent' ? tax.amount.toFixed(2) : formatCurrencyVue(tax.amount) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">{{ $t('clinic.add_other_patient') }}</h1>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form @submit="submitForm">
            <div class="row">
              <div class="col-md-4">
                <div class="text-center">
                  <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <input type="file" ref="profileInputRef" class="form-control d-none" id="logo" name="profile_image"
                      accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
                    <label class="btn btn-info" for="logo">{{
                      $t("messages.upload")
                      }}</label>
                    <input type="button" class="btn btn-danger" name="remove" :value="$t('settings.remove')"
                      @click="removeLogo()" v-if="ImageViewer" />
                  </div>
                  <span class="text-danger">{{ errors.profile_image }}</span>
                </div>
              </div>
              <div class="col-md-8">
                <div class="mb-3">
                  <label for="firstName" class="form-label">{{
                    $t("clinic.lbl_first_name")
                    }}</label>
                  <input type="text" class="form-control" id="firstName" v-model="firstName"
                    :placeholder="$t('clinic.lbl_first_name')" />
                  <span id="firstNameError" class="text-danger"></span>
                </div>

                <div class="mb-3">
                  <label for="lastName" class="form-label">{{
                    $t("clinic.lbl_last_name")
                    }}</label>
                  <input type="text" class="form-control" id="lastName" v-model="lastName"
                    :placeholder="$t('clinic.lbl_last_name')" />
                  <span id="lastNameError" class="text-danger"></span>
                </div>

                <div class="mb-3">
                  <label for="dob" class="form-label">{{
                    $t("clinic.date_of_birth")
                    }}</label>
                  <flat-pickr v-model="dob" :config="dobConfig" class="form-control" id="dob"
                    :placeholder="$t('clinic.date_of_birth')">
                  </flat-pickr>
                  <span id="dobError" class="text-danger"></span>
                </div>

                <div class="mb-3">
                  <label for="phoneNumber" class="form-label">{{
                    $t("clinic.lbl_contact_number")
                    }}</label>
                  <div class="input-group">
                    <vue-tel-input class="w-100" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }"
                      autocomplete="new-password" :inputOptions="{
                        placeholder: $t('employee.lbl_phone_number_placeholder'),
                      }"></vue-tel-input>
                    <span id="phoneNumberError" class="text-danger"></span>
                  </div>
                </div>

                <!-- Gender Selection -->
                <div class="mb-3">
                  <label class="form-label">{{ $t("clinic.lbl_gender") }}</label>
                  <div class="d-flex gap-2">
                    <input type="radio" class="btn-check" name="gender" id="male" value="Male" v-model="gender"
                      autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="male">
                      {{ $t("clinic.lbl_male") }}
                    </label>

                    <input type="radio" class="btn-check" name="gender" id="female" value="Female" v-model="gender"
                      autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="female">
                      {{ $t("clinic.lbl_female") }}
                    </label>

                    <input type="radio" class="btn-check" name="gender" id="other" value="Other" v-model="gender"
                      autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="other">
                      {{ $t("clinic.other") }}
                    </label>
                  </div>
                  <span id="genderError" class="text-danger"></span>
                </div>

                <!-- Relation Selection -->
                <div class="mb-3">
                  <label class="form-label">{{ $t("clinic.relation") }}</label>
                  <div class="d-flex flex-wrap gap-2">
                    <input type="radio" class="btn-check" name="relation" id="parents" value="Parents"
                      v-model="relation" autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="parents">
                      {{ $t("clinic.parents") }}
                    </label>

                    <input type="radio" class="btn-check" name="relation" id="siblings" value="Siblings"
                      v-model="relation" autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="siblings">
                      {{ $t("clinic.sibling") }}
                    </label>

                    <input type="radio" class="btn-check" name="relation" id="spouse" value="Spouse" v-model="relation"
                      autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="spouse">
                      {{ $t("clinic.spouse") }}
                    </label>

                    <input type="radio" class="btn-check" name="relation" id="others" value="Others" v-model="relation"
                      autocomplete="off" />
                    <label class="btn btn-outline-primary rounded-pill px-4" for="others">
                      {{ $t("clinic.other") }}
                    </label>
                  </div>
                  <span id="relationError" class="text-danger"></span>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">
            {{ $t("clinic.close") }}
          </button>
          <button type="submit" class="btn btn-primary" @click.prevent="submitForm">
            {{ $t("clinic.save_changes") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import moment from "moment";
import * as yup from "yup";
import { useField, useForm } from "vee-validate";
import {
  EDIT_URL,
  STORE_URL,
  UPDATE_URL,
  PATIENT_OTHER_STORE_URL,
  CLINIC_LIST,
  DOCTOR_LIST,
  SERVICE_LIST,
  OTHERPATIENT_LIST,
  PATIENT_LIST,
  SERVICE_PRICE,
  TAX_DATA,
  GET_AVALABLE_SLOT,
  SAVE_PAYMENT,
} from "../constant/clinic-appointment";
import {
  useModuleId,
  useRequest,
  useOnOffcanvasHide,
  useOnOffcanvasShow,
} from "@/helpers/hooks/useCrudOpration";
import { readFile } from "@/helpers/utilities";
import { useSelect } from "@/helpers/hooks/useSelect";
import FormHeader from "@/vue/components/form-elements/FormHeader.vue";
import FlatPickr from "vue-flatpickr-component";
import ImageComponent from "@/vue/components/form-elements/imageComponent.vue";
import "flatpickr/dist/flatpickr.css";
import { VueTelInput } from "vue3-tel-input";
import "vue3-tel-input/dist/vue3-tel-input.css"; // Add this line
// props
const props = defineProps({
  createTitle: { type: String, default: "" },
  editTitle: { type: String, default: "" },
  customefield: { type: Array, default: () => [] },
  defaultImage: {
    type: String,
    default: "https://dummyimage.com/600x300/cfcfcf/000000.png",
  },
  role: { type: String, default: "" },
  userId: { type: Number, default: "" },
});

const changeLogo = (e) => {
  fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: profile_image });
};

// File Upload Function
const ImageViewer = ref(null);
const profileInputRef = ref(null);
const defaultImage = window.auth_profile_image;
const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0];
  console.log(file);
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64;
    console.log(imageViewerBS64.value);
    profileInputRef.value.value = "";
  });
  changeFile.value = file;
};
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null;
  changeFile.value = null;
};
const selectedPatientId = ref(null);

const selectPatient = (patientId) => {
  selectedPatientId.value = patientId;
};
// flatepicker
const handleInput = (Number, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    phoneNumber.value = phoneObject.formatted;
  }
};

const config = ref({
  dateFormat: "Y-m-d",
  static: true,
  minDate: "today",
});
const dobConfig = ref({
  dateFormat: "Y-m-d",
  maxDate: "today",
  enableTime: false,
  altInput: true,
  altFormat: "F j, Y",
  static: true,
});
const role = () => {
  return window.auth_role[0];
};

const isReceptionist = computed(() => role() === "receptionist");
const isDoctor = computed(() => role() == "doctor");

const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value);
  }
  return value;
};

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest();

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
});

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data);
      }
    });
  } else {
    setFormData(defaultData());
  }
});

const showAdditionalPatient = ref(false);

// Validations
const validationSchema = yup.object({
  clinic_id: yup.string().required("Clinic name is required"),
  service_id: yup.string().required("Service is required"),
  doctor_id: yup.string().required("Doctor is required"),
  appointment_date: yup.string().required("Appointment Date is required"),
  patient_id: yup.string().required("Patient is required"),
  // firstName: yup.string().when([], {
  //   is: () => showAdditionalPatient.value === true,
  //   then: (schema) => schema.required("First Name is Required"),
  //   otherwise: (schema) => schema.notRequired(),
  // }),
  // lastName: yup.string().when([], {
  //   is: () => showAdditionalPatient.value === true,
  //   then: (schema) => schema.required("Last Name is Required"),
  //   otherwise: (schema) => schema.notRequired(),
  // }),
  // dob: yup.string().when([], {
  //   is: () => showAdditionalPatient.value === true,
  //   then: (schema) => schema.required("Date Of Birth is Required"),
  //   otherwise: (schema) => schema.notRequired(),
  // }),
  // phoneNumber: yup.string().when([], {
  //   is: () => showAdditionalPatient.value === true,
  //   then: (schema) => schema.required("Phone number is Required"),
  //   otherwise: (schema) => schema.notRequired(),
  // }),
  // relation: yup.string().when([], {
  //   is: () => showAdditionalPatient.value === true,
  //   then: (schema) => schema.required("Relation is Required"),
  //   otherwise: (schema) => schema.notRequired(),
  // }),
});

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema,
});

const { value: clinic_id } = useField("clinic_id");
const { value: service_id } = useField("service_id");
const { value: doctor_id } = useField("doctor_id");
const { value: appointment_date } = useField("appointment_date");
const { value: patient_id } = useField("patient_id");
const { value: file_url } = useField("file_url");
const { value: firstName } = useField("firstName");
const { value: lastName } = useField("lastName");
const { value: profile_image } = useField("profile_image");
const { value: dob } = useField("dob");
const { value: phoneNumber } = useField("phoneNumber");
const { value: gender } = useField("gender");
const { value: relation } = useField("relation");

const errorMessages = ref({});

onMounted(() => {
  setFormData(defaultData());
  getClinic();
  getPatient();
  gettaxData();
});

// Default FORM DATA
const defaultData = () => {
  totalAmount.value = 0;
  totalTaxAmount.value = 0;
  available_slots.value = [];
  errorMessages.value = {};
  ImageViewer.value = props.defaultImage;
  if (isDoctor) {
    doctor_id.value = props.userId;
    getServiceList(doctor_id.value);
  }
  return {
    clinic_id: "",
    service_id: "",
    doctor_id: "",
    appointment_date: "",
    patient_id: "",
    file_url: [],
    firstName: "",
    lastName: "",
    dob: "",
    phoneNumber: "",
    gender: "",
    profile_image: [],
    relation: "",
    profileImage: null,
    errors: {},
  };
};
const patientlist = ref({ options: [], list: [] });

const getPatient = () => {
  useSelect({ url: PATIENT_LIST }, { value: "id", label: "name" }).then((data) => {
    patientlist.value = data;
    console.log("patientlist", patientlist.value);
    if (patient_id.value) {
      getotherpatient(patient_id.value);
    }
  });
};
watch(patient_id, (newVal) => {
  if (newVal) {
    console.log("Calling getotherpatient with:", newVal);
    getotherpatient(newVal);
  }
});

const selectedPatient = computed(
  () => patientlist.value.list.find((patient) => patient.id == patient_id.value) ?? null
);

const removePatient = () => {
  patient_id.value = null;
  setFormData(defaultData());
  selectedSlot.value = "";
};
const IS_SUBMITED = ref(false);

const tax_data = ref([]);

const tax_data_value = ref([]);

const gettaxData = () => {
  const module_type = "services";
  const tax_type = "exclusive";

  listingRequest({
    url: TAX_DATA,
    data: { module_type: module_type, tax_type: tax_type },
  }).then((res) => {
    tax_data.value = res;
    tax_data_value.value = res;
  });
};
const discountAmount = ref(0);
const totalAmount = ref(0);
const totalTaxAmount = ref(0);
const apliedTax = ref([]);

const taxCalculation = (amount) => {
  apliedTax.value = [];
  let tax_amount = 0;
  totalTaxAmount.value = 0;

  if (tax_data.value) {
    tax_data.value.forEach((item) => {
      let taxValue = item.value;

      if (item.type === "fixed") {
        tax_amount += taxValue;
        apliedTax.value.push({ name: item.title, amount: taxValue, type: item.type });
      } else if (item.type === "percent") {
        let temp_tax_amount = amount * (taxValue / 100);
        tax_amount += temp_tax_amount;
        apliedTax.value.push({
          name: item.title,
          amount: temp_tax_amount,
          value: taxValue,
          type: item.type,
        });
      }
    });
  }

  // Subtract inclusive taxes from the base amount

  totalTaxAmount.value = tax_amount;
  const total_amount = amount + totalTaxAmount.value;

  return total_amount;
};

const isDisabled = computed(() => {
  return IS_SUBMITED.value || totalAmount.value < 1 || selectedSlot.value === "";
});

const cliniclist = ref({ options: [], list: [] });

const getClinic = () => {
  useSelect({ url: CLINIC_LIST }, { value: "id", label: "clinic_name" }).then((data) => {
    cliniclist.value = data;
    if (isReceptionist.value && cliniclist.value.list.length > 0) {
      clinic_id.value = [cliniclist.value.list[0].id];
      getDoctorList(clinic_id.value);
    }
  });
};

const otherpatientlist = ref({ options: [], list: [] });

const getotherpatient = (value) => {
  useSelect(
    { url: OTHERPATIENT_LIST, data: value },
    { value: "user_id", label: "first_name" }
  ).then((data) => {
    otherpatientlist.value = { options: data || [], list: data || [] };
  });
};

const doctorlist = ref({ options: [], list: [] });

const getDoctorList = (value) => {
  (doctor_id.value = ""),
    (service_id.value = ""),
    useSelect(
      { url: DOCTOR_LIST, data: value },
      { value: "doctor_id", label: "doctor_name" }
    ).then((data) => (doctorlist.value = data));
};

const servicelist = ref({ options: [], list: [] });
const inclusive_tax = ref([]);
const inclusive_tax_price = ref(0);

const inclusiveTax = ref([]);

const calculateInclusiveTax = (amount, inclusive_tax) => {

  inclusiveTax.value = [];
  let total_tax = 0;

  if (inclusive_tax && inclusive_tax.length) {
    inclusive_tax.forEach((item) => {
      const taxValue = parseFloat(item.value);

      if (item.type === "fixed") {
        total_tax += taxValue;
        inclusiveTax.value.push({
          name: item.title,
          amount: taxValue,
          type: item.type,
        });
      } else if (item.type === "percent") {
        const taxAmount = amount * (taxValue / 100);
        total_tax += taxAmount;
        inclusiveTax.value.push({
          name: item.title,
          amount: taxAmount,
          value: taxValue,
          type: item.type,
        });
      }
    });
  }

  inclusive_tax_price.value = parseFloat(total_tax.toFixed(2));
  return total_tax;
};
const getServiceList = (value) => {
  service_id.value = "";

  useSelect(
    { url: SERVICE_LIST, data: { doctor_id: value, clinic_id: clinic_id.value } },
    { value: "id", label: "name" }
  ).then((data) => {
    servicelist.value = data;

    console.log(data);

    //const list = data?.list || []
    // const selectedService = list.find(item => item.id === service_id.value) || list[list.length - 1]

    // if (selectedService) {
    //  console.log('selectedService',selectedService);
    //   inclusive_tax.value =
    //     typeof selectedService.inclusive_tax === 'string'
    //       ? JSON.parse(selectedService.inclusive_tax)
    //       : selectedService.inclusive_tax
    // } else {
    //   inclusive_tax.value = null // or set a default
    // }
  });
  AppointmentDateChange();
};

const serviceAmount = ref(0);
const subTotalAmount = ref(0);
const basePrice = ref(0);

const checkTotalAmount = (value) => {
  if (isDoctor.value) {
    doctor_id.value = props.userId;
  }
  listingRequest({
    url: SERVICE_PRICE,
    data: { ServiceId: service_id.value, DoctorId: doctor_id.value },
  }).then((res) => {
      if (res) {
        basePrice.value = res.base_price || 0;
        let amount = res.service_charge;
        serviceAmount.value = res.service_charge;
        // Set discount from response
        discountAmount.value = res.discount || 0;
        let taxAmount = calculateInclusiveTax(serviceAmount.value, res.inclusive_tax_data);
        console.log("text amount", taxAmount);
        subTotalAmount.value = serviceAmount.value + taxAmount;
        let total_amount = taxCalculation(subTotalAmount.value);

        totalAmount.value = total_amount;
      }
  });
  AppointmentDateChange();
};

const available_slots = ref([]);

const AppointmentDateChange = () => {
  if (isDoctor.value) {
    doctor_id.value = props.userId;
  }
  if (
    appointment_date.value != "" &&
    doctor_id.value != "" &&
    clinic_id.value != "" &&
    service_id.value != ""
  ) {
    listingRequest({
      url: GET_AVALABLE_SLOT,
      data: {
        Appointment_date: appointment_date.value,
        DoctorId: doctor_id.value,
        ClinicId: clinic_id.value,
        serviceId: service_id.value,
      },
    }).then((res) => {
      if (res) {
        available_slots.value = res.availableSlot;
      }
    });
  }
};

const selectedSlot = ref("");

const selectSlot = (value) => {
  selectedSlot.value = value;
};
const image_url = ref();

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.file_url || props.defaultImage;
  image_url.value = data.profile_image;
  selectedSlot.value = "";

  resetForm({
    values: {
      clinic_id: data.clinic_id ?? "",
      service_id: data.service_id ?? "",
      doctor_id: data.doctor_id ?? "",
      appointment_date: data.appointment_date ?? "",
      patient_id: data.patient_id ?? "",
      file_url: data.file_url ?? "",
      profile_image: data.profile_image,
    },
  });
};

const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false;
  if (res.status) {
    window.successSnackbar(res.message);
    renderedDataTable.ajax.reload(null, false);
    bootstrap.Offcanvas.getInstance("#appointment-form-offcanvas").hide();
    setFormData(defaultData());
  } else {
    window.errorSnackbar(res.message);
    errorMessages.value = res.all_message;
  }
};
const newPtientId = ref();
const formSubmit = handleSubmit((values) => {
  console.log("values", values);
  IS_SUBMITED.value = true;

  if (props.customefield > 0) {
    values.custom_fields_data = JSON.stringify(values.custom_fields_data);
  }
  if (newPtientId) {
    values.newPtientId = newPtientId.value;
  }

  // Check if slot is selected
  if (selectedSlot.value === "") {
    document.querySelector(`#avaible_slot_error`).textContent =
      "Appointment Slot Is Required";
    IS_SUBMITED.value = false; // Stop the loader when no slot is selected
    return; // Prevent form submission
  } else {
    document.querySelector(`#avaible_slot_error`).textContent = ""; // Clear the error message
  }

  values.status = "confirmed";
  values.otherpatient_id = selectedPatientId.value;
  values.user_id = patient_id.value;
  values.appointment_time = selectedSlot.value;
  values.tax = JSON.stringify(tax_data_value.value);

  if (values.file_url && values.file_url.length > 0) {
    values.file_url.forEach((fileUrl, index) => {
      values[`file_url[${index}]`] = fileUrl;
    });
  }

  if (currentId.value > 0) {
    updateRequest({
      url: UPDATE_URL,
      id: currentId.value,
      body: values,
      type: "file",
    }).then((res) => reset_datatable_close_offcanvas(res));
  } else {
    storeRequest({ url: STORE_URL, body: values, type: "file" }).then((res) => {
      if (res.status == true) {
        storeRequest({ url: SAVE_PAYMENT, body: res.data }).then((data) =>
          reset_datatable_close_offcanvas(data)
        );
      } else {
        reset_datatable_close_offcanvas(res);
      }
    });
  }
});
useOnOffcanvasHide("appointment-form-offcanvas", () => {
  IS_SUBMITED.value = false;
  setFormData(defaultData());
});
useOnOffcanvasShow("appointment-form-offcanvas", () => {
  IS_SUBMITED.value = false;
});

const clearErrors = () => {
  document.getElementById("firstNameError").innerText = "";
  document.getElementById("lastNameError").innerText = "";
  document.getElementById("dobError").innerText = "";
  document.getElementById("phoneNumberError").innerText = "";
  document.getElementById("genderError").innerText = "";
  document.getElementById("relationError").innerText = "";
};

const submitForm = () => {
  // IS_SUBMITED.value = true;
  clearErrors();

  if (!firstName.value) {
    document.getElementById("firstNameError").innerText = "First Name is required";
  }
  if (!lastName.value) {
    document.getElementById("lastNameError").innerText = "Last Name is required";
  }
  if (!dob.value) {
    document.getElementById("dobError").innerText = "Date of Birth is required";
  }
  if (!phoneNumber.value) {
    document.getElementById("phoneNumberError").innerText = "Phone Number is required";
  }
  if (!gender.value) {
    document.getElementById("genderError").innerText = "Gender is required";
  }
  if (!relation.value) {
    document.getElementById("relationError").innerText = "Relation is required";
  }
  const isValid =
    firstName.value &&
    lastName.value &&
    dob.value &&
    phoneNumber.value &&
    gender.value &&
    relation.value;
  if (!isValid) return;

  const values = {
    first_name: firstName.value,
    last_name: lastName.value,
    contactNumber: phoneNumber.value,
    dob: dob.value,
    relation: relation.value,
    gender: gender.value,
    user_id: patient_id.value,
    otherpatient_id: selectedPatientId.value,
    profile_image: profile_image.value,
  };

  console.log(values);

  storeRequest({
    url: PATIENT_OTHER_STORE_URL,
    id: currentId.value,
    body: values,
    type: "file",
  }).then((res) => {
    if (res.status) {
      newPtientId.value = res.data.id;
      //     console.log(res.data);
      //     if (!Array.isArray(otherpatientlist.value.list)) {
      //   otherpatientlist.value.list = [];
      // }
      //     otherpatientlist.value.list.push({
      //       id: res.data.id,
      //       first_name: values.first_name,
      //       last_name: values.last_name,
      //       profile_image: res.data.profile_image
      //     });
      getotherpatient(patient_id.value);
      console.log(otherpatientlist.value.list);
      window.successSnackbar(res.message);
      firstName.value = "";
      lastName.value = "";
      phoneNumber.value = "";
      dob.value = "";
      relation.value = "";
      gender.value = "";
      profile_image.value = null;
      selectedPatientId.value = null;
      closeModal();
    }
  });
};
const closeModal = () => {
  const modalElement = document.getElementById("exampleModal1");
  if (modalElement) {
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
      modalInstance.hide();
    } else {
      new bootstrap.Modal(modalElement).hide();
    }
  }
};
</script>
<style>
.flatpickr-wrapper {
  width: 100%;
}
</style>
