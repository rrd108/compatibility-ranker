<template>
  <div>
    <font-awesome-icon icon="star" v-for="i in stars" :key="i" />
  </div>
</template>

<script>
export default {
  name: 'Stars',
  props: ['points'],
  data() {
    return {
      ranges: [[1,17], [18,24], [25,27], [28,32], [33,36]],
    }
  },
  computed: {
    stars() {
      return this.ranges.findIndex(range => (this.points >= range[0] && this.points <= range[1])) + 1
    }
  }
}
</script>

<style></style>