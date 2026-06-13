<template>
  <CardTitle :title="$t('setting_sidebar.lbl_commission')" icon="fa-solid fa-bars">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="form-modal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i> {{$t('commission.lbl_add_commission')}}</button>
  </CardTitle>
  <CommissionForm :id="tableId" @onSubmit="fetchTableData()"></CommissionForm>
  <div class="table-responsive mt-4">
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>{{ $t('commission.lbl_sr_no') }}</th>
          <th>{{ $t('commission.lbl_title') }}</th>
          <th>{{ $t('commission.lbl_value') }}</th>
          <th>{{ $t('commission.lbl_commission_type') }}</th>

          <th>{{ $t('commission.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="(item, index) in tableList" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ item.title }}</td>
            <td>  <span v-if="item.commission_type === 'percentage'">
                {{ item.commission_value }}%
              </span>
              <span v-else>

                {{formatCurrencyVue(item.commission_value)}}

              </span>
            </td>
            <td class="text-capitalize">{{ formatType(item.type) }}</td>
            <td>
              <button type="button" class="btn text-primary p-0 fs-5 me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="changeId(item.id)" aria-controls="form-offcanvas"><i class="ph ph-pencil-simple-line"></i></button>
              <button type="button" class="btn text-danger p-0 fs-5" @click="destroyData(item.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="ph ph-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </template>
      <template v-else>
        <!-- Render message when tableList is null or empty -->
        <tr class="text-center">
          <td colspan="9" class="py-3">Data is not available in this Table</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { LISTING_URL, DELETE_URL } from '@/vue/constants/commission'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import CommissionForm from './Forms/CommissionForm.vue'
import { confirmSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const tableId = ref(null)
const changeId = (id) => {
  tableId.value = id
}
const formatCurrencyVue = window.currencyFormat


onMounted(() => {
  fetchTableData()
})

// Request
const { getRequest, deleteRequest } = useRequest()

// Define variables
const tableList = ref(null)

const fetchTableData = () => {
  getRequest({ url: LISTING_URL }).then((res) => {
    if (res.status) {
            tableList.value = res.data
      tableId.value = 0
    }
  })
}
  const formatType = (type) => {
  return type.replace(/_/g, ' ').toLowerCase()
}
const destroyData = (id, message) => {
  confirmSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return
    deleteRequest({ url: DELETE_URL, id }).then((res) => {
      if (res.status) {
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
        fetchTableData()
      }
    })
  })
}
</script>
