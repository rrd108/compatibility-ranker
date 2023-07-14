<script setup lang="ts">
  import { computed } from 'vue'

  const props = defineProps<{
    points: number
  }>()

  const ranges = [
    [1, 17],
    [18, 24],
    [25, 27],
    [28, 32],
    [33, 35],
  ]
  const stars = computed(
    () =>
      ranges.findIndex(
        range => props.points >= range[0] && props.points <= range[1]
      ) + 1
  )
</script>

<template>
  <div>
    <font-awesome-icon icon="heart" v-for="i in stars" :key="i" />
  </div>
</template>

<style scoped>
  .svg-inline--fa {
    margin: 0 0.2rem;
    color: #d64933;
  }
</style>
