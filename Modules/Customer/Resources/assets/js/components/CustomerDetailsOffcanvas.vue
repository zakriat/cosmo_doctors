<template>
    <div class="offcanvas offcanvas-end offcanvas-w-40 " tabindex="-1" id="customerDetails-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="$t('customer.details')"></FormHeader>
        <div class="offcanvas-body">
            <h5>{{ $t('customer.patient_information') }}</h5>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-sm-row flex-column align-items-sm-center flex-wrap">
                        <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-80 avatar-rounded mb-2"/>
                        <div>
                            <h5 class="mb-2">{{ customer.full_name ?? '-' }}</h5>
                            <div class="d-flex flex-wrap gap-lg-5 gap-2">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <h5 class="mb-0"><i class="ph ph-envelope align-middle"></i></h5>
                                    <span class="text-secondary">{{ customer.email ?? '-' }}</span>
                                </div>
                                <div class="d-inline-flex align-items-center gap-2" v-if="customer.mobile">
                                    <h5 class="mb-0"><i class="ph ph-phone align-middle"></i></h5>
                                    <span class="text-primary" >{{ customer.mobile ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 mt-5" v-if="customer.profile && customer.profile.about_self">
                        <h6>{{ $t('customer.about') }}:</h6>
                        <p>{{ customer.profile.about_self ?? '-' }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-3">
                        <div>
                            <p class="mb-1">{{ $t('customer.total_reviews') }}</p>
                            <h6 class="mb-0">{{customer.total_rating ?? '-'}}</h6>
                        </div>
                        <div>
                            <p class="mb-1">{{ $t('customer.total_appointments') }}</p>
                            <h6 class="mb-0">{{ customer.total_appointment ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ $t('customer.appointments') }}</h5>
                <div v-if="customer.appointments && customer.appointments.length > 0">
                    <a @click.prevent="appointment_viewall()" role="button">{{ $t('customer.view_all') }}</a>
                </div>
            </div>
            <div v-if="customer.appointments && customer.appointments.length > 0">
                <div class="row ">
                    <div class="col-lg-6" v-for="(appointment, index) in customer.appointments" :key="appointment.id">
                        <div class="card card-block card-height" v-if="index < 5">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="div">
                                            <div class="text-secondary small fw-semibold d-flex align-items-center gap-2 flex-wrap">
                                                <span>{{ formatDate(appointment.appointment_date ) ?? '-'}}</span>
                                                <span><i class="ph ph-line-vertical"></i></span>
                                                <span>{{ formatTimeRange(appointment.appointment_time, appointment.duration) ?? '-'}}</span>
                                            </div>
                                            <!-- <p class="text-danger">{{ formatDate(appointment.appointment_date) }} | {{ formatTimeRange(appointment.appointment_time, appointment.duration) }} </p> -->
                                            <h4 class="mt-4 mb-2">{{ appointment.clinicservice.name ?? '-' }}</h4>
                                            <p class="mb-0">{{ $t('customer.doctor_name') }} : {{ appointment.doctor.first_name  ?? '-'}}  {{ appointment.doctor.last_name  ?? '-'}}</p> 
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h4 class="text-primary mb-0">$ {{ appointment.clinicservice.charges  ?? '-'}}</h4>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="text-center">{{ $t('customer.data_not_found') }}</p>
                    </div>
                </div>  
            </div>
        </div>
        
    </div>
</template>

<script setup>
 import { ref, onMounted ,reactive } from 'vue'
 import { CUSTOMER_URL} from '../constant/customer'
 import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
 import { useForm, useField } from 'vee-validate'
 import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
 import moment from 'moment'
 const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)

 const defaultImage='https://dummyimage.com/600x300/cfcfcf/000000.png';
 const { getRequest } = useRequest()

 const ImageViewer = ref(null)
 const customer=ref({});

 const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');

 function appointment_viewall(){
    window.location.href = `appointments?user_id=${customer.value.id}`;
 }

 const DoctorId = useModuleId(() => {

getRequest({ url: CUSTOMER_URL, id: DoctorId.value }).then((res) => {
    if (res.status) {
        customer.value = res.data;
        console.log(customer.value);
        ImageViewer.value = customer.value.profile_image
    }
    })
  }, 'customer-details')
  const datetimeformate = () => {
      const datefrm = window.dateformate
      console.log(datefrm)
      const timefrm = window.timeformate
   }
   onMounted(() => {
    datetimeformate()
  })
  const padZero = (num) => (num < 10 ? `0${num}` : num);

const formatDate = (dateString) => {
  const datefrm = window.dateformate || 'Y-m-d';
  const date = new Date(dateString);

  const year = date.getFullYear();
  const month = padZero(date.getMonth() + 1); // Months are 0-based
  const day = padZero(date.getDate());
  const hours = padZero(date.getHours());
  const minutes = padZero(date.getMinutes());
  const seconds = padZero(date.getSeconds());

  const formatMap = {
    'Y-m-d': `${year}-${month}-${day}`,
    'm-d-Y': `${month}-${day}-${year}`,
    'd-m-Y': `${day}-${month}-${year}`,
    'd/m/Y': `${day}/${month}/${year}`,
    'm/d/Y': `${month}/${day}/${year}`,
    'Y/m/d': `${year}/${month}/${day}`,
    'Y.m.d': `${year}.${month}.${day}`,
    'd.m.Y': `${day}.${month}.${year}`,
    'm.d.Y': `${month}.${day}.${year}`,
    'jS M Y': `${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${date.toLocaleString('default', { month: 'short' })} ${year}`,
    'M jS Y': `${date.toLocaleString('default', { month: 'short' })} ${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${year}`,
    'D, M d, Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.toLocaleString('default', { month: 'short' })} ${day}, ${year}`,
    'D, d M, Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${day} ${date.toLocaleString('default', { month: 'short' })}, ${year}`,
    'D, M jS Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.toLocaleString('default', { month: 'short' })} ${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${year}`,
    'D, jS M Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${date.toLocaleString('default', { month: 'short' })} ${year}`,
    'F j, Y': `${date.toLocaleString('default', { month: 'long' })} ${date.getDate()}, ${year}`,
    'd F, Y': `${date.getDate()} ${date.toLocaleString('default', { month: 'long' })}, ${year}`,
    'jS F, Y': `${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${date.toLocaleString('default', { month: 'long' })}, ${year}`,
    'l jS F Y': `${date.toLocaleString('default', { weekday: 'long' })} ${date.getDate()}${['th', 'st', 'nd', 'rd'][date.getDate() % 10 > 3 ? 0 : (date.getDate() % 100 - date.getDate() % 10 != 10) * date.getDate() % 10]} ${date.toLocaleString('default', { month: 'long' })} ${year}`,
    'l, F j, Y': `${date.toLocaleString('default', { weekday: 'long' })}, ${date.toLocaleString('default', { month: 'long' })} ${date.getDate()}, ${year}`
  };

  return formatMap[datefrm] || `${year}-${month}-${day}`;
};
    // const formatDate = (dateString) => {
    //   const date = new Date(dateString);
    //   const options = { day: '2-digit', month: 'short', year: 'numeric' };
    //   const formattedDate = date.toLocaleDateString('en-US', options);
    //   const [month, day, year] = formattedDate.split(' ');
    //   return `${day} ${month}, ${year}`;
    // }

    const formatTimeRange = (startTime, duration) => {
        const formatTime = (time) => {
            const parsedTime = new Date(`2000-01-01T${time}`);
            const formattedTime = parsedTime.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            });
            return formattedTime.replace(/^0/, '');  
        };

        const [startHour, startMinute] = startTime.split(':').map(Number);
        const endMinute = startMinute + duration;
        const endHour = startHour + Math.floor(endMinute / 60);
        const formattedEndHour = endHour % 12 || 12;  
        const formattedEndMinute = endMinute % 60;

        const formattedStartTime = formatTime(startTime);
        const formattedEndTime = formatTime(`${formattedEndHour.toString().padStart(2, '0')}:${formattedEndMinute.toString().padStart(2, '0')}`);

        return `${formattedStartTime} - ${formattedEndTime}`;
    };
 

</script>
