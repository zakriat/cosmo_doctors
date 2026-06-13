<template>
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="mt-4 mb-3 fw-normal">{{$t('customization.menu_hide')}}</h5>
    <div class="form-check fw-normal form-switch">
      <input
        type="checkbox"
        id="sidebar-hide"
        class="form-check-input"
        :checked="sidebarShow"
        @change="onUpdate"
      />
      
    </div>
  </div>
</template>

<script setup>
import { useSetting } from '@/store/index';
import { computed } from 'vue';

const store = useSetting();
const sidebarShow = computed({
  get: () => store.setting.sidebar_show.value === 'sidebar-none',
  set: (value) => {
    store.sidebar_show(value ? 'sidebar-none' : 'sidebar');
  },
});

const onUpdate = (event) => {
  sidebarShow.value = event.target.checked;
};
</script>
