<script setup lang="ts">
  import Login from '@/components/Login.vue'
  import Ranker from '@/components/Ranker.vue'
  import { useStore } from '@/store'

  const store = useStore()
</script>

<template>
  <div>
    <Login v-if="!store.token" />
    <Ranker v-if="store.token" />
  </div>
</template>
