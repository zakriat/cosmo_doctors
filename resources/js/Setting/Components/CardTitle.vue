<template>
  <div class="d-flex justify-content-between align-items-center">
    <h4 class="mb-3">
      <i :class="icon"></i> {{ title }}
    </h4>
    <slot></slot>
  </div>
</template>
<script setup>
defineProps({
  title: { type: String, default: '' },
  icon:  { type: String, default: '' }
})
</script>
