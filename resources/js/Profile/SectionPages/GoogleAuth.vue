<template>
  <div>
    <div v-if="!isLoggedIn">
      <button @click="login" class="btn btn-sm btn-primary">
        <span class="d-flex align-items-center">
          <img src="/img/logo/google.svg" class="mr-1" alt="Google" />
          {{$t('settings.lbl_connect_google')}}
        </span>
      </button>
      <p style="font-weight: 500; margin-top: 15px">{{$t('settings.lbl_google_text')}}</p>
    </div>
    <div v-else>
      <div class="d-flex justify-content-between flex-wrap gap-3" style="margin: 30px 15px">
        <p style="font-weight: 500">{{$t('settings.lbl_success_google')}}</p>
        <button @click="logout" class="btn btn-sm btn-primary">{{$t('settings.lbl_disconnect')}}</button>
      </div>
    </div>
  </div>
</template>

<script>
import { googleSdkLoaded } from 'vue3-google-login'
import axios from 'axios'

export default {
  data() {
    return {
      isLoggedIn: false,
      userDetails: null
    }
  },
  mounted() {
    const storedLogin = localStorage.getItem('isLoggedIn')
    if (storedLogin) {
      const storedTokenExpiry = localStorage.getItem('tokenExpiry')
      const tokenExpiry = new Date(storedTokenExpiry)
      const now = new Date()

      if (tokenExpiry && tokenExpiry > now) {
        // Token is not expired
        this.isLoggedIn = true
      } else {
        // Token is expired or not found
        this.isLoggedIn = false
        localStorage.removeItem('isLoggedIn')
        localStorage.removeItem('userDetails')
      }
    }
  },
  mounted() {
    this.fetchSettingData()
  },
  methods: {
    async fetchSettingData() {
      try {
        const response = await axios.get(`data-configuration`)        
        this.settingData = response.data;
        this.googleClientId = this.settingData.google_clientid;
        this.googleClientSecret = this.settingData.google_secret_key;    
      } catch (error) {
        console.error('Error fetching setting data:', error.response.data)
      }
    },
    async login() {
      googleSdkLoaded((google) => {
        google.accounts.oauth2
          .initCodeClient({
            client_id: this.googleClientId,
            scope: 'email profile openid',
            redirect_uri: 'postmessage',
            callback: (response) => response.code && this.sendCodeToBackend(response.code)
          })
          .requestCode()
      })
    },
    async sendCodeToBackend(code) {
      const response = await axios.post('https://oauth2.googleapis.com/token', {
        code,
        client_id: this.googleClientId,
        client_secret: this.googleClientSecret,
        redirect_uri: 'postmessage',
        grant_type: 'authorization_code'
      })
      const { access_token, expires_in, refresh_token, scope, token_type, id_token } = response.data

      await axios.post('store-access-token', {
        access_token,
        expires_in,
        refresh_token,
        scope,
        token_type,
        id_token,
        is_telmet: 1
      })

      localStorage.setItem('isLoggedIn', true)
      this.isLoggedIn = true
    },
    async logout() {
      try {
        await axios.post('token-revoke')
        localStorage.removeItem('isLoggedIn')
        this.isLoggedIn = false
      } catch (error) {
        console.error('Logout failed:', error.response.data)
      }
    }
  }
}
</script>
