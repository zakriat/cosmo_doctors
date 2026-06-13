<template>
  <div class="offcanvas-footer border-top pt-4">
    <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
      <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">
        {{ $t('messages.cancel') }}
      </button>
      <button :disabled="IS_SUBMITED" class="btn btn-secondary" name="submit">
        <template v-if="IS_SUBMITED">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
        </template>
        <template v-else> {{ $t('messages.save') }}</template>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  IS_SUBMITED: false
})
</script>

