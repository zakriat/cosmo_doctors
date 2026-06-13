<template>
  <div>
    <!-- Tab Buttons -->
    <ul class="nav nav-underline gap-4 mb-3" role="tablist">
      <li v-for="tab in tabs" :key="tab.id" class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTabId === tab.id }" @click="changeTab(tab.id)" type="button" role="tab">
          {{ parseFormData(tab.formdata).form_title }}
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <div v-for="tab in tabs" :key="tab.id" class="tab-pane fade" :class="{ 'show active': activeTabId === tab.id }" role="tabpanel">
        <customform :form_id="tab.id" :appointment_id="appointmentId" :type="'appointment'" :title="parseFormData(tab.formdata).form_title"></customform>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  tabs: { type: Array, required: true },
  appointmentId: { type: Number, required: true }
})

// Active tab state
const activeTabId = ref(props.tabs.length ? props.tabs[0].id : null)

// Method to change active tab
function changeTab(tabId = 1) {
  console.log('Switching tab to ID:', tabId) // Debugging
  activeTabId.value = tabId
}

// Safely parse JSON formdata
function parseFormData(formdata) {
  try {
    return JSON.parse(formdata)
  } catch (e) {
    console.error('Invalid formdata JSON:', formdata)
    return {}
  }
}
</script>
