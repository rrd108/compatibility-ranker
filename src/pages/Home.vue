<script>
  import Login from '@/components/Login.vue'
  import Ranker from '@/components/Ranker.vue'

  export default {
    name: 'Home',
    components: {
      Login,
      Ranker,
    },
    data() {
      return {
        token: sessionStorage.getItem('cR'),
      }
    },
    methods: {
      tokenReceived(data) {
        this.token = data
        sessionStorage.setItem('cR', data)
      },
    },
  }
</script>

<template>
  <div>
    <Login v-if="!token" @token="tokenReceived" />
    <Ranker v-if="token" :token="token" />
  </div>
</template>
