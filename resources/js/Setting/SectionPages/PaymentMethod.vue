<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_payment')" icon="fa-solid fa-coins"></CardTitle>
    </div>
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="payment_method_razorpay">{{ $t('setting_payment_method.lbl_razorpay') }} </label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="razor_payment_method"
            :checked="razor_payment_method == 1 ? true : false" name="razor_payment_method" id="payment_method_razorpay"
            type="checkbox" v-model="razor_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="razor_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="razorpay_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="razorpay_secretkey" id="razorpay_secretkey"
              name="razorpay_secretkey" :errorMessage="errors.razorpay_secretkey"
              :errorMessages="errorMessages.razorpay_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.razorpay_secretkey" :key="msg">{{ msg }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="razorpay_publickey">{{ $t('setting_payment_method.lbl_app_key') }}</label>
            <input type="text" class="form-control" v-model="razorpay_publickey" id="razorpay_publickey"
              name="razorpay_publickey" :errorMessage="errors.razorpay_publickey"
              :errorMessages="errorMessages.razorpay_publickey" />
            <p class="text-danger" v-for="msg in errorMessages.razorpay_publickey" :key="msg">{{ msg }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="payment_method_stripe">{{ $t('setting_payment_method.lbl_stripe') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="str_payment_method"
            :checked="str_payment_method == 1 ? true : false" name="str_payment_method" id="payment_method_stripe"
            type="checkbox" v-model="str_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="str_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="stripe_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="stripe_secretkey" id="stripe_secretkey"
              name="stripe_secretkey" :errorMessage="errors.stripe_secretkey"
              :errorMessages="errorMessages.stripe_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.stripe_secretkey" :key="msg">{{ msg }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="stripe_publickey">{{ $t('setting_payment_method.lbl_app_key') }}</label>
            <input type="text" class="form-control" v-model="stripe_publickey" id="stripe_publickey"
              name="stripe_publickey" :errorMessage="errors.stripe_publickey"
              :errorMessages="errorMessages.stripe_publickey" />
            <p class="text-danger" v-for="msg in errorMessages.stripe_publickey" :key="msg">{{ msg }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="payment_method_paystack">{{ $t('setting_payment_method.lbl_paystack') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="paystack_payment_method"
            :checked="paystack_payment_method == 1 ? true : false" name="paystack_payment_method" id="payment_method_paystack"
            type="checkbox" v-model="paystack_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="paystack_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="paystack_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="paystack_secretkey" id="paystack_secretkey"
              name="paystack_secretkey" :errorMessage="errors.paystack_secretkey"
              :errorMessages="errorMessages.paystack_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.paystack_secretkey" :key="msg">{{ msg }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="paystack_publickey">{{ $t('setting_payment_method.lbl_app_key') }}</label>
            <input type="text" class="form-control" v-model="paystack_publickey" id="paystack_publickey"
              name="paystack_publickey" :errorMessage="errors.paystack_publickey"
              :errorMessages="errorMessages.paystack_publickey" />
            <p class="text-danger" v-for="msg in errorMessages.paystack_publickey" :key="msg">{{ msg }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="payment_method_paypal">{{ $t('setting_payment_method.lbl_paypal') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="paypal_payment_method"
            :checked="paypal_payment_method == 1 ? true : false" name="paypal_payment_method" id="payment_method_paypal"
            type="checkbox" v-model="paypal_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="paypal_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="paypal_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="paypal_secretkey" id="paypal_secretkey"
              name="paypal_secretkey" :errorMessage="errors.paypal_secretkey"
              :errorMessages="errorMessages.paypal_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.paypal_secretkey" :key="msg">{{ msg }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="paypal_clientid">{{ $t('setting_payment_method.lbl_client_id') }}</label>
            <input type="text" class="form-control" v-model="paypal_clientid" id="paypal_clientid"
              name="paypal_clientid" :errorMessage="errors.paypal_clientid"
              :errorMessages="errorMessages.paypal_clientid" />
            <p class="text-danger" v-for="msg in errorMessages.paypal_clientid" :key="msg">{{ msg }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group border-bottom pb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label m-0" for="flutterwave_method_paypal">{{ $t('setting_payment_method.lbl_flutterwave') }}</label>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="flutterwave_payment_method"
            :checked="flutterwave_payment_method == 1 ? true : false" name="flutterwave_payment_method" id="flutterwave_method_paypal"
            type="checkbox" v-model="flutterwave_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="flutterwave_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="flutterwave_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="flutterwave_secretkey" id="flutterwave_secretkey"
              name="flutterwave_secretkey" :errorMessage="errors.flutterwave_secretkey"
              :errorMessages="errorMessages.flutterwave_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.flutterwave_secretkey" :key="msg">{{ msg }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-label" for="flutterwave_publickey">{{ $t('setting_payment_method.lbl_app_key') }}</label>
            <input type="text" class="form-control" v-model="flutterwave_publickey" id="flutterwave_publickey"
              name="flutterwave_publickey" :errorMessage="errors.flutterwave_publickey"
              :errorMessages="errorMessages.flutterwave_publickey" />
            <p class="text-danger" v-for="msg in errorMessages.flutterwave_publickey" :key="msg">{{ msg }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="airtel_money">{{ $t('setting_payment_method.lbl_airtel_money') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="airtel_payment_method"
            :checked="airtel_payment_method == 1 ? true : false" name="airtel_payment_method" id="airtel_payment_method"
            type="checkbox" v-model="airtel_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="airtel_payment_method == 1">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label for="airtel_clientid">{{ $t('setting_payment_method.lbl_client_id') }}</label>
            <input type="text" class="form-control" v-model="airtel_clientid" id="airtel_clientid"
              name="airtel_clientid" :errorMessage="errors.airtel_clientid"
              :errorMessages="errorMessages.airtel_clientid" />
            <p class="text-danger" v-for="msg in errorMessages.airtel_clientid" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.airtel_clientid }}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="airtel_secretkey">{{ $t('setting_payment_method.lbl_secret_key') }}</label>
            <input type="text" class="form-control" v-model="airtel_secretkey" id="airtel_secretkey"
              name="airtel_secretkey" :errorMessage="errors.airtel_secretkey"
              :errorMessages="errorMessages.airtel_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.airtel_secretkey" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.airtel_secretkey }}</span>
          </div>
        </div>
       
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="phone_pay">{{ $t('setting_payment_method.lbl_phone_pay') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="phonepay_payment_method"
            :checked="phonepay_payment_method == 1 ? true : false" name="phonepay_payment_method" id="phonepay_payment_method"
            type="checkbox" v-model="phonepay_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="phonepay_payment_method == 1">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label for="phonepay_app_id">{{ $t('setting_payment_method.lbl_app_id') }}</label>
            <input type="text" class="form-control" v-model="phonepay_app_id" id="phonepay_app_id"
              name="phonepay_app_id" :errorMessage="errors.phonepay_app_id"
              :errorMessages="errorMessages.phonepay_app_id" />
            <p class="text-danger" v-for="msg in errorMessages.phonepay_app_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.phonepay_app_id }}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="phonepay_merchant_id">{{ $t('setting_payment_method.lbl_merchant_id') }}</label>
            <input type="text" class="form-control" v-model="phonepay_merchant_id" id="phonepay_merchant_id"
              name="phonepay_merchant_id" :errorMessage="errors.phonepay_merchant_id"
              :errorMessages="errorMessages.phonepay_merchant_id" />
            <p class="text-danger" v-for="msg in errorMessages.phonepay_merchant_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.phonepay_merchant_id }}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="phonepay_salt_key">{{ $t('setting_payment_method.lbl_salt_key') }}</label>
            <input type="text" class="form-control" v-model="phonepay_salt_key" id="phonepay_salt_key"
              name="phonepay_salt_key" :errorMessage="errors.phonepay_salt_key"
              :errorMessages="errorMessages.phonepay_salt_key" />
            <p class="text-danger" v-for="msg in errorMessages.phonepay_salt_key" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.phonepay_salt_key }}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="phonepay_salt_index">{{ $t('setting_payment_method.lbl_salt_index') }}</label>
            <input type="number" class="form-control" v-model="phonepay_salt_index" id="phonepay_salt_index"
              name="phonepay_salt_index" :errorMessage="errors.phonepay_salt_index"
              :errorMessages="errorMessages.phonepay_salt_index" />
            <p class="text-danger" v-for="msg in errorMessages.phonepay_salt_index" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.phonepay_salt_index }}</span>
          </div>
        </div>
       
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="midtrans">{{ $t('setting_payment_method.lbl_midtrans') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="midtrans_payment_method"
            :checked="midtrans_payment_method == 1 ? true : false" name="midtrans_payment_method" id="midtrans_payment_method"
            type="checkbox" v-model="midtrans_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="midtrans_payment_method == 1">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label for="midtrans_clientid">{{ $t('setting_payment_method.lbl_client_id') }}</label>
            <input type="text" class="form-control" v-model="midtrans_clientid" id="midtrans_clientid"
              name="midtrans_clientid" :errorMessage="errors.midtrans_clientid"
              :errorMessages="errorMessages.midtrans_clientid" />
            <p class="text-danger" v-for="msg in errorMessages.midtrans_clientid" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.midtrans_clientid }}</span>
          </div>
        </div>

      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="cinet">{{ $t('setting_payment_method.lbl_cinet') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="cinet_payment_method"
            :checked="cinet_payment_method == 1 ? true : false" name="cinet_payment_method" id="cinet_payment_method"
            type="checkbox" v-model="cinet_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="cinet_payment_method == 1">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label for="cinet_siteid">{{ $t('setting_payment_method.cinet_siteid') }}</label>
            <input type="text" class="form-control" v-model="cinet_siteid" id="cinet_siteid"
              name="cinet_siteid" :errorMessage="errors.cinet_siteid"
              :errorMessages="errorMessages.cinet_siteid" />
            <p class="text-danger" v-for="msg in errorMessages.cinet_siteid" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.cinet_siteid }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="cinet_apikey">{{ $t('setting_payment_method.cinet_apikey') }}</label>
            <input type="text" class="form-control" v-model="cinet_apikey" id="cinet_apikey"
              name="cinet_apikey" :errorMessage="errors.cinet_apikey"
              :errorMessages="errorMessages.cinet_apikey" />
            <p class="text-danger" v-for="msg in errorMessages.cinet_apikey" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.cinet_apikey }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="cinet_secretkey">{{ $t('setting_payment_method.cinet_secretkey') }}</label>
            <input type="text" class="form-control" v-model="cinet_secretkey" id="cinet_secretkey"
              name="cinet_secretkey" :errorMessage="errors.cinet_secretkey"
              :errorMessages="errorMessages.cinet_secretkey" />
            <p class="text-danger" v-for="msg in errorMessages.cinet_secretkey" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.cinet_secretkey }}</span>
          </div>
        </div>

      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="sadad">{{ $t('setting_payment_method.lbl_sadad') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="sadad_payment_method"
            :checked="sadad_payment_method == 1 ? true : false" name="sadad_payment_method" id="sadad_payment_method"
            type="checkbox" v-model="sadad_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="sadad_payment_method == 1">
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label for="sadad_id">{{ $t('setting_payment_method.sadad_id') }}</label>
            <input type="text" class="form-control" v-model="sadad_id" id="sadad_id"
              name="sadad_id" :errorMessage="errors.sadad_id"
              :errorMessages="errorMessages.sadad_id" />
            <p class="text-danger" v-for="msg in errorMessages.sadad_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.sadad_id }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="sadad_key">{{ $t('setting_payment_method.sadad_key') }}</label>
            <input type="text" class="form-control" v-model="sadad_key" id="sadad_key"
              name="sadad_key" :errorMessage="errors.sadad_key"
              :errorMessages="errorMessages.sadad_key" />
            <p class="text-danger" v-for="msg in errorMessages.sadad_key" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.sadad_key }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="sadad_domain">{{ $t('setting_payment_method.sadad_domain') }}</label>
            <input type="text" class="form-control" v-model="sadad_domain" id="sadad_domain"
              name="sadad_domain" :errorMessage="errors.sadad_domain"
              :errorMessages="errorMessages.sadad_domain" />
            <p class="text-danger" v-for="msg in errorMessages.sadad_domain" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.sadad_domain }}</span>
          </div>
        </div>

      </div>
    </div>

    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
//
import * as yup from 'yup';
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      razor_payment_method: data.razor_payment_method || 0,
      razorpay_secretkey: data.razorpay_secretkey || '',
      razorpay_publickey: data.razorpay_publickey || '',
      str_payment_method: data.str_payment_method || 0,
      stripe_secretkey: data.stripe_secretkey || '',
      stripe_publickey: data.stripe_publickey || '',
      paystack_payment_method: data.paystack_payment_method || 0,
      paystack_secretkey: data.paystack_secretkey || '',
      paystack_publickey: data.paystack_publickey || '',
      paypal_payment_method: data.paypal_payment_method || 0,
      paypal_secretkey: data.paypal_secretkey || '',
      paypal_clientid: data.paypal_clientid || '',
      flutterwave_payment_method: data.flutterwave_payment_method || 0,
      flutterwave_secretkey: data.flutterwave_secretkey || '',
      flutterwave_publickey: data.flutterwave_publickey || '',
      airtel_payment_method: data.airtel_payment_method || 0,
      airtel_secretkey: data.airtel_secretkey || '',
      airtel_clientid: data.airtel_clientid || '',
      phonepay_payment_method: data.phonepay_payment_method || 0,
      phonepay_app_id: data.phonepay_app_id || '',
      phonepay_merchant_id: data.phonepay_merchant_id || '',
      phonepay_salt_key: data.phonepay_salt_key || '',
      phonepay_salt_index: data.phonepay_salt_index || '',
      midtrans_payment_method: data.midtrans_payment_method || 0,
      midtrans_clientid: data.midtrans_clientid || '',
      cinet_payment_method: data.cinet_payment_method || 0,
      cinet_siteid: data.cinet_siteid || '',
      cinet_apikey: data.cinet_apikey || '',
      cinet_secretkey: data.cinet_secretkey || '',
      sadad_payment_method: data.sadad_payment_method || 0,
      sadad_id: data.sadad_id || '',
      sadad_key: data.sadad_key || '',
      sadad_domain: data.sadad_domain || '',
    }
  })
}
const validationSchema = yup.object({
  razorpay_secretkey: yup.string().test('razorpay_secretkey', 'Must be a valid RazorPay key', function (value) {
    if (this.parent.razor_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  razorpay_publickey: yup.string().test("Must be a valid RazorPay Publickey", function (value) {
    if (this.parent.razor_payment_method == '1' && !value) {
    
      return false;
    }
    return true
  }),
  stripe_secretkey: yup.string().test('stripe_secretkey', 'Must be a valid Stripe key', function (value) {
    if (this.parent.str_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  stripe_publickey: yup.string().test("Must be a valid Stripe Publickey", function (value) {
    if (this.parent.str_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  paystack_secretkey: yup.string().test('paystack_secretkey', 'Must be a valid Paystack key', function (value) {
    if (this.parent.paystack_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  paystack_publickey: yup.string().test("Must be a valid Paystack Publickey", function (value) {
    if (this.parent.paystack_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  paypal_secretkey: yup.string().test('paypal_secretkey', 'Must be a valid Paypal key', function (value) {
    if (this.parent.paypal_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  paypal_clientid: yup.string().test("Must be a valid Paypal Publickey", function (value) {
    if (this.parent.paypal_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  flutterwave_secretkey: yup.string().test('flutterwave_secretkey', 'Must be a valid Flutterwave key', function (value) {
    if (this.parent.flutterwave_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  flutterwave_publickey: yup.string().test("Must be a valid Flutterwave Publickey", function (value) {
    if (this.parent.flutterwave_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  airtel_secretkey: yup.string().test('airtel_secretkey', 'Must be a valid Airtel Money secret key', function (value) {
    if (this.parent.airtel_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  airtel_clientid: yup.string().test('airtel_clientid', "Must be a valid Airtel Money ClientId", function (value) {
    if (this.parent.airtel_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  phonepay_app_id: yup.string().test('phonepay_app_id', "Must be a valid Phone Pay App Id", function (value) {
    if (this.parent.phonepay_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  phonepay_merchant_id: yup.string().test('phonepay_merchant_id', "Must be a valid Phone Pay Merchant Id", function (value) {
    if (this.parent.phonepay_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  phonepay_salt_key: yup.string().test('phonepay_salt_key', "Must be a valid Phone Pay Salt Id", function (value) {
    if (this.parent.phonepay_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  phonepay_salt_index: yup.string().test('phonepay_salt_index', "Must be a valid Phone Pay Salt Index", function (value) {
    if (this.parent.phonepay_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  midtrans_clientid: yup.string().test('midtrans_clientid', "Must be a valid Midtrans ClientId", function (value) {
    if (this.parent.midtrans_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
  cinet_siteid: yup.string().test('cinet_siteid', "Must be a valid Cinet Site Id", function (value) {
    if (this.parent.cinet_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  cinet_apikey: yup.string().test('cinet_apikey', "Must be a valid Cinet Api Key", function (value) {
    if (this.parent.cinet_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  cinet_secretkey: yup.string().test('cinet_secretkey', "Must be a valid Cinet Secret Key", function (value) {
    if (this.parent.cinet_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  sadad_id: yup.string().test('sadad_id', "Must be a valid Sadad Id", function (value) {
    if (this.parent.sadad_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  sadad_key: yup.string().test('sadad_key', "Must be a valid Sadad Key", function (value) {
    if (this.parent.sadad_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),

  sadad_domain: yup.string().test('sadad_domain', "Must be a valid Sadad Domain", function (value) {
    if (this.parent.sadad_payment_method == '1' && !value) {
      return false;
    }
    return true
  }),
})
const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const errorMessages = ref({})
const { value: razor_payment_method } = useField('razor_payment_method')
const { value: razorpay_secretkey } = useField('razorpay_secretkey')
const { value: razorpay_publickey } = useField('razorpay_publickey')
const { value: str_payment_method } = useField('str_payment_method')
const { value: stripe_secretkey } = useField('stripe_secretkey')
const { value: stripe_publickey } = useField('stripe_publickey')
const { value: paystack_payment_method } = useField('paystack_payment_method')
const { value: paystack_secretkey } = useField('paystack_secretkey')
const { value: paystack_publickey } = useField('paystack_publickey')
const { value: paypal_payment_method } = useField('paypal_payment_method')
const { value: paypal_secretkey } = useField('paypal_secretkey')
const { value: paypal_clientid } = useField('paypal_clientid')
const { value: flutterwave_payment_method } = useField('flutterwave_payment_method')
const { value: flutterwave_secretkey } = useField('flutterwave_secretkey')
const { value: flutterwave_publickey } = useField('flutterwave_publickey')
const { value: airtel_payment_method } = useField('airtel_payment_method')
const { value: airtel_secretkey } = useField('airtel_secretkey')
const { value: airtel_clientid } = useField('airtel_clientid')
const { value: phonepay_payment_method } = useField('phonepay_payment_method')
const { value: phonepay_app_id } = useField('phonepay_app_id')
const { value: phonepay_merchant_id } = useField('phonepay_merchant_id')
const { value: phonepay_salt_key } = useField('phonepay_salt_key')
const { value: phonepay_salt_index } = useField('phonepay_salt_index')
const { value: midtrans_payment_method } = useField('midtrans_payment_method')
const { value: midtrans_clientid } = useField('midtrans_clientid')
const { value: cinet_payment_method } = useField('cinet_payment_method')
const { value: cinet_siteid } = useField('cinet_siteid')
const { value: cinet_apikey } = useField('cinet_apikey')
const { value: cinet_secretkey } = useField('cinet_secretkey')
const { value: sadad_payment_method } = useField('sadad_payment_method')
const { value: sadad_id } = useField('sadad_id')
const { value: sadad_key } = useField('sadad_key')
const { value: sadad_domain } = useField('sadad_domain')

watch(() => razor_payment_method.value, (value) => {
  if(value == '0') {
    razorpay_secretkey.value = ''
    razorpay_publickey.value = ''
  }
}, {deep: true})
watch(() => str_payment_method.value, (value) => {
  if(value == '0') {
    stripe_secretkey.value = ''
    stripe_publickey.value = ''
  }
}, {deep: true})
watch(() => paystack_payment_method.value, (value) => {
  if(value == '0') {
    paystack_secretkey.value = ''
    paystack_publickey.value = ''
  }
}, {deep: true})
watch(() => paypal_payment_method.value, (value) => {
  if(value == '0') {
    paypal_secretkey.value = ''
    paypal_clientid.value = ''
  }
}, {deep: true})
watch(() => flutterwave_payment_method.value, (value) => {
  if(value == '0') {
    flutterwave_secretkey.value = ''
    flutterwave_publickey.value = ''
  }
}, {deep: true})
watch(() => airtel_payment_method.value, (value) => {
  if(value == '0') {
    airtel_secretkey.value = ''
    airtel_clientid.value = ''
  }
}, {deep: true})
watch(() => phonepay_payment_method.value, (value) => {
  if(value == '0') {
    phonepay_app_id.value = ''
    phonepay_merchant_id.value = ''
    phonepay_salt_key.value = ''
    phonepay_salt_index.value = ''
  }
}, {deep: true})

watch(() => midtrans_payment_method.value, (value) => {
  if(value == '0') {
    midtrans_clientid.value = ''
  }
}, {deep: true})

watch(() => cinet_payment_method.value, (value) => {
  if(value == '0') {
    cinet_siteid.value = ''
    cinet_apikey.value = ''
    cinet_secretkey.value = ''
  }
}, {deep: true})

watch(() => sadad_payment_method.value, (value) => {
  if(value == '0') {
    sadad_id.value = ''
    sadad_key.value = ''
    sadad_domain.value = ''
  }
}, {deep: true})
// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
  }
}

//fetch data
const data = 'razor_payment_method,razorpay_secretkey,razorpay_publickey,str_payment_method,stripe_secretkey,stripe_publickey,paystack_payment_method,paystack_secretkey,paystack_publickey,paypal_payment_method,paypal_secretkey,paypal_clientid,,flutterwave_payment_method,flutterwave_secretkey,flutterwave_publickey,airtel_payment_method,airtel_secretkey,airtel_clientid,phonepay_payment_method,phonepay_app_id,phonepay_merchant_id,phonepay_salt_key,phonepay_salt_index,midtrans_payment_method,midtrans_clientid,cinet_payment_method,cinet_siteid,cinet_apikey,cinet_secretkey,sadad_payment_method,sadad_id,sadad_key,sadad_domain,'
onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const newValues = {}
  Object.keys(values).forEach((key) => {
    if(values[key] !== '') {
      newValues[key] = values[key] || ''
    }
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
  }).then((res) => display_submit_message(res))
})

defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})
</script>
