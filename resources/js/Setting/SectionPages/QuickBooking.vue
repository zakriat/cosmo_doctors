<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_quick_booking')" icon="fa-solid fa-paper-plane"></CardTitle>
    </div>
   

  <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="quick_booking_method">{{ $t('quick_booking.lbl_quick_booking') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="clinic"
            :checked="clinic == 1 ? true : false" name="clinic" id="clinic"
            type="checkbox" v-model="clinic" />
        </div>
      </div>
    </div>

  <div v-if="clinic == 1"  class="mb-3">
    <!-- <pre class="heading-color">&lt;iframe&nbsp;src=&quot;http://127.0.0.1:8000/quick&#45;booking&quot;&nbsp;frameborder=&quot;0&quot;&nbsp;scrolling=&quot;yes&quot;&nbsp;style=&quot;display:block;&nbsp;width:100%;&nbsp;height:100vh;&quot;&gt;&lt;/iframe&gt;</pre> -->
    <h6>{{ $t('quick_booking.lbl_shared_link') }}</h6>
     <a :href="clinic_url" target="_blank">{{ clinic_url }}</a>
  </div>

  <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, onMounted} from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import SubmitButton from './Forms/SubmitButton.vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { createRequest } from '@/helpers/utilities'
import { GET_URL, STORE_URL } from '@/vue/constants/setting'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup';

const { storeRequest } = useRequest()

const url = ref('') // Set the URL
const clinic_url = ref('')
const IS_SUBMITED = ref(false)

const setFormData = (data) => {
  console.log(data.clinic);
  resetForm({
    values: {
      clinic: data.clinic || 0,
    }
  })
}

const enable_multi_vendor = () => {
  return window.multiVendorEnabled
}


const role = () => {
    return window.auth_role[0];
}

const auth_id = () => {

    return window.id;
}



const validationSchema = yup.object({
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })


const { value: clinic } = useField('clinic')

const data = 'clinic';

onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)

    if(enable_multi_vendor()==1 && role()=='vendor' && auth_id() !=null ){

      clinic_url.value = `${response.clinic_booking_url}?id=${auth_id()}`;

    }else{

      clinic_url.value = response.clinic_booking_url
   
    }
     
  })
})

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const newValues = {}
  Object.keys(values).forEach((key) => {
    newValues[key] = values[key] || '0'
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
  }).then((res) => display_submit_message(res))
})

const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
  }
}


</script>
<style scoped>
.copy-icon {
  cursor: pointer;
  margin-left: 0.5rem;
}

.copy-icon i {
  color: #888;
}
</style>




