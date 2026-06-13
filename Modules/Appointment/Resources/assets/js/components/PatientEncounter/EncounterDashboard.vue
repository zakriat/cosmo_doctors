<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-w-80" tabindex="-1" id="patient-encounter-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :createTitle="createTitle"></FormHeader>

      <div class="offcanvas-body" id="patient-encounter">
        <div class="row" v-if="EncounterDetails != null">
          <div class="col-lg-3 col-md-4">
            <div class="card">
              <div class="card-header mb-4">
                <h4 class="card-title mb-0">{{ $t('appointment.encounter_detail') }}</h4>
              </div>
              <div class="card-body border-top">
                <ul class="list-inline m-0 p-0">
                  <li class="item mb-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.lbl_name') }}:</h6>
                      <span v-if="EncounterDetails.user && EncounterDetails.user.first_name && EncounterDetails.user.last_name"> {{ EncounterDetails.user.first_name }} {{ EncounterDetails.user.last_name }} </span>
                    </div>
                  </li>
                  <li class="item mb-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.lbl_email') }}:</h6>
                      <a v-if="EncounterDetails.user.email" :href="'mailto:' + EncounterDetails.user.email">{{ EncounterDetails.user.email }}</a>
                    </div>
                  </li>
                  <li class="item mb-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.encounter_date') }}:</h6>
                      <span>{{ moment(EncounterDetails.encounter_date) }}</span>
                    </div>
                  </li>
                  <li class="item">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.address') }}:</h6>
                      <div>
                        <span v-if="EncounterDetails.user.address || EncounterDetails.user.city || EncounterDetails.user.country">
                          {{ EncounterDetails.user.address }}
                          <span v-if="EncounterDetails.user.cities && EncounterDetails.user.cities.name"> , {{ EncounterDetails.user.cities.name }} </span>
                          <span v-if="EncounterDetails.user.countries && EncounterDetails.user.countries.name"> , {{ EncounterDetails.user.countries.name }} </span>
                        </span>
                        <span v-else>-</span>
                        <!-- <span v-if="EncounterDetails.user.city">{{ EncounterDetails.user.cities.name }}</span>  <span v-else>--</span>,
                                    <span v-if="EncounterDetails.user.country">{{ EncounterDetails.user.countries.name }}</span>  <span v-else>--</span> -->
                      </div>
                    </div>
                  </li>
                </ul>
                <ul class="list-inline mt-3 pt-3 mx-0 mb-0 pb-0 border-top">
                  <li class="item mb-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.clinic_name') }}:</h6>
                      <span v-if="EncounterDetails.clinic.name">{{ EncounterDetails.clinic.name }}</span> <span v-else>-</span>
                    </div>
                  </li>
                  <li class="item mb-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.doctor_name') }}:</h6>
                      <span v-if="EncounterDetails.doctor && EncounterDetails.doctor.first_name && EncounterDetails.doctor.last_name"> {{ EncounterDetails.doctor.first_name }} {{ EncounterDetails.doctor.last_name }} </span>
                      <span v-else>-</span>
                    </div>
                  </li>
                  <li class="item" v-if="EncounterDetails.description">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.description') }}:</h6>
                      <span>{{ EncounterDetails.description }}</span>
                    </div>
                  </li>

                  <li class="item">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                      <h6 class="mb-0">{{ $t('appointment.status') }}:</h6>
                      <span v-if="EncounterDetails.status == 1" class="text-success">{{ $t('appointment.open') }}</span>
                      <span v-else class="text-danger"> {{ $t('appointment.close') }}</span>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card encounter-temeplate">
              <div class="card-body" v-if="EncounterDetails.status == 1">
                <h6>{{ $t('appointment.select_encounter_templates') }}</h6>
                <Multiselect id="template_id" v-model="template_id" :value="template_id" @select="setTemplate" :placeholder="$t('clinic.lbl_select_template')" v-bind="singleSelectOption" :options="templateList.options" class="form-group"></Multiselect>
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-md-8">
            <div class="card">
              <div class="d-flex justify-content-between flex-wrap gap-3 card-header mb-4">
                <h4 class="card-title mb-0">{{ $t('appointment.clinic_detail') }}</h4>
                <div>
                  <div class="d-flex flex-wrap gap-3">
                    <button class="btn btn-primary" v-if="EncounterDetails.status == 1 && enable_soap == 1" data-bs-dismiss="offcanvas" @click.prevent="opensoap()" aria-label="Close">
                      {{ $t('appointment.soap') }}
                    </button>
                    <button id="printButton" class="btn btn-primary" v-if="selectedproblemList.length > 0 || selectedObservationList.length > 0 || notesList.length > 0 || prescriptionList.length > 0" @click="handlePrint">
                      <i class="ph ph-file-text me-1"></i>
                      {{ $t('appointment.print_encounters') }}
                    </button>
                    <button v-if="enable_bodychart == 1" @click.prevent="bodychart()" class="btn btn-primary">
                      <i class="ph ph-person-simple-circle me-1"></i>
                      {{ $t('appointment.body_chart') }}
                    </button>
                    <button class="btn btn-primary" v-if="EncounterDetails.status == 0" @click.prevent="billingDetail()">
                      <i class="ph ph-file-text me-1"></i>
                      {{ $t('appointment.billing_details') }}
                    </button>
                    <button class="btn btn-primary" v-if="EncounterDetails.status == 1" v-b-toggle.uploadReport>
                      <i class="ph ph-upload me-1"></i>
                      {{ $t('appointment.upload_report') }} {{ $t('appointment.medical_report') }}
                    </button>

                    <button class="btn btn-danger" v-if="EncounterDetails.status == 1 && EncounterDetails.appointment_id === null" data-bs-toggle="modal" data-bs-target="#Billing_Modal" @click="changeBillingtableId(0)">
                      <i class="ph ph-x-circle me-1"></i>
                      {{ $t('appointment.close_encounter') }}
                    </button>

                    <button class="btn btn-danger" v-if="EncounterDetails.appointment_id !== null && EncounterDetails.status == 1 && EncounterDetails.appointment_status == 'check_in'" data-bs-toggle="modal" data-bs-target="#Billing_Modal" @click="changeBillingtableId(0)">
                      <i class="ph ph-x-circle"></i>
                      {{ $t('appointment.close_encounter') }} & {{ $t('appointment.check_out') }}
                    </button>

                    <span class="d-flex flex-wrap gap-3" v-if="EncounterDetails.appointment_id !== null">
                      <button v-for="form in customform" :key="form.id" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#custom_form_modal" @click="changeformId(form.id)">
                        <i class="fas fa-book-medical"></i>
                        {{ parseFormTitle(form.formdata) }}
                        <!-- Call the parseFormTitle function -->
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="card-body border-top">
                <b-collapse id="uploadReport">
                  <div class="card bg-body">
                    <div class="card-header d-flex justify-content-between flex-wrap gap-3 mb-4">
                      <h5 class="mb-0">{{ $t('appointment.medical_report') }}</h5>
                      <div class="d-flex align-items-center gap-3">
                        <button v-if="MedicalReportList.length > 0" class="btn btn-primary" @click="SendmedicalReport(0)">
                          <i class="ph ph-paper-plane-tilt me-1"></i>
                          {{ $t('appointment.email') }}
                        </button>
                        <button class="btn btn-primary" @click="AddmedicalReport(0)">
                          <i class="ph ph-plus me-1"></i>
                          {{ $t('appointment.add_medical_report') }}
                        </button>
                        <button class="btn btn-primary" v-b-toggle.uploadReport @click="closeMedicalReport(0)">
                          <i class="ph ph-caret-double-left me-1"></i>
                          {{ $t('appointment.back') }}
                        </button>
                      </div>
                    </div>
                    <div class="card-body border-top">
                      <AddMedicalReport v-if="add_report == 1" :encounter_id="EncounterId" :user_id="UserId" :report_id="medicalId" @submitMedical="updateMedicalReport" @closeTemplate="closeMedicalReportAdd"> </AddMedicalReport>
                      <SendMedicalReport v-if="send_report == 1" :encounter_id="EncounterId" :user_id="UserId" @send_report="sendMedicalReportstatus"> </SendMedicalReport>

                      <div class="mt-4">
                        <div class="table-responsive">
                          <table class="table dataTable">
                            <thead>
                              <tr>
                                <th ref="col">
                                  {{ $t('appointment.name') }}
                                </th>
                                <th ref="col">
                                  {{ $t('appointment.date') }}
                                </th>
                                <th ref="col">
                                  {{ $t('appointment.lbl_action') }}
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(medical, index) in MedicalReportList" :key="index">
                                <td>
                                  {{ medical.name }}
                                </td>
                                <td>
                                  <span>{{ moment(medical.date).format('MMMM DD, YYYY') }} </span>
                                </td>
                                <td>
                                  <div class="d-flex gap-3 align-items-center">
                                    <button type="button" class="btn text-success p-0 fs-5" @click="editmedicalReport(medical.id)">
                                      <i class="ph ph-pencil-simple-line align-middle"></i>
                                    </button>

                                    <a class="btn text-primary p-0 fs-5" @click="openInNewTab(medical.file_url)">
                                      <i class="ph ph-eye align-middle"></i>
                                    </a>

                                    <button type="button" class="btn text-danger p-0 fs-4" @click="deletemedicalReport(medical.id, 'Are you sure you want to delete it?')">
                                      <i class="ph ph-trash align-middle"></i>
                                    </button>
                                  </div>
                                </td>
                              </tr>

                              <tr v-if="MedicalReportList.length == 0">
                                <td colspan="3">
                                  <div class="my-1 text-danger text-center">{{ $t('appointment.data_not_found') }}</div>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </b-collapse>
                <div class="row">
                  <div class="col-lg-4 col-md-6" v-if="enableProblem">
                    <div class="card bg-body">
                      <div class="card-header mb-4">
                        <h5 class="card-title">{{ $t('appointment.problems') }}</h5>
                      </div>
                      <div class="card-body medial-history-card border-top">
                        <ul class="list-inline m-0 p-0">
                          <li class="mb-3 pb-3 border-bottom" v-for="(problem, index) in selectedproblemList" :key="index">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                              <span>{{ index + 1 }}. {{ problem.title }}</span>
                              <button class="btn p-0 text-danger" @click="removeProblem(problem.id)" v-if="EncounterDetails.status == 1">
                                <i class="ph ph-x-circle"></i>
                              </button>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <div class="card-footer border-top" v-if="EncounterDetails.status == 1">
                        <Multiselect id="problem" v-model="problem_id" :placeholder="$t('appointment.select_problems')" v-bind="multiselectOption" :options="problems.options" @select="selectProblem" class="form-group"></Multiselect>
                        <p class="mt-2 mb-0 fs-12 clinical_details_notes">
                          <b>{{ $t('appointment.note_encounter_problem') }}</b>
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6" v-if="enableobservation">
                    <div class="card bg-body">
                      <div class="card-header mb-4">
                        <h5 class="card-title">{{ $t('appointment.observation') }}</h5>
                      </div>
                      <div class="card-body medial-history-card border-top">
                        <ul class="list-inline m-0 p-0">
                          <li class="mb-3 pb-3 border-bottom" v-for="(observation, index) in selectedObservationList" :key="index">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                              <span>{{ index + 1 }}. {{ observation.title }}</span>
                              <button class="btn p-0 text-danger" @click="removeObservation(observation.id)" v-if="EncounterDetails.status == 1">
                                <i class="ph ph-x-circle"></i>
                              </button>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <div class="card-footer border-top" v-if="EncounterDetails.status == 1">
                        <Multiselect id="Observations" v-model="observation_id" :placeholder="$t('appointment.select_observation')" v-bind="multiselectOption" :options="observations.options" @select="selectObservation" class="form-group"></Multiselect>
                        <p class="mt-2 mb-0 fs-12 clinical_details_notes">
                          <b>{{ $t('appointment.note_encounter_observation') }}</b>
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4" v-if="enablenote">
                    <div class="card bg-body">
                      <div class="card-header mb-4">
                        <h5 class="card-title">{{ $t('appointment.note') }}</h5>
                      </div>
                      <div class="card-body medial-history-card border-top">
                        <ul class="list-inline m-0 p-0">
                          <li class="mb-3 pb-3 border-bottom" v-for="(note, index) in notesList" :key="index">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                              <span>{{ index + 1 }}. {{ note.title }}</span>
                              <button class="btn p-0 text-danger" @click="removeNotes(note.id)" v-if="EncounterDetails.status == 1">
                                <i class="ph ph-x-circle"></i>
                              </button>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <div class="card-footer border-top" v-if="EncounterDetails.status == 1">
                        <textarea class="form-control h-auto" rows="2" :placeholder="$t('appointment.enter_note')" v-model="notes" name="notes" id="notes" style="min-height: max-content"></textarea>
                        <button class="btn btn-primary mt-3 w-100" @click="addNotesValue()"><i class="ph ph-plus me-2"></i>{{ $t('appointment.add') }}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card bg-body" v-if="enablePrescription">
                  <div class="card-header d-flex justify-content-between flex-wrap gap-3">
                    <h5 class="card-title">{{ $t('appointment.prescription') }}</h5>
                    <div v-if="EncounterDetails.status == 1">
                      <div class="d-flex align-items-center flex-wrap gap-3">
                        <button class="btn btn-sm btn-primary" v-if="prescriptionList.length > 0" @click="SendPrescription()">
                          <div class="d-inline-flex align-items-center gap-1" v-if="Mail_Sending == 0">
                            <i class="ph ph-paper-plane-tilt"></i>
                            {{ $t('appointment.email') }}
                          </div>
                          <div v-else class="d-inline-flex align-items-center gap-1">
                            <i class="ph ph-paper-plane-tilt"></i>
                            {{ $t('appointment.sending') }}
                          </div>
                        </button>
                        <!-- <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upload_prescription_modal">
                                         <div class="d-inline-flex align-items-center gap-1">
                                            <i class="ph ph-file-arrow-down"></i>
                                            {{$t('appointment.import_data')}}
                                         </div>
                                      </button> -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="changeId(0)">
                          <div class="d-inline-flex align-items-center gap-1">
                            <i class="ph ph-plus"></i>
                            {{ $t('appointment.add_prescription') }}
                          </div>
                        </button>

                        <button id="printButton" class="btn btn-sm btn-primary" v-if="prescriptionList.length > 0" @click="downloadPrescription">
                          <i class="ph ph-file-text me-1"></i>
                          {{ $t('appointment.lbl_download') }}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive rounded">
                      <table class="table table-lg m-0">
                        <thead>
                          <tr class="text-white">
                            <th scope="col">{{ $t('appointment.name') }}</th>
                            <th scope="col">{{ $t('appointment.frequency') }}</th>
                            <th scope="col">{{ $t('appointment.duration') }}</th>
                            <th scope="col" v-if="EncounterDetails.status == 1">{{ $t('appointment.action') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(prescription, index) in prescriptionList" :key="index">
                            <td>
                              <h6 class="text-primary">
                                {{ prescription.name }}
                              </h6>
                              <p class="m-0">
                                {{ prescription.instruction }}
                              </p>
                            </td>
                            <td>
                              {{ prescription.frequency }}
                            </td>
                            <td>
                              {{ prescription.duration }}
                            </td>
                            <td class="action" v-if="EncounterDetails.status == 1">
                              <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn text-primary p-0 fs-5 me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="changeId(prescription.id)" aria-controls="form-offcanvas"><i class="ph ph-pencil-simple-line"></i></button>
                                <button type="button" class="btn text-danger p-0 fs-5" @click="destroyData(prescription.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="ph ph-trash"></i></button>
                              </div>
                            </td>
                          </tr>

                          <tr v-if="prescriptionList.length == 0">
                            <td colspan="5">
                              <div class="my-1 text-danger text-center">{{ $t('appointment.no_prescription_found') }}</div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="card bg-body other-detail">
                  <div class="card-header">
                    <h6 class="card-title mb-0">{{ $t('appointment.other_information') }}</h6>
                  </div>
                  <div class="card-body">
                    <textarea class="form-control h-auto" rows="3" :placeholder="$t('appointment.enter_other_details')" v-model="other_details" name="other_details" id="other_details" style="min-height: max-content"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer border-top pt-4">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
          <button class="btn btn-white d-block" type="button" @click="close_off_canvas()">
            {{ $t('messages.cancel') }}
          </button>
          <button :disabled="IS_SUBMITED" class="btn btn-secondary" name="submit" @click="saveEncounterDetails()">
            <template v-if="IS_SUBMITED">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ $t('appointment.loading') }}
            </template>
            <template v-else> {{ $t('messages.save') }}</template>
          </button>
        </div>
      </div>
    </div>
    <AddPrescription :encounter_id="EncounterId" :user_id="UserId" :id="tableId" @submit="updateprescriptionDetail"></AddPrescription>

    <UploadPrescription :encounter_id="EncounterId" :user_id="UserId" @import_prescription="importprescriptionDetail"></UploadPrescription>

    <AddBilling :encounter_id="EncounterId" :user_id="UserId" :id="BillingtableId" @save_billing="SaveBillingData"></AddBilling>

    <CustomForm :encounter_id="EncounterId" :form_id="formId" @save_customform="SavecustomformData"></CustomForm>
  </form>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { DOWNLOAD_INVOICE, ENCOUNTER_DETAIL, GET_SEARCH_DATA, SAVE_OPTION_DATA, REMOVE_DATA, DELETE_PRESCRIPTION_URL, SAVE_OTHER_DETAIL_DATA, DELETE_MEDICAL_REPORT_URL, SEND_PRESCRIPTION_URL, EXPORT_PRESCRIPTION_URL, TEMPLATE_LIST, TEMPLATE_DETAIL, SAVE_ENCOUNTER_DETAILS, DOWNLOAD_PRESCRIPTION } from '../../constant/encounter_dashboard'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { confirmSwal } from '@/helpers/utilities'
import FlatPickr from 'vue-flatpickr-component'
import AddPrescription from '@/vue/components/Modal/AddPrescription.vue'
import AddMedicalReport from '../../components/PatientEncounter/AddMedicalReport.vue'
import SendMedicalReport from '../../components/PatientEncounter/SendMedicalReport.vue'
import UploadPrescription from '@/vue/components/Modal/UploadPrescription.vue'
import AddBilling from '@/vue/components/Modal/AddBilling.vue'
import CustomForm from '@/vue/components/Modal/Customform.vue'
import moment from 'moment'

const tableId = ref(null)
const changeId = (id) => {
  tableId.value = id
}

const BillingtableId = ref(null)

const changeBillingtableId = () => {
  EncounterId.value = EncounterDetails.value.id
}

const formId = ref(null)

const changeformId = (id) => {
  formId.value = id
}

defineProps({
  createTitle: { type: String, default: '' }
})
const enableProblem = ref(false)
const enableobservation = ref(false)
const enablePrescription = ref(false)
const enablenote = ref(false)
const encounterdashboard = () => {
  const encounterdata = window.encounter
  enableProblem.value = encounterdata['is_encounter_problem'] == '1'
  enableobservation.value = encounterdata['is_encounter_observation'] == '1'
  enablePrescription.value = encounterdata['is_encounter_prescription'] == '1'
  enablenote.value = encounterdata['is_encounter_note'] == '1'
}

const other_details = ref('')
const MedicalReportList = ref([])
const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)

const { getRequest, listingRequest, storeRequest, deleteRequest } = useRequest()

const EncounterDetails = ref(null)
const UserId = ref(null)
const medicalId = ref(null)
const template_id = ref(null)
const is_appontment = ref(0)

const prescriptionList = ref([])
const customform = ref([])

const EncounterDetailsValue = computed(() => {
  return EncounterDetails.value
})

const EncounterId = useModuleId(() => {
  getRequest({ url: ENCOUNTER_DETAIL, id: EncounterId.value }).then((res) => {
    if (res.status) {
      EncounterDetails.value = res.data
      selectedproblemList.value = res.data.selectedProblemList
      selectedObservationList.value = res.data.selectedObservationList
      prescriptionList.value = res.data.prescriptions
      notesList.value = res.data.notesList
      UserId.value = EncounterDetails.value.user_id
      other_details.value = res.data.other_details
      template_id.value = EncounterDetails.value.encounter_template_id
      MedicalReportList.value = res.data.medicalReport
      customform.value = res.data.customform

      if (res.data.appointment_id > 0) {
        is_appontment.value = 1
      }
    }
  })
}, 'patient-dashboard')

const parseFormTitle = (formdata) => {
  const parsedData = JSON.parse(formdata)
  return parsedData.form_title || 'Untitled Form'
}

const problems = ref({ options: [], list: [] })
const observations = ref({ options: [], list: [] })
const templateList = ref({ options: [], list: [] })

const multiselectOption = ref({
  tag: true,
  searchable: true,
  //multiple: true,
  closeOnSelect: true,
  createOption: true
})

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const getTemplateData = () => {
  listingRequest({ url: TEMPLATE_LIST }).then((res) => {
    templateList.value.list = res
    templateList.value.options = buildMultiSelectObject(res, {
      value: 'id',
      label: 'name'
    })
  })
}

const setTemplate = (value) => {
  getRequest({ url: TEMPLATE_DETAIL, id: value }).then((res) => {
    selectedproblemList.value = res.data.selectedProblemList
    selectedObservationList.value = res.data.selectedObservationList
    prescriptionList.value = res.data.prescriptions
    notesList.value = res.data.notesList
    other_details.value = res.data.other_details
  })
}

const getproblems = () => {
  listingRequest({ url: GET_SEARCH_DATA, data: { type: 'encounter_problem' } }).then((res) => {
    problems.value.list = res.results
    problems.value.options = buildMultiSelectObject(res.results, {
      value: 'name',
      label: 'name'
    })
  })
}

const getObservations = () => {
  listingRequest({ url: GET_SEARCH_DATA, data: { type: 'encounter_observations' } }).then((res) => {
    observations.value.list = res.results
    observations.value.options = buildMultiSelectObject(res.results, {
      value: 'name',
      label: 'name'
    })
  })
}

const selectedProblemsComputed = computed(() => {
  return selectedproblemList.value
})

const selectedproblemList = ref([])

const values = ref([])

const selectProblem = (value) => {
  //   values.value = {
  //      encounter_id : EncounterDetails.value.id,
  //      user_id : EncounterDetails.value.user_id,
  //      type: 'encounter_problem',
  //      name: value
  //      };

  //      storeRequest({  url: SAVE_OPTION_DATA, body: values.value}).then((res) => {
  //                         problems.value.list = res.data;
  //                         selectedproblemList.value=res.medical_histroy

  //                    });

  //      problem_id.value=''
  const problemIndex = selectedproblemList.value.findIndex((problem) => problem.title === value)

  if (problemIndex === -1) {
    // If the problem does not exist, add it to the list
    values.value = {
      encounter_id: EncounterDetails.value.id,
      user_id: EncounterDetails.value.user_id,
      type: 'encounter_problem',
      name: value
    }

    storeRequest({ url: SAVE_OPTION_DATA, body: values.value }).then((res) => {
      problems.value.list = res.data
      selectedproblemList.value = res.medical_histroy
    })

    problem_id.value = ''
  } else {
    // If the problem exists, remove it from the list
    const problemToRemove = selectedproblemList.value[problemIndex]
    removeProblem(problemToRemove.id)
    problem_id.value = ''
  }
}

const removeProblem = (value) => {
  selectedproblemList.value = selectedproblemList.value.filter((problem) => problem.id !== value)

  listingRequest({ url: REMOVE_DATA, data: { id: value, type: 'encounter_problem' } }).then((res) => {
    selectedproblemList.value.list = res.medical_histroy
  })
}

const selectedObservationComputed = computed(() => {
  return selectedObservationList.value
})

const selectedObservationList = ref([])

const observationvalues = ref([])

const selectObservation = (value) => {
  //   observationvalues.value = {

  //       type:'encounter_observations',
  //       name: value,
  //       encounter_id : EncounterDetails.value.id,
  //       user_id : EncounterDetails.value.user_id,
  //   };

  //   storeRequest({  url: SAVE_OPTION_DATA, body: observationvalues.value}).then((res) => {
  //                         observations.value.list = res.data;
  //                         selectedObservationList.value=res.medical_histroy

  //                    });

  //                    observation_id.value=''
  const observationsIndex = selectedObservationList.value.findIndex((observations) => observations.title === value)

  if (observationsIndex === -1) {
    // If the observations does not exist, add it to the list
    observationvalues.value = {
      encounter_id: EncounterDetails.value.id,
      user_id: EncounterDetails.value.user_id,
      type: 'encounter_observations',
      name: value
    }

    storeRequest({ url: SAVE_OPTION_DATA, body: observationvalues.value }).then((res) => {
      observations.value.list = res.data
      selectedObservationList.value = res.medical_histroy
    })

    observation_id.value = ''
  } else {
    // If the observations exists, remove it from the list
    const observationsToRemove = selectedObservationList.value[observationsIndex]
    removeObservation(observationsToRemove.id)
    observation_id.value = ''
  }
}

const removeObservation = (value) => {
  selectedObservationList.value = selectedObservationList.value.filter((observation) => observation.id !== value)

  listingRequest({ url: REMOVE_DATA, data: { id: value, type: 'encounter_observations' } }).then((res) => {
    selectedObservationList.value.list = res.medical_histroy
  })
}
//bodychart
function bodychart() {
  window.location.href = `bodychart/${EncounterDetails.value.id}`
}

function billingDetail() {
  window.location.href = `billing-record/encounter_billing_detail?id=${EncounterDetails.value.id}`
}

function opensoap() {
  window.location.href = `patient-record?id=${EncounterDetails.value.id}`
}

const enable_bodychart = window.bodyChartEnabled
const enable_soap = window.soapEnabled
const encounter = window.encounter
const problem_id = ref('')
const observation_id = ref('')
const notes = ref('')
const notedata = ref([])
const notesList = ref([])

const addNotesValue = () => {
  notedata.value = {
    type: 'encounter_notes',
    name: notes.value,
    encounter_id: EncounterDetails.value.id,
    user_id: EncounterDetails.value.user_id
  }

  if (notes.value != '') {
    storeRequest({ url: SAVE_OPTION_DATA, body: notedata.value }).then((res) => {
      notesList.value = res.medical_histroy
    })
  }

  notes.value = ''
}

const removeNotes = (value) => {
  notesList.value = notesList.value.filter((note) => note.id !== value)

  listingRequest({ url: REMOVE_DATA, data: { id: value, type: 'encounter_notes' } }).then((res) => {
    selectedObservationList.value.list = res.medical_histroy
  })
}

onMounted(() => {
  encounterdashboard()
  getproblems()
  getObservations()
  getTemplateData()
  selectedproblemList.value = []
  selectedObservationList.value = []
  notesList.value = []
  problem_id.value = ''
  notes.value = ''
  observation_id.value = ''
  tableId.value = 0
})

const updateprescriptionDetail = (e) => {
  prescriptionList.value = e.value
}

const importprescriptionDetail = (e) => {
  prescriptionList.value = e.value
}

const destroyData = (id, message) => {
  confirmSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return
    deleteRequest({ url: DELETE_PRESCRIPTION_URL, id }).then((res) => {
      if (res.status) {
        prescriptionList.value = res.prescription
        Swal.fire({
          title: 'Deleted',
          text: res.message,
          icon: 'success',
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__zoomOut'
          }
        })
      }
    })
  })
}

const OtherDetails = ref([])

const addOtherDetails = () => {
  OtherDetails.value = {
    other_details: other_details.value,
    encounter_id: EncounterDetails.value.id,
    user_id: EncounterDetails.value.user_id
  }

  storeRequest({ url: SAVE_OTHER_DETAIL_DATA, body: OtherDetails.value }).then((res) => {})
}
const add_report = ref(0)
const send_report = ref(0)

const AddmedicalReport = (value) => {
  add_report.value = 1
  medicalId.value = value
  send_report.value = 0
}

const editmedicalReport = (value) => {
  medicalId.value = value
  add_report.value = 1
}

const closeMedicalReport = (value) => {
  medicalId.value = value
  add_report.value = 0
}

const updateMedicalReport = (medical) => {
  add_report.value = 0
  MedicalReportList.value = medical.value
}

const closeMedicalReportAdd = () => {
  medicalId.value = 0
  add_report.value = 0
}

const openInNewTab = (url) => {
  window.open(url, '_blank')
}

const SendmedicalReport = () => {
  add_report.value = 0
  send_report.value = 1
}

const deletemedicalReport = (id, message) => {
  confirmSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return
    deleteRequest({ url: DELETE_MEDICAL_REPORT_URL, id }).then((res) => {
      if (res.status) {
        MedicalReportList.value = res.medical_report

        Swal.fire({
          title: 'Deleted',
          text: res.message,
          icon: 'success',
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__zoomOut'
          }
        })
      }
    })
  })
}

const sendMedicalReportstatus = () => {
  send_report.value = 0
}

const prescriptiondata = ref([])

const Mail_Sending = ref(0)

const reset_datatable_close_offcanvas = (res) => {
  Mail_Sending.value = 0
  if (res.status) {
    window.successSnackbar(res.message)
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const SendPrescription = () => {
  Mail_Sending.value = 1
  ;(prescriptiondata.encounter_id = EncounterDetails.value.id),
    (prescriptiondata.user_id = EncounterDetails.value.user_id),
    storeRequest({ url: SEND_PRESCRIPTION_URL, body: prescriptiondata }).then((res) => {
      reset_datatable_close_offcanvas(res)
    })
}
const exportData = ref([])

const EmportFile = (value) => {
  ;(exportData.encounter_id = EncounterDetails.value.id),
    (exportData.type = value),
    storeRequest({ url: EXPORT_PRESCRIPTION_URL, body: exportData }).then((res) => {
      reset_datatable_close_offcanvas(res)
    })
}

const EncounterData = ref([])

const IS_SUBMITED = ref(false)
const reset_datatable_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)

    bootstrap.Offcanvas.getInstance('#patient-encounter-offcanvas').hide()
    renderedDataTable.ajax.reload(null, false)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const close_off_canvas = () => {
  bootstrap.Offcanvas.getInstance('#patient-encounter-offcanvas').hide()
  renderedDataTable.ajax.reload(null, false)
}

const saveEncounterDetails = () => {
  IS_SUBMITED.value = true
  EncounterData.encounter_id = EncounterDetails.value.id
  EncounterData.user_id = EncounterDetails.value.user_id
  EncounterData.selectedproblemList = selectedproblemList.value
  EncounterData.selectedObservationList = selectedObservationList.value
  EncounterData.prescriptionList = prescriptionList.value
  EncounterData.notesList = notesList.value
  EncounterData.other_details = other_details.value
  EncounterData.template_id = template_id.value

  storeRequest({ url: SAVE_ENCOUNTER_DETAILS, body: EncounterData }).then((res) => {
    reset_datatable_offcanvas(res)
  })
}

const SaveBillingData = () => {
  getRequest({ url: ENCOUNTER_DETAIL, id: EncounterId.value }).then((res) => {
    if (res.status) {
      EncounterDetails.value = res.data
      selectedproblemList.value = res.data.selectedProblemList
      selectedObservationList.value = res.data.selectedObservationList
      prescriptionList.value = res.data.prescriptions
      notesList.value = res.data.notesList
      UserId.value = EncounterDetails.value.user_id
      other_details.value = res.data.other_details
      template_id.value = EncounterDetails.value.encounter_template_id
      MedicalReportList.value = res.data.medicalReport
    }
  })
}

//  const handlePrint = () => {
//    getRequest({ url: DOWNLOAD_INVOICE, id: EncounterId.value }).then((data));
// //    print(EncounterDetails.value,selectedproblemList.value,
// //  selectedObservationList.value,
// //  prescriptionList.value,
// //  notesList.value,
// //  other_details.value);
//  }

const handlePrint = () => {
  const encounterid = EncounterDetails.value.id
  const username = EncounterDetails.value.user ? `${EncounterDetails.value.user.first_name}_${EncounterDetails.value.user.last_name}` : 'default_user'
  listingRequest({ url: DOWNLOAD_INVOICE, data: { id: encounterid } })
    .then((response) => {
      if (response.status === true) {
        const link = document.createElement('a')
        link.href = response.link
        link.download = `${username}_${encounterid}.pdf`
        link.style.display = 'none'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      } else {
        console.error('Error generating PDF:', response.error)
      }
    })
    .catch((error) => {
      console.error('Error:', error)
    })
}

const downloadPrescription = () => {
  const encounterid = EncounterDetails.value.id
  listingRequest({ url: DOWNLOAD_PRESCRIPTION, data: { id: encounterid } })
    .then((response) => {
      if (response.status === true) {
        const link = document.createElement('a')
        link.href = response.link
        link.download = 'prescription.pdf'
        link.style.display = 'none'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      } else {
        console.error('Error generating PDF:', response.error)
      }
    })
    .catch((error) => {
      console.error('Error:', error)
    })
}

const print = (EncounterDetails, selectedproblemList, selectedObservationList, prescriptionList, notesList, other_details) => {
  let problemRows = ''
  selectedproblemList.forEach((problem, index) => {
    problemRows += `
              <li class="mb-3 pb-3">
                  <div class="d-flex align-items-start justify-content-between gap-1">
                      <span>${index + 1}. ${problem.title}</span>
                  </div>
              </li>`
  })

  let observationRows = ''
  selectedObservationList.forEach((observation, index) => {
    observationRows += `
              <li class="mb-3 pb-3">
                  <div class="d-flex align-items-start justify-content-between gap-1">
                      <span>${index + 1}. ${observation.title}</span>
                  </div>
              </li>`
  })

  let noteRows = ''
  notesList.forEach((note, index) => {
    noteRows += `
              <li class="mb-3 pb-3">
                  <div class="d-flex align-items-start justify-content-between gap-1">
                      <span>${index + 1}. ${note.title}</span>
                  </div>
              </li>`
  })

  let prescriptionRows = ''
  prescriptionList.forEach((prescription, index) => {
    prescriptionRows += `
              <tr>
                    <td>
                       <h6 class="text-primary">${index + 1}. ${prescription.name}</h6>
                       <p class="m-0">${prescription.instruction}</p>
                    </td>
                    <td>${prescription.frequency}</td>
                    <td>${prescription.duration}</td>
              </tr>`
  })

  const printContent = `<!DOCTYPE html>
        <html lang="en">

           <head>
              <meta charset="UTF-8">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Medical Certificate</title>
              <link rel='stylesheet'
                 href='https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i%7CHeebo:300,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&#038;display=swap'
                 type='text/css' media='all' />
              <style>
                 body{
                       height: 98vh;
                 }

                 h1,
                 h2,
                 h3,
                 h4,
                 h5,
                 h6,
                 p {
                       margin: 0;
                 }

                 .c-row {
                       display: flex;
                       flex-wrap: wrap;
                 }

                 .c-col-7 {
                       flex: 0 0 auto;
                       width: 58.33333333%;
                 }

                 .c-col-5 {
                       flex: 0 0 auto;
                       width: 41.66666667%;
                 }

                 ul {
                       margin: 0;
                       padding: 0;
                       list-style-type: none;
                 }

                 ul li:not(:last-child) {
                       margin-bottom: 5px;
                 }

                 .border {
                       border: 1px solid #000;
                 }

                 .border-bottom {
                       border-bottom: 1px solid #000;
                 }

                 table {
                       width: 100%;
                       vertical-align: middle;
                 }

                 table th,
                 table td {
                       padding: 5px 10px;
                       text-align: start;
                 }

                 .table-border {
                       border: 1px solid #000;
                       border-collapse: collapse;
                 }

                 .table-border td,
                 .table-border th {
                       border: 1px solid #000;
                 }

                 .table-flex-element {
                       display: flex;
                       gap: 15px;
                 }

                 .table-flex-element .title {
                       margin-bottom: 5px;
                 }

                 .m-0 {
                       margin: 0;
                 }

                 .text-center {
                       text-align: center;
                 }

                 .main-content{
                       height: 100%;
                       display: flex;
                       flex-direction: column;
                       justify-content: space-between;
                 }

                 .d-flex{
                       display: flex;
                 }

                 .signature{
                       width: 130px;
                 }

                 .gap-15{
                       gap: 15px;
                 }

              </style>
           </head>
           <body style="font-size: 16px; color: #000;">
              <div class="main-content">
                 <div class="content-top">

                    <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                       <div class="main-logo" style="align-items: center;gap: 15px;">
                          <h2>Encounter Details</h2>
                       </div>

                    </div>
                    <div class="c-row">
                       <div class="c-col-7">
                          <ul>
                                <li style="display: flex; align-items: center; gap: 10px;"><b>Name:</b>
                                   <h3>${EncounterDetails.user.first_name} ${EncounterDetails.user.last_name}</h3>
                                </li>
                                <li><b>Email:</b> ${EncounterDetails.user.email} </li>
                                <li><b>Encounter Date:</b> ${EncounterDetails.encounter_date}</li>
                                <li><b>Address:</b> ${EncounterDetails.user.address}</li>
                          </ul>
                       </div>
                       <div class="c-col-5">
                          <ul>
                                <li><b>Clinic Name:</b> ${EncounterDetails.clinic.name}</li>
                                <li><b>Doctor Name:</b> ${EncounterDetails.doctor.first_name} ${EncounterDetails.doctor.last_name}</li>
                                <li><b>Description:</b> ${EncounterDetails.description}</li>
                          </ul>
                       </div>
                    </div>
                    <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                       <div class="main-logo" style="align-items: center;gap: 15px;">
                          <h2>Clinic Details</h2>
                       </div>

                    </div>
                    <table class="table-striped table-border" style="margin: 30px 0 20px;">
                       <thead>
                          <tr>
                                <th>
                                   <p class="m-0 text-center">Problems</p>
                                </th>
                                <th>
                                   <p class="m-0 text-center">Observations</p>
                                </th>
                                <th>
                                   <p class="m-0 text-center">Notes</p>
                                </th>
                          </tr>
                       </thead>
                       <tbody>
                          <tr>
                                <td>
                                   <div class="table-flex-element">
                                      <ul>
                                         ${problemRows}
                                      </ul>
                                   </div>
                                </td>
                                <td>
                                   <div class="table-flex-element">

                                      <ul>
                                           ${observationRows}
                                      </ul>
                                   </div>
                                </td>
                                <td>
                                   <div class="table-flex-element">

                                      <ul>
                                           ${noteRows}
                                      </ul>
                                   </div>
                                </td>

                          </tr>

                          </tr>
                       </tbody>
                    </table>

                    <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                       <div class="main-logo" style="align-items: center;gap: 15px;">
                          <h2>Prescription</h2>
                       </div>

                    </div>
                    <table class="table-striped table-border" style="margin: 30px 0 20px;">
                       <thead>
                          <tr>
                                <th>
                                   <p class="m-0 text-center">Name</p>
                                </th>
                                <th>
                                   <p class="m-0 text-center">Frequency</p>
                                </th>
                                <th>
                                   <p class="m-0 text-center">Duration</p>
                                </th>
                          </tr>
                       </thead>
                       <tbody>
                          ${prescriptionRows}
                       </tbody>
                    </table>

                    <table class="table-border" style="margin: 30px 0 20px;">
                       <tr>
                          <th>Other Information</th>
                          <td>${other_details}</td>
                       </tr>
                    </table>
                    <div class="d-flex gap-15">
                       <h5 class="m-0">Doctor's Signature : </h5>
                       <div>
                          <img src="${EncounterDetails.signature}" class="signature">
                       </div>
                    </div>

                 </div>
              </div>
           </body>
        </html>`

  const printWindow = window.open('')
  printWindow.document.write(printContent)
  printWindow.document.close()
  setTimeout(() => {
    printWindow.print()
    printWindow.close()
  }, 1000)
}
</script>
