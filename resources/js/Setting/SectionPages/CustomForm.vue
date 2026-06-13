<template>
  <CardTitle :title="$t('messages.customforms')" icon="fa-solid fa-file-code">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="exampleModal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i> {{ $t('messages.create') }} {{ $t('messages.form') }}</button>
  </CardTitle>
  <FormAddOffcanvas :id="tableId" @onSubmit="fetchTableData()"></FormAddOffcanvas>
  <div class="table-responsive mt-4">
    <table class="table table-condense">
      <thead>
        <tr>
          <th>{{ $t('currency.lbl_ID') }}</th>
          <th>{{ $t('messages.name') }}</th>
          <th>{{ $t('messages.type') }}</th>
          <th>{{ $t('messages.status') }}</th>
          <th>{{ $t('currency.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="item in tableList" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.type || '--' }}</td>
            <td>{{ item.status === 1 ? 'Active' : 'Inactive' }}</td>

            <td>
              <button type="button" class="btn text-primary p-0 fs-5 me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="changeId(item.id)" aria-controls="exampleModal">
                <i class="ph ph-pencil-simple-line"></i>
              </button>
              <button type="button" class="btn text-danger p-0 fs-5" @click="destroyData(item.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip">
                <i class="ph ph-trash"></i>
              </button>
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
    <div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center mb-3 gap-2">
          <div>
            <label>
              Show
              <select v-model="pagination.meta.per_page" @change="fetchTableData(1)" class="form-select form-select-sm w-auto d-inline-block">
                <option v-for="option in [10, 25, 50, 100]" :key="option" :value="option">{{ option }}</option>
              </select>
              entries
            </label>
          </div>
          <div>Showing {{ pagination.meta.from || 0 }} to {{ pagination.meta.to || 0 }} of {{ pagination.meta.total || 0 }} entries</div>
        </div>
        <div>
          <nav aria-label="Page navigation">
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: !pagination?.links.prev }">
                <a class="page-link" href="#" @click.prevent="fetchTableData(pagination.meta.current_page - 1)"> Previous </a>
              </li>
              <li class="page-item" v-for="page in generatePageNumbers()" :key="page" :class="{ active: page === pagination.meta.current_page }">
                <a class="page-link" href="#" @click.prevent="fetchTableData(page)">
                  {{ page }}
                </a>
              </li>
              <li class="page-item" :class="{ disabled: !pagination?.links.next }">
                <a class="page-link" href="#" @click.prevent="fetchTableData(pagination.meta.current_page + 1)"> Next </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import FormAddOffcanvas from './Forms/FormAddOffcanvas.vue'
import { LISTING_URL, DELETE_URL } from '@/vue/constants/custom_form'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { confirmSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const tableId = ref(null)
const changeId = (id) => {
  tableId.value = id
}
const pagination = ref({
  meta: {
    from: 0,
    to: 0,
    total: 0,
    current_page: 1,
    last_page: 1,
    per_page: 10
  },
  links: {
    prev: null,
    next: null
  }
})
// Request
const { getRequest, deleteRequest, listingRequest } = useRequest()

onMounted(() => {
  // Hide the sidebar on mounted
  const sidebarMenu = document.getElementById('setting-sidebar-menu')
  if (sidebarMenu) {
    sidebarMenu.style.display = 'none'
  }

  // Change the class of the main content div to full width
  const mainContent = document.getElementById('setting-main-content')
  if (mainContent) {
    // Replace column classes with full-width class
    mainContent.classList.remove('col-md-8', 'col-lg-9')
    mainContent.classList.add('col-md-12', 'col-lg-12')
  }

  // Change the content of the breadcrumb title
  const moduleTitle = document.getElementById('module-titlee1')
  if (moduleTitle) {
    moduleTitle.textContent = 'PROMs' // Set your desired title here
  }

  // Get the element by ID and replace its content
  const breadcrumbElement = document.getElementById('breadcrumbcustom')
  if (breadcrumbElement) {
    breadcrumbElement.textContent = 'Custom Forms'
  }

  fetchTableData()
})

const generatePageNumbers = () => {
  const totalPages = pagination.value.meta.last_page
  return Array.from({ length: totalPages }, (_, i) => i + 1)
}

// Define variables
const tableList = ref(null)

// fetch all data
// listingRequest({ url: GET_CUSTOM_FORM, data: { form_id: formId.value, appointent_id: appointmentId.value, type: appointmentType.value } }).then((res)
const fetchTableData = (page = pagination.value.meta.current_page, perPage = pagination.value.meta.per_page) => {
  console.log(page, perPage)
  listingRequest({ url: LISTING_URL, data: { page, perPage } }).then((res) => {
    if (res.status) {
      tableList.value = res.data
      pagination.value.meta = res.meta
      pagination.value.links = res.links
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
</script>
