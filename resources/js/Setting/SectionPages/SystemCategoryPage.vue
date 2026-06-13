<template>
  <CardTitle :title="$t('setting_sidebar.lbl_system_category')" icon="">
    <form id="quick-action-form" :class="{'form-disabled': !selectedItems.length}" class="d-flex gap-3 align-items-center">
      <select v-model="actionType" class="select2 form-select" style="width: 100%;" >
        <option value="">no_action</option>
        <option value="change-status">status</option>
        <option value="delete">delete</option>
      </select>
      
      <div v-if="selectedItems.length > 0 && actionType === 'change-status'" class="select-status" style="width: 100%;">
        <select v-model="status" class="select2 form-select" style="width: 100%;">
          <option value="1">active</option>
          <option value="0">inactive</option>
        </select>
      </div>
      
      <button @click.prevent="applyAction" class="btn btn-primary" :disabled="!selectedItems.length || !actionType">apply</button>
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="exampleModal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i> {{$t('messages.create')}} {{$t('setting_sidebar.lbl_system_category')}}</button>
  </CardTitle>
  <SystemCategoryFormOffCanvas :id="tableId" @onSubmit="fetchTableData()"></SystemCategoryFormOffCanvas>
  <div class="table-responsive">
    <table class="table table-condense">
      <thead>
        <tr>
          <th>
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                id="selectAll" 
                :checked="selectedItems.length > 0"
                @change="selectAllItems"
              />
              <label class="form-check-label" for="selectAll"></label>
            </div>
          </th>
          
          <th>{{ $t('category.image') }}</th>
          <th>{{ $t('category.lbl_name') }}</th>
          <th>{{ $t('category.lbl_category') }}</th>
          <th>{{ $t('category.lbl_status') }}</th>
          <th>{{ $t('category.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="(systemcategory, index) in tableList" :key="index">
            <td>
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  :id="'checkbox_' + systemcategory.id" 
                  :value="systemcategory.id" 
                  v-model="selectedItems"
                />
                <label class="form-check-label" :for="'checkbox_' + systemcategory.id"></label>
              </div>
            </td>
           
            <td><img :src="systemcategory.file_url" alt="Image" class="avatar avatar-50 rounded-pill"></td>
            <td>{{ systemcategory.name }}</td>
            <td>
              <span v-if="systemcategory.parent_id">{{ systemcategory.main_category.name }}</span>
              <span v-else>-</span>
            </td>
            <td>
              <div class="form-check form-switch">
                <input 
                  class="form-check-input" 
                  :true-value="1" 
                  :false-value="0" 
                  :value="systemcategory.status"
                  :checked="systemcategory.status == 1" 
                  @change="updateStatus(systemcategory.id, $event.target.checked)"
                  type="checkbox" 
                />
              </div>
            </td>
            <td>
              <button type="button" class="btn text-primary p-0 fs-5 me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="changeId(systemcategory.id)" aria-controls="exampleModal"><i class="ph ph-pencil-simple-line"></i></button>
              <button type="button" class="btn text-danger p-0 fs-5" @click="destroyData(systemcategory.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="ph ph-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </template>
      <template v-else>
        <!-- Render message when tableList is null or empty -->
        <tr class="text-center">
          <td colspan="7" class="py-3">Data is not available in this Table</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import SystemCategoryFormOffCanvas from './Forms/SystemCategoryFormOffCanvas.vue'
import { LISTING_URL, DELETE_URL, UPDATE_STATUS_URL,BULK_ACTION_URL } from '@/vue/constants/system_category'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { confirmSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const tableId = ref(null)

const changeId = (id) => {
  tableId.value = id
}

// Request
const { getRequest, deleteRequest, updateRequest } = useRequest()

onMounted(() => {
  fetchTableData()
})

// Define variables
const tableList = ref(null)
const selectedItems = ref([])
const actionType = ref('')
const status = ref('1'); 
// fetch all data
const fetchTableData = () => {
  getRequest({ url: LISTING_URL }).then((res) => {
    if (res.status) {
      tableList.value = res.data
      tableId.value = 0
    }
  })
}

// destroy data
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
  function showMessage(message) {
            Snackbar.show({
                text: message,
                pos: 'bottom-left'
            });
        }
  const updateStatus = (categoryId, checked) => {
  const status = checked ? 1 : 0;
  updateRequest({ url: UPDATE_STATUS_URL,id: categoryId,body: {status: status}}).then((res) => {
    console.log(res);
    if (res.status) {
      showMessage(res.message);
    } else {
      console.error("Failed to update status");
     
    }
  })
}


const selectAllItems = (event) => {
  if (event.target.checked) {
    selectedItems.value = tableList.value.map(item => item.id)
    
  } else {
    selectedItems.value = []
    actionType.value = '';
  }
}

const applyAction = () => {
  if (!actionType.value || selectedItems.value.length === 0) return; 
  
  const formData = {
    action_type: actionType.value,
    status: status.value, 
    rowIds: selectedItems.value.join(',')
  };

  if (actionType.value === 'delete') {
    updateRequest({ url: BULK_ACTION_URL, body: formData }).then((res) => {
      if (res.status) {
        showMessage(res.message)
        fetchTableData()
        selectedItems.value = [];
        actionType.value = '';
      } else {
        console.error("Failed to perform bulk delete");
        
      }
    })
  } else if (actionType.value === 'change-status') {
    updateRequest({ url: BULK_ACTION_URL, body: formData }).then((res) => {
      if (res.status) {
        showMessage(res.message)
        fetchTableData()
        selectedItems.value = [];
        actionType.value = '';
      } else {
        console.error("Failed to perform bulk status change");
      }
    })
  }
}



</script>
