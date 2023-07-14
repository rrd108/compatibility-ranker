<script setup lang="ts">
  import { ref } from 'vue'
  import Login from '@/components/Login.vue'
  import Ranker from '@/components/Ranker.vue'

  const token = ref(sessionStorage.getItem('cR'))
  const tokenReceived = (tokenString: string) => {
    token.value = tokenString
    sessionStorage.setItem('cR', tokenString)
  }
</script>

<template>
  <div>
    <Login v-if="!token" @tokenReceived="tokenReceived" />
    <Ranker v-if="token" :token="token" />
  </div>
</template>
