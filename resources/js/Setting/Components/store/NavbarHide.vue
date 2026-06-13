<template>
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="mt-4 mb-3 fw-normal">{{$t('customization.navbar_hide')}}</h5>
    <div class="form-check fw-normal form-switch">
      <checkbox-input btn-name="navbar_show" type="switch" label-title="Mini" label-class="overflow-hidden fw-normal p-0" id="navbar-hide" :value="store.setting.navbar_show.value == 'iq-navbar' ? 'iq-navbar iq-navbar-none' : 'iq-navbar'" @onChange="onUpdate"> </checkbox-input>
    </div>
  </div>
</template>
<script setup>
import {useSetting} from '@/store/index'
import CheckboxInput from '../store/elements/CheckboxInput.vue'
const store = useSetting()
console.log(store.setting.navbar_show);
console.log(store.setting.navbar_show.value)
const onUpdate = (value) => {
  console.log(value);
  store.navbar_show(value[0])
}
</script>
