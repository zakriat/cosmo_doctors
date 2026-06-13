<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="view_commission_list" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h4 class="offcanvas-title" id="form-offcanvasLabel">  
        
        <span>{{Username}}</span>
       
        </h4>

        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

      </div>

      <div class="offcanvas-body">
          <div div class="list-group list-group-flush">
            <div v-for="(item, index) in commissions" :key="item" class="list-group-item d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center flex-grow-1 gap-2 my-2">
          
                <div class="flex-grow-1"> {{ item.main_commission.title}} </div>

                <div class="flex-grow-1" v-if="item.main_commission.commission_type=='percentage'"> {{ item.main_commission.commission_value}}%</div>

                <div class="flex-grow-1" v-else> {{formatCurrencyVue(item.main_commission.commission_value)}}</div>
        
              </div>            
            </div>
          </div>
      </div>      
    </div>
  </form>


  
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { GET_EMPLOYEE_COMMISSSION_URL } from '../constant/earning'
import { useModuleId, useRequest,useOnOffcanvasHide,useOnOffcanvasShow} from '@/helpers/hooks/useCrudOpration'
import { confirmSwal } from '@/helpers/utilities'



// Request
const { deleteRequest, getRequest, listingRequest} = useRequest()

const props=defineProps({
  type: { type: String, default: '' },
})
const formatCurrencyVue = window.currencyFormat

const commissions = ref([])

const Username = ref(null)

const EmployeeId = useModuleId(() => {

  const commissionType = document.querySelector('[data-assign-event="assign_commssions"]').dataset.assignCommissionType;
   
  listingRequest({ url: GET_EMPLOYEE_COMMISSSION_URL, data: { id: EmployeeId.value, type: commissionType }}).then((res) => {
    if (res.status && res.data) {

      commissions.value = res.data.commission_data
      console.log(commissions.value);
      Username.value = res.data.full_name + "'s Commissions list"

      EmployeeId.value = 0
    }
  })
}, 'assign_commssions')

</script>