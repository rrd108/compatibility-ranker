<template>
  <div>
    <Login v-if="!token" @token="tokenReceived" />
    <Ranker v-if="token" :token="token" />
  </div>
</template>

<script>
import Login from '@/components/Login'
import Ranker from '@/components/Ranker'

export default {
  name: 'App',
  components: {
    Login,
    Ranker
  },
  data() {
    return {
      token: null,
    }
  },
  methods: {
    tokenReceived(data) {
      this.token = data
      sessionStorage.setItem('cR', data)
    }
  }
}
</script>
