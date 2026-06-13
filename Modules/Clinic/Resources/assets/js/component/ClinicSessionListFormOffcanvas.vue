<template>
    <form @submit.prevent="formSubmit">
      <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="clinic-session" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom">
          <h6 class="m-0 h5">
            <span>{{ $t('clinic.clinic_session_list') }}</span>
          </h6>
          <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ph ph-x-circle"></i></button>
        </div>
  
        <div class="offcanvas-body">
          <div class="form-group">
            <div class="d-grid">
              <div class="list-group list-group-flush">
                <!-- Check if selectedDay is empty -->
                <div v-if="selectedDay.length === 0" class="list-group-item">
                  <p class="m-0">{{ $t('messages.data_not_available') }}</p>
                </div>
                <!-- Render clinic sessions if available -->
                <template v-else>
                    <div class="row">
                      <div class="col-md-6" v-for="(item) in selectedDay" :key="item">              
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex align-items-start justify-between flex-grow-1 gap-3">
                              <img :src="item.avatar" class="avatar avatar-40 img-fluid rounded-pill" alt="user" />
                              <div class="flex-grow-1"> 
                                <h6 class="m-0">{{ item.clinic_name }}</h6>
                                <p class="m-0 d-flex align-items-center gap-2">
                                  {{ item.days.join(', ') }}
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </template>
  <script setup>
  import { ref, onMounted } from 'vue'
  import {DOCTOR_DAYS_LIST,EDIT_URL} from '../constant/doctor-session'
  import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
  import { buildMultiSelectObject } from '@/helpers/utilities'
  import { useSelect } from '@/helpers/hooks/useSelect'
  
  const { listingRequest, getRequest,} = useRequest()
  const selectedDay = ref([])
const Title = ref(null)
const doctorId = useModuleId(() => {
  getRequest({url: EDIT_URL, id: doctorId.value}).then((res) => {
    if(res.status && res.data) {
      const clinicIds = Array.from(new Set(res.data.map(item => item.clinic_id))); 
        fetchClinicSessions(clinicIds);
    }
  });
}, 'clinic_session');

const fetchClinicSessions = (clinicIds) => {
  listingRequest({ url: DOCTOR_DAYS_LIST, data: { clinic_id: clinicIds,  doctor_id: doctorId.value } }).then((data) => {
    selectedDay.value = data
  });
};
  </script>
  