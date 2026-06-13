<template>
  <div class="offcanvas-header border-bottom">
    <h4 class="offcanvas-title" id="form-offcanvasLabel">
      <template v-if="currentId != 0">
        <span>{{ editTitle }}</span>
      </template>
      <template v-else>
        <span>{{ createTitle }}</span>
      </template>
    </h4>
    <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="btn-close-offcanvas"><i class="ph ph-x-circle"></i></button>
  </div>
</template>

<script setup>
defineProps({
  currentId: { type: Number, default: 0 },
  editTitle: { type: String, default: '' },
  createTitle: { type: String, default: '' }
})
</script>
