<template>
    <form @submit.prevent="formSubmit">
      <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="clinic-list" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom" >
          <h6 class="m-0 h5">
            <span>{{ Title }}</span>
          </h6>
          <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ph ph-x-circle"></i></button>
        </div>
        <div class="offcanvas-body">
          <div class="form-group">
            <div class="d-grid">
              <div class="list-group list-group-flush">
                <div v-if="isLoading" class="list-group-item">
                  <p class="m-0"> {{ $t('appointment.loading') }}</p>
                </div>
                <div v-else-if="selectedClinic.length === 0" class="list-group-item">
                  <p class="m-0"> {{ $t('clinic.no_data_available') }}</p>
                </div>
                <template v-else>
                    <div class="row">
                      <div class="col-md-6" v-for="(item) in selectedClinic" :key="item">              
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex align-items-center justify-between flex-grow-1 gap-3">
                              <img :src="item.avatar" class="avatar avatar-40 img-fluid rounded-pill" alt="user" />
                              <div class="flex-grow-1"> 
                                <h6 class="m-0">{{ item.clinic_name }}</h6>
                                <p class="m-0 d-flex align-items-center gap-2"><i class="ph ph-map-pin"></i>{{ item.address }}</p>
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
  import {CLINIC_LIST,EDIT_URL} from '../constant/doctor'
  import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
  import { buildMultiSelectObject } from '@/helpers/utilities'
  import { useSelect } from '@/helpers/hooks/useSelect'
  
  const { listingRequest, getRequest,} = useRequest()
  const selectedClinic = ref([])
const Title = ref(null)
const isLoading = ref(false) 
const doctorId = useModuleId(() => {
  getRequest({url: EDIT_URL, id: doctorId.value}).then((res) => {

        if(res.status && res.data) {
        Title.value = res.data.first_name + ' ' + res.data.last_name + "'s Clinic List"
        
        fetchClinicList(res.data.clinic_id);
        }
    })
}, 'clinic_list');

const fetchClinicList = (clinicIds) => {
  isLoading.value = true
  listingRequest({ url: CLINIC_LIST, data: { clinic_id: clinicIds } })
    .then((data) => {
      selectedClinic.value = data
      console.log(selectedClinic.value);
      isLoading.value = false  // End loading
    })
    .catch(() => {
      isLoading.value = false  // End loading even if there's an error
    })
};
  
  </script>
  