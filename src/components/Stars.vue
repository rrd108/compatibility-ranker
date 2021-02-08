<template>
  <div>
    <font-awesome-icon icon="heart" v-for="i in stars" :key="i" />
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

<style scoped>
.svg-inline--fa {
  margin: 0 0.2rem;
  color: #d64933;
}
</style>